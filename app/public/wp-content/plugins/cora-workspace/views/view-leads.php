<?php
/**
 * Cora Platform — Enterprise Lead Management Suite View
 *
 * Provides full CRM inquiry pipeline, interactive drag & drop Kanban board,
 * searchable directory table, funnel analytics, activity log timeline,
 * direct outreach, and right-sliding side drawer sheets.
 *
 * @package Cora_Workspace
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

?>
<script>
window.coraData = window.coraData || {};
window.coraData.ajax_url = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
window.coraData.nonce = '<?php echo wp_create_nonce( 'cora_ajax_nonce' ); ?>';

// Immediate Window Global Subtab Switcher (guarantees availability before DOM ready)
window.coraSwitchLeadSubtab = function(tabName) {
    const activeClasses = 'active bg-white text-zinc-950 dark:bg-zinc-800 dark:text-white shadow-2xs font-bold border border-zinc-200/80 dark:border-zinc-700/80';
    const inactiveClasses = 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white font-medium hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50';
    const classesToRemove = 'active bg-white text-zinc-950 dark:bg-zinc-800 dark:text-white shadow-2xs font-bold border border-zinc-200/80 dark:border-zinc-700/80 bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 shadow-sm font-semibold text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white font-medium hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 text-zinc-600 hover:bg-zinc-100 dark:hover:bg-zinc-800';

    if (window.jQuery) {
        jQuery('.cora-lead-subtab-btn').removeClass(classesToRemove).addClass(inactiveClasses);
        jQuery(`.cora-lead-subtab-btn[data-tab="${tabName}"]`).removeClass(classesToRemove).addClass(activeClasses);
        jQuery('.cora-lead-tab-pane').addClass('hidden');
        jQuery(`#cora-lead-pane-${tabName}`).removeClass('hidden');
    } else {
        document.querySelectorAll('.cora-lead-subtab-btn').forEach(b => {
            const isTarget = b.getAttribute('data-tab') === tabName;
            b.classList.remove('active', 'bg-white', 'text-zinc-950', 'dark:bg-zinc-800', 'dark:text-white', 'shadow-2xs', 'font-bold', 'border', 'border-zinc-200/80', 'dark:border-zinc-700/80');
            if (isTarget) {
                b.className += ' ' + activeClasses;
            } else {
                b.className += ' ' + inactiveClasses;
            }
        });
        document.querySelectorAll('.cora-lead-tab-pane').forEach(p => p.classList.add('hidden'));
        const pane = document.getElementById('cora-lead-pane-' + tabName);
        if (pane) pane.classList.remove('hidden');
    }

    if (window.history && window.history.replaceState) {
        try {
            const url = new URL(window.location);
            url.searchParams.set('sub_page', 'leads');
            url.searchParams.set('subtab', tabName);
            window.history.replaceState(null, '', url);
        } catch(e){}
    }
};

// In-Column Real-Time Search Handler (Immediate Window Global)
window.coraToggleColumnSearch = function(btnEl) {
    const col = btnEl.closest('.cora-kanban-column');
    if (!col) return;
    const searchBox = col.querySelector('.cora-col-search-box');
    if (!searchBox) return;

    // Hide search boxes in other columns for clean UX
    document.querySelectorAll('.cora-col-search-box').forEach(el => {
        if (el !== searchBox) el.classList.add('hidden');
    });

    const isHidden = searchBox.classList.contains('hidden');
    if (isHidden) {
        searchBox.classList.remove('hidden');
        const input = searchBox.querySelector('.cora-col-search-input');
        if (input) setTimeout(() => input.focus(), 50);
    } else {
        window.coraClearColumnSearch(btnEl);
    }
};

window.coraFilterColumnCards = function(inputEl) {
    const query = (inputEl.value || '').toLowerCase().trim();
    const col = inputEl.closest('.cora-kanban-column');
    if (!col) return;
    const cardsContainer = col.querySelector('.cora-cards-container');
    if (!cardsContainer) return;
    const cards = cardsContainer.querySelectorAll('.cora-lead-card');

    let visibleCount = 0;
    cards.forEach(card => {
        const text = (
            (card.getAttribute('data-name') || '') + ' ' +
            (card.getAttribute('data-email') || '') + ' ' +
            (card.getAttribute('data-phone') || '') + ' ' +
            (card.getAttribute('data-city') || '') + ' ' +
            card.textContent
        ).toLowerCase();

        if (!query || text.includes(query)) {
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.classList.add('hidden');
        }
    });

    let noResults = cardsContainer.querySelector('.cora-col-no-results');
    if (query && visibleCount === 0) {
        if (!noResults) {
            noResults = document.createElement('div');
            noResults.className = 'cora-col-no-results flex flex-col items-center justify-center p-4 my-2 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl text-center select-none bg-white/40 dark:bg-zinc-900/40';
            noResults.innerHTML = `
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400 mb-1"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <span class="text-[11px] font-bold text-zinc-600 dark:text-zinc-400">No leads match "${query}"</span>
                <span class="text-[9.5px] text-zinc-400 dark:text-zinc-500 mt-0.5">Try searching another term</span>
            `;
            cardsContainer.appendChild(noResults);
        } else {
            const span = noResults.querySelector('span');
            if (span) span.textContent = `No leads match "${query}"`;
            noResults.classList.remove('hidden');
        }
    } else if (noResults) {
        noResults.classList.add('hidden');
    }
};

window.coraClearColumnSearch = function(btnEl) {
    const col = btnEl.closest('.cora-kanban-column');
    if (!col) return;
    const searchBox = col.querySelector('.cora-col-search-box');
    const input = col.querySelector('.cora-col-search-input');
    if (input) {
        input.value = '';
        window.coraFilterColumnCards(input);
    }
    if (searchBox) searchBox.classList.add('hidden');
};

window.coraToggleSelectAllLeads = function(inputEl) {
    const isChecked = inputEl.checked;
    const checkboxes = document.querySelectorAll('.cora-lead-row-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = isChecked;
    });
};

window.coraSwitchLeadDetailTab = function(tabName) {
    // 1. Manage active tab button styling
    const tabs = document.querySelectorAll('.cora-lead-detail-tab-btn');
    tabs.forEach(tab => {
        tab.classList.remove('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'font-bold', 'shadow-xs');
        tab.classList.add('text-zinc-500', 'hover:text-zinc-900', 'dark:hover:text-white');
    });
    
    const activeTab = document.getElementById(`cora-lead-detail-tab-btn-${tabName}`);
    if (activeTab) {
        activeTab.classList.add('bg-white', 'dark:bg-zinc-900', 'text-zinc-950', 'dark:text-white', 'font-bold', 'shadow-xs');
        activeTab.classList.remove('text-zinc-500', 'hover:text-zinc-900', 'dark:hover:text-white');
    }

    // 2. Toggle content panes
    const panes = document.querySelectorAll('.cora-lead-detail-tab-pane');
    panes.forEach(pane => pane.classList.add('hidden'));
    
    const activePane = document.getElementById(`cora-lead-detail-tab-${tabName}`);
    if (activePane) {
        activePane.classList.remove('hidden');
    }
};

window.coraUpdateStageToggleLabel = function(checkboxEl) {
    const parent = checkboxEl.closest('label');
    if (!parent) return;
    const textEl = parent.querySelector('.cora-toggle-text');
    if (textEl) {
        textEl.textContent = checkboxEl.checked ? 'Show' : 'Hide';
    }
};

window.coraUpdateStageBadgePreview = function(selectEl) {
    const colors = ['blue', 'amber', 'purple', 'violet', 'indigo', 'emerald', 'rose', 'zinc', 'sky', 'orange'];
    const classes = selectEl.className.split(' ');
    const cleanedClasses = classes.filter(c => {
        return !colors.some(color => c.includes(color));
    });
    const selectedVal = selectEl.value;
    selectEl.className = cleanedClasses.join(' ') + ' ' + selectedVal;
};

// ============================================================
// CORA STAGE COLOR PICKER — Native input[type=color] handler
// ============================================================

window.coraStageColorChange = function(inputEl) {
    var hex = inputEl.value;
    var row = inputEl.closest('.cora-stage-config-row');
    if (!row) return;

    // Update the visible swatch circle
    var swatch = row.querySelector('.cora-stage-color-swatch');
    if (swatch) swatch.style.background = hex;

    // Store hex on row for save handler
    row.setAttribute('data-badge-hex', hex);

    // Try to match a known palette entry and sync the hidden badge select
    var palette = [
        ['#22c55e','bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800'],
        ['#f59e0b','bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800'],
        ['#3b82f6','bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800'],
        ['#8b5cf6','bg-violet-500/10 text-violet-600 dark:text-violet-400 border-violet-200 dark:border-violet-800'],
        ['#ec4899','bg-pink-500/10 text-pink-600 dark:text-pink-400 border-pink-200 dark:border-pink-800'],
        ['#f43f5e','bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-800'],
        ['#0ea5e9','bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-200 dark:border-sky-800'],
        ['#6366f1','bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800'],
        ['#a855f7','bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800'],
        ['#f97316','bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-800'],
        ['#14b8a6','bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-200 dark:border-teal-800'],
        ['#84cc16','bg-lime-500/10 text-lime-600 dark:text-lime-400 border-lime-200 dark:border-lime-800'],
        ['#ef4444','bg-red-500/10 text-red-600 dark:text-red-400 border-red-200 dark:border-red-800'],
        ['#06b6d4','bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border-cyan-200 dark:border-cyan-800'],
        ['#d946ef','bg-fuchsia-500/10 text-fuchsia-600 dark:text-fuchsia-400 border-fuchsia-200 dark:border-fuchsia-800'],
        ['#71717a','bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800']
    ];
    var select = row.querySelector('.cora-stage-badge-select');
    if (select) {
        var matched = palette.find(function(e) { return e[0] === hex; });
        if (matched) {
            Array.from(select.options).forEach(function(opt) {
                opt.selected = (opt.value === matched[1]);
            });
        }
        select.dispatchEvent(new Event('change', { bubbles: true }));
    }
};

// Kept for backward compat — unused but referenced by old HTML
window.coraSelectStageColor = function() {};
window.coraCycleStageColor  = function() {};
window.coraOpenStagePicker  = function() {};

</script>
<?php


// Fetch leads and initial datasets
$cora_leads_raw = cora_db_get_leads();
$cora_clients_raw = function_exists('cora_db_get_clients') ? cora_db_get_clients() : array();
$cora_users_list = get_users( array( 'fields' => array( 'ID', 'display_name', 'user_email' ) ) );

// Compute KPI Metrics
$total_leads_count = count( $cora_leads_raw );
$pipeline_total_value = 0;
$converted_count = 0;
$hot_leads_count = 0;

$default_stages = array(
    'New Lead'    => array( 'key' => 'New Lead', 'label' => 'New Inquiries', 'badge' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800', 'enabled' => true ),
    'Contacted'   => array( 'key' => 'Contacted', 'label' => 'Proposal Sent', 'badge' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800', 'enabled' => true ),
    'Site Visit'  => array( 'key' => 'Site Visit', 'label' => 'Site Visit / Viewing', 'badge' => 'bg-violet-500/10 text-violet-600 dark:text-violet-400 border-violet-200 dark:border-violet-800', 'enabled' => true ),
    'Negotiation' => array( 'key' => 'Negotiation', 'label' => 'Negotiation', 'badge' => 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800', 'enabled' => true ),
    'Converted'   => array( 'key' => 'Converted', 'label' => 'Converted', 'badge' => 'bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-200 dark:border-sky-800', 'enabled' => true ),
    'Lost'        => array( 'key' => 'Lost', 'label' => 'On Hold', 'badge' => 'bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-800', 'enabled' => false ),
);

$saved_stages = get_option( 'cora_workspace_lead_stages', array() );
$stages_config = ! empty( $saved_stages ) ? $saved_stages : $default_stages;

$stages_summary = array();
foreach ( $stages_config as $s_key => $s_val ) {
    if ( isset( $s_val['enabled'] ) && ! $s_val['enabled'] ) {
        continue;
    }
    $stages_summary[$s_key] = array(
        'key'   => $s_key,
        'count' => 0,
        'value' => 0,
        'label' => $s_val['label'] ?? $s_key,
        'badge' => $s_val['badge'] ?? 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800',
    );
}

foreach ( $cora_leads_raw as $l ) {
    $numeric_price = (float) preg_replace( '/[^0-9.]/', '', $l['price'] ?? '0' );
    $pipeline_total_value += $numeric_price;

    $st = $l['status'] ?? 'New Lead';
    if ( isset( $stages_summary[$st] ) ) {
        $stages_summary[$st]['count']++;
        $stages_summary[$st]['value'] += $numeric_price;
    }

    if ( $st === 'Converted' || ! empty( $l['converted_to_client'] ) ) {
        $converted_count++;
    }

    if ( isset($l['score']) && strtolower($l['score']) === 'hot' ) {
        $hot_leads_count++;
    }
}

$conversion_rate = $total_leads_count > 0 ? round( ( $converted_count / $total_leads_count ) * 100, 1 ) : 0;

$cora_initial_subtab = sanitize_text_field( $_GET['subtab'] ?? '' );
if ( empty( $cora_initial_subtab ) || ! in_array( $cora_initial_subtab, array( 'kanban', 'directory', 'analytics', 'activity' ), true ) ) {
    $is_mobile_ua = isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/(mobile|android|iphone|ipad|ipod)/i', $_SERVER['HTTP_USER_AGENT'] );
    $cora_initial_subtab = $is_mobile_ua ? 'directory' : 'kanban';
}
?>

<div id="cora-leads-module-container" class="space-y-6 select-none font-sans text-zinc-900 dark:text-zinc-100">    <!-- STANDARD PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">Client Leads (CRM)</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Nurture client inquiries, drag & drop deal stages, track funnel conversion, and close shoots.</p>
        </div>
        <!-- Desktop Action Bar -->
        <div class="hidden sm:flex items-center gap-2 shrink-0">
            <button type="button" id="cora-top-header-activity-btn" class="px-3 py-2 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white font-semibold rounded-xl text-xs transition-all flex items-center gap-1.5 cursor-pointer border border-zinc-200/80 dark:border-zinc-800 shadow-2xs" onclick="coraSwitchLeadSubtab('activity')">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 16 14"></polyline></svg>
                <span>Activity Log</span>
            </button>
            <button type="button" id="cora-top-header-customize-cols" class="px-3 py-2 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white font-semibold rounded-xl text-xs transition-all flex items-center gap-1.5 cursor-pointer border border-zinc-200/80 dark:border-zinc-800 shadow-2xs" onclick="coraOpenManageStagesDrawer()">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                <span>Customize Columns</span>
            </button>
            <button type="button" class="px-3.5 py-2 bg-white dark:bg-zinc-900 text-zinc-800 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-800 font-semibold rounded-xl text-xs transition-all flex items-center gap-1.5 cursor-pointer border border-zinc-200/80 dark:border-zinc-800 shadow-2xs" onclick="coraExportLeadsCSV()">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                <span>Export CSV</span>
            </button>
            <button type="button" class="px-4 py-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold rounded-xl text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all flex items-center gap-2 cursor-pointer shadow-xs" onclick="coraOpenCreateLeadDrawer()">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Add Lead</span>
            </button>
        </div>

        <!-- Mobile Prioritized Actions (Priority 1: Add Lead, Priority 2: Customize Columns, Low Priority: Overflow Menu) -->
        <div class="flex sm:hidden items-center gap-2 w-full">
            <button type="button" class="flex-1 py-2.5 px-4 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold rounded-xl text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-xs" onclick="coraOpenCreateLeadDrawer()">
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Add Lead</span>
            </button>
            <button type="button" class="px-3 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-zinc-800 dark:text-zinc-200 font-semibold rounded-xl text-xs transition-all flex items-center gap-1.5 cursor-pointer shadow-2xs" onclick="coraOpenManageStagesDrawer()">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                <span>Columns</span>
            </button>
            <div class="relative">
                <button type="button" id="cora-mobile-more-actions-btn" class="px-2.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold rounded-xl text-xs hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-all flex items-center justify-center cursor-pointer shadow-2xs" onclick="coraToggleMobileActionsMenu(event)">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                </button>
                <div id="cora-mobile-more-actions-popover" class="hidden absolute right-0 top-full mt-1.5 w-48 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl z-50 p-1.5 font-sans space-y-1">
                    <button type="button" class="w-full px-3 py-2 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl flex items-center gap-2 cursor-pointer transition-colors" onclick="coraSwitchLeadSubtab('activity'); coraToggleMobileActionsMenu();">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 16 14"></polyline></svg>
                        <span>Activity Log</span>
                    </button>
                    <button type="button" class="w-full px-3 py-2 text-left text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl flex items-center gap-2 cursor-pointer transition-colors" onclick="coraExportLeadsCSV(); coraToggleMobileActionsMenu();">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>Export CSV</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- TOP KPI STAT CARDS (2x2 GRID ON MOBILE FOR HIGH DECISION-MAKING & ZERO CLUTTER) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
        <!-- Card 1: Pipeline Value -->
        <div class="p-3 sm:p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs flex flex-col justify-between space-y-2 sm:space-y-3 min-w-0">
            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                <span class="text-[9.5px] sm:text-[10px] font-extrabold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 truncate">Pipeline Value</span>
                <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center font-extrabold text-xs shrink-0 select-none">
                    ₹
                </div>
            </div>
            <div>
                <div class="text-base sm:text-2xl font-black tracking-tight text-zinc-950 dark:text-white truncate">₹<?php echo number_format( $pipeline_total_value ); ?></div>
                <div class="mt-1 sm:mt-2">
                    <span class="text-[9px] sm:text-[10px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2 sm:px-2.5 py-0.5 rounded-full border border-emerald-200/60 dark:border-emerald-800/60 inline-flex items-center gap-1 truncate max-w-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span> Active Deals
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Inquiries -->
        <div class="p-3 sm:p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs flex flex-col justify-between space-y-2 sm:space-y-3 min-w-0">
            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                <span class="text-[9.5px] sm:text-[10px] font-extrabold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 truncate">Total Inquiries</span>
                <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                </div>
            </div>
            <div>
                <div class="text-base sm:text-2xl font-black tracking-tight text-zinc-950 dark:text-white truncate"><?php echo $total_leads_count; ?></div>
                <div class="mt-1 sm:mt-2">
                    <span class="text-[9px] sm:text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 px-2 sm:px-2.5 py-0.5 rounded-full border border-zinc-200/60 dark:border-zinc-700 inline-flex items-center gap-1 truncate max-w-full">
                        <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M12 2c.6 3.3 4 6 4 10a4 4 0 1 1-8 0c0-4 3.4-6.7 4-10z"></path></svg>
                        <?php echo $hot_leads_count; ?> Hot Deals
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 3: Conversion Rate -->
        <div class="p-3 sm:p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs flex flex-col justify-between space-y-2 sm:space-y-3 min-w-0">
            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                <span class="text-[9.5px] sm:text-[10px] font-extrabold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 truncate">Conversion Rate</span>
                <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                </div>
            </div>
            <div>
                <div class="text-base sm:text-2xl font-black tracking-tight text-zinc-950 dark:text-white truncate"><?php echo $conversion_rate; ?>%</div>
                <div class="mt-1 sm:mt-2">
                    <span class="text-[9px] sm:text-[10px] font-bold text-sky-700 dark:text-sky-400 bg-sky-50 dark:bg-sky-950/50 px-2 sm:px-2.5 py-0.5 rounded-full border border-sky-200/60 dark:border-sky-800/60 inline-flex items-center gap-1 truncate max-w-full">
                        <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <?php echo $converted_count; ?> Converted
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 4: Avg Response Time -->
        <div class="p-3 sm:p-4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs flex flex-col justify-between space-y-2 sm:space-y-3 min-w-0">
            <div class="flex items-center justify-between text-zinc-500 dark:text-zinc-400">
                <span class="text-[9.5px] sm:text-[10px] font-extrabold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 truncate">Avg Response</span>
                <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 flex items-center justify-center shrink-0">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 16 14"></polyline></svg>
                </div>
            </div>
            <div>
                <div class="text-base sm:text-2xl font-black tracking-tight text-zinc-950 dark:text-white truncate">18 mins</div>
                <div class="mt-1 sm:mt-2">
                    <span class="text-[9px] sm:text-[10px] font-bold text-zinc-600 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 sm:px-2.5 py-0.5 rounded-full border border-zinc-200/60 dark:border-zinc-700 inline-flex items-center gap-1 truncate max-w-full">
                        <svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Target &lt; 30m
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- OPTIMIZED SEGMENTED TOOLBAR -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 p-3 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3 shadow-2xs overflow-hidden">
        <div class="flex items-center gap-1 bg-zinc-100/80 dark:bg-zinc-800/80 p-1 rounded-xl shrink-0 overflow-x-auto max-w-full">
            <?php
            $active_cls = 'active bg-white text-zinc-950 dark:bg-zinc-800 dark:text-white shadow-2xs font-bold border border-zinc-200/80 dark:border-zinc-700/80';
            $inactive_cls = 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white font-medium hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50';
            ?>
            <button type="button" class="cora-lead-subtab-btn shrink-0 px-3.5 py-1.5 text-xs rounded-lg transition-all cursor-pointer <?php echo ($cora_initial_subtab === 'kanban') ? $active_cls : $inactive_cls; ?>" data-tab="kanban" onclick="coraSwitchLeadSubtab('kanban')">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="9" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>
                    <span>Kanban Pipeline</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 border border-zinc-200/80 dark:border-zinc-700"><?php echo $total_leads_count; ?></span>
                </div>
            </button>
            <button type="button" class="cora-lead-subtab-btn shrink-0 px-3.5 py-1.5 text-xs rounded-lg transition-all cursor-pointer <?php echo ($cora_initial_subtab === 'directory') ? $active_cls : $inactive_cls; ?>" data-tab="directory" onclick="coraSwitchLeadSubtab('directory')">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                    <span>Leads Directory</span>
                </div>
            </button>
            <button type="button" class="cora-lead-subtab-btn shrink-0 px-3.5 py-1.5 text-xs rounded-lg transition-all cursor-pointer <?php echo ($cora_initial_subtab === 'analytics') ? $active_cls : $inactive_cls; ?>" data-tab="analytics" onclick="coraSwitchLeadSubtab('analytics')">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    <span>Funnel & Analytics</span>
                </div>
            </button>
        </div>

        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-2.5 w-full md:w-auto shrink-0">
            <div class="relative w-full md:w-64">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="cora-lead-search-input" placeholder="Search leads by name, email, city..." 
                       class="w-full pl-9 pr-3 py-2 md:py-1.5 text-xs bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:outline-none focus:ring-1 focus:ring-zinc-950 dark:focus:ring-white transition-all font-medium placeholder:text-zinc-400"
                       onkeyup="coraFilterLeadsList()">
            </div>

            <div class="grid grid-cols-2 md:flex md:items-center gap-2 w-full md:w-auto">
                <select id="cora-lead-stage-filter" class="w-full md:w-auto shrink-0 px-2.5 sm:px-3 py-2 md:py-1.5 text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-800 dark:text-zinc-200 font-medium focus:outline-none cursor-pointer truncate" onchange="coraFilterLeadsList()">
                    <option value="all">All Stages</option>
                    <?php foreach ( $stages_summary as $sk => $sd ) : ?>
                        <option value="<?php echo esc_attr( $sk ); ?>"><?php echo esc_html( $sd['label'] ); ?></option>
                    <?php endforeach; ?>
                </select>

                <select id="cora-lead-assignee-filter" class="w-full md:w-auto shrink-0 px-2.5 sm:px-3 py-2 md:py-1.5 text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-800 dark:text-zinc-200 font-medium focus:outline-none cursor-pointer truncate" onchange="coraFilterLeadsList()">
                    <option value="all">All Team Members</option>
                    <?php foreach ( $cora_users_list as $u ) : ?>
                        <option value="<?php echo esc_attr( $u->ID ); ?>"><?php echo esc_html( $u->display_name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- SUB-TAB 1: KANBAN PIPELINE BOARD -->
    <div id="cora-lead-pane-kanban" class="cora-lead-tab-pane <?php echo ($cora_initial_subtab === 'kanban') ? '' : 'hidden'; ?>">
        <style>
            .cora-kanban-column {
                width: 300px !important;
                min-width: 300px !important;
                max-width: 300px !important;
                box-sizing: border-box !important;
                background-color: var(--col-bg, #f8f8fa) !important;
                border: 1px solid var(--col-border, #e4e4e7) !important;
            }
            .dark .cora-kanban-column {
                background-color: var(--col-bg-dark, rgba(24, 24, 27, 0.4)) !important;
                border: 1px solid var(--col-border-dark, rgba(63, 63, 70, 0.4)) !important;
            }

            /* Custom Monochromatic Toggle Switches */
            .cora-toggle-slider {
                width: 42px !important;
                height: 24px !important;
                background-color: #e4e4e7 !important;
                border-radius: 9999px !important;
                transition: background-color 0.2s ease, border-color 0.2s ease !important;
                position: relative !important;
                display: inline-block !important;
                cursor: pointer !important;
                border: 1px solid #d4d4d8 !important;
            }
            .dark .cora-toggle-slider {
                background-color: #27272a !important;
                border-color: #3f3f46 !important;
            }
            .cora-toggle-slider::after {
                content: '' !important;
                position: absolute !important;
                top: 2px !important;
                left: 2px !important;
                width: 18px !important;
                height: 18px !important;
                background-color: #ffffff !important;
                border-radius: 9999px !important;
                transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
                box-shadow: 0 1px 3px rgba(0,0,0,0.2) !important;
            }
            .cora-toggle-checkbox:checked + .cora-toggle-slider {
                background-color: #09090b !important;
                border-color: #09090b !important;
            }
            .dark .cora-toggle-checkbox:checked + .cora-toggle-slider {
                background-color: #ffffff !important;
                border-color: #ffffff !important;
            }
            .cora-toggle-checkbox:checked + .cora-toggle-slider::after {
                transform: translateX(18px) !important;
            }
            .dark .cora-toggle-checkbox:checked + .cora-toggle-slider::after {
                background-color: #09090b !important;
            }
        </style>
        <div class="flex overflow-x-auto gap-4 items-stretch pb-8 pt-1" style="scrollbar-width: thin;">
            <?php 
            $stage_styles = array(
                'New Lead' => array(
                    'col_bg'         => '',
                    'icon_bg'        => 'bg-emerald-600 dark:bg-emerald-500 text-white',
                    'icon_color'     => 'text-white',
                    'sum_text'       => 'text-emerald-600 dark:text-emerald-400 font-extrabold',
                    'accent_color'   => 'text-emerald-600 dark:text-emerald-400',
                    'progress_bg'    => 'bg-emerald-500 dark:bg-emerald-400',
                    'add_btn'        => 'text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 bg-white dark:bg-zinc-900',
                    'add_label'      => 'Add Inquiry',
                    'header_icon'    => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
                    'empty_badge'    => '<div class="absolute -bottom-1.5 -right-1.5 bg-emerald-600 text-white rounded-full p-1 shadow-md border border-white dark:border-zinc-900"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg></div>',
                    'empty_desc'     => 'New inquiries inbox is clear.',
                    'empty_subdesc'  => 'Create or assign a new lead to start the sales funnel.'
                ),
                'Contacted' => array(
                    'col_bg'         => '',
                    'icon_bg'        => 'bg-amber-600 dark:bg-amber-500 text-white',
                    'icon_color'     => 'text-white',
                    'sum_text'       => 'text-amber-600 dark:text-amber-500 font-extrabold',
                    'accent_color'   => 'text-amber-600 dark:text-amber-500',
                    'progress_bg'    => 'bg-amber-600 dark:bg-amber-500',
                    'add_btn'        => 'text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 bg-white dark:bg-zinc-900',
                    'add_label'      => 'Add Inquiry',
                    'header_icon'    => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>',
                    'empty_badge'    => '<div class="absolute -bottom-1.5 -right-1.5 bg-amber-600 text-white rounded-full p-1 shadow-md border border-white dark:border-zinc-900"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg></div>',
                    'empty_desc'     => 'No proposals sent yet.',
                    'empty_subdesc'  => 'Contact a lead and move them here once a proposal is shared.'
                ),
                'Site Visit' => array(
                    'col_bg'         => '',
                    'icon_bg'        => 'bg-violet-600 dark:bg-violet-500 text-white',
                    'icon_color'     => 'text-white',
                    'sum_text'       => 'text-violet-600 dark:text-violet-400 font-extrabold',
                    'accent_color'   => 'text-violet-600 dark:text-violet-400',
                    'progress_bg'    => 'bg-violet-600 dark:bg-violet-500',
                    'add_btn'        => 'text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 bg-white dark:bg-zinc-900',
                    'add_label'      => 'Add Inquiry',
                    'header_icon'    => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>',
                    'empty_badge'    => '<div class="absolute -bottom-1.5 -right-1.5 bg-violet-600 text-white rounded-full p-1 shadow-md border border-white dark:border-zinc-900"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg></div>',
                    'empty_desc'     => 'No site visits scheduled.',
                    'empty_subdesc'  => 'Schedule a property viewing or location recce to progress deals.'
                ),
                'Negotiation' => array(
                    'col_bg'         => '',
                    'icon_bg'        => 'bg-purple-600 dark:bg-purple-500 text-white',
                    'icon_color'     => 'text-white',
                    'sum_text'       => 'text-purple-600 dark:text-purple-400 font-extrabold',
                    'accent_color'   => 'text-purple-600 dark:text-purple-400',
                    'progress_bg'    => 'bg-purple-600 dark:bg-purple-500',
                    'add_btn'        => 'text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 bg-white dark:bg-zinc-900',
                    'add_label'      => 'Add Inquiry',
                    'header_icon'    => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
                    'empty_badge'    => '<div class="absolute -bottom-1.5 -right-1.5 bg-purple-600 text-white rounded-full p-1 shadow-md border border-white dark:border-zinc-900"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg></div>',
                    'empty_desc'     => 'Negotiation channel is silent.',
                    'empty_subdesc'  => 'Start pitching proposal estimates and reviewing project terms.'
                ),
                'Converted' => array(
                    'col_bg'         => '',
                    'icon_bg'        => 'bg-blue-600 dark:bg-blue-500 text-white',
                    'icon_color'     => 'text-white',
                    'sum_text'       => 'text-blue-600 dark:text-blue-400 font-extrabold',
                    'accent_color'   => 'text-blue-600 dark:text-blue-400',
                    'progress_bg'    => 'bg-blue-600 dark:bg-blue-500',
                    'add_btn'        => 'text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 bg-white dark:bg-zinc-900',
                    'add_label'      => 'Add Inquiry',
                    'header_icon'    => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>',
                    'empty_badge'    => '<div class="absolute -bottom-1.5 -right-1.5 bg-blue-600 text-white rounded-full p-1 shadow-md border border-white dark:border-zinc-900"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg></div>',
                    'empty_desc'     => 'No converted bookings yet.',
                    'empty_subdesc'  => 'Once a deal is finalized, drag it here to mark as won.'
                ),
                'Lost' => array(
                    'col_bg'         => '',
                    'icon_bg'        => 'bg-zinc-550 dark:bg-zinc-500 text-white',
                    'icon_color'     => 'text-white',
                    'sum_text'       => 'text-zinc-550 dark:text-zinc-400 font-extrabold',
                    'accent_color'   => 'text-zinc-500 dark:text-zinc-400',
                    'progress_bg'    => 'bg-zinc-550 dark:bg-zinc-500',
                    'add_btn'        => 'text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 bg-white dark:bg-zinc-900',
                    'add_label'      => 'Add Inquiry',
                    'header_icon'    => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><rect x="6" y="4" width="4" height="16" rx="1"></rect><rect x="14" y="4" width="4" height="16" rx="1"></rect></svg>',
                    'empty_badge'    => '<div class="absolute -bottom-1.5 -right-1.5 bg-zinc-550 text-white rounded-full p-1 shadow-md border border-white dark:border-zinc-900"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><rect x="6" y="4" width="4" height="16" rx="0.5"></rect><rect x="14" y="4" width="4" height="16" rx="0.5"></rect></svg></div>',
                    'empty_desc'     => 'No inquiries on hold',
                    'empty_subdesc'  => 'Move inquiries here to pause progress'
                )
            );

            $fallback_style = array(
                'col_bg'         => 'bg-zinc-50 dark:bg-zinc-800/40',
                'icon_bg'        => 'bg-zinc-500 dark:bg-zinc-650',
                'icon_color'     => 'text-white',
                'sum_text'       => 'text-zinc-650 dark:text-zinc-300 font-extrabold',
                'accent_color'   => 'text-zinc-500 dark:text-zinc-400',
                'progress_bg'    => 'bg-zinc-500 dark:bg-zinc-400',
                'add_btn'        => 'text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800 bg-white dark:bg-zinc-900',
                'add_label'      => 'Add Inquiry',
                'header_icon'    => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
                'empty_badge'    => '<div class="absolute -bottom-1.5 -right-1.5 bg-zinc-500 text-white rounded-full p-1 shadow-md border border-white dark:border-zinc-900"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg></div>',
                'empty_desc'     => 'No active items here.',
                'empty_subdesc'  => 'Track your project flow by moving cards into this stage.'
            );

            $col_bg_mapping = array(
                'default' => array('bg' => '#f8f8fa', 'border' => '#e4e4e7', 'bg_dark' => 'rgba(24, 24, 27, 0.4)', 'border_dark' => 'rgba(63, 63, 70, 0.4)'),
                'white'   => array('bg' => '#ffffff', 'border' => '#e4e4e7', 'bg_dark' => '#09090b', 'border_dark' => '#27272a'),
                'zinc'    => array('bg' => '#f4f4f5', 'border' => '#e4e4e7', 'bg_dark' => '#18181b', 'border_dark' => '#27272a'),
                'slate'   => array('bg' => '#f1f5f9', 'border' => '#e2e8f0', 'bg_dark' => '#1e293b', 'border_dark' => '#334155'),
                'cream'   => array('bg' => '#fafaf9', 'border' => '#e7e5e4', 'bg_dark' => '#1c1917', 'border_dark' => '#292524')
            );

            foreach ( $stages_summary as $stage_key => $stage_data ) : 
                $col_leads = array_filter( $cora_leads_raw, function($lead) use ($stage_key) {
                    $st = $lead['status'] ?? 'New Lead';
                    return $st === $stage_key;
                });

                $style = $stage_styles[$stage_key] ?? $fallback_style;
                $col_bg_key = $stage_data['bg_color'] ?? 'default';
                $bg_styles = $col_bg_mapping[$col_bg_key] ?? $col_bg_mapping['default'];
                $col_inline_style = sprintf(
                    'width: 300px !important; min-width: 300px !important; max-width: 300px !important; --col-bg: %s; --col-border: %s; --col-bg-dark: %s; --col-border-dark: %s;',
                    $bg_styles['bg'],
                    $bg_styles['border'],
                    $bg_styles['bg_dark'],
                    $bg_styles['border_dark']
                );
            ?>
            <div class="cora-kanban-column flex flex-col p-3.5 rounded-3xl shrink-0 relative transition-all duration-200"
                 style="<?php echo esc_attr( $col_inline_style ); ?>"
                 data-status="<?php echo esc_attr( $stage_key ); ?>"
                 ondragover="coraLeadDragOver(event, this)"
                 ondrop="coraLeadDrop(event, this)">
                
                <!-- Column Header -->
                <div class="mb-3.5 flex flex-col gap-2 shrink-0 px-0.5 pt-0.5">
                    <div class="flex items-center justify-between gap-1.5">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 <?php echo $style['icon_bg']; ?> <?php echo $style['icon_color']; ?>">
                                <?php echo $style['header_icon']; ?>
                            </div>
                            <span class="text-[11px] font-black text-zinc-900 dark:text-zinc-100 uppercase tracking-wider truncate">
                                <?php echo esc_html( $stage_data['label'] ); ?>
                            </span>
                            <span class="text-[10px] text-zinc-500 dark:text-zinc-400 font-bold bg-zinc-100 dark:bg-zinc-800/80 px-2 py-0.5 rounded-full col-count shrink-0">
                                <?php echo count($col_leads); ?>
                            </span>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" class="cora-col-search-btn w-6 h-6 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all flex items-center justify-center cursor-pointer shrink-0" title="Search in Column" onclick="coraToggleColumnSearch(this)">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </button>
                            <button type="button" class="w-6 h-6 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all flex items-center justify-center cursor-pointer shrink-0" title="Quick Add Lead" onclick="coraOpenCreateLeadDrawer('<?php echo esc_attr($stage_key); ?>')">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            </button>
                            <button type="button" class="w-6 h-6 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all flex items-center justify-center cursor-pointer shrink-0" title="Column Menu" onclick="coraOpenManageStagesDrawer()">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><circle cx="12" cy="5" r="2"></circle><circle cx="12" cy="12" r="2"></circle><circle cx="12" cy="19" r="2"></circle></svg>
                            </button>
                        </div>
                    </div>

                    <!-- In-Column Real-Time Search Box -->
                    <div class="cora-col-search-box hidden pt-1">
                        <div class="relative flex items-center">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none" class="absolute left-2.5 text-zinc-400 pointer-events-none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            <input type="text" class="cora-col-search-input w-full pl-7 pr-7 py-1 bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-700 focus:border-zinc-900 dark:focus:border-zinc-100 rounded-xl text-[11px] text-zinc-900 dark:text-zinc-100 font-medium placeholder-zinc-400 outline-none transition-all shadow-2xs" placeholder="Search this column..." oninput="coraFilterColumnCards(this)">
                            <button type="button" class="absolute right-2 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 border-none bg-transparent cursor-pointer p-0.5 flex items-center justify-center" onclick="coraClearColumnSearch(this)" title="Clear Search">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[10.5px] text-zinc-400 font-medium pt-1.5 border-t border-zinc-200/60 dark:border-zinc-800/60">
                        <span>Pipeline Value</span>
                        <span class="<?php echo $style['sum_text']; ?>">
                            ₹<?php echo number_format($stage_data['value']); ?>
                        </span>
                    </div>
                </div>

                <!-- Cards Container -->
                <div class="cora-cards-container flex-1 space-y-3 pb-4">
                    <?php if ( empty($col_leads) ) : ?>
                        <!-- Empty State Graphic: Mailbox Icon with Floating Status Badge -->
                        <div class="flex flex-col items-center justify-center p-6 my-1 border border-dashed border-zinc-200/90 dark:border-zinc-800/80 rounded-2xl bg-white/50 dark:bg-zinc-900/30 text-center select-none min-h-[220px]">
                            <div class="relative mb-3 flex items-center justify-center w-11 h-11 rounded-full bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200/50 dark:border-zinc-700/40">
                                <!-- Mailbox SVG -->
                                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="1.5" fill="none" class="text-zinc-300 dark:text-zinc-500">
                                    <path d="M22 12h-6l-2 3h-4l-2-3H2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5.45 5.11L2 12v6a2 2 0 0 2 2h16a2 2 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <?php echo $style['empty_badge']; ?>
                            </div>
                            <h5 class="text-xs font-bold text-zinc-800 dark:text-zinc-200 leading-tight"><?php echo esc_html($style['empty_desc']); ?></h5>
                            <p class="text-[9.5px] text-zinc-400 dark:text-zinc-500 leading-normal max-w-[180px] mt-1"><?php echo esc_html($style['empty_subdesc']); ?></p>
                        </div>
                    <?php else : ?>
                        <?php foreach ( $col_leads as $lead ) : 
                            $score = isset($lead['score']) ? strtolower($lead['score']) : 'warm';
                            $is_won = ( ( $lead['status'] ?? '' ) === 'Converted' || ( $stage_key ?? '' ) === 'Converted' );

                            // Lead Temperature Color Psychology Theme Rules
                            if ( $is_won ) {
                                $pill_class = 'bg-emerald-50/80 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300';
                                $dot_color_class = 'bg-emerald-500';
                                $score_label = 'Won';
                                $score_icon = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                            } else {
                                if ($score === 'hot') {
                                    $pill_class = 'bg-rose-50/80 dark:bg-rose-950/20 text-rose-800 dark:text-rose-300';
                                    $dot_color_class = 'bg-rose-500';
                                    $score_label = 'Hot';
                                    $score_icon = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M12 2c.6 3.3 4 6 4 10a4 4 0 1 1-8 0c0-4 3.4-6.7 4-10z"></path></svg>';
                                } else if ($score === 'cold') {
                                    $pill_class = 'bg-sky-50/80 dark:bg-sky-950/20 text-sky-800 dark:text-sky-300';
                                    $dot_color_class = 'bg-sky-500';
                                    $score_label = 'Cold';
                                    $score_icon = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M20 12H4M12 20V4M17.66 17.66L6.34 6.34M17.66 6.34L6.34 17.66"/></svg>';
                                } else {
                                    $pill_class = 'bg-amber-50/80 dark:bg-amber-950/20 text-amber-800 dark:text-amber-300';
                                    $dot_color_class = 'bg-amber-500';
                                    $score_label = 'Warm';
                                    $score_icon = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>';
                                }
                            }

                            $format_tag = $lead['format'] ?? 'Photoshoot';
                            $assigned_to_id = $lead['assigned_to'] ?? '';
                            if ( empty( $assigned_to_id ) ) {
                                foreach ( $cora_users_list as $u ) {
                                    if ( isset($lead['assignee_name']) && strtolower( trim( $u->display_name ) ) === strtolower( trim( $lead['assignee_name'] ) ) ) {
                                        $assigned_to_id = $u->ID;
                                        break;
                                    }
                                }
                            }
                            if ( empty( $assigned_to_id ) && ! empty( $cora_users_list ) ) {
                                $assigned_to_id = $cora_users_list[0]->ID;
                            }

                            $assigned_user = null;
                            foreach ( $cora_users_list as $u ) {
                                if ( (string) $u->ID === (string) $assigned_to_id ) {
                                    $assigned_user = $u;
                                    break;
                                }
                            }
                            $assignee_display_name = $assigned_user ? $assigned_user->display_name : ($lead['assignee_name'] ?? 'Shruti Sharma');
                            $assignee_first_name = explode( ' ', $assignee_display_name )[0];
                            $assignee_role = $lead['assignee_role'] ?? 'Super Admin';
                            if ( $assignee_role === 'Super Admin' ) {
                                $assignee_role = 'Admin';
                            }
                            $assignee_init = strtoupper( substr( $assignee_display_name, 0, 1 ) );
                            if ( strpos( $assignee_display_name, ' ' ) !== false ) {
                                $name_parts = explode( ' ', $assignee_display_name );
                                $assignee_init = strtoupper( substr( $name_parts[0], 0, 1 ) . substr( end( $name_parts ), 0, 1 ) );
                            }
                            $checklist = $lead['checklist'] ?? '1/2 (50%)';
                            $checklist_pct = $lead['checklist_pct'] ?? 50;
                            $price_display = $lead['price'] ?? '0';

                        ?>
                        <div class="cora-lead-card bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 hover:translate-y-[-1px] transition-all cursor-grab active:cursor-grabbing flex flex-col gap-3 relative group"
                             draggable="true"
                             data-id="<?php echo esc_attr( $lead['id'] ); ?>"
                             data-name="<?php echo esc_attr( $lead['names'] ); ?>"
                             data-email="<?php echo esc_attr( $lead['email'] ?? 'client@example.com' ); ?>"
                             data-phone="<?php echo esc_attr( $lead['phone'] ?? '+91 98765 43210' ); ?>"
                             data-price="<?php echo esc_attr( $lead['price'] ?? '0' ); ?>"
                             data-city="<?php echo esc_attr( $lead['city'] ?? 'Mumbai' ); ?>"
                             data-score="<?php echo esc_attr( $score ); ?>"
                             data-status="<?php echo esc_attr( $stage_key ); ?>"
                             data-notes="<?php echo esc_attr( $lead['notes'] ?? '' ); ?>"
                             data-assigned-to="<?php echo esc_attr( $assigned_to_id ); ?>"
                             ondragstart="coraLeadDragStart(event, this)"
                             ondragend="coraLeadDragEnd(event, this)"
                             onclick="coraOpenLeadDetailDrawer('<?php echo esc_attr( $lead['id'] ); ?>')">
                             
                             <!-- Top Row: Client Name & Temperature Badge -->
                             <div class="flex items-center justify-between gap-2">
                                 <span class="font-bold text-[10px] uppercase tracking-wider truncate max-w-[160px] <?php echo $style['accent_color']; ?>">
                                     <?php echo esc_html( $lead['names'] ); ?>
                                 </span>
                                 <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold <?php echo $pill_class; ?>" title="<?php echo esc_attr($score_label); ?>">
                                     <?php echo $score_icon; ?>
                                     <?php echo esc_html($score_label); ?>
                                 </span>
                             </div>

                             <!-- Project Title & Location -->
                             <div>
                                 <h4 class="font-bold text-zinc-950 dark:text-white text-[14px] tracking-tight leading-tight truncate" title="<?php echo esc_attr( $lead['scale'] ); ?>">
                                     <?php echo esc_html( $lead['scale'] ?? 'Standard Shoot' ); ?>
                                 </h4>
                                 <p class="text-[11px] text-zinc-500 dark:text-zinc-400 font-medium mt-0.5 truncate">
                                     <?php echo esc_html( $lead['city'] ?? 'Location TBD' ); ?>
                                 </p>
                             </div>

                             <!-- Price & Format Tag Row -->
                             <div class="flex items-center justify-between gap-1.5">
                                 <span class="font-extrabold text-[13px] text-zinc-950 dark:text-white tracking-tight">
                                     <?php echo esc_html( $price_display ); ?>
                                 </span>
                                 <span class="px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 font-semibold text-[9px] uppercase tracking-wider truncate max-w-[100px]">
                                     <?php echo esc_html( $format_tag ); ?>
                                 </span>
                             </div>
                             <?php
                             $stage_action_map = [
                                 'New Lead' => [
                                     'next_step' => 'Next: Contact & Pitch',
                                     'cta_label' => 'Contact Client',
                                     'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>',
                                     'cta_style' => 'bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-zinc-200',
                                 ],
                                 'Contacted' => [
                                     'next_step' => 'Next: Schedule Visit',
                                     'cta_label' => 'Schedule Visit',
                                     'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
                                     'cta_style' => 'bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-zinc-200',
                                 ],
                                 'Site Visit' => [
                                     'next_step' => 'Next: Send Quote',
                                     'cta_label' => 'Negotiate',
                                     'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M17 18a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2"></path><rect x="3" y="4" width="18" height="12" rx="2"></rect></svg>',
                                     'cta_style' => 'bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-zinc-200',
                                 ],
                                 'Negotiation' => [
                                     'next_step' => 'Next: Close & Convert',
                                     'cta_label' => 'Convert Deal',
                                     'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>',
                                     'cta_style' => 'bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:text-zinc-950 shadow-xs',
                                 ],
                                 'Converted' => [
                                     'next_step' => 'Status: Deal Won',
                                     'cta_label' => 'Converted',
                                     'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>',
                                     'cta_style' => 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
                                 ],
                                 'Lost' => [
                                     'next_step' => 'Status: Closed / Lost',
                                     'cta_label' => 'Closed',
                                     'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
                                     'cta_style' => 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400',
                                 ],
                             ];
                             $stage_info = $stage_action_map[$stage_key] ?? $stage_action_map['New Lead'];
                             ?>

                             <!-- Next Step Milestone Banner -->
                             <div class="flex items-center justify-between text-[9.5px] font-extrabold px-2.5 py-1 rounded-lg bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200/60 dark:border-zinc-700/60">
                                 <span class="text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-[8.5px]">Next Action</span>
                                 <span class="text-zinc-900 dark:text-zinc-100 font-bold truncate max-w-[170px]"><?php echo esc_html($stage_info['next_step']); ?></span>
                             </div>

                             <!-- Assignee & Action Row -->
                             <div class="flex items-center justify-between gap-1.5 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                                 <div class="flex items-center gap-2 min-w-0">
                                     <div class="w-6 h-6 rounded-full bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 flex items-center justify-center font-bold text-[9px] shrink-0 border border-zinc-200 dark:border-zinc-800" title="Assigned to <?php echo esc_attr( $assignee_display_name ); ?>">
                                         <?php echo esc_html( $assignee_init ); ?>
                                     </div>
                                     <div class="min-w-0 flex flex-col">
                                         <?php $display_role = ($assignee_role === 'Just Shruti' || $assignee_role === 'Super Admin') ? 'Admin' : $assignee_role; ?>
                                         <span class="font-bold text-zinc-900 dark:text-white text-[11px] leading-none truncate"><?php echo esc_html( $assignee_first_name ); ?></span>
                                         <span class="text-[9px] text-zinc-400 dark:text-zinc-500 leading-none mt-0.5"><?php echo esc_html( $display_role ); ?></span>
                                     </div>
                                 </div>

                                 <div class="flex items-center gap-1 shrink-0">
                                     <!-- Direct WhatsApp Shortcut -->
                                     <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $lead['phone'] ?? '919876543210'); ?>" target="_blank" onclick="event.stopPropagation()" class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-800/80 flex items-center justify-center hover:bg-emerald-100 transition-colors shadow-2xs" title="Chat on WhatsApp">
                                         <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.67-1.616-.919-2.213-.242-.58-.487-.502-.67-.511l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c-.001 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413"/></svg>
                                     </a>

                                     <!-- Action CTA Button -->
                                     <button type="button" class="px-2.5 py-1.5 font-bold rounded-lg text-[10px] transition-all cursor-pointer flex items-center gap-1 shrink-0 shadow-2xs <?php echo $stage_info['cta_style']; ?>" onclick="event.stopPropagation(); coraOpenLeadDetailDrawer('<?php echo esc_attr($lead['id']); ?>')">
                                         <?php echo $stage_info['cta_icon']; ?>
                                         <span><?php echo esc_html($stage_info['cta_label']); ?></span>
                                     </button>
                                 </div>
                             </div>

                             <!-- Progress Checklist Row -->
                             <div class="space-y-1 -mt-0.5">
                                 <div class="flex items-center justify-between text-[9px] font-semibold text-zinc-400 dark:text-zinc-500">
                                     <span>Progress</span>
                                     <span class="text-zinc-500 dark:text-zinc-400"><?php echo esc_html( $checklist ); ?></span>
                                 </div>
                                 <div class="w-full h-1 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                     <div class="h-full rounded-full transition-all <?php echo $style['progress_bg']; ?>" style="width: <?php echo intval($checklist_pct); ?>%;"></div>
                                 </div>
                             </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Column Footer Add Button -->
                <div class="pt-3">
                    <button type="button" class="w-full py-2 text-center text-xs font-bold rounded-xl border transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-2xs <?php echo $style['add_btn']; ?>" onclick="coraOpenCreateLeadDrawer('<?php echo esc_attr($stage_key); ?>')">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span><?php echo esc_html($style['add_label']); ?></span>
</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SUB-TAB 2: LEADS DIRECTORY (RESPONSIVE CARD GRID & VIEW SWITCHER) -->
    <div id="cora-lead-pane-directory" class="cora-lead-tab-pane <?php echo ($cora_initial_subtab === 'directory') ? '' : 'hidden'; ?> space-y-4">
        
        <!-- Directory Header Toolbar with View Mode Toggle + Column Density Picker -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 p-3.5 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs">
            <div class="flex items-center gap-2">
                <span class="font-extrabold text-xs text-zinc-950 dark:text-white tracking-tight uppercase">Prospect Directory</span>
                <span class="px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-[10.5px] font-bold text-zinc-600 dark:text-zinc-400 border border-zinc-200/60 dark:border-zinc-700/60" id="cora-directory-total-badge"><?php echo count($cora_leads_raw); ?> Total Leads</span>
            </div>

            <!-- Col density + view switcher: desktop only (mobile always shows card grid) -->
            <div class="hidden sm:flex items-center gap-2 self-end sm:self-auto flex-wrap justify-end">

                <!-- Column Density Picker (only visible in card grid mode) -->
                <div id="cora-dir-col-picker" class="flex items-center gap-1 p-1 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-xs font-semibold">
                    <span class="px-1.5 text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Cols</span>
                    <button type="button" id="cora-dir-col-btn-1" class="w-7 h-7 rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 dark:hover:text-white font-bold text-[11px] flex items-center justify-center" onclick="coraSetGridColumns(1)" title="1 column">1</button>
                    <button type="button" id="cora-dir-col-btn-2" class="w-7 h-7 rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 dark:hover:text-white font-bold text-[11px] flex items-center justify-center" onclick="coraSetGridColumns(2)" title="2 columns">2</button>
                    <button type="button" id="cora-dir-col-btn-3" class="w-7 h-7 rounded-lg transition-all cursor-pointer bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white font-extrabold shadow-2xs flex items-center justify-center text-[11px]" onclick="coraSetGridColumns(3)" title="3 columns (max)">3</button>
                </div>

                <!-- View Mode Switcher: Cards Grid vs Table List -->
                <div class="flex items-center gap-1 p-1 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-xs font-semibold">
                    <button type="button" id="cora-dir-view-btn-grid" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white font-extrabold shadow-2xs flex items-center gap-1.5" onclick="coraSwitchDirectoryViewMode('grid')">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect></svg>
                        <span>Card Grid</span>
                    </button>
                    <button type="button" id="cora-dir-view-btn-table" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 dark:hover:text-white flex items-center gap-1.5" onclick="coraSwitchDirectoryViewMode('table')">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        <span>Table List</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- VIEW MODE 1: DESKTOP & MOBILE RESPONSIVE CARD GRID (DEFAULT, MAX 3 COLS) -->
        <div id="cora-directory-grid-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php if ( empty($cora_leads_raw) ) : ?>
                <div class="col-span-full p-8 text-center text-zinc-400 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl">
                    No leads registered in workspace yet. Click "Add Lead" to create your first inquiry.
                </div>
            <?php else : ?>
                <?php foreach ( $cora_leads_raw as $lead ) : 
                    $st = $lead['status'] ?? 'New Lead';
                    $style = $stage_styles[$st] ?? $fallback_style;
                    $badge = $stages_summary[$st]['badge'] ?? 'bg-zinc-100 text-zinc-800';
                    $score = strtolower($lead['score'] ?? 'warm');
                    $is_won_lead = ( $st === 'Converted' );

                    if ($is_won_lead) {
                        $pill_class = 'bg-emerald-50/80 dark:bg-emerald-950/20 text-emerald-800 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/80';
                        $score_label = 'Won';
                        $score_icon = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                    } else if ($score === 'hot') {
                        $pill_class = 'bg-rose-50/80 dark:bg-rose-950/20 text-rose-800 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800/80';
                        $score_label = 'Hot';
                        $score_icon = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 2c.6 3.3 4 6 4 10a4 4 0 1 1-8 0c0-4 3.4-6.7 4-10z"></path></svg>';
                    } else if ($score === 'cold') {
                        $pill_class = 'bg-sky-50/80 dark:bg-sky-950/20 text-sky-800 dark:text-sky-300 border border-sky-200/80 dark:border-sky-800/80';
                        $score_label = 'Cold';
                        $score_icon = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 2v20M17 5l-5 5-5-5M2 12h20M7 19l5-5 5 5"></path></svg>';
                    } else {
                        $pill_class = 'bg-amber-50/80 dark:bg-amber-950/20 text-amber-800 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/80';
                        $score_label = 'Warm';
                        $score_icon = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line></svg>';
                    }

                    $assigned_to_id = $lead['assigned_to'] ?? '';
                    if ( empty( $assigned_to_id ) && ! empty( $cora_users_list ) ) {
                        $assigned_to_id = $cora_users_list[0]->ID;
                    }
                    $assigned_user = null;
                    foreach ( $cora_users_list as $u ) {
                        if ( (string) $u->ID === (string) $assigned_to_id ) {
                            $assigned_user = $u;
                            break;
                        }
                    }
                    $assignee_display_name = $assigned_user ? $assigned_user->display_name : ($lead['assignee_name'] ?? 'Shruti Sharma');
                    $assignee_initials = strtoupper( substr( $assignee_display_name, 0, 1 ) );
                    $price_display = $lead['price'] ?? '0';
                    $num_price = intval(preg_replace('/[^0-9]/', '', $price_display));
                ?>
                <div class="cora-lead-card bg-white dark:bg-zinc-900 p-4.5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs hover:shadow-md transition-all cursor-pointer flex flex-col justify-between gap-3.5 relative group" data-id="<?php echo esc_attr($lead['id']); ?>" data-assigned-to="<?php echo esc_attr($assigned_to_id); ?>" onclick="coraOpenLeadDetailDrawer('<?php echo esc_attr($lead['id']); ?>')">
                    <!-- Top Row: Client Initial Avatar & Name + Score Badge -->
                    <div class="flex items-center justify-between gap-2 border-b border-zinc-100 dark:border-zinc-800/80 pb-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-xl bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-black text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                <?php echo esc_html(strtoupper(substr($lead['names'] ?? 'C', 0, 1))); ?>
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-extrabold text-xs sm:text-sm text-zinc-900 dark:text-white leading-tight truncate">
                                    <?php echo esc_html($lead['names']); ?>
                                </h4>
                                <p class="text-[10.5px] text-zinc-400 font-medium truncate mt-0.5">
                                    <?php echo esc_html($lead['scale'] ?? 'Standard Shoot'); ?>
                                </p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9.5px] font-extrabold shrink-0 <?php echo $pill_class; ?>">
                            <?php echo $score_icon; ?>
                            <?php echo esc_html($score_label); ?>
                        </span>
                    </div>

                    <!-- Middle Row: Deal Value & Contact Email/Phone -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[10px] uppercase font-bold text-zinc-400 tracking-wider">Deal Value</span>
                            <span class="font-black text-sm sm:text-base text-zinc-950 dark:text-white tracking-tight">
                                ₹<?php echo number_format($num_price); ?>
                            </span>
                        </div>
                        <div class="p-2.5 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200/60 dark:border-zinc-800 space-y-0.5">
                            <div class="text-[11px] font-medium text-zinc-800 dark:text-zinc-200 truncate flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                <span class="truncate"><?php echo esc_html($lead['email']); ?></span>
                            </div>
                            <div class="text-[10.5px] font-medium text-zinc-500 dark:text-zinc-400 flex items-center justify-between gap-1 pt-0.5">
                                <span><?php echo esc_html($lead['phone'] ?? 'N/A'); ?></span>
                                <span class="px-1.5 py-0.5 rounded bg-zinc-200/60 dark:bg-zinc-700 text-[9.5px] font-semibold text-zinc-600 dark:text-zinc-300 truncate max-w-[90px]">
                                    <?php echo esc_html($lead['city'] ?? 'Mumbai'); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Row: Stage Badge & Assignee + Quick Action -->
                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border shrink-0 <?php echo $badge; ?>">
                                <?php echo esc_html($st); ?>
                            </span>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $lead['phone'] ?? '919876543210'); ?>" target="_blank" class="w-7 h-7 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/80 flex items-center justify-center hover:bg-emerald-100 transition-all shadow-2xs" title="Chat on WhatsApp">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.67-1.616-.919-2.213-.242-.58-.487-.502-.67-.511l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c-.001 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413"/></svg>
                            </a>
                            <button type="button" class="px-2.5 py-1 text-[11px] font-extrabold bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 hover:bg-zinc-800 rounded-xl transition-all cursor-pointer shadow-2xs" onclick="coraOpenLeadDetailDrawer('<?php echo esc_attr($lead['id']); ?>')">
                                View Deal
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <!-- Filter empty state (shown by JS when search/filter yields 0 results) -->
            <div id="cora-grid-empty-state" class="col-span-full hidden">
                <div class="flex flex-col items-center justify-center gap-4 py-16 px-6 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl">
                    <div class="w-12 h-12 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400 dark:text-zinc-500"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-bold text-zinc-800 dark:text-zinc-200">No leads found</p>
                        <p id="cora-grid-empty-msg" class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Try adjusting your search or filters.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- VIEW MODE 2: TABLE LIST VIEW (OPTIONAL TOGGLE) -->
        <div id="cora-directory-table-container" class="hidden bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-600 dark:text-zinc-300 table-fixed min-w-[950px]">
                    <colgroup>
                        <col class="w-[4%]">
                        <col class="w-[17%]">
                        <col class="w-[17%]">
                        <col class="w-[11%]">
                        <col class="w-[10%]">
                        <col class="w-[9%]">
                        <col class="w-[13%]">
                        <col class="w-[9%]">
                        <col class="w-[10%]">
                    </colgroup>
                    <thead class="bg-zinc-50 dark:bg-zinc-800/40 text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider text-[10px] border-b border-zinc-200 dark:border-zinc-800">
                        <tr>
                            <th class="p-4 text-center">
                                <input type="checkbox" id="cora-leads-select-all" class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-950 focus:ring-0 cursor-pointer" onchange="coraToggleSelectAllLeads(this)">
                            </th>
                            <th class="p-4">Lead / Client Name</th>
                            <th class="p-4">Contact Details</th>
                            <th class="p-4">Deal Stage</th>
                            <th class="p-4">Budget / Value</th>
                            <th class="p-4">Temperature</th>
                            <th class="p-4">Assigned To</th>
                            <th class="p-4">Location</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="cora-leads-table-body" class="divide-y divide-zinc-200/80 dark:divide-zinc-800/60">
                        <?php if ( empty($cora_leads_raw) ) : ?>
                            <tr>
                                <td colspan="9" class="p-8 text-center text-zinc-400">No leads registered in workspace yet. Click "Add Lead" to create your first inquiry.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $cora_leads_raw as $lead ) : 
                                $st = $lead['status'] ?? 'New Lead';
                                $badge = $stages_summary[$st]['badge'] ?? 'bg-zinc-100 text-zinc-800';
                                $assigned_to_id = $lead['assigned_to'] ?? '';
                                if ( empty( $assigned_to_id ) && ! empty( $cora_users_list ) ) {
                                    $assigned_to_id = $cora_users_list[0]->ID;
                                }
                                $assigned_user = null;
                                foreach ( $cora_users_list as $u ) {
                                    if ( (string) $u->ID === (string) $assigned_to_id ) {
                                        $assigned_user = $u;
                                        break;
                                    }
                                }
                                $assignee_display_name = $assigned_user ? $assigned_user->display_name : ($lead['assignee_name'] ?? 'Shruti Sharma');
                                $assignee_initials = strtoupper( substr( $assignee_display_name, 0, 1 ) );
                            ?>
                            <tr class="hover:bg-zinc-50/65 dark:hover:bg-zinc-800/25 transition-colors cursor-pointer" data-assigned-to="<?php echo esc_attr( $assigned_to_id ); ?>" onclick="coraOpenLeadDetailDrawer('<?php echo esc_attr($lead['id']); ?>')">
                                <td class="p-4 text-center" onclick="event.stopPropagation()">
                                    <input type="checkbox" class="cora-lead-row-checkbox rounded border-zinc-300 dark:border-zinc-700 text-zinc-950 focus:ring-0 cursor-pointer" value="<?php echo esc_attr($lead['id']); ?>">
                                </td>
                                <td class="p-4 font-bold text-zinc-900 dark:text-white truncate">
                                    <div class="truncate"><?php echo esc_html( $lead['names'] ); ?></div>
                                    <span class="block text-[10px] font-normal text-zinc-400 dark:text-zinc-500 mt-0.5 truncate"><?php echo esc_html( $lead['scale'] ?? 'Standard Shoot' ); ?></span>
                                </td>
                                <td class="p-4 truncate">
                                    <div class="font-medium text-zinc-800 dark:text-zinc-200 truncate"><?php echo esc_html($lead['email']); ?></div>
                                    <div class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-0.5"><?php echo esc_html($lead['phone'] ?? 'N/A'); ?></div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border <?php echo $badge; ?>">
                                        <?php echo esc_html( $st ); ?>
                                    </span>
                                </td>
                                <td class="p-4 font-bold text-zinc-900 dark:text-white">
                                    <?php 
                                        $num_price = intval(preg_replace('/[^0-9]/', '', $lead['price'] ?? '0'));
                                        echo '₹' . number_format($num_price);
                                    ?>
                                </td>
                                <td class="p-4">
                                    <?php 
                                    $sc = strtolower($lead['score'] ?? 'warm');
                                    $is_won_lead = ( $st === 'Converted' );
                                    if ($is_won_lead) {
                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>Won</span>';
                                    } else if ($sc === 'hot') {
                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 2c.6 3.3 4 6 4 10a4 4 0 1 1-8 0c0-4 3.4-6.7 4-10z"></path></svg>Hot</span>';
                                    } else if ($sc === 'cold') {
                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800/60"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 2v20M17 5l-5 5-5-5M2 12h20M7 19l5-5 5 5"></path></svg>Cold</span>';
                                    } else {
                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line></svg>Warm</span>';
                                    }
                                    ?>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-full bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 flex items-center justify-center font-bold text-[9px] shrink-0 border border-zinc-200 dark:border-zinc-800">
                                            <?php echo esc_html( $assignee_initials ); ?>
                                        </div>
                                        <span class="font-bold text-zinc-900 dark:text-white text-xs truncate">
                                            <?php echo esc_html( $assignee_display_name ); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="p-4 text-center" onclick="event.stopPropagation()">
                                    <input type="checkbox" class="cora-lead-row-checkbox rounded border-zinc-300 dark:border-zinc-700 text-zinc-950 focus:ring-0 cursor-pointer" value="<?php echo esc_attr($lead['id']); ?>">
                                </td>
                                <td class="p-4 font-bold text-zinc-900 dark:text-white truncate">
                                    <div class="truncate"><?php echo esc_html( $lead['names'] ); ?></div>
                                    <span class="block text-[10px] font-normal text-zinc-400 dark:text-zinc-500 mt-0.5 truncate"><?php echo esc_html( $lead['scale'] ?? 'Standard Shoot' ); ?></span>
                                </td>
                                <td class="p-4 truncate">
                                    <div class="font-medium text-zinc-800 dark:text-zinc-200 truncate"><?php echo esc_html($lead['email']); ?></div>
                                    <div class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-0.5"><?php echo esc_html($lead['phone'] ?? 'N/A'); ?></div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border <?php echo $badge; ?>">
                                        <?php echo esc_html( $st ); ?>
                                    </span>
                                </td>
                                <td class="p-4 font-bold text-zinc-900 dark:text-white">
                                    <?php 
                                        $num_price = intval(preg_replace('/[^0-9]/', '', $lead['price'] ?? '0'));
                                        echo '₹' . number_format($num_price);
                                    ?>
                                </td>
                                <td class="p-4">
                                    <?php 
                                    $sc = strtolower($lead['score'] ?? 'warm');
                                    $is_won_lead = ( $st === 'Converted' );
                                    if ($is_won_lead) {
                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>Won</span>';
                                    } else if ($sc === 'hot') {
                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800/60"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2c.6 3.3 4 6 4 10a4 4 0 1 1-8 0c0-4 3.4-6.7 4-10z"></path></svg>Hot</span>';
                                    } else if ($sc === 'cold') {
                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800/60"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v20M17 5l-5 5-5-5M2 12h20M7 19l5-5 5 5"></path></svg>Cold</span>';
                                    } else {
                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60"><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line></svg>Warm</span>';
                                    }
                                    ?>
                                </td>
                                <td class="p-4 font-medium text-zinc-700 dark:text-zinc-300 truncate">
                                    <?php echo esc_html( $lead['city'] ?? 'Mumbai' ); ?>
                                </td>
                                <td class="p-4 text-zinc-400 dark:text-zinc-500 font-medium">
                                    <?php echo esc_html( date( 'd M Y', $lead['created_at'] ) ); ?>
                                </td>
                                <td class="p-4 text-right space-x-1" onclick="event.stopPropagation()">
                                    <button type="button" class="px-2.5 py-1 text-[11px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-md transition-all cursor-pointer" onclick="coraOpenLeadDetailDrawer('<?php echo esc_attr($lead['id']); ?>')">
                                        View Deal
                                    </button>
                                    <button type="button" class="px-2.5 py-1 text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 rounded-md border border-emerald-200 dark:border-emerald-800 transition-all cursor-pointer" onclick="coraConvertLeadToClient('<?php echo esc_attr($lead['id']); ?>')">
                                        Convert
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <!-- Filter empty state row (shown by JS) -->
                        <tr id="cora-table-empty-state" class="hidden">
                            <td colspan="9">
                                <div class="flex flex-col items-center justify-center gap-3 py-14 px-6">
                                    <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400 dark:text-zinc-500"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm font-bold text-zinc-800 dark:text-zinc-200">No leads found</p>
                                        <p id="cora-table-empty-msg" class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5">Try adjusting your search or filters.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div><!-- /#cora-directory-table-container -->
        <!-- SERVER-SIDE PAGINATION BAR (DESKTOP & MOBILE) -->
        <div id="cora-directory-pagination" class="mt-4 p-3.5 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-3 text-xs select-none">
            <div class="flex items-center gap-2 text-zinc-500 dark:text-zinc-400 font-medium">
                <span id="cora-pagination-info">Showing 1–<?php echo min(25, count($cora_leads_raw)); ?> of <?php echo count($cora_leads_raw); ?> leads</span>
                <span class="hidden sm:inline text-zinc-300 dark:text-zinc-700">|</span>
                <div class="flex items-center gap-1.5">
                    <span class="text-[11px] font-semibold text-zinc-400">Per Page:</span>
                    <select id="cora-pagination-per-page" class="px-2 py-1 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-800 dark:text-zinc-200 font-bold text-xs cursor-pointer outline-none" onchange="coraChangePerPage(this.value)">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-1.5" id="cora-pagination-controls">
                <button type="button" id="cora-pagination-prev" class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold rounded-xl text-xs hover:bg-zinc-200 dark:hover:bg-zinc-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all flex items-center gap-1 cursor-pointer" onclick="coraGoToPage('prev')" disabled>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    <span>Prev</span>
                </button>
                <span id="cora-pagination-page-label" class="px-2 text-xs font-bold text-zinc-900 dark:text-white">Page 1 of 1</span>
                <button type="button" id="cora-pagination-next" class="px-3 py-1.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold rounded-xl text-xs hover:bg-zinc-200 dark:hover:bg-zinc-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all flex items-center gap-1 cursor-pointer" onclick="coraGoToPage('next')">
                    <span>Next</span>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- SUB-TAB 3: FUNNEL & REVENUE ANALYTICS (ACTIONABLE VELOCITY HUB) -->
    <div id="cora-lead-pane-analytics" class="cora-lead-tab-pane <?php echo ($cora_initial_subtab === 'analytics') ? '' : 'hidden'; ?> space-y-6">
        <!-- TOP PRIORITY DEAL SLA & ACTION LAUNCHERS -->
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-black text-sm text-zinc-950 dark:text-white tracking-tight flex items-center gap-2">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" class="text-amber-500"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                        High-Priority Deal SLA & Next-Action Launchers
                    </h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Real-time priority alerts to convert prospects faster before SLAs expire.</p>
                </div>
                <span class="text-[10px] font-bold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/50 px-2.5 py-1 rounded-full border border-rose-200/60 dark:border-rose-800/60 inline-flex items-center gap-1 shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span> Action Required
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                <!-- Action Launcher 1: New Inquiries -->
                <div class="p-3.5 bg-zinc-50 dark:bg-zinc-800/60 rounded-xl border border-zinc-200/80 dark:border-zinc-700/80 flex flex-col justify-between gap-3 group hover:border-zinc-400 dark:hover:border-zinc-600 transition-all">
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-zinc-900 dark:text-white">
                            <span class="flex items-center gap-1.5"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" class="text-amber-500"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg> Uncontacted Leads</span>
                            <span class="px-1.5 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 text-[10px]">High SLA</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1">2 fresh inquiries awaiting initial response (&lt; 30 min target).</p>
                    </div>
                    <button type="button" onclick="coraJumpToStageInDirectory('New Lead')" class="w-full py-1.5 bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 font-bold rounded-lg text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all cursor-pointer shadow-2xs flex items-center justify-center gap-1">
                        <span>Filter & Contact</span>
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </div>

                <!-- Action Launcher 2: Negotiation -->
                <div class="p-3.5 bg-zinc-50 dark:bg-zinc-800/60 rounded-xl border border-zinc-200/80 dark:border-zinc-700/80 flex flex-col justify-between gap-3 group hover:border-zinc-400 dark:hover:border-zinc-600 transition-all">
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-zinc-900 dark:text-white">
                            <span class="flex items-center gap-1.5"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-rose-500"><path d="M12 2c.6 3.3 4 6 4 10a4 4 0 1 1-8 0c0-4 3.4-6.7 4-10z"></path></svg> Negotiation Deals</span>
                            <span class="px-1.5 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 text-[10px]">₹10.2L Value</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1">3 active proposals in final closing stage requiring follow-up.</p>
                    </div>
                    <button type="button" onclick="coraJumpToStageInDirectory('Negotiation')" class="w-full py-1.5 bg-emerald-600 text-white hover:bg-emerald-700 dark:bg-emerald-500 dark:text-zinc-950 font-bold rounded-lg text-xs transition-all cursor-pointer shadow-2xs flex items-center justify-center gap-1">
                        <span>Review & Convert</span>
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </button>
                </div>

                <!-- Action Launcher 3: Site Visits -->
                <div class="p-3.5 bg-zinc-50 dark:bg-zinc-800/60 rounded-xl border border-zinc-200/80 dark:border-zinc-700/80 flex flex-col justify-between gap-3 group hover:border-zinc-400 dark:hover:border-zinc-600 transition-all">
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold text-zinc-900 dark:text-white">
                            <span class="flex items-center gap-1.5"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none" class="text-blue-500"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg> Upcoming Visits</span>
                            <span class="px-1.5 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-400 text-[10px]">Site Viewing</span>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-1">3 client property viewings scheduled for this week.</p>
                    </div>
                    <button type="button" onclick="coraJumpToStageInDirectory('Site Visit')" class="w-full py-1.5 bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 font-bold rounded-lg text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all cursor-pointer shadow-2xs flex items-center justify-center gap-1">
                        <span>View Schedule</span>
                        <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Stage Breakdown Bar (Interactive Click-to-Filter) -->
            <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-zinc-900 dark:text-white">Interactive Funnel Conversion Stage Breakdown</h3>
                        <p class="text-[11px] text-zinc-400 mt-0.5">Click any stage bar to filter leads directly in Directory view.</p>
                    </div>
                    <span class="text-xs text-zinc-400 font-medium"><?php echo $total_leads_count; ?> Total Deals</span>
                </div>

                <div class="space-y-3 pt-2">
                    <?php foreach ( $stages_summary as $k => $sd ) :
                        $pct = $total_leads_count > 0 ? round( ($sd['count'] / $total_leads_count) * 100, 1 ) : 0;
                    ?>
                    <div class="p-2 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/60 transition-all cursor-pointer group" onclick="coraJumpToStageInDirectory('<?php echo esc_attr($k); ?>')" title="Click to filter <?php echo esc_attr($sd['label']); ?> deals">
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span class="text-zinc-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors flex items-center gap-1.5">
                                <span><?php echo esc_html($sd['label']); ?></span>
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" class="opacity-0 group-hover:opacity-100 transition-opacity"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                            </span>
                            <span class="text-zinc-500 dark:text-zinc-400 font-mono text-[11px]"><?php echo $sd['count']; ?> deals (<?php echo $pct; ?>%) &bull; ₹<?php echo number_format($sd['value']); ?></span>
                        </div>
                        <div class="w-full h-2.5 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-zinc-950 dark:bg-white group-hover:bg-amber-500 rounded-full transition-all duration-500" style="width: <?php echo $pct; ?>%;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Lead Channels & Sources (Marked as COMING SOON) -->
            <div id="cora-lead-channels-card" class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm space-y-4 relative overflow-hidden">
                <!-- Coming Soon Overlay Badge Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-sm text-zinc-900 dark:text-white">Lead Acquisition Channels</h3>
                        <p class="text-[11px] text-zinc-400 mt-0.5">Multi-channel campaign & webhook attribution.</p>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-800 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 px-2.5 py-1 rounded-full shadow-2xs flex items-center gap-1">
                        <span>✨ Coming Soon</span>
                    </span>
                </div>

                <!-- Preview Content with Subtle Opacity overlay -->
                <div class="space-y-3 pt-2 opacity-65 pointer-events-none select-none">
                    <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200/60 dark:border-zinc-800">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-zinc-900 text-white flex items-center justify-center font-bold text-xs">WEB</div>
                            <div>
                                <span class="font-bold text-xs block text-zinc-900 dark:text-white">Website Inquiry Forms</span>
                                <span class="text-[10px] text-zinc-400">Direct booking submissions</span>
                            </div>
                        </div>
                        <span class="font-black text-xs text-zinc-900 dark:text-white">45%</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200/60 dark:border-zinc-800">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">WA</div>
                            <div>
                                <span class="font-bold text-xs block text-zinc-900 dark:text-white">WhatsApp Business</span>
                                <span class="text-[10px] text-zinc-400">Direct chat inquiries</span>
                            </div>
                        </div>
                        <span class="font-black text-xs text-zinc-900 dark:text-white">30%</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200/60 dark:border-zinc-800">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">REF</div>
                            <div>
                                <span class="font-bold text-xs block text-zinc-900 dark:text-white">Client Referrals</span>
                                <span class="text-[10px] text-zinc-400">Word-of-mouth & agency recommendations</span>
                            </div>
                        </div>
                        <span class="font-black text-xs text-zinc-900 dark:text-white">25%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SUB-TAB 4: ACTIVITY & OUTREACH LOG -->
    <div id="cora-lead-pane-activity" class="cora-lead-tab-pane <?php echo ($cora_initial_subtab === 'activity') ? '' : 'hidden'; ?> space-y-4">
        <div class="bg-white dark:bg-zinc-900 p-5 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-zinc-200/80 dark:border-zinc-800">
                <div>
                    <h3 class="font-bold text-sm text-zinc-900 dark:text-white">Workspace Activity & Outreach Timeline</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Automated logging throttled periodically (5m interval) to prevent server bloat.</p>
                </div>
                <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-1 rounded-full border border-emerald-200/60 dark:border-emerald-800/60 inline-flex items-center gap-1.5 shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> 7-Day Auto-Purge Schedule
                </span>
            </div>
            
            <div id="cora-lead-activity-timeline" class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-zinc-200 dark:before:bg-zinc-800 mt-4">
                <?php
                $cora_activity_logs_list = function_exists('cora_db_get_activity_logs') ? cora_db_get_activity_logs() : array();
                if ( ! empty( $cora_activity_logs_list ) ) :
                    foreach ( array_slice( $cora_activity_logs_list, 0, 25 ) as $log_item ) :
                        $act_title = esc_html( $log_item['action_type'] ?? 'Activity' );
                        $act_desc  = esc_html( $log_item['description'] ?? 'Workspace event recorded.' );
                        $act_user  = esc_html( $log_item['user_name'] ?? 'System' );
                        $act_ts    = intval( $log_item['timestamp'] ?? time() );
                        $act_time_str = date( 'M j, Y \a\t g:i A', $act_ts );
                ?>
                <div class="relative">
                    <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full bg-zinc-900 dark:bg-white border-2 border-white dark:border-zinc-900"></div>
                    <div class="text-xs">
                        <span class="font-bold text-zinc-900 dark:text-white"><?php echo $act_title; ?>:</span>
                        <span class="text-zinc-600 dark:text-zinc-300"> <?php echo $act_desc; ?></span>
                        <div class="text-[10px] text-zinc-400 mt-0.5"><?php echo $act_time_str; ?> • <?php echo $act_user; ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else : ?>
                <div class="relative">
                    <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white dark:border-zinc-900"></div>
                    <div class="text-xs">
                        <span class="font-bold text-zinc-900 dark:text-white">Workspace Activity Log Active:</span>
                        <span class="text-zinc-600 dark:text-zinc-300"> Logs are stored for 7 days on a periodic schedule and auto-purged to protect server resources.</span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- SLIDING SIDE DRAWER 1: RESIZABLE MULTI-TAB PROSPECT OPERATIONS WORKSPACE   -->
<!-- ========================================================================= -->
<aside id="cora-lead-detail-drawer" class="cora-prospect-detail-drawer cora-side-drawer hidden collapsed fixed top-0 right-0 w-full sm:w-[540px] md:w-[50vw] max-w-full sm:max-w-xl h-full bg-white dark:bg-zinc-900 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 dark:border-zinc-800 flex flex-col font-sans overflow-hidden">
    
    <!-- Drag Handle Bar on Left Edge (Desktop Only) -->
    <div id="cora-drawer-resize-handle" class="hidden sm:flex absolute top-0 bottom-0 -left-2 w-4 cursor-ew-resize group z-20 items-center justify-center" title="Drag left/right to resize drawer">
        <div class="w-1.5 h-14 rounded-full bg-zinc-300 dark:bg-zinc-700 group-hover:bg-zinc-950 dark:group-hover:bg-white group-hover:w-2 transition-all shadow-xs"></div>
    </div>

    <!-- Header: Clean, Uncluttered & Sticky Top -->
    <div class="p-4 sm:p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-white dark:bg-zinc-900 sticky top-0 z-30">
        <div class="flex items-center gap-3 min-w-0 pr-2">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-black text-sm sm:text-base flex items-center justify-center shadow-sm shrink-0">
                <span id="cora-drawer-avatar-initial">C</span>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                    <h3 id="cora-drawer-lead-name" class="font-black text-base sm:text-lg text-zinc-900 dark:text-white leading-tight truncate">Corporate Brand Film</h3>
                    <span id="cora-drawer-lead-score" class="px-2.5 py-0.5 rounded-full text-[9.5px] font-extrabold uppercase tracking-wider bg-amber-500/10 text-amber-600 border border-amber-200 shrink-0">Warm</span>
                </div>
                <p id="cora-drawer-lead-email" class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 font-medium truncate">Shoot: Brand Film – Bengaluru</p>
            </div>
        </div>
        
        <button type="button" class="text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 transition-all cursor-pointer shrink-0 flex items-center justify-center" onclick="window.coraCloseAllDrawers()" title="Close Drawer">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Segmented Tab Header Bar -->
    <div class="px-3 sm:px-6 py-2 bg-zinc-50/90 dark:bg-zinc-950/80 border-b border-zinc-200/80 dark:border-zinc-800 shrink-0">
        <div class="overflow-x-auto no-scrollbar flex items-center gap-1 p-1 bg-zinc-200/70 dark:bg-zinc-800/80 rounded-2xl text-xs font-semibold max-w-full">
            <button type="button" id="cora-lead-detail-tab-btn-overview" class="cora-lead-detail-tab-btn shrink-0 py-2 px-3.5 rounded-xl transition-all cursor-pointer bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white font-extrabold shadow-xs flex items-center justify-center gap-1.5 whitespace-nowrap text-xs" onclick="coraSwitchLeadDetailTab('overview')">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                <span>Details</span>
            </button>
            <button type="button" id="cora-lead-detail-tab-btn-automation" class="cora-lead-detail-tab-btn shrink-0 py-2 px-3.5 rounded-xl transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 dark:hover:text-white flex items-center justify-center gap-1.5 whitespace-nowrap text-xs font-semibold" onclick="coraSwitchLeadDetailTab('automation')">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                <span>Automations</span>
            </button>
            <button type="button" id="cora-lead-detail-tab-btn-checklist" class="cora-lead-detail-tab-btn shrink-0 py-2 px-3.5 rounded-xl transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 dark:hover:text-white flex items-center justify-center gap-1.5 whitespace-nowrap text-xs font-semibold" onclick="coraSwitchLeadDetailTab('checklist')">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                <span>Tasks</span>
            </button>
            <button type="button" id="cora-lead-detail-tab-btn-audit" class="cora-lead-detail-tab-btn shrink-0 py-2 px-3.5 rounded-xl transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 dark:hover:text-white flex items-center justify-center gap-1.5 whitespace-nowrap text-xs font-semibold" onclick="coraSwitchLeadDetailTab('audit')">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 16 14"></polyline></svg>
                <span>History</span>
            </button>
        </div>
    </div>

    <!-- Content Body Panes -->
    <div class="p-4 sm:p-6 overflow-y-auto flex-1 space-y-4 sm:space-y-5">
        <input type="hidden" id="cora-drawer-lead-id" value="">

        <!-- TAB 1: DETAILS & CONTACT -->
        <div id="cora-lead-detail-tab-overview" class="cora-lead-detail-tab-pane space-y-3.5 text-xs">
            
            <!-- ALWAYS VISIBLE: 1-TAP QUICK ACTION BAR -->
            <div class="p-3.5 bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-2xl shadow-2xs space-y-2.5">
                <div class="flex items-center justify-between gap-2 border-b border-zinc-100 dark:border-zinc-800 pb-2">
                    <span class="text-[10px] uppercase font-extrabold tracking-wider text-zinc-500 dark:text-zinc-400">Quick Outreach & Actions</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>SLA: <strong id="cora-drawer-sla-timer" class="text-zinc-950 dark:text-white font-extrabold">18m remaining</strong></span>
                    </span>
                </div>
                <div class="grid grid-cols-3 gap-2 pt-0.5">
                    <a id="cora-drawer-whatsapp-btn" href="#" target="_blank" class="py-2.5 px-2 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/80 font-bold rounded-xl text-xs hover:bg-emerald-100 transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" class="shrink-0 text-emerald-600 dark:text-emerald-400"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.67-1.616-.919-2.213-.242-.58-.487-.502-.67-.511l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c-.001 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413"/></svg>
                        <span>WhatsApp</span>
                    </a>
                    <a id="cora-drawer-sla-email-btn" href="#" target="_blank" class="py-2.5 px-2 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white border border-zinc-200 dark:border-zinc-700 font-bold rounded-xl text-xs hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 dark:text-zinc-400"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        <span>Email</span>
                    </a>
                    <button type="button" id="cora-convert-lead-btn" class="py-2.5 px-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-extrabold rounded-xl text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all shadow-sm cursor-pointer flex items-center justify-center gap-1.5" onclick="coraConvertCurrentLeadToClient()">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Convert</span>
                    </button>
                </div>
            </div>

            <!-- ACCORDION SECTION 1: DEAL STATUS & OWNER ASSIGNMENT (COLLAPSED BY DEFAULT) -->
            <div class="cora-accordion-card bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-2xs overflow-hidden">
                <button type="button" class="w-full p-4 flex items-center justify-between gap-2 text-left cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" onclick="coraToggleDrawerAccordion(this)">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-xs text-zinc-900 dark:text-white">Deal Status & Assignee</h4>
                            <p class="text-[10.5px] text-zinc-400 font-medium">Pipeline stage and team owner</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">Tap to Edit</span>
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" class="cora-accordion-icon text-zinc-400 transition-transform duration-200"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                </button>
                <div class="cora-accordion-body hidden p-4 pt-0 border-t border-zinc-100 dark:border-zinc-800/80 space-y-3 mt-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <div>
                            <label class="block font-bold text-[11px] text-zinc-600 dark:text-zinc-400 mb-1">Pipeline Stage</label>
                            <select id="cora-drawer-stage-select" class="w-full text-xs font-extrabold bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 px-3 py-2.5 rounded-xl text-zinc-900 dark:text-white focus:outline-none cursor-pointer" onchange="coraUpdateLeadStageFromDrawer()">
                                <option value="New Lead">New Lead</option>
                                <option value="Contacted">Proposal Sent</option>
                                <option value="Site Visit">Site Visit / Viewing</option>
                                <option value="Negotiation">Negotiation</option>
                                <option value="Converted">Converted</option>
                                <option value="Lost">Closed / Lost</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-[11px] text-zinc-600 dark:text-zinc-400 mb-1">Assigned Team Member</label>
                            <select id="cora-drawer-input-assigned-to" class="w-full px-3 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white text-xs font-semibold focus:outline-none transition-colors cursor-pointer" onchange="coraUpdateLeadAssignee(document.getElementById('cora-drawer-lead-id').value, this.value)">
                                <?php foreach ( $cora_users_list as $u ) : ?>
                                    <option value="<?php echo esc_attr( $u->ID ); ?>"><?php echo esc_html( $u->display_name ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCORDION SECTION 2: CONTACT DETAILS & LOCATION (OPEN BY DEFAULT FOR INSTANT COMFORT) -->
            <div class="cora-accordion-card bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-2xs overflow-hidden">
                <button type="button" class="w-full p-4 flex items-center justify-between gap-2 text-left cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" onclick="coraToggleDrawerAccordion(this)">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-xs text-zinc-900 dark:text-white">Contact & Client Information</h4>
                            <p class="text-[10.5px] text-zinc-400 font-medium">Name, email, phone & city</p>
                        </div>
                    </div>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" class="cora-accordion-icon text-zinc-400 transition-transform duration-200 transform rotate-180"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div class="cora-accordion-body p-4 pt-0 border-t border-zinc-100 dark:border-zinc-800/80 space-y-3 mt-3">
                    <div class="pt-2">
                        <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Full Name / Prospect Title</label>
                        <input type="text" id="cora-drawer-input-names" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none text-xs">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Email Address</label>
                            <input type="email" id="cora-drawer-input-email" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Phone / WhatsApp</label>
                            <input type="text" id="cora-drawer-input-phone" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none text-xs">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <label class="block font-bold text-zinc-700 dark:text-zinc-300">Target City / Geo-Location</label>
                            <button type="button" class="text-[10px] font-bold text-zinc-600 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white flex items-center gap-1 cursor-pointer" onclick="coraDetectCurrentGeoCity()">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <span>Auto-Detect Geo</span>
                            </button>
                        </div>
                        <input type="text" id="cora-drawer-input-city" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none text-xs" placeholder="e.g. Mumbai, BKC / Bengaluru">
                        
                        <!-- Quick Studio Hub City Pills for Instant Selection -->
                        <div class="flex items-center gap-1.5 flex-wrap pt-2">
                            <span class="text-[9.5px] font-bold text-zinc-400 uppercase tracking-wider">Quick Hubs:</span>
                            <button type="button" class="px-2 py-0.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-[10px] font-semibold transition-colors cursor-pointer" onclick="$('#cora-drawer-input-city').val('Mumbai')">Mumbai</button>
                            <button type="button" class="px-2 py-0.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-[10px] font-semibold transition-colors cursor-pointer" onclick="$('#cora-drawer-input-city').val('Bengaluru')">Bengaluru</button>
                            <button type="button" class="px-2 py-0.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-[10px] font-semibold transition-colors cursor-pointer" onclick="$('#cora-drawer-input-city').val('Goa')">Goa</button>
                            <button type="button" class="px-2 py-0.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-[10px] font-semibold transition-colors cursor-pointer" onclick="$('#cora-drawer-input-city').val('Delhi NCR')">Delhi NCR</button>
                            <button type="button" class="px-2 py-0.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 text-[10px] font-semibold transition-colors cursor-pointer" onclick="$('#cora-drawer-input-city').val('Hyderabad')">Hyderabad</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCORDION SECTION 3: DEAL BUDGET & PRIORITY (COLLAPSED BY DEFAULT) -->
            <div class="cora-accordion-card bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-2xs overflow-hidden">
                <button type="button" class="w-full p-4 flex items-center justify-between gap-2 text-left cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" onclick="coraToggleDrawerAccordion(this)">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-xs text-zinc-900 dark:text-white">Deal Budget & Intent Priority</h4>
                            <p class="text-[10.5px] text-zinc-400 font-medium">Value (₹) and priority level</p>
                        </div>
                    </div>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" class="cora-accordion-icon text-zinc-400 transition-transform duration-200"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div class="cora-accordion-body hidden p-4 pt-0 border-t border-zinc-100 dark:border-zinc-800/80 space-y-3 mt-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <div>
                            <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Deal Budget (₹)</label>
                            <input type="text" id="cora-drawer-input-price" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Priority Level</label>
                            <select id="cora-drawer-input-score" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none text-xs">
                                <option value="hot">Hot (High Priority)</option>
                                <option value="warm">Warm (Standard Interest)</option>
                                <option value="cold">Cold (Low Priority)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCORDION SECTION 4: NOTES & SHOOT SPECIFICATIONS (COLLAPSED BY DEFAULT) -->
            <div class="cora-accordion-card bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-2xs overflow-hidden">
                <button type="button" class="w-full p-4 flex items-center justify-between gap-2 text-left cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" onclick="coraToggleDrawerAccordion(this)">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-xs text-zinc-900 dark:text-white">Deal Notes & Shoot Specifications</h4>
                            <p class="text-[10.5px] text-zinc-400 font-medium">Inquiry requirements & dates</p>
                        </div>
                    </div>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" class="cora-accordion-icon text-zinc-400 transition-transform duration-200"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div class="cora-accordion-body hidden p-4 pt-0 border-t border-zinc-100 dark:border-zinc-800/80 space-y-3 mt-3">
                    <div class="pt-2">
                        <textarea id="cora-drawer-input-notes" rows="3" class="w-full px-3.5 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none resize-none text-xs" placeholder="Client specifications, requested deliverables, shoot dates..."></textarea>
                    </div>
                </div>
            </div>

        </div>

        <!-- TAB 2: WORKFLOWS & AUTOMATION -->
        <div id="cora-lead-detail-tab-automation" class="cora-lead-detail-tab-pane hidden space-y-4 text-xs">
            <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-1">
                <h4 class="font-bold text-xs text-zinc-900 dark:text-white flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                    Automated Sequences & Drip Workflows
                </h4>
                <p class="text-[11px] text-zinc-500">Configure automated customer journeys and notification rules for this deal.</p>
            </div>

            <div class="space-y-3">
                <div class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xs text-zinc-900 dark:text-white">Instant Welcome WhatsApp & Email</div>
                        <div class="text-[11px] text-zinc-500 mt-0.5">Sends automated welcome portfolio deck when lead is created.</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" checked class="cora-toggle-checkbox sr-only">
                        <div class="cora-toggle-slider"></div>
                    </label>
                </div>

                <div class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xs text-zinc-900 dark:text-white">3-Day Auto Proposal Reminder Drip</div>
                        <div class="text-[11px] text-zinc-500 mt-0.5">Reminds client if proposal remains unreviewed for 72 hours.</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" checked class="cora-toggle-checkbox sr-only">
                        <div class="cora-toggle-slider"></div>
                    </label>
                </div>

                <div class="p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xs text-zinc-900 dark:text-white">High-Value VIP Alert (> ₹2,00,000)</div>
                        <div class="text-[11px] text-zinc-500 mt-0.5">Alerts studio head and assigns lead senior producer immediately.</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" checked class="cora-toggle-checkbox sr-only">
                        <div class="cora-toggle-slider"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- TAB 3: SCOPE & INTAKE CHECKLIST -->
        <div id="cora-lead-detail-tab-checklist" class="cora-lead-detail-tab-pane hidden space-y-4 text-xs">
            <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-2">
                <div class="flex items-center justify-between">
                    <h4 class="font-bold text-xs text-zinc-900 dark:text-white">Deal Intake Checklist</h4>
                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 font-mono">2/4 Completed (50%)</span>
                </div>
                <div class="w-full h-1.5 bg-zinc-200 dark:bg-zinc-800 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: 50%;"></div>
                </div>
            </div>

            <!-- Interactive Checklist Items Container -->
            <div id="cora-lead-checklist-container" class="space-y-2">
                <label class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-300 transition-all cursor-pointer">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <input type="checkbox" checked class="w-4 h-4 text-emerald-600 rounded border-zinc-300 focus:ring-emerald-500">
                        <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 truncate line-through">Verify Shoot Date & Venue Licensing</span>
                    </div>
                    <span class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 border border-emerald-200">Done</span>
                </label>

                <label class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-300 transition-all cursor-pointer">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <input type="checkbox" checked class="w-4 h-4 text-emerald-600 rounded border-zinc-300 focus:ring-emerald-500">
                        <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 truncate line-through">Deliver Itemized Commercial Proposal</span>
                    </div>
                    <span class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 border border-emerald-200">Done</span>
                </label>

                <label class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-300 transition-all cursor-pointer">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <input type="checkbox" class="w-4 h-4 text-emerald-600 rounded border-zinc-300 focus:ring-emerald-500">
                        <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 truncate">Confirm 50% Booking Advance Deposit</span>
                    </div>
                    <span class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-950/40 text-amber-600 border border-amber-200">Pending</span>
                </label>

                <label class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:border-zinc-300 transition-all cursor-pointer">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <input type="checkbox" class="w-4 h-4 text-emerald-600 rounded border-zinc-300 focus:ring-emerald-500">
                        <span class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 truncate">Assign Lead Videographer & Crew Roster</span>
                    </div>
                    <span class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-amber-50 dark:bg-amber-950/40 text-amber-600 border border-amber-200">Pending</span>
                </label>
            </div>

            <!-- Dynamic Intake Task Adder -->
            <div class="pt-2 flex items-center gap-2">
                <input type="text" id="cora-new-checklist-input" class="flex-1 px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white text-xs focus:outline-none" placeholder="Add custom intake task...">
                <button type="button" class="px-3.5 py-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold rounded-xl text-xs hover:bg-zinc-800 transition-all cursor-pointer shrink-0" onclick="coraAddLeadChecklistItem()">
                    + Add Task
                </button>
            </div>
        </div>

        <!-- TAB 4: AUDIT TRAIL & CALL LOGS -->
        <div id="cora-lead-detail-tab-audit" class="cora-lead-detail-tab-pane hidden space-y-4 text-xs">
            <!-- Add Call Note Logger -->
            <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-2">
                <span class="font-bold text-xs text-zinc-900 dark:text-white block">Log Prospect Call / Meeting Note</span>
                <textarea id="cora-audit-note-input" rows="2" class="w-full px-3 py-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white text-xs focus:outline-none resize-none" placeholder="Record client feedback, phone conversation, or project scope update..."></textarea>
                <div class="flex justify-end">
                    <button type="button" class="px-3.5 py-1.5 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold rounded-xl text-xs hover:bg-zinc-800 transition-all cursor-pointer" onclick="coraAddLeadAuditLogNote()">
                        + Record Call Note
                    </button>
                </div>
            </div>

            <!-- Chronological Audit Timeline -->
            <div id="cora-lead-audit-timeline" class="space-y-4 pt-2">
                <div class="relative pl-6 pb-4 border-l-2 border-zinc-200 dark:border-zinc-800 ml-3">
                    <div class="absolute -left-[7px] top-1 w-3 h-3 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-zinc-900"></div>
                    <div class="flex items-center justify-between gap-2 min-w-0">
                        <span class="font-bold text-xs text-zinc-900 dark:text-white">Stage Moved to Negotiation</span>
                        <span class="text-[10px] text-zinc-400 font-mono shrink-0">Today, 2:15 PM</span>
                    </div>
                    <p class="text-xs text-zinc-500 mt-0.5">User moved deal stage from Proposal Sent to Negotiation.</p>
                </div>

                <div class="relative pl-6 pb-4 border-l-2 border-zinc-200 dark:border-zinc-800 ml-3">
                    <div class="absolute -left-[7px] top-1 w-3 h-3 rounded-full bg-blue-500 ring-2 ring-white dark:ring-zinc-900"></div>
                    <div class="flex items-center justify-between gap-2 min-w-0">
                        <span class="font-bold text-xs text-zinc-900 dark:text-white">Proposal Estimate Sent</span>
                        <span class="text-[10px] text-zinc-400 font-mono shrink-0">Yesterday, 11:30 AM</span>
                    </div>
                    <p class="text-xs text-zinc-500 mt-0.5">Itemized commercial quotation PDF sent via WhatsApp.</p>
                </div>

                <div class="relative pl-6 border-l-2 border-zinc-200 dark:border-zinc-800 ml-3">
                    <div class="absolute -left-[7px] top-1 w-3 h-3 rounded-full bg-zinc-400 ring-2 ring-white dark:ring-zinc-900"></div>
                    <div class="flex items-center justify-between gap-2 min-w-0">
                        <span class="font-bold text-xs text-zinc-900 dark:text-white">Lead Inquiry Registered</span>
                        <span class="text-[10px] text-zinc-400 font-mono shrink-0">2 days ago</span>
                    </div>
                    <p class="text-xs text-zinc-500 mt-0.5">Inquiry captured via Website Intake Form.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer: Sticky Bottom Action Bar -->
    <div class="p-3.5 sm:p-4 border-t border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-white dark:bg-zinc-900 sticky bottom-0 z-30 shadow-lg">
        <button type="button" class="px-2.5 sm:px-3.5 py-2 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0" onclick="coraDeleteCurrentLead()">
            Delete Lead
        </button>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" class="px-3 sm:px-4 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-semibold rounded-xl text-xs cursor-pointer hover:bg-zinc-200" onclick="window.coraCloseAllDrawers()">
                Cancel
            </button>
            <button type="button" class="px-3.5 sm:px-5 py-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold rounded-xl text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all cursor-pointer shadow-sm" onclick="coraSaveLeadFromDrawer()">
                Save Deal Changes
            </button>
        </div>
    </div>
</aside>

<!-- ========================================================================= -->
<!-- SLIDING SIDE DRAWER 2: CREATE / EDIT LEAD DRAWER                         -->
<!-- ========================================================================= -->
<!-- SLIDING SIDE DRAWER 2: REGISTER NEW LEAD (MULTISTEP WIZARD & DYNAMIC STAGES) -->
<!-- ========================================================================= -->
<aside id="cora-create-lead-drawer" class="cora-side-drawer hidden collapsed fixed top-0 right-0 w-full sm:w-[540px] md:w-[50vw] max-w-full sm:max-w-xl h-full bg-white dark:bg-zinc-900 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 dark:border-zinc-800 flex flex-col font-sans overflow-hidden">
    
    <!-- Drawer Header with Live Stage Indicator -->
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-zinc-50/50 dark:bg-zinc-850/50">
        <div>
            <div class="flex items-center gap-2">
                <h3 class="font-extrabold text-base text-zinc-900 dark:text-white">Register New Lead Inquiry</h3>
                <span id="cora-create-lead-target-badge" class="px-2 py-0.5 rounded-full bg-zinc-900 dark:bg-white text-white dark:text-zinc-950 font-bold text-[9.5px] tracking-wider uppercase">
                    Stage: New Inquiries
                </span>
            </div>
            <p class="text-xs text-zinc-400 mt-0.5">3-Step Intelligent Lead Capture Wizard.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-800 dark:hover:text-white p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer" onclick="window.coraCloseAllDrawers()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Step Progress Indicator Bar -->
    <div class="px-6 py-3 bg-zinc-50/80 dark:bg-zinc-950/40 border-b border-zinc-200/80 dark:border-zinc-800/80 flex items-center justify-between gap-2 shrink-0">
        <button type="button" onclick="coraGoToCreateLeadStep(1)" class="flex-1 flex items-center gap-2 text-left cursor-pointer">
            <span id="cora-lead-step-ind-1" class="w-6 h-6 rounded-full border flex items-center justify-center text-[10.5px] font-bold transition-all bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 border-zinc-950 dark:border-white shadow-xs">1</span>
            <span class="text-[11px] font-bold text-zinc-800 dark:text-zinc-200 hidden sm:inline">Client Contact</span>
        </button>
        <div class="w-4 h-px bg-zinc-200 dark:bg-zinc-800 shrink-0"></div>
        <button type="button" onclick="coraGoToCreateLeadStep(2)" class="flex-1 flex items-center gap-2 text-left cursor-pointer">
            <span id="cora-lead-step-ind-2" class="w-6 h-6 rounded-full border flex items-center justify-center text-[10.5px] font-bold transition-all bg-zinc-100 dark:bg-zinc-800 text-zinc-400 border-zinc-200 dark:border-zinc-700">2</span>
            <span class="text-[11px] font-bold text-zinc-600 dark:text-zinc-400 hidden sm:inline">Stage & Budget</span>
        </button>
        <div class="w-4 h-px bg-zinc-200 dark:bg-zinc-800 shrink-0"></div>
        <button type="button" onclick="coraGoToCreateLeadStep(3)" class="flex-1 flex items-center gap-2 text-left cursor-pointer">
            <span id="cora-lead-step-ind-3" class="w-6 h-6 rounded-full border flex items-center justify-center text-[10.5px] font-bold transition-all bg-zinc-100 dark:bg-zinc-800 text-zinc-400 border-zinc-200 dark:border-zinc-700">3</span>
            <span class="text-[11px] font-bold text-zinc-600 dark:text-zinc-400 hidden sm:inline">Scope & Notes</span>
        </button>
    </div>

    <!-- Main Form Container -->
    <form id="cora-create-lead-form" class="p-6 overflow-y-auto flex-1 text-xs" onsubmit="event.preventDefault(); coraSubmitNewLeadForm();">
        <input type="hidden" id="cora-new-lead-stage" value="New Lead">

        <!-- STEP 1: CLIENT CONTACT INFO -->
        <div id="cora-create-lead-step-1" class="cora-create-lead-step space-y-4">
            <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-1">
                <h4 class="font-bold text-xs text-zinc-900 dark:text-white flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Step 1: Client Contact Information
                </h4>
                <p class="text-[11px] text-zinc-500">Provide primary contact details to initiate the lead record.</p>
            </div>

            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Full Name / Client Name <span class="text-rose-500">*</span></label>
                <input type="text" id="cora-new-lead-names" required class="w-full px-3 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none focus:border-zinc-900 dark:focus:border-zinc-100 transition-colors" placeholder="e.g. Vikramaditya Singhania">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" id="cora-new-lead-email" required class="w-full px-3 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none focus:border-zinc-900 dark:focus:border-zinc-100 transition-colors" placeholder="vikram@singhania.com">
                </div>
                <div>
                    <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Phone / WhatsApp</label>
                    <input type="tel" id="cora-new-lead-phone" class="w-full px-3 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none focus:border-zinc-900 dark:focus:border-zinc-100 transition-colors" placeholder="+91 98765 43210" oninput="this.value = this.value.replace(/[^0-9+\-\s()]/g, '')">
                </div>
            </div>

            <div class="pt-6 flex items-center justify-between border-t border-zinc-200 dark:border-zinc-800">
                <button type="button" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold rounded-xl text-xs cursor-pointer hover:bg-zinc-200 transition-colors" onclick="window.coraCloseAllDrawers()">
                    Cancel
                </button>
                <button type="button" class="px-5 py-2.5 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold rounded-xl text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all cursor-pointer flex items-center gap-1.5 shadow-sm" onclick="coraGoToCreateLeadStep(2)">
                    <span>Next: Stage & Budget</span>
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            </div>
        </div>

        <!-- STEP 2: STAGE & BUDGET STRATEGY -->
        <div id="cora-create-lead-step-2" class="cora-create-lead-step hidden space-y-4">
            <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-1">
                <h4 class="font-bold text-xs text-zinc-900 dark:text-white flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                    Step 2: Initial Stage & Value Scoping
                </h4>
                <p class="text-[11px] text-zinc-500">Select the target pipeline stage and assign deal temperature priority.</p>
            </div>

            <!-- Dynamic Interactive Stage Cards Grid -->
            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1.5">Target Pipeline Stage <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-2">
                    <?php foreach ( $stages_config as $s_key => $s_val ) : 
                        if ( isset( $s_val['enabled'] ) && ! $s_val['enabled'] ) continue;
                        $s_label = $s_val['label'] ?? $s_key;
                        $s_badge = $s_val['badge'] ?? 'bg-zinc-500/10 text-zinc-600 border-zinc-200';
                    ?>
                    <button type="button" class="cora-stage-select-btn p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-left transition-all cursor-pointer flex flex-col justify-between gap-1.5 hover:border-zinc-400 group" data-stage="<?php echo esc_attr($s_key); ?>" onclick="coraSelectCreateLeadStage('<?php echo esc_attr($s_key); ?>')">
                        <div class="flex items-center justify-between gap-1 w-full">
                            <span class="cora-stage-title font-bold text-xs text-zinc-900 dark:text-white truncate group-hover:text-zinc-950"><?php echo esc_html($s_label); ?></span>
                            <span class="w-2 h-2 rounded-full shrink-0 <?php echo (strpos($s_badge, 'emerald') !== false) ? 'bg-emerald-500' : ((strpos($s_badge, 'purple') !== false) ? 'bg-purple-500' : ((strpos($s_badge, 'sky') !== false || strpos($s_badge, 'blue') !== false) ? 'bg-blue-500' : 'bg-amber-500')); ?>"></span>
                        </div>
                        <div class="flex items-center justify-between gap-1 w-full">
                            <span class="text-[9.5px] font-mono text-zinc-400 truncate"><?php echo esc_html($s_key); ?></span>
                            <span class="px-1.5 py-0.5 rounded text-[8.5px] font-bold border <?php echo esc_attr($s_badge); ?>">Active</span>
                        </div>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Estimated Deal Budget (₹)</label>
                    <input type="text" id="cora-new-lead-price" class="w-full px-3 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none focus:border-zinc-900 transition-colors" placeholder="e.g. 150000" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                <div>
                    <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Temperature / Intent Priority</label>
                    <select id="cora-new-lead-score" class="w-full px-3 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none transition-colors">
                        <option value="warm">Warm (Standard Interest)</option>
                        <option value="hot">Hot (High Intent / Urgency)</option>
                        <option value="cold">Cold (Low Priority)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Assign Team Member</label>
                <select id="cora-new-lead-assigned-to" class="w-full px-3 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none transition-colors">
                    <?php foreach ( $cora_users_list as $u ) : ?>
                        <option value="<?php echo esc_attr( $u->ID ); ?>"><?php echo esc_html( $u->display_name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="pt-6 flex items-center justify-between border-t border-zinc-200 dark:border-zinc-800">
                <button type="button" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold rounded-xl text-xs cursor-pointer hover:bg-zinc-200 transition-colors flex items-center gap-1.5" onclick="coraGoToCreateLeadStep(1)">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <span>Back</span>
                </button>
                <button type="button" class="px-5 py-2.5 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold rounded-xl text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all cursor-pointer flex items-center gap-1.5 shadow-sm" onclick="coraGoToCreateLeadStep(3)">
                    <span>Next: Scope & Notes</span>
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            </div>
        </div>

        <!-- STEP 3: SHOOT SCOPE & NOTES -->
        <div id="cora-create-lead-step-3" class="cora-create-lead-step hidden space-y-4">
            <div class="p-3.5 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200/80 dark:border-zinc-800 rounded-xl space-y-1">
                <h4 class="font-bold text-xs text-zinc-900 dark:text-white flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    Step 3: Shoot Scope & Specific Notes
                </h4>
                <p class="text-[11px] text-zinc-500">Add project deliverable details, shoot location, and intake requirements.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Scope / Property Type</label>
                    <input type="text" id="cora-new-lead-scale" class="w-full px-3 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none focus:border-zinc-900 transition-colors" placeholder="e.g. Commercial Villa / Studio Shoot">
                </div>
                <div>
                    <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">City / Location</label>
                    <input type="text" id="cora-new-lead-city" class="w-full px-3 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none focus:border-zinc-900 transition-colors" placeholder="e.g. Mumbai, BKC">
                </div>
            </div>

            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Inquiry Notes & Intake Requirements</label>
                <textarea id="cora-new-lead-notes" rows="4" class="w-full px-3 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-900 dark:text-white font-medium focus:outline-none focus:border-zinc-900 transition-colors resize-none" placeholder="Add any background notes, client preferences, or specific deliverables..."></textarea>
            </div>

            <div class="pt-6 flex items-center justify-between border-t border-zinc-200 dark:border-zinc-800">
                <button type="button" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-semibold rounded-xl text-xs cursor-pointer hover:bg-zinc-200 transition-colors flex items-center gap-1.5" onclick="coraGoToCreateLeadStep(2)">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <span>Back</span>
                </button>
                <button type="submit" class="px-6 py-2.5 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold rounded-xl text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all cursor-pointer shadow-sm flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Create Lead Inquiry</span>
                </button>
            </div>
        </div>
    </form>
</aside>

<!-- ========================================================================= -->
<!-- SLIDING SIDE DRAWER 3: SCHEDULE FOLLOW-UP TASK                            -->
<!-- ========================================================================= -->
<aside id="cora-lead-schedule-drawer" class="cora-side-drawer hidden collapsed fixed top-0 right-0 w-full sm:w-[480px] max-w-full sm:max-w-md h-full bg-white dark:bg-zinc-900 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 dark:border-zinc-800 flex flex-col font-sans overflow-hidden">
    <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-zinc-50/50 dark:bg-zinc-850/50">
        <div>
            <h3 class="font-extrabold text-base text-zinc-900 dark:text-white">Schedule Follow-Up Action</h3>
            <p class="text-xs text-zinc-400 mt-0.5">Set reminders or crew tasks for lead nurturing.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-800 dark:hover:text-white p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer" onclick="window.coraCloseAllDrawers()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form class="p-6 overflow-y-auto flex-1 space-y-4 text-xs" onsubmit="event.preventDefault(); coraSubmitScheduleTask();">
        <input type="hidden" id="cora-task-lead-id" value="">

        <div>
            <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Follow-Up Action Type</label>
            <select id="cora-task-action" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none">
                <option value="call">Phone Call Nurture</option>
                <option value="proposal">Send Proposal / Estimate</option>
                <option value="viewing">Site Visit / Viewing</option>
                <option value="whatsapp">WhatsApp Quick Ping</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Target Date</label>
                <input type="date" id="cora-task-date" required class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Target Time</label>
                <input type="time" id="cora-task-time" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none" value="11:00">
            </div>
        </div>

        <div>
            <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Assigned Team Member</label>
            <select id="cora-task-assignee" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none">
                <option value="me">Assigned to Me</option>
                <?php foreach ($cora_users_list as $u) : ?>
                    <option value="<?php echo esc_attr($u->ID); ?>"><?php echo esc_html($u->display_name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block font-bold text-zinc-700 dark:text-zinc-300 mb-1">Reminder Notes</label>
            <textarea id="cora-task-note" rows="3" class="w-full px-3 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-white focus:outline-none resize-none" placeholder="Details for follow up..."></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800">
            <button type="button" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-semibold rounded-lg text-xs cursor-pointer" onclick="window.coraCloseAllDrawers()">
                Cancel
            </button>
            <button type="submit" class="px-5 py-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold rounded-lg text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all cursor-pointer shadow-sm">
                Save Task
            </button>
        </div>
    </form>
</aside>

<!-- ========================================================================= -->
<!-- SLIDING SIDE DRAWER 4: CUSTOMIZE PIPELINE STAGES & COLUMNS                 -->
<!-- ========================================================================= -->
<aside id="cora-lead-stages-drawer" class="cora-side-drawer hidden collapsed fixed top-0 right-0 w-full sm:w-[540px] max-w-full sm:max-w-lg h-full bg-white dark:bg-zinc-900 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200/80 dark:border-zinc-800 flex flex-col font-sans select-none overflow-hidden">
    <!-- Header Bar -->
    <div class="p-4 px-5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-white dark:bg-zinc-900">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white flex items-center justify-center shrink-0 border border-zinc-200/60 dark:border-zinc-700/60 shadow-2xs">
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
            </div>
            <div>
                <h3 class="font-extrabold text-sm text-zinc-950 dark:text-white tracking-tight">Customize Pipeline Columns</h3>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Reorder, rename, or toggle visibility of stage workflow columns.</p>
            </div>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-800 dark:hover:text-white p-1.5 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer" onclick="window.coraCloseAllDrawers()">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Main Content Form -->
    <form id="cora-manage-stages-form" class="p-5 overflow-y-auto flex-1 space-y-3 text-xs" onsubmit="event.preventDefault(); coraSavePipelineStages();">
        <div class="flex items-center justify-between pb-2.5 mb-1 border-b border-zinc-200/80 dark:border-zinc-800">
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-black uppercase tracking-wider text-zinc-800 dark:text-zinc-200">Pipeline Stage Workflow</span>
                <span class="px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-[10px] font-bold text-zinc-600 dark:text-zinc-400 border border-zinc-200/60 dark:border-zinc-700/60 whitespace-nowrap" id="cora-stage-count-badge"><?php echo count($stages_config); ?> Stages</span>
            </div>
            <button type="button" class="px-3 py-1.5 bg-zinc-950 text-white dark:bg-white dark:text-zinc-950 font-bold rounded-xl text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all flex items-center gap-1.5 cursor-pointer shadow-2xs whitespace-nowrap" onclick="coraAddNewStageRow()">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add Column
            </button>
        </div>

        <div id="cora-stages-list-container" class="space-y-2.5">
            <?php foreach ( $stages_config as $s_key => $s_val ) : ?>
            <div class="cora-stage-config-row p-3 rounded-2xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-all flex items-center justify-between gap-3 relative group min-h-[52px]"
                 draggable="true"
                 data-key="<?php echo esc_attr($s_key); ?>"
                 ondragstart="coraStageRowDragStart(event)"
                 ondragover="coraStageRowDragOver(event)"
                 ondrop="coraStageRowDrop(event)"
                 ondragend="coraStageRowDragEnd(event)">
                
                <!-- Left: Grip + Title Input -->
                <div class="flex items-center gap-2.5 flex-1 min-w-0">
                    <div class="w-7 h-7 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 flex items-center justify-center cursor-grab active:cursor-grabbing shrink-0 select-none transition-colors border border-zinc-200/60 dark:border-zinc-700/60" title="Drag to reorder">
                        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><circle cx="9" cy="6" r="1.5"></circle><circle cx="15" cy="6" r="1.5"></circle><circle cx="9" cy="12" r="1.5"></circle><circle cx="15" cy="12" r="1.5"></circle><circle cx="9" cy="18" r="1.5"></circle><circle cx="15" cy="18" r="1.5"></circle></svg>
                    </div>
                    <input type="text" class="cora-stage-label-input px-3 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-zinc-900 dark:focus:border-zinc-100 rounded-xl font-bold text-zinc-900 dark:text-zinc-100 text-xs flex-1 min-w-0 outline-none transition-all" value="<?php echo esc_attr($s_val['label'] ?? $s_key); ?>" placeholder="Stage Title">
                </div>

                <!-- Right: Color Swatch + Toggle Switch + Delete -->
                <div class="flex items-center gap-2.5 shrink-0">
                    <select class="cora-stage-bg-select hidden">
                        <option value="default" selected>Default Gray</option>
                    </select>

                    <?php
                    /* Map saved badge class → hex for the native color input */
                    $badge_val = $s_val['badge'] ?? '';
                    $swatch_palette = [
                        'emerald' => '#22c55e', 'amber'   => '#f59e0b', 'blue'    => '#3b82f6',
                        'violet'  => '#8b5cf6', 'pink'    => '#ec4899', 'rose'    => '#f43f5e',
                        'sky'     => '#0ea5e9', 'indigo'  => '#6366f1', 'purple'  => '#a855f7',
                        'orange'  => '#f97316', 'teal'    => '#14b8a6', 'lime'    => '#84cc16',
                        'red'     => '#ef4444', 'cyan'    => '#06b6d4', 'fuchsia' => '#d946ef',
                        'zinc'    => '#71717a'
                    ];
                    $swatch_hex = '#71717a';
                    foreach ($swatch_palette as $name => $hex) {
                        if (strpos($badge_val, $name) !== false) { $swatch_hex = $hex; break; }
                    }
                    $picker_id = 'cora-color-input-' . sanitize_key($s_key);
                    ?>
                    <div class="cora-color-picker-container flex items-center shrink-0">
                        <!-- Hidden badge select — kept for save handler compatibility -->
                        <select class="cora-stage-badge-select hidden" onchange="coraUpdateStageBadgePreview(this)">
                            <option value="bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800" <?php echo (strpos($badge_val,'emerald')!==false)?'selected':'';?>>Emerald</option>
                            <option value="bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800" <?php echo (strpos($badge_val,'amber')!==false)?'selected':'';?>>Amber</option>
                            <option value="bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800" <?php echo (strpos($badge_val,'blue')!==false && strpos($badge_val,'sky')===false)?'selected':'';?>>Blue</option>
                            <option value="bg-violet-500/10 text-violet-600 dark:text-violet-400 border-violet-200 dark:border-violet-800" <?php echo (strpos($badge_val,'violet')!==false)?'selected':'';?>>Violet</option>
                            <option value="bg-pink-500/10 text-pink-600 dark:text-pink-400 border-pink-200 dark:border-pink-800" <?php echo (strpos($badge_val,'pink')!==false)?'selected':'';?>>Pink</option>
                            <option value="bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-200 dark:border-rose-800" <?php echo (strpos($badge_val,'rose')!==false)?'selected':'';?>>Rose</option>
                            <option value="bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-200 dark:border-sky-800" <?php echo (strpos($badge_val,'sky')!==false)?'selected':'';?>>Sky</option>
                            <option value="bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-800" <?php echo (strpos($badge_val,'indigo')!==false)?'selected':'';?>>Indigo</option>
                            <option value="bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800" <?php echo (strpos($badge_val,'purple')!==false)?'selected':'';?>>Purple</option>
                            <option value="bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-200 dark:border-orange-800" <?php echo (strpos($badge_val,'orange')!==false)?'selected':'';?>>Orange</option>
                            <option value="bg-teal-500/10 text-teal-600 dark:text-teal-400 border-teal-200 dark:border-teal-800" <?php echo (strpos($badge_val,'teal')!==false)?'selected':'';?>>Teal</option>
                            <option value="bg-lime-500/10 text-lime-600 dark:text-lime-400 border-lime-200 dark:border-lime-800" <?php echo (strpos($badge_val,'lime')!==false)?'selected':'';?>>Lime</option>
                            <option value="bg-red-500/10 text-red-600 dark:text-red-400 border-red-200 dark:border-red-800" <?php echo (strpos($badge_val,'red')!==false)?'selected':'';?>>Red</option>
                            <option value="bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border-cyan-200 dark:border-cyan-800" <?php echo (strpos($badge_val,'cyan')!==false)?'selected':'';?>>Cyan</option>
                            <option value="bg-fuchsia-500/10 text-fuchsia-600 dark:text-fuchsia-400 border-fuchsia-200 dark:border-fuchsia-800" <?php echo (strpos($badge_val,'fuchsia')!==false)?'selected':'';?>>Fuchsia</option>
                            <option value="bg-zinc-500/10 text-zinc-600 dark:text-zinc-400 border-zinc-200 dark:border-zinc-800" <?php echo (strpos($badge_val,'zinc')!==false)?'selected':'';?>>Zinc</option>
                        </select>

                        <!-- Native colour picker: hidden input + styled round swatch label -->
                        <label for="<?php echo esc_attr($picker_id); ?>" class="cora-stage-color-swatch-label" title="Choose stage colour" style="display:flex;align-items:center;cursor:pointer;">
                            <!-- The visible round swatch -->
                            <span class="cora-stage-color-swatch" style="
                                display:block;
                                width:26px;height:26px;
                                border-radius:50%;
                                background:<?php echo esc_attr($swatch_hex); ?>;
                                border:2px solid rgba(0,0,0,0.10);
                                box-shadow:0 1px 3px rgba(0,0,0,0.10),inset 0 0 0 1.5px rgba(255,255,255,0.18);
                                transition:transform 0.12s,box-shadow 0.12s;
                                flex-shrink:0;
                            "
                            onmouseenter="this.style.transform='scale(1.12)';this.style.boxShadow='0 3px 8px rgba(0,0,0,0.18)'"
                            onmouseleave="this.style.transform='scale(1)';this.style.boxShadow='0 1px 3px rgba(0,0,0,0.10),inset 0 0 0 1.5px rgba(255,255,255,0.18)'"
                            ></span>
                            <!-- The native colour picker — visually zero-size, clicks proxy from label -->
                            <input
                                type="color"
                                id="<?php echo esc_attr($picker_id); ?>"
                                class="cora-stage-native-color-input"
                                value="<?php echo esc_attr($swatch_hex); ?>"
                                oninput="coraStageColorChange(this)"
                                style="width:0;height:0;padding:0;border:0;opacity:0;position:absolute;"
                            >
                        </label>
                    </div>

                    <!-- Monochromatic Toggle Switch -->
                    <label class="inline-flex items-center gap-1.5 cursor-pointer select-none shrink-0" title="Toggle column visibility">
                        <input type="checkbox" class="cora-stage-enable-checkbox cora-toggle-checkbox sr-only" <?php echo ( ! isset($s_val['enabled']) || $s_val['enabled'] ) ? 'checked' : ''; ?> onchange="coraUpdateStageToggleLabel(this)">
                        <span class="cora-toggle-slider"></span>
                        <span class="cora-toggle-text text-[11px] font-bold text-zinc-600 dark:text-zinc-400 select-none w-8 text-left"><?php echo ( ! isset($s_val['enabled']) || $s_val['enabled'] ) ? 'Show' : 'Hide'; ?></span>
                    </label>

                    <button type="button" class="w-7 h-7 rounded-xl text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-transparent transition-all flex items-center justify-center cursor-pointer shrink-0" onclick="coraRemoveStageRow(this)" title="Delete stage">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </form>

    <!-- Footer Action Bar -->
    <div class="p-4 px-5 border-t border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between shrink-0 shadow-lg">
        <button type="button" class="px-3.5 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold rounded-xl text-xs transition-all flex items-center gap-1.5 cursor-pointer whitespace-nowrap" onclick="coraResetDefaultStages()">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M23 4v6h-6"></path><path d="M1 20v-6h6"></path><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
            Reset to Default
        </button>
        <div class="flex items-center gap-2">
            <button type="button" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-semibold rounded-xl text-xs cursor-pointer transition-all whitespace-nowrap" onclick="window.coraCloseAllDrawers()">
                Cancel
            </button>
            <button type="button" onclick="coraSavePipelineStages()" class="px-4 py-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold rounded-xl text-xs hover:bg-zinc-800 dark:hover:bg-zinc-200 transition-all cursor-pointer shadow-sm whitespace-nowrap">
                Save Pipeline Columns
            </button>
        </div>
    </div>
</aside>

<script>
window.coraToggleDrawerAccordion = function(btn) {
    if (!btn) return;
    var card = btn.closest('.cora-accordion-card');
    if (!card) return;
    var body = card.querySelector('.cora-accordion-body');
    var icon = card.querySelector('.cora-accordion-icon');
    if (!body) return;
    if (body.classList.contains('hidden')) {
        body.classList.remove('hidden');
        if (icon) icon.classList.add('rotate-180');
    } else {
        body.classList.add('hidden');
        if (icon) icon.classList.remove('rotate-180');
    }
};

window.coraSwitchDirectoryViewMode = function(mode) {
    var gridContainer = document.getElementById('cora-directory-grid-container');
    var tableContainer = document.getElementById('cora-directory-table-container');
    var gridBtn = document.getElementById('cora-dir-view-btn-grid');
    var tableBtn = document.getElementById('cora-dir-view-btn-table');
    var colPicker = document.getElementById('cora-dir-col-picker');
    if (!gridContainer || !tableContainer) return;

    if (mode === 'grid') {
        gridContainer.classList.remove('hidden');
        tableContainer.classList.add('hidden');
        if (colPicker) colPicker.classList.remove('hidden');
        if (gridBtn) {
            gridBtn.className = "px-3 py-1.5 rounded-lg transition-all cursor-pointer bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white font-extrabold shadow-2xs flex items-center gap-1.5";
        }
        if (tableBtn) {
            tableBtn.className = "px-3 py-1.5 rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 dark:hover:text-white flex items-center gap-1.5";
        }
        // Restore saved column preference
        var savedCols = parseInt(localStorage.getItem('cora_grid_cols') || '3');
        window.coraSetGridColumns(savedCols, true);
    } else {
        gridContainer.classList.add('hidden');
        tableContainer.classList.remove('hidden');
        if (colPicker) colPicker.classList.add('hidden');
        if (tableBtn) {
            tableBtn.className = "px-3 py-1.5 rounded-lg transition-all cursor-pointer bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white font-extrabold shadow-2xs flex items-center gap-1.5";
        }
        if (gridBtn) {
            gridBtn.className = "px-3 py-1.5 rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 dark:hover:text-white flex items-center gap-1.5";
        }
    }
};

// Column density controller — max 3 columns, persists preference to localStorage
window.coraSetGridColumns = function(cols, silent) {
    var grid = document.getElementById('cora-directory-grid-container');
    if (!grid) return;

    // Clamp to valid range 1–3
    cols = Math.min(3, Math.max(1, parseInt(cols) || 3));

    // Remove all column classes then apply the chosen one
    grid.classList.remove(
        'grid-cols-1', 'grid-cols-2', 'grid-cols-3',
        'md:grid-cols-1', 'md:grid-cols-2', 'md:grid-cols-3',
        'lg:grid-cols-1', 'lg:grid-cols-2', 'lg:grid-cols-3'
    );

    var colMap = {
        1: ['grid-cols-1'],
        2: ['grid-cols-1', 'md:grid-cols-2'],
        3: ['grid-cols-1', 'md:grid-cols-2', 'lg:grid-cols-3']
    };
    (colMap[cols] || colMap[3]).forEach(function(c) { grid.classList.add(c); });

    // Update active state on col picker buttons
    [1, 2, 3].forEach(function(n) {
        var btn = document.getElementById('cora-dir-col-btn-' + n);
        if (!btn) return;
        if (n === cols) {
            btn.className = "w-7 h-7 rounded-lg transition-all cursor-pointer bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white font-extrabold shadow-2xs flex items-center justify-center text-[11px]";
        } else {
            btn.className = "w-7 h-7 rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 dark:hover:text-white font-bold text-[11px] flex items-center justify-center";
        }
    });

    // Persist to localStorage unless this is a silent restore
    if (!silent) {
        try { localStorage.setItem('cora_grid_cols', cols); } catch(e) {}
    }
};

jQuery(document).ready(function($) {
    const urlParams = new URLSearchParams(window.location.search);
    let subtab = urlParams.get('subtab');
    if (!subtab && window.innerWidth < 768) {
        if (typeof window.coraSwitchLeadSubtab === 'function') {
            window.coraSwitchLeadSubtab('directory');
        }
    }

    // Restore saved column preference on page load (grid is default view)
    var savedCols = parseInt(localStorage.getItem('cora_grid_cols') || '3');
    if (typeof window.coraSetGridColumns === 'function') {
        window.coraSetGridColumns(savedCols, true);
    }
});
</script>
