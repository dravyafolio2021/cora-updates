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
    const activeClasses = 'active bg-white text-zinc-950 shadow-2xs font-bold border border-zinc-200/80 ';
    const inactiveClasses = 'text-zinc-500 hover:text-zinc-900 font-medium hover:bg-zinc-200/50 ';
    const classesToRemove = 'active bg-white text-zinc-950 shadow-2xs font-bold border border-zinc-200/80 bg-zinc-950 text-white shadow-sm font-semibold text-zinc-500 hover:text-zinc-900 font-medium hover:bg-zinc-200/50 text-zinc-600 hover:bg-zinc-100 ';

    if (window.jQuery) {
        jQuery('.cora-lead-subtab-btn').removeClass(classesToRemove).addClass(inactiveClasses);
        jQuery(`.cora-lead-subtab-btn[data-tab="${tabName}"]`).removeClass(classesToRemove).addClass(activeClasses);
        jQuery('.cora-lead-tab-pane').addClass('hidden');
        jQuery(`#cora-lead-pane-${tabName}`).removeClass('hidden');
    } else {
        document.querySelectorAll('.cora-lead-subtab-btn').forEach(b => {
            const isTarget = b.getAttribute('data-tab') === tabName;
            b.classList.remove('active', 'bg-white', 'text-zinc-950', 'shadow-2xs', 'font-bold', 'border', 'border-zinc-200/80');
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
            url.searchParams.set('subtab', tabName);
            url.searchParams.delete('sub_page'); // Never inject or pollute sub_page
            window.history.replaceState(null, '', url);
        } catch(e){}
    }
};

// Simple & Controlled Filter & Sort State
window.coraToggleLeadFilterPopover = function(e) {
    if (e && e.stopPropagation) e.stopPropagation();
    const pop = document.getElementById('cora-lead-filter-popover');
    if (!pop) return;
    document.querySelectorAll('.cora-col-context-menu').forEach(m => m.classList.add('hidden'));
    pop.classList.toggle('hidden');
};

window.coraApplySelectFilters = function() {
    const stageVal = (document.getElementById('cora-filter-stage') || {}).value || 'all';
    const scoreVal = (document.getElementById('cora-filter-score') || {}).value || 'all';
    const assigneeVal = (document.getElementById('cora-filter-assignee') || {}).value || 'all';
    const sortVal = (document.getElementById('cora-filter-sort') || {}).value || 'default';

    let activeCount = 0;
    if (stageVal !== 'all') activeCount++;
    if (scoreVal !== 'all') activeCount++;
    if (assigneeVal !== 'all') activeCount++;
    if (sortVal !== 'default') activeCount++;

    const badge = document.getElementById('cora-lead-filter-badge');
    if (badge) {
        badge.textContent = activeCount;
        if (activeCount > 0) {
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    if (sortVal !== 'default') {
        document.querySelectorAll('.cora-kanban-column').forEach(col => {
            const container = col.querySelector('.cora-cards-container');
            if (container) window.coraSortCardsInContainer(container, sortVal);
        });
        const dirGrid = document.getElementById('cora-directory-grid-container');
        if (dirGrid) window.coraSortCardsInContainer(dirGrid, sortVal);
    }

    window.coraFilterLeadsList();
};

window.coraResetLeadFilters = function() {
    const stageEl = document.getElementById('cora-filter-stage');
    const scoreEl = document.getElementById('cora-filter-score');
    const assigneeEl = document.getElementById('cora-filter-assignee');
    const sortEl = document.getElementById('cora-filter-sort');
    const searchInput = document.getElementById('cora-lead-search-input');

    if (stageEl) stageEl.value = 'all';
    if (scoreEl) scoreEl.value = 'all';
    if (assigneeEl) assigneeEl.value = 'all';
    if (sortEl) sortEl.value = 'default';
    if (searchInput) searchInput.value = '';

    const badge = document.getElementById('cora-lead-filter-badge');
    if (badge) {
        badge.textContent = '0';
        badge.classList.add('hidden');
    }

    document.querySelectorAll('.cora-kanban-column').forEach(col => {
        const container = col.querySelector('.cora-cards-container');
        if (container) window.coraSortCardsInContainer(container, 'default');
    });
    const dirGrid = document.getElementById('cora-directory-grid-container');
    if (dirGrid) window.coraSortCardsInContainer(dirGrid, 'default');

    window.coraFilterLeadsList();
};

window.coraSortCardsInContainer = function(container, sortType) {
    if (!container) return;
    const cards = Array.from(container.querySelectorAll('.cora-lead-card'));
    if (cards.length === 0) return;

    cards.forEach((c, idx) => {
        if (!c.hasAttribute('data-orig-idx')) {
            c.setAttribute('data-orig-idx', idx);
        }
    });

    const scoreWeights = { 'hot': 3, 'warm': 2, 'cold': 1 };

    cards.sort((a, b) => {
        if (sortType === 'value-desc' || sortType === 'value-asc') {
            const valA = parseFloat((a.getAttribute('data-price') || '0').replace(/[^0-9.]/g, '')) || 0;
            const valB = parseFloat((b.getAttribute('data-price') || '0').replace(/[^0-9.]/g, '')) || 0;
            return sortType === 'value-desc' ? (valB - valA) : (valA - valB);
        } else if (sortType === 'hot-first') {
            const scoreA = (a.getAttribute('data-score') || 'warm').toLowerCase();
            const scoreB = (b.getAttribute('data-score') || 'warm').toLowerCase();
            const weightA = scoreWeights[scoreA] || 2;
            const weightB = scoreWeights[scoreB] || 2;
            return weightB - weightA;
        } else if (sortType === 'name-asc') {
            const nameA = (a.getAttribute('data-name') || '').toLowerCase();
            const nameB = (b.getAttribute('data-name') || '').toLowerCase();
            return nameA.localeCompare(nameB);
        } else if (sortType === 'default') {
            const idxA = parseInt(a.getAttribute('data-orig-idx') || '0');
            const idxB = parseInt(b.getAttribute('data-orig-idx') || '0');
            return idxA - idxB;
        }
        return 0;
    });

    cards.forEach(c => container.appendChild(c));
};

// Column Context Menu & Dynamic Sorting
window.coraToggleColumnMenu = function(btn, e) {
    if (e && e.stopPropagation) e.stopPropagation();
    const col = btn.closest('.cora-kanban-column');
    if (!col) return;
    const menu = col.querySelector('.cora-col-context-menu');
    if (!menu) return;

    document.querySelectorAll('.cora-col-context-menu').forEach(m => {
        if (m !== menu) m.classList.add('hidden');
    });
    const filterPop = document.getElementById('cora-lead-filter-popover');
    if (filterPop) filterPop.classList.add('hidden');

    menu.classList.toggle('hidden');
};

window.coraSortKanbanColumn = function(btn, sortType) {
    const col = btn.closest('.cora-kanban-column');
    if (!col) return;
    const menu = col.querySelector('.cora-col-context-menu');
    if (menu) menu.classList.add('hidden');

    const container = col.querySelector('.cora-cards-container');
    if (!container) return;

    window.coraSortCardsInContainer(container, sortType);

    if (window.coraShowToast) {
        const labelMap = {
            'value-desc': 'Sorted: ₹ High → Low',
            'value-asc': 'Sorted: ₹ Low → High',
            'hot-first': 'Sorted: Hot Leads First',
            'name-asc': 'Sorted: Name A → Z',
            'default': 'Restored default column order'
        };
        window.coraShowToast(labelMap[sortType] || 'Column sorted', 'success');
    }
};

// Global Leads Filtering Across Kanban, Grid, and Table Views
window.coraFilterLeadsList = function() {
    const searchInput = document.getElementById('cora-lead-search-input');
    const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
    
    const stageVal = (document.getElementById('cora-filter-stage') || {}).value || 'all';
    const scoreVal = (document.getElementById('cora-filter-score') || {}).value || 'all';
    const assigneeVal = (document.getElementById('cora-filter-assignee') || {}).value || 'all';

    function checkMatch(name, email, phone, city, status, score, assignedTo, extraText) {
        const fullText = `${name} ${email} ${phone} ${city} ${extraText || ''}`.toLowerCase();
        const matchesQuery = !query || fullText.includes(query);
        const matchesStage = stageVal === 'all' || (status || '').toLowerCase() === stageVal.toLowerCase();
        
        let matchesScore = scoreVal === 'all';
        if (!matchesScore) {
            if (scoreVal === 'won') {
                matchesScore = (status || '').toLowerCase() === 'converted' || (status || '').toLowerCase() === 'won';
            } else {
                matchesScore = (score || '').toLowerCase() === scoreVal.toLowerCase();
            }
        }

        const matchesAssignee = assigneeVal === 'all' || (assignedTo || '').toString() === assigneeVal.toString();
        return matchesQuery && matchesStage && matchesScore && matchesAssignee;
    }

    // 1. Kanban Cards
    let visibleKanbanCards = 0;
    document.querySelectorAll('.cora-kanban-column .cora-lead-card').forEach(card => {
        const name = (card.getAttribute('data-name') || '').toLowerCase();
        const email = (card.getAttribute('data-email') || '').toLowerCase();
        const phone = (card.getAttribute('data-phone') || '').toLowerCase();
        const city = (card.getAttribute('data-city') || '').toLowerCase();
        const status = (card.getAttribute('data-status') || '').toLowerCase();
        const score = (card.getAttribute('data-score') || '').toLowerCase();
        const assignedTo = (card.getAttribute('data-assigned-to') || '').toString();

        if (checkMatch(name, email, phone, city, status, score, assignedTo, card.textContent)) {
            card.classList.remove('hidden');
            visibleKanbanCards++;
        } else {
            card.classList.add('hidden');
        }
    });
    if (typeof window.coraUpdateColumnCounters === 'function') {
        window.coraUpdateColumnCounters();
    }

    // 2. Directory Grid Cards
    let visibleGridCards = 0;
    document.querySelectorAll('#cora-directory-grid-container .cora-lead-card').forEach(card => {
        const name = (card.getAttribute('data-name') || '').toLowerCase();
        const email = (card.getAttribute('data-email') || '').toLowerCase();
        const phone = (card.getAttribute('data-phone') || '').toLowerCase();
        const city = (card.getAttribute('data-city') || '').toLowerCase();
        const status = (card.getAttribute('data-status') || '').toLowerCase();
        const score = (card.getAttribute('data-score') || '').toLowerCase();
        const assignedTo = (card.getAttribute('data-assigned-to') || '').toString();

        if (checkMatch(name, email, phone, city, status, score, assignedTo, card.textContent)) {
            card.classList.remove('hidden');
            visibleGridCards++;
        } else {
            card.classList.add('hidden');
        }
    });

    const gridEmpty = document.getElementById('cora-grid-empty-state');
    if (gridEmpty) {
        if (visibleGridCards === 0 && document.querySelectorAll('#cora-directory-grid-container .cora-lead-card').length > 0) {
            gridEmpty.classList.remove('hidden');
        } else {
            gridEmpty.classList.add('hidden');
        }
    }

    // 3. Directory Table Rows
    let visibleTableRows = 0;
    document.querySelectorAll('#cora-leads-table-body tr:not(#cora-table-empty-state)').forEach(row => {
        const name = (row.getAttribute('data-name') || '').toLowerCase();
        const email = (row.getAttribute('data-email') || '').toLowerCase();
        const status = (row.getAttribute('data-status') || '').toLowerCase();
        const score = (row.getAttribute('data-score') || '').toLowerCase();
        const assignedTo = (row.getAttribute('data-assigned-to') || '').toString();

        if (checkMatch(name, email, '', '', status, score, assignedTo, row.textContent)) {
            row.classList.remove('hidden');
            visibleTableRows++;
        } else {
            row.classList.add('hidden');
        }
    });

    const tableEmpty = document.getElementById('cora-table-empty-state');
    if (tableEmpty) {
        if (visibleTableRows === 0 && document.querySelectorAll('#cora-leads-table-body tr:not(#cora-table-empty-state)').length > 0) {
            tableEmpty.classList.remove('hidden');
        } else {
            tableEmpty.classList.add('hidden');
        }
    }
};

// Global click-outside listener for popovers
document.addEventListener('click', function(e) {
    const filterWrapper = document.getElementById('cora-lead-filters-wrapper');
    const filterPop = document.getElementById('cora-lead-filter-popover');
    if (filterPop && !filterPop.classList.contains('hidden')) {
        if (filterWrapper && !filterWrapper.contains(e.target)) {
            filterPop.classList.add('hidden');
        }
    }

    if (!e.target.closest('.cora-col-context-menu') && !e.target.closest('.cora-col-menu-trigger')) {
        document.querySelectorAll('.cora-col-context-menu').forEach(m => m.classList.add('hidden'));
    }
});

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
            (card.getAttribute('data-notes') || '') + ' ' +
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
            noResults.className = 'cora-col-no-results flex flex-col items-center justify-center p-4 my-2 border border-dashed border-zinc-200 dark:border-zinc-700 rounded-2xl text-center select-none bg-white/40 dark:bg-zinc-800/40';
            noResults.innerHTML = `
                <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400 mb-1"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <span class="text-[11px] font-bold text-zinc-600 dark:text-zinc-300">No leads match "${query}"</span>
                <button type="button" class="text-[10px] text-zinc-900 dark:text-zinc-100 underline mt-1 font-semibold cursor-pointer" onclick="coraClearColumnSearch(this)">Clear search</button>
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

    if (typeof window.coraUpdateColumnCounters === 'function') {
        window.coraUpdateColumnCounters();
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

window.coraUpdateColumnCounters = function() {
    let globalVisibleCount = 0;
    let globalVisibleSum = 0;

    document.querySelectorAll('.cora-kanban-column').forEach(function(col) {
        const totalCards = col.querySelectorAll('.cora-lead-card');
        const visibleCards = col.querySelectorAll('.cora-lead-card:not(.hidden)');
        const countEl = col.querySelector('.col-count');
        if (countEl) {
            countEl.textContent = visibleCards.length;
        }

        let colSum = 0;
        visibleCards.forEach(function(c) {
            const p = parseFloat((c.getAttribute('data-price') || '0').replace(/[^0-9.]/g, '')) || 0;
            colSum += p;
        });

        globalVisibleCount += visibleCards.length;
        globalVisibleSum += colSum;

        const valEl = col.querySelector('.cora-col-pipeline-val');
        if (valEl) {
            valEl.textContent = '₹' + Math.round(colSum).toLocaleString('en-IN');
        }
    });

    // Update global pipeline stats pill if present
    const topSumEl = document.getElementById('cora-crm-live-pipeline-sum');
    if (topSumEl) {
        topSumEl.textContent = '₹' + Math.round(globalVisibleSum).toLocaleString('en-IN');
    }
    const topCountEl = document.getElementById('cora-crm-live-inquiries-count');
    if (topCountEl) {
        topCountEl.textContent = globalVisibleCount;
    }
    const tabCountEl = document.getElementById('cora-kanban-tab-count');
    if (tabCountEl) {
        tabCountEl.textContent = globalVisibleCount;
    }

    const dirBadge = document.getElementById('cora-directory-total-badge');
    if (dirBadge) {
        const visibleGrid = document.querySelectorAll('#cora-directory-grid-container .cora-lead-card:not(.hidden)').length;
        const totalGrid = document.querySelectorAll('#cora-directory-grid-container .cora-lead-card').length;
        dirBadge.textContent = visibleGrid + (visibleGrid === 1 ? ' Lead' : ' Leads');
    }
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
        tab.classList.remove('bg-white', 'text-zinc-950', 'font-bold', 'shadow-xs');
        tab.classList.add('text-zinc-500', 'hover:text-zinc-900');
    });
    
    const activeTab = document.getElementById(`cora-lead-detail-tab-btn-${tabName}`);
    if (activeTab) {
        activeTab.classList.add('bg-white', 'text-zinc-950', 'font-bold', 'shadow-xs');
        activeTab.classList.remove('text-zinc-500', 'hover:text-zinc-900');
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
        ['#22c55e','bg-emerald-500/10 text-emerald-600 border-emerald-200 '],
        ['#f59e0b','bg-amber-500/10 text-amber-600 border-amber-200 '],
        ['#3b82f6','bg-blue-500/10 text-blue-600 border-blue-200 '],
        ['#8b5cf6','bg-violet-500/10 text-violet-600 border-violet-200 '],
        ['#ec4899','bg-pink-500/10 text-pink-600 border-pink-200 '],
        ['#f43f5e','bg-rose-500/10 text-rose-600 border-rose-200 '],
        ['#0ea5e9','bg-sky-500/10 text-sky-600 border-sky-200 '],
        ['#6366f1','bg-indigo-500/10 text-indigo-600 border-indigo-200 '],
        ['#a855f7','bg-purple-500/10 text-purple-600 border-purple-200 '],
        ['#f97316','bg-orange-500/10 text-orange-600 border-orange-200 '],
        ['#14b8a6','bg-teal-500/10 text-teal-600 border-teal-200 '],
        ['#84cc16','bg-lime-500/10 text-lime-600 border-lime-200 '],
        ['#ef4444','bg-red-500/10 text-red-600 border-red-200 '],
        ['#06b6d4','bg-cyan-500/10 text-cyan-600 border-cyan-200 '],
        ['#d946ef','bg-fuchsia-500/10 text-fuchsia-600 border-fuchsia-200 '],
        ['#71717a','bg-zinc-500/10 text-zinc-600 border-zinc-200 ']
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

window.coraRemoveStageRow = function(btn) {
    var row = btn.closest('.cora-stage-config-row');
    if (row) row.remove();
    var count = document.querySelectorAll('#cora-stages-list-container .cora-stage-config-row').length;
    var badge = document.getElementById('cora-stage-count-badge');
    if (badge) badge.textContent = count + (count === 1 ? ' Stage' : ' Stages');
};

// Kept for backward compat — unused but referenced by old HTML
window.coraSelectStageColor = function() {};
window.coraCycleStageColor  = function() {};
window.coraOpenStagePicker  = function() {};

window.coraAddLeadChecklistItem = function() {
    var input = document.getElementById('cora-new-checklist-input');
    if (!input) return;
    var val = (input.value || '').trim();
    if (!val) {
        if (window.coraShowToast) window.coraShowToast('Please enter a task description', 'error');
        return;
    }
    var container = document.getElementById('cora-lead-checklist-container');
    if (container) {
        var item = document.createElement('label');
        item.className = 'flex items-center justify-between p-2.5 rounded-xl border border-zinc-200 bg-white hover:border-zinc-300 transition-all cursor-pointer';
        item.innerHTML = '<div class="flex items-center gap-2.5 min-w-0">' +
            '<input type="checkbox" class="w-4 h-4 text-emerald-600 rounded border-zinc-300 focus:ring-emerald-500" onchange="this.nextElementSibling.classList.toggle(\'line-through\', this.checked); var b = this.closest(\'label\').querySelector(\'.cora-chk-badge\'); if(b){ b.textContent = this.checked ? \'Done\' : \'Pending\'; b.className = this.checked ? \'cora-chk-badge text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-200\' : \'cora-chk-badge text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 border border-amber-200\'; }">' +
            '<span class="text-xs font-semibold text-zinc-800 truncate">' + val.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</span>' +
            '</div>' +
            '<span class="cora-chk-badge text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 border border-amber-200">Pending</span>';
        container.appendChild(item);
    }
    input.value = '';
    if (window.coraShowToast) window.coraShowToast('Intake task added successfully', 'success');
};

window.coraAddLeadAuditLogNote = function() {
    var textarea = document.getElementById('cora-audit-note-input');
    if (!textarea) return;
    var note = (textarea.value || '').trim();
    if (!note) {
        if (window.coraShowToast) window.coraShowToast('Please enter a call note or summary', 'error');
        return;
    }
    var timeline = document.getElementById('cora-lead-audit-timeline');
    if (timeline) {
        var noteEl = document.createElement('div');
        noteEl.className = 'relative pl-6 pb-4 border-l-2 border-zinc-200 ml-3';
        noteEl.innerHTML = '<div class="absolute -left-[7px] top-1 w-3 h-3 rounded-full bg-emerald-500 ring-2 ring-white"></div>' +
            '<div class="flex items-center justify-between gap-2 min-w-0">' +
            '<span class="font-bold text-xs text-zinc-900">Call / Note Logged</span>' +
            '<span class="text-[10px] text-zinc-400 font-mono shrink-0">Just now</span>' +
            '</div>' +
            '<p class="text-xs text-zinc-500 mt-0.5">' + note.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</p>';
        timeline.insertBefore(noteEl, timeline.firstChild);
    }
    textarea.value = '';
    if (window.coraShowToast) window.coraShowToast('Call note recorded', 'success');
};

window.coraAiSummarizeCallNotes = function() {
    var textarea = document.getElementById('cora-audit-note-input');
    if (!textarea) return;
    var rawNotes = (textarea.value || '').trim();
    var leadId = (document.getElementById('cora-drawer-lead-id') || {}).value || '';

    if (!rawNotes) {
        if (window.coraShowToast) window.coraShowToast('Please enter raw call notes to synthesize', 'error');
        return;
    }

    var btn = document.getElementById('btn-cora-ai-synthesize-note');
    var origHtml = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-3.5 w-3.5 text-zinc-800" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Synthesizing with AI...';
    }

    var ajaxUrl = window.coraData ? window.coraData.ajax_url : (window.cora_workspace_vars ? window.cora_workspace_vars.ajaxUrl : '/wp-admin/admin-ajax.php');
    var ajaxNonce = window.coraData ? window.coraData.nonce : (window.cora_workspace_vars ? window.cora_workspace_vars.ajaxNonce : '');

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_ai_summarize_sales_call',
            nonce: ajaxNonce,
            security: ajaxNonce,
            lead_id: leadId,
            notes: rawNotes
        },
        success: function(res) {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }
            if (res.success && res.data) {
                var previewBox = document.getElementById('cora-ai-synthesized-preview');
                var summaryEl = document.getElementById('cora-ai-summary-text');
                var badgeEl = document.getElementById('cora-ai-sentiment-badge');
                var itemsList = document.getElementById('cora-ai-action-items-list');

                if (previewBox && summaryEl) {
                    summaryEl.textContent = res.data.summary || 'Summary synthesized.';
                    if (badgeEl) {
                        badgeEl.textContent = (res.data.sentiment || 'Positive').toUpperCase();
                    }
                    if (itemsList && Array.isArray(res.data.next_steps)) {
                        itemsList.innerHTML = res.data.next_steps.map(function(step) {
                            return '<div class="flex items-start gap-1.5 text-[11px] text-zinc-300"><span class="text-emerald-400 font-bold">•</span><span>' + step.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</span></div>';
                        }).join('');
                    }
                    previewBox.classList.remove('hidden');
                }

                // Add entry to audit timeline
                var timeline = document.getElementById('cora-lead-audit-timeline');
                if (timeline) {
                    var noteEl = document.createElement('div');
                    noteEl.className = 'relative pl-6 pb-4 border-l-2 border-zinc-200 ml-3';
                    noteEl.innerHTML = '<div class="absolute -left-[7px] top-1 w-3 h-3 rounded-full bg-purple-500 ring-2 ring-white"></div>' +
                        '<div class="flex items-center justify-between gap-2 min-w-0">' +
                        '<span class="font-bold text-xs text-zinc-900">✨ AI Call Synthesis Logged</span>' +
                        '<span class="text-[10px] text-zinc-400 font-mono shrink-0">Just now</span>' +
                        '</div>' +
                        '<p class="text-xs text-zinc-600 mt-0.5">' + (res.data.summary || rawNotes).replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</p>';
                    timeline.insertBefore(noteEl, timeline.firstChild);
                }

                if (window.coraShowToast) window.coraShowToast('Call notes synthesized and vector memory synced', 'success');
            } else {
                if (window.coraShowToast) window.coraShowToast((res.data && res.data.message) || 'Failed to synthesize note', 'error');
            }
        },
        error: function() {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }
            if (window.coraShowToast) window.coraShowToast('Network error while analyzing note', 'error');
        }
    });
};

window.coraAiRescorePipeline = function() {
    var btn = document.getElementById('btn-cora-ai-rescore');
    var origText = btn ? btn.innerHTML : '';
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin -ml-1 mr-1.5 h-3 w-3 text-white inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Rescoring...';
    }

    var ajaxUrl = window.coraData ? window.coraData.ajax_url : (window.cora_workspace_vars ? window.cora_workspace_vars.ajaxUrl : '/wp-admin/admin-ajax.php');
    var ajaxNonce = window.coraData ? window.coraData.nonce : (window.cora_workspace_vars ? window.cora_workspace_vars.ajaxNonce : '');

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_ai_rescore_pipeline',
            nonce: ajaxNonce,
            security: ajaxNonce
        },
        success: function(res) {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origText;
            }
            if (res.success && res.data) {
                if (window.coraShowToast) window.coraShowToast(res.data.message, 'success');
                setTimeout(function() {
                    window.location.reload();
                }, 800);
            } else {
                if (window.coraShowToast) window.coraShowToast('Pipeline scoring failed', 'error');
            }
        },
        error: function() {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origText;
            }
            if (window.coraShowToast) window.coraShowToast('Network error during pipeline scoring', 'error');
        }
    });
};

window.coraAiFetchBriefing = function() {
    var ajaxUrl = window.coraData ? window.coraData.ajax_url : (window.cora_workspace_vars ? window.cora_workspace_vars.ajaxUrl : '/wp-admin/admin-ajax.php');
    var ajaxNonce = window.coraData ? window.coraData.nonce : (window.cora_workspace_vars ? window.cora_workspace_vars.ajaxNonce : '');

    jQuery.ajax({
        url: ajaxUrl,
        type: 'POST',
        data: {
            action: 'cora_ai_get_daily_briefing',
            nonce: ajaxNonce,
            security: ajaxNonce
        },
        success: function(res) {
            if (res.success && res.data && res.data.summary) {
                var summaryEl = document.getElementById('cora-crm-ai-briefing-summary');
                if (summaryEl) {
                    summaryEl.textContent = res.data.summary;
                }
            }
        }
    });
};

window.coraAiOpenPriorityPlanModal = function() {
    var searchInput = document.getElementById('cora-lead-search-input');
    if (searchInput) {
        // Toggle priority filter
        if (searchInput.value === 'score:hot') {
            searchInput.value = '';
            if (window.coraShowToast) window.coraShowToast('Showing all pipeline leads', 'info');
        } else {
            searchInput.value = 'score:hot';
            if (window.coraShowToast) window.coraShowToast('Filtered to High Priority Hot Leads', 'success');
        }
        if (typeof coraFilterLeadsList === 'function') coraFilterLeadsList();
    }
};

document.addEventListener('DOMContentLoaded', function() {
    if (typeof coraAiFetchBriefing === 'function') {
        coraAiFetchBriefing();
    }
});

</script>
<?php


// Helper function to safely sanitize assignee names and initials (Zero numeric/hash IDs)
if ( ! function_exists( 'cora_get_clean_lead_assignee_info' ) ) {
    function cora_get_clean_lead_assignee_info( $assigned_to_id, $raw_assignee_name = '', $users = array() ) {
        $name = trim( (string) $raw_assignee_name );
        $is_invalid = empty( $name ) || preg_match( '/^[0-9_a-f]+$/i', $name ) || strlen( $name ) <= 2 || stripos( $name, 'shruti' ) !== false;
        
        if ( $is_invalid && ! empty( $assigned_to_id ) ) {
            foreach ( $users as $u ) {
                if ( (string) $u->ID === (string) $assigned_to_id ) {
                    $u_name = trim( (string) $u->display_name );
                    if ( ! empty( $u_name ) && ! preg_match( '/^[0-9_a-f]+$/i', $u_name ) && strlen( $u_name ) > 2 && stripos( $u_name, 'shruti' ) === false ) {
                        $name = $u_name;
                        $is_invalid = false;
                        break;
                    }
                }
            }
        }
        
        if ( $is_invalid ) {
            $fallback_roster = array( 'Studio Admin', 'Aarav Mehta', 'Kavya Patel', 'Rohan Verma' );
            $idx = abs( intval( $assigned_to_id ) ) % count( $fallback_roster );
            $name = $fallback_roster[$idx];
        }
        
        $parts = preg_split( '/\s+/', $name );
        $first_name = $parts[0];
        $initials = '';
        if ( count( $parts ) >= 2 ) {
            $initials = strtoupper( substr( $parts[0], 0, 1 ) . substr( end( $parts ), 0, 1 ) );
        } else {
            $initials = strtoupper( substr( $parts[0], 0, min( 2, strlen( $parts[0] ) ) ) );
        }
        
        return array(
            'full_name'  => $name,
            'first_name' => $first_name,
            'initials'   => $initials,
        );
    }
}

// Fetch leads and initial datasets
$cora_leads_raw = cora_db_get_leads();
$cora_clients_raw = function_exists('cora_db_get_clients') ? cora_db_get_clients() : array();
$cora_users_list = get_users( array( 'fields' => array( 'ID', 'display_name', 'user_email' ) ) );
$cora_clean_users = array();
foreach ( $cora_users_list as $u ) {
    $uname = trim( $u->display_name );
    if ( preg_match( '/^[0-9_a-f]+$/i', $uname ) || strlen( $uname ) <= 2 || stripos( $uname, 'shruti' ) !== false ) {
        continue;
    }
    $cora_clean_users[] = $u;
}
if ( empty( $cora_clean_users ) ) {
    $cora_clean_users = array(
        (object) array( 'ID' => 1, 'display_name' => 'Studio Admin', 'user_email' => 'admin@cora.local' ),
        (object) array( 'ID' => 2, 'display_name' => 'Aarav Mehta', 'user_email' => 'aarav@cora.local' ),
        (object) array( 'ID' => 3, 'display_name' => 'Kavya Patel', 'user_email' => 'kavya@cora.local' ),
    );
}

// Compute KPI Metrics
$total_leads_count = count( $cora_leads_raw );
$pipeline_total_value = 0;
$converted_count = 0;
$hot_leads_count = 0;

$default_stages = array(
    'New Lead'    => array( 'key' => 'New Lead', 'label' => 'New Inquiries', 'badge' => 'bg-emerald-500/10 text-emerald-600 border-emerald-200 ', 'enabled' => true ),
    'Contacted'   => array( 'key' => 'Contacted', 'label' => 'Proposal Sent', 'badge' => 'bg-amber-500/10 text-amber-600 border-amber-200 ', 'enabled' => true ),
    'Site Visit'  => array( 'key' => 'Site Visit', 'label' => 'Site Visit / Viewing', 'badge' => 'bg-violet-500/10 text-violet-600 border-violet-200 ', 'enabled' => true ),
    'Negotiation' => array( 'key' => 'Negotiation', 'label' => 'Negotiation', 'badge' => 'bg-purple-500/10 text-purple-600 border-purple-200 ', 'enabled' => true ),
    'Converted'   => array( 'key' => 'Converted', 'label' => 'Converted', 'badge' => 'bg-sky-500/10 text-sky-600 border-sky-200 ', 'enabled' => true ),
    'Lost'        => array( 'key' => 'Lost', 'label' => 'On Hold', 'badge' => 'bg-orange-500/10 text-orange-600 border-orange-200 ', 'enabled' => false ),
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
        'badge' => $s_val['badge'] ?? 'bg-zinc-500/10 text-zinc-600 border-zinc-200 ',
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
$avg_deal_size = $total_leads_count > 0 ? round( $pipeline_total_value / $total_leads_count ) : 0;

$cora_initial_subtab = sanitize_text_field( $_GET['subtab'] ?? '' );
if ( empty( $cora_initial_subtab ) || ! in_array( $cora_initial_subtab, array( 'kanban', 'directory', 'analytics', 'activity' ), true ) ) {
    $is_mobile_ua = isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/(mobile|android|iphone|ipad|ipod)/i', $_SERVER['HTTP_USER_AGENT'] );
    $cora_initial_subtab = $is_mobile_ua ? 'directory' : 'kanban';
}
?>

<div id="cora-leads-module-container" class="space-y-4 select-none font-sans text-zinc-900">
<?php
$leads_header_args = array(
    'title'              => 'Leads',
    'mobile_title'       => 'Leads',
    'description'        => 'Nurture client inquiries, drag & drop deal stages, track funnel conversion, and close shoots.',
    'mobile_description' => 'Drag & drop deal stages, track funnel conversion',
    'icon'               => '<svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
    'ai_stack'           => true,
    'tutorial_onclick'   => "window.open('https://www.youtube.com/@heycora', '_blank')",
    'cta'                => array(
        'id'          => 'btn-cora-top-add-lead',
        'text'        => 'Add Lead',
        'mobile_text' => 'Add Lead',
        'onclick'     => 'coraOpenCreateLeadDrawer()',
        'icon'        => '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>',
        'visible'     => true,
    ),
    'extra_actions_html' => '
        <button type="button" id="cora-top-header-customize-cols" class="w-9 h-9 text-zinc-700 hover:text-zinc-950 bg-white hover:bg-zinc-50 border border-zinc-200/90 rounded-xl transition-all flex items-center justify-center cursor-pointer shrink-0 active:scale-95" onclick="coraOpenManageStagesDrawer()" title="Customize Columns" aria-label="Customize Columns">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
        </button>
        <button type="button" class="h-9 px-3.5 text-xs font-semibold text-zinc-800 bg-white hover:bg-zinc-50 border border-zinc-200/90 rounded-xl transition-all flex items-center gap-1.5 cursor-pointer shrink-0 active:scale-95" onclick="coraExportLeadsCSV()">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <span>Export CSV</span>
        </button>
    ',
    'mobile_extra_actions_html' => '
        <button type="button" class="w-7 h-7 text-zinc-700 bg-white border border-zinc-200/90 rounded-lg transition-all flex items-center justify-center cursor-pointer shrink-0 active:scale-95" onclick="coraOpenManageStagesDrawer()" title="Customize Columns" aria-label="Customize Columns">
            <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
        </button>
    ',
);
cora_render_workspace_header( $leads_header_args );
?>

    <!-- UNIFIED TOOLBAR: TABS + LIVE KPI PILLS + INSTANT SEARCH + INDEPENDENT FILTERS -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 p-2.5 sm:p-3 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-3 select-none">
        
        <!-- Left: Segmented Tabs -->
        <div class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-800 p-1 rounded-xl shrink-0 overflow-x-auto max-w-full">
            <?php
            $active_cls = 'active bg-white dark:bg-zinc-900 text-zinc-950 dark:text-white font-bold border border-zinc-200/90 dark:border-zinc-700 ';
            $inactive_cls = 'text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white font-medium hover:bg-zinc-200/50 dark:hover:bg-zinc-800/50 ';
            ?>
            <button type="button" class="cora-lead-subtab-btn shrink-0 px-3.5 py-1.5 text-xs rounded-lg transition-all cursor-pointer <?php echo ($cora_initial_subtab === 'kanban') ? $active_cls : $inactive_cls; ?>" data-tab="kanban" onclick="coraSwitchLeadSubtab('kanban')">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="9" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>
                    <span>Kanban Pipeline</span>
                    <span id="cora-kanban-tab-count" class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700"><?php echo $total_leads_count; ?></span>
                </div>
            </button>
            <button type="button" class="cora-lead-subtab-btn shrink-0 px-3.5 py-1.5 text-xs rounded-lg transition-all cursor-pointer <?php echo ($cora_initial_subtab === 'directory') ? $active_cls : $inactive_cls; ?>" data-tab="directory" onclick="coraSwitchLeadSubtab('directory')">
                <div class="flex items-center gap-2">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                    <span>Leads Directory</span>
                </div>
            </button>
        </div>

        <!-- Middle: Sleek Inline Live Metrics Strip -->
        <div class="hidden xl:flex items-center gap-2 text-xs font-semibold text-zinc-500 dark:text-zinc-400 shrink-0">
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200/60 dark:border-zinc-700/60">
                <span class="text-[10px] text-zinc-400 uppercase tracking-wider">Pipeline</span>
                <span id="cora-crm-live-pipeline-sum" class="font-extrabold text-zinc-900 dark:text-zinc-100 font-mono">₹<?php echo number_format( $pipeline_total_value ); ?></span>
            </div>
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200/60 dark:border-zinc-700/60">
                <span class="text-[10px] text-zinc-400 uppercase tracking-wider">Deals</span>
                <span id="cora-crm-live-inquiries-count" class="font-extrabold text-zinc-900 dark:text-zinc-100 font-mono"><?php echo $total_leads_count; ?></span>
            </div>
            <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-zinc-50 dark:bg-zinc-800/80 border border-zinc-200/60 dark:border-zinc-700/60">
                <span class="text-[10px] text-zinc-400 uppercase tracking-wider">Conversion</span>
                <span class="font-extrabold text-zinc-900 dark:text-zinc-100 font-mono"><?php echo $conversion_rate; ?>%</span>
            </div>
        </div>

        <!-- Right: Real-Time Search & Independent Filters Popover -->
        <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
            <!-- Unified Real-Time Search Input with Clear Button -->
            <div class="relative flex-1 md:w-64">
                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 pointer-events-none">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" id="cora-lead-search-input" placeholder="Search leads by name, email, city..." 
                       class="w-full pl-8 pr-7 py-1.5 text-xs bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 rounded-xl focus:outline-none focus:border-zinc-900 dark:focus:border-white transition-all font-medium placeholder:text-zinc-400"
                       oninput="coraFilterLeadsList()" onkeyup="coraFilterLeadsList()">
                <button type="button" class="absolute right-2 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 border-none bg-transparent cursor-pointer p-0.5 flex items-center justify-center" onclick="document.getElementById('cora-lead-search-input').value=''; coraFilterLeadsList();" title="Clear Search">
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <!-- Clean Filter & Sort Button -->
            <div class="relative inline-block text-left" id="cora-lead-filters-wrapper">
                <button type="button" 
                        id="cora-lead-filter-btn" 
                        onclick="coraToggleLeadFilterPopover(event)" 
                        class="h-8 px-3 bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-semibold text-zinc-800 dark:text-zinc-200 transition-all flex items-center justify-center gap-1.5 cursor-pointer shrink-0 active:scale-95">
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <span>Filter & Sort</span>
                    <span id="cora-lead-filter-badge" class="hidden px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-mono">0</span>
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>

                <!-- Independent Select Dropdowns Popover Card -->
                <div id="cora-lead-filter-popover" class="hidden absolute right-0 top-full mt-2 w-72 max-w-[92vw] bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-2xl shadow-xl z-50 p-3.5 space-y-2.5 font-sans select-none text-xs">
                    <div class="flex items-center justify-between pb-2 border-b border-zinc-100 dark:border-zinc-800">
                        <span class="font-bold text-xs text-zinc-900 dark:text-white tracking-tight">Filter & Sort</span>
                        <button type="button" onclick="coraResetLeadFilters()" class="text-[11px] font-semibold text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors cursor-pointer">Reset</button>
                    </div>

                    <!-- 1. Pipeline Stage -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Pipeline Stage</label>
                        <select id="cora-filter-stage" class="w-full h-8 px-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-medium text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-950 dark:focus:border-white transition-all cursor-pointer" onchange="coraApplySelectFilters()">
                            <option value="all">All Pipeline Stages</option>
                            <?php foreach ( $stages_summary as $sk => $sd ) : ?>
                            <option value="<?php echo esc_attr( $sk ); ?>"><?php echo esc_html( $sd['label'] ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 2. Lead Temperature -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Lead Temperature</label>
                        <select id="cora-filter-score" class="w-full h-8 px-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-medium text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-950 dark:focus:border-white transition-all cursor-pointer" onchange="coraApplySelectFilters()">
                            <option value="all">All Temperatures</option>
                            <option value="hot">🔥 Hot Leads</option>
                            <option value="warm">☀️ Warm Leads</option>
                            <option value="cold">❄️ Cold Leads</option>
                            <option value="won">🎯 Converted / Won</option>
                        </select>
                    </div>

                    <!-- 3. Assigned Team Member -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Assigned Team Member</label>
                        <select id="cora-filter-assignee" class="w-full h-8 px-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-medium text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-950 dark:focus:border-white transition-all cursor-pointer" onchange="coraApplySelectFilters()">
                            <option value="all">All Team Members</option>
                            <?php foreach ( $cora_clean_users as $u ) : ?>
                            <option value="<?php echo esc_attr( $u->ID ); ?>"><?php echo esc_html( $u->display_name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 4. Sorting -->
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Sort By</label>
                        <select id="cora-filter-sort" class="w-full h-8 px-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-medium text-zinc-900 dark:text-zinc-100 outline-none focus:border-zinc-950 dark:focus:border-white transition-all cursor-pointer" onchange="coraApplySelectFilters()">
                            <option value="default">Default Order</option>
                            <option value="value-desc">Deal Value: High → Low</option>
                            <option value="value-asc">Deal Value: Low → High</option>
                            <option value="hot-first">🔥 Hot Leads First</option>
                            <option value="name-asc">Client Name: A → Z</option>
                        </select>
                    </div>
                </div>
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
                width: 34px !important;
                height: 18px !important;
                background-color: #e4e4e7 !important;
                border-radius: 9999px !important;
                transition: background-color 0.2s ease, border-color 0.2s ease !important;
                position: relative !important;
                display: inline-block !important;
                cursor: pointer !important;
                border: 1px solid #d4d4d8 !important;
                flex-shrink: 0 !important;
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
                width: 12px !important;
                height: 12px !important;
                background-color: #ffffff !important;
                border-radius: 50% !important;
                transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15) !important;
            }
            .cora-toggle-checkbox:checked + .cora-toggle-slider {
                background-color: #18181b !important;
                border-color: #18181b !important;
            }
            .dark .cora-toggle-checkbox:checked + .cora-toggle-slider {
                background-color: #f4f4f5 !important;
                border-color: #f4f4f5 !important;
            }
            .cora-toggle-checkbox:checked + .cora-toggle-slider::after {
                transform: translateX(16px) !important;
            }
            .dark .cora-toggle-checkbox:checked + .cora-toggle-slider::after {
                background-color: #18181b !important;
            }
        </style>

        <div class="flex gap-4 overflow-x-auto pb-6 select-none cora-kanban-board-scroll" id="cora-kanban-board">
            <?php
            // Dynamic Pastel Color Tint Palette Mapping for Distinct Column Backgrounds
            $color_tint_palette = array(
                'emerald' => array('bg' => '#f0fdf4', 'border' => '#bbf7d0', 'bg_dark' => 'rgba(6, 78, 59, 0.18)', 'border_dark' => 'rgba(16, 185, 129, 0.3)', 'icon_bg' => 'bg-emerald-600', 'sum_text' => 'text-emerald-700 dark:text-emerald-400 font-black'),
                'amber'   => array('bg' => '#fffbeb', 'border' => '#fde68a', 'bg_dark' => 'rgba(120, 53, 15, 0.18)', 'border_dark' => 'rgba(245, 158, 11, 0.3)', 'icon_bg' => 'bg-amber-600', 'sum_text' => 'text-amber-700 dark:text-amber-400 font-black'),
                'violet'  => array('bg' => '#f5f3ff', 'border' => '#ddd6fe', 'bg_dark' => 'rgba(91, 33, 182, 0.18)', 'border_dark' => 'rgba(139, 92, 246, 0.3)', 'icon_bg' => 'bg-violet-600', 'sum_text' => 'text-violet-700 dark:text-violet-400 font-black'),
                'purple'  => array('bg' => '#faf5ff', 'border' => '#e9d5ff', 'bg_dark' => 'rgba(107, 33, 168, 0.18)', 'border_dark' => 'rgba(168, 85, 247, 0.3)', 'icon_bg' => 'bg-purple-600', 'sum_text' => 'text-purple-700 dark:text-purple-400 font-black'),
                'blue'    => array('bg' => '#eff6ff', 'border' => '#bfdbfe', 'bg_dark' => 'rgba(30, 58, 138, 0.18)', 'border_dark' => 'rgba(59, 130, 246, 0.3)', 'icon_bg' => 'bg-blue-600', 'sum_text' => 'text-blue-700 dark:text-blue-400 font-black'),
                'sky'     => array('bg' => '#f0f9ff', 'border' => '#bae6fd', 'bg_dark' => 'rgba(12, 74, 110, 0.18)', 'border_dark' => 'rgba(14, 165, 233, 0.3)', 'icon_bg' => 'bg-sky-600', 'sum_text' => 'text-sky-700 dark:text-sky-400 font-black'),
                'indigo'  => array('bg' => '#eef2ff', 'border' => '#c7d2fe', 'bg_dark' => 'rgba(49, 46, 129, 0.18)', 'border_dark' => 'rgba(99, 102, 241, 0.3)', 'icon_bg' => 'bg-indigo-600', 'sum_text' => 'text-indigo-700 dark:text-indigo-400 font-black'),
                'rose'    => array('bg' => '#fff1f2', 'border' => '#fecdd3', 'bg_dark' => 'rgba(136, 19, 55, 0.18)', 'border_dark' => 'rgba(244, 63, 94, 0.3)', 'icon_bg' => 'bg-rose-600', 'sum_text' => 'text-rose-700 dark:text-rose-400 font-black'),
                'pink'    => array('bg' => '#fdf2f8', 'border' => '#fbcfe8', 'bg_dark' => 'rgba(131, 24, 67, 0.18)', 'border_dark' => 'rgba(236, 72, 153, 0.3)', 'icon_bg' => 'bg-pink-600', 'sum_text' => 'text-pink-700 dark:text-pink-400 font-black'),
                'teal'    => array('bg' => '#f0fdfa', 'border' => '#99f6e4', 'bg_dark' => 'rgba(19, 78, 74, 0.18)', 'border_dark' => 'rgba(20, 184, 166, 0.3)', 'icon_bg' => 'bg-teal-600', 'sum_text' => 'text-teal-700 dark:text-teal-400 font-black'),
                'orange'  => array('bg' => '#fff7ed', 'border' => '#fed7aa', 'bg_dark' => 'rgba(124, 45, 18, 0.18)', 'border_dark' => 'rgba(249, 115, 22, 0.3)', 'icon_bg' => 'bg-orange-600', 'sum_text' => 'text-orange-700 dark:text-orange-400 font-black'),
                'lime'    => array('bg' => '#f7fee7', 'border' => '#d9f99d', 'bg_dark' => 'rgba(54, 83, 20, 0.18)', 'border_dark' => 'rgba(132, 204, 22, 0.3)', 'icon_bg' => 'bg-lime-600', 'sum_text' => 'text-lime-700 dark:text-lime-400 font-black'),
                'red'     => array('bg' => '#fef2f2', 'border' => '#fecaca', 'bg_dark' => 'rgba(127, 29, 29, 0.18)', 'border_dark' => 'rgba(239, 68, 68, 0.3)', 'icon_bg' => 'bg-red-600', 'sum_text' => 'text-red-700 dark:text-red-400 font-black'),
                'zinc'    => array('bg' => '#f8f8fa', 'border' => '#e4e4e7', 'bg_dark' => 'rgba(24, 24, 27, 0.4)', 'border_dark' => 'rgba(63, 63, 70, 0.4)', 'icon_bg' => 'bg-zinc-700', 'sum_text' => 'text-zinc-700 dark:text-zinc-300 font-black'),
            );

            $stage_icon_templates = array(
                'New Lead'       => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
                'New Inquiries'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
                'Contacted'      => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>',
                'Proposal Sent'  => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>',
                'Site Visit'     => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>',
                'Viewing'        => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>',
                'Negotiation'    => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
                'Converted'      => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>',
                'Lost'           => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><rect x="6" y="4" width="4" height="16" rx="1"></rect><rect x="14" y="4" width="4" height="16" rx="1"></rect></svg>',
            );

            // Default fallback color palette assignment based on stage key
            $default_stage_palette_map = array(
                'New Lead'       => 'emerald',
                'New Inquiries'  => 'emerald',
                'Contacted'      => 'amber',
                'Proposal Sent'  => 'amber',
                'Site Visit'     => 'violet',
                'Viewing'        => 'violet',
                'Negotiation'    => 'purple',
                'Converted'      => 'blue',
                'Lost'           => 'zinc',
            );

            $fallback_style = array(
                'col_bg'         => 'bg-zinc-50 ',
                'icon_bg'        => 'bg-zinc-600 text-white',
                'icon_color'     => 'text-white',
                'sum_text'       => 'text-zinc-700 dark:text-zinc-300 font-black',
                'header_icon'    => '<svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
                'empty_desc'     => 'No active inquiries here.',
                'empty_subdesc'  => 'Track deals by dragging cards into this stage.'
            );

            foreach ( $stages_summary as $stage_key => $stage_data ) : 
                $col_leads = array_filter( $cora_leads_raw, function($lead) use ($stage_key) {
                    $st = $lead['status'] ?? 'New Lead';
                    return $st === $stage_key;
                });

                // Detect color tint from configured badge string or default mapping
                $badge_str = $stage_data['badge'] ?? '';
                $detected_palette_key = $default_stage_palette_map[$stage_key] ?? 'zinc';
                foreach ( array_keys( $color_tint_palette ) as $pal_key ) {
                    if ( strpos( $badge_str, $pal_key ) !== false ) {
                        $detected_palette_key = $pal_key;
                        break;
                    }
                }

                $bg_styles = $color_tint_palette[$detected_palette_key] ?? $color_tint_palette['zinc'];
                $header_icon_svg = $stage_icon_templates[$stage_key] ?? $fallback_style['header_icon'];

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
                            <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 <?php echo $bg_styles['icon_bg']; ?> text-white shadow-2xs">
                                <?php echo $header_icon_svg; ?>
                            </div>
                            <span class="text-[11px] font-black text-zinc-900 dark:text-zinc-100 uppercase tracking-wider truncate">
                                <?php echo esc_html( $stage_data['label'] ); ?>
                            </span>
                            <span class="text-[10px] text-zinc-600 dark:text-zinc-300 font-bold bg-white/80 dark:bg-zinc-800/80 px-2 py-0.5 rounded-full col-count shrink-0 border border-zinc-200/50 dark:border-zinc-700/50">
                                <?php echo count($col_leads); ?>
                            </span>
                        </div>
                        <div class="flex items-center gap-1 shrink-0 relative">
                            <button type="button" class="cora-col-search-btn w-6 h-6 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all flex items-center justify-center cursor-pointer shrink-0" title="Search in Column" onclick="coraToggleColumnSearch(this)">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </button>
                            <button type="button" class="w-6 h-6 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all flex items-center justify-center cursor-pointer shrink-0" title="Quick Add Lead" onclick="coraOpenCreateLeadDrawer('<?php echo esc_attr($stage_key); ?>')">
                                <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            </button>
                            <button type="button" class="cora-col-menu-trigger w-6 h-6 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all flex items-center justify-center cursor-pointer shrink-0" title="Column Sort & Options" onclick="coraToggleColumnMenu(this, event)">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><circle cx="12" cy="5" r="2"></circle><circle cx="12" cy="12" r="2"></circle><circle cx="12" cy="19" r="2"></circle></svg>
                            </button>

                            <!-- Column Context Popover Menu -->
                            <div class="cora-col-context-menu hidden absolute right-0 top-full mt-1.5 w-48 bg-white dark:bg-zinc-900 border border-zinc-200/90 dark:border-zinc-800 rounded-xl shadow-xl z-50 p-1.5 space-y-1 font-sans text-xs">
                                <div class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-zinc-400">Sort Column</div>
                                <button type="button" class="w-full text-left px-2.5 py-1.5 rounded-lg text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 font-medium flex items-center justify-between cursor-pointer" onclick="coraSortKanbanColumn(this, 'value-desc')">
                                    <span>₹ High → Low</span>
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </button>
                                <button type="button" class="w-full text-left px-2.5 py-1.5 rounded-lg text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 font-medium flex items-center justify-between cursor-pointer" onclick="coraSortKanbanColumn(this, 'value-asc')">
                                    <span>₹ Low → High</span>
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><polyline points="18 15 12 9 6 15"></polyline></svg>
                                </button>
                                <button type="button" class="w-full text-left px-2.5 py-1.5 rounded-lg text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 font-medium flex items-center justify-between cursor-pointer" onclick="coraSortKanbanColumn(this, 'hot-first')">
                                    <span>🔥 Hot Leads First</span>
                                </button>
                                <button type="button" class="w-full text-left px-2.5 py-1.5 rounded-lg text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 font-medium flex items-center justify-between cursor-pointer" onclick="coraSortKanbanColumn(this, 'name-asc')">
                                    <span>Name: A → Z</span>
                                </button>
                                <button type="button" class="w-full text-left px-2.5 py-1.5 rounded-lg text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 font-medium flex items-center justify-between cursor-pointer border-t border-zinc-100 dark:border-zinc-800 pt-1.5" onclick="coraSortKanbanColumn(this, 'default')">
                                    <span class="text-zinc-400">Default Order</span>
                                </button>
                                <div class="border-t border-zinc-100 dark:border-zinc-800 my-1"></div>
                                <button type="button" class="w-full text-left px-2.5 py-1.5 rounded-lg text-zinc-700 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 font-medium flex items-center gap-1.5 cursor-pointer" onclick="coraOpenManageStagesDrawer()">
                                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                    <span>Customize Stage</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- In-Column Real-Time Search Box -->
                    <div class="cora-col-search-box hidden pt-1">
                        <div class="relative flex items-center">
                            <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2.2" fill="none" class="absolute left-2.5 text-zinc-400 pointer-events-none"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            <input type="text" class="cora-col-search-input w-full pl-7 pr-7 py-1 bg-white dark:bg-zinc-800 border border-zinc-200/90 dark:border-zinc-700 focus:border-zinc-900 dark:focus:border-white rounded-xl text-[11px] text-zinc-900 dark:text-zinc-100 font-medium placeholder-zinc-400 outline-none transition-all shadow-2xs" placeholder="Search this column..." oninput="coraFilterColumnCards(this)">
                            <button type="button" class="absolute right-2 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 border-none bg-transparent cursor-pointer p-0.5 flex items-center justify-center" onclick="coraClearColumnSearch(this)" title="Clear Search">
                                <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[10.5px] text-zinc-500 dark:text-zinc-400 font-medium pt-1.5 border-t border-zinc-200/60 dark:border-zinc-700/60">
                        <span>Pipeline Value</span>
                        <span class="cora-col-pipeline-val <?php echo $bg_styles['sum_text']; ?>">
                            ₹<?php echo number_format($stage_data['value']); ?>
                        </span>
                    </div>
                </div>

                <!-- Cards Container -->
                <div class="cora-cards-container flex-1 space-y-2.5 pb-2">
                    <?php if ( empty($col_leads) ) : ?>
                        <!-- Empty State Graphic -->
                        <div class="flex flex-col items-center justify-center p-6 my-1 border border-dashed border-zinc-200/90 dark:border-zinc-700/80 rounded-2xl bg-white/40 dark:bg-zinc-800/40 text-center select-none min-h-[160px]">
                            <div class="relative mb-2 flex items-center justify-center w-9 h-9 rounded-full bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-700/50">
                                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.6" fill="none" class="text-zinc-400">
                                    <path d="M22 12h-6l-2 3h-4l-2-3H2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M5.45 5.11L2 12v6a2 2 0 0 2 2h16a2 2 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h5 class="text-xs font-bold text-zinc-700 dark:text-zinc-300 leading-tight">No leads in stage</h5>
                            <p class="text-[10px] text-zinc-400 leading-normal max-w-[170px] mt-0.5">Drag cards here or click add lead</p>
                        </div>
                    <?php else : ?>
                        <?php foreach ( $col_leads as $lead ) : 
                            $score = isset($lead['score']) ? strtolower($lead['score']) : 'warm';
                            $is_won = ( ( $lead['status'] ?? '' ) === 'Converted' || ( $stage_key ?? '' ) === 'Converted' );

                            if ( $is_won ) {
                                $pill_class = 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60';
                                $score_label = 'Won';
                                $score_icon = '<svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2.5" fill="none" class="shrink-0"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                            } else if ($score === 'hot') {
                                $pill_class = 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/60';
                                $score_label = 'Hot';
                                $score_icon = '<svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M12 2c.6 3.3 4 6 4 10a4 4 0 1 1-8 0c0-4 3.4-6.7 4-10z"></path></svg>';
                            } else if ($score === 'cold') {
                                $pill_class = 'bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800/60';
                                $score_label = 'Cold';
                                $score_icon = '<svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M20 12H4M12 20V4M17.66 17.66L6.34 6.34M17.66 6.34L6.34 17.66"/></svg>';
                            } else {
                                $pill_class = 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60';
                                $score_label = 'Warm';
                                $score_icon = '<svg viewBox="0 0 24 24" width="9" height="9" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line></svg>';
                            }

                            $format_tag = $lead['format'] ?? 'Photoshoot';
                            $assigned_to_id = $lead['assigned_to'] ?? '';
                            $assignee_info = cora_get_clean_lead_assignee_info( $assigned_to_id, $lead['assignee_name'] ?? '', $cora_clean_users );
                            $assignee_display_name = $assignee_info['full_name'];
                            $assignee_first_name = $assignee_info['first_name'];
                            $assignee_init = $assignee_info['initials'];
                            $assignee_role = 'Admin';

                            $checklist = $lead['checklist'] ?? '1/2 (50%)';
                            $checklist_pct = $lead['checklist_pct'] ?? 50;
                            $raw_price_str = $lead['price'] ?? '0';
                            $num_price_val = (float) preg_replace( '/[^0-9.]/', '', $raw_price_str );
                            $formatted_price = '₹' . number_format( $num_price_val );

                            $stage_action_map = [
                                'New Lead' => [
                                    'next_step' => 'Next: Contact & Pitch',
                                    'cta_label' => 'Contact',
                                    'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>',
                                    'cta_style' => 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-zinc-100',
                                ],
                                'Contacted' => [
                                    'next_step' => 'Next: Schedule Visit',
                                    'cta_label' => 'Schedule',
                                    'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
                                    'cta_style' => 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-zinc-100',
                                ],
                                'Site Visit' => [
                                    'next_step' => 'Next: Send Quote',
                                    'cta_label' => 'Negotiate',
                                    'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M17 18a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2"></path><rect x="3" y="4" width="18" height="12" rx="2"></rect></svg>',
                                    'cta_style' => 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-zinc-100',
                                ],
                                'Negotiation' => [
                                    'next_step' => 'Next: Close & Convert',
                                    'cta_label' => 'Convert',
                                    'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>',
                                    'cta_style' => 'bg-emerald-600 text-white hover:bg-emerald-700',
                                ],
                                'Converted' => [
                                    'next_step' => 'Status: Deal Won',
                                    'cta_label' => 'Won',
                                    'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>',
                                    'cta_style' => 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800',
                                ],
                                'Lost' => [
                                    'next_step' => 'Status: Closed / Lost',
                                    'cta_label' => 'Closed',
                                    'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
                                    'cta_style' => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500',
                                ],
                            ];
                            $stage_info = $stage_action_map[$stage_key] ?? $stage_action_map['New Lead'];
                        ?>
                        <div class="cora-lead-card bg-white dark:bg-zinc-900 p-3.5 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 transition-all cursor-grab active:cursor-grabbing flex flex-col gap-2.5 relative group select-none"
                             draggable="true"
                             data-id="<?php echo esc_attr( $lead['id'] ); ?>"
                             data-name="<?php echo esc_attr( $lead['names'] ); ?>"
                             data-email="<?php echo esc_attr( $lead['email'] ?? 'client@example.com' ); ?>"
                             data-phone="<?php echo esc_attr( $lead['phone'] ?? '+91 98765 43210' ); ?>"
                             data-price="<?php echo esc_attr( $raw_price_str ); ?>"
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
                                 <span class="font-bold text-[11px] uppercase tracking-wider text-zinc-900 dark:text-zinc-100 truncate max-w-[165px]">
                                     <?php echo esc_html( $lead['names'] ); ?>
                                 </span>
                                 <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold shrink-0 <?php echo $pill_class; ?>" title="<?php echo esc_attr($score_label); ?>">
                                     <?php echo $score_icon; ?>
                                     <?php echo esc_html($score_label); ?>
                                 </span>
                             </div>

                             <!-- Project Title & Location -->
                             <div>
                                 <h4 class="font-semibold text-zinc-900 dark:text-zinc-100 text-[13px] tracking-tight leading-tight truncate" title="<?php echo esc_attr( $lead['scale'] ?? 'Standard Shoot' ); ?>">
                                     <?php echo esc_html( $lead['scale'] ?? 'Standard Shoot' ); ?>
                                 </h4>
                                 <p class="text-[11px] text-zinc-400 font-medium mt-0.5 truncate flex items-center gap-1">
                                     <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0 text-zinc-400"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                     <span><?php echo esc_html( $lead['city'] ?? 'Mumbai' ); ?></span>
                                 </p>
                             </div>

                             <!-- Price & Format Tag Row -->
                             <div class="flex items-center justify-between gap-1.5 pt-0.5">
                                 <span class="font-extrabold text-[13.5px] text-zinc-950 dark:text-zinc-100 font-mono tracking-tight">
                                     <?php echo esc_html( $formatted_price ); ?>
                                 </span>
                                 <span class="px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 font-bold text-[9px] uppercase tracking-wider truncate max-w-[110px]">
                                     <?php echo esc_html( $format_tag ); ?>
                                 </span>
                             </div>

                             <!-- Next Step Milestone Banner -->
                             <div class="flex items-center justify-between text-[9.5px] px-2.5 py-1 rounded-lg bg-zinc-50 dark:bg-zinc-800/70 border border-zinc-200/60 dark:border-zinc-700/60">
                                 <span class="text-zinc-400 uppercase tracking-wider text-[8.5px] font-bold">Next Action</span>
                                 <span class="text-zinc-800 dark:text-zinc-200 font-semibold truncate max-w-[170px]"><?php echo esc_html($stage_info['next_step']); ?></span>
                             </div>

                             <!-- Assignee & Action Row -->
                             <div class="flex items-center justify-between gap-1.5 pt-1.5 border-t border-zinc-100 dark:border-zinc-800">
                                 <div class="flex items-center gap-1.5 min-w-0">
                                     <div class="w-6 h-6 rounded-full bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 flex items-center justify-center font-bold text-[9px] shrink-0 border border-zinc-200 dark:border-zinc-700" title="Assigned to <?php echo esc_attr( $assignee_display_name ); ?>">
                                         <?php echo esc_html( $assignee_init ); ?>
                                     </div>
                                     <div class="min-w-0 flex flex-col">
                                         <span class="font-bold text-zinc-900 dark:text-zinc-100 text-[10.5px] leading-none truncate"><?php echo esc_html( $assignee_first_name ); ?></span>
                                         <span class="text-[9px] text-zinc-400 leading-none mt-0.5"><?php echo esc_html( $assignee_role ); ?></span>
                                     </div>
                                 </div>

                                 <div class="flex items-center gap-1 shrink-0">
                                     <!-- Direct WhatsApp Shortcut -->
                                     <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $lead['phone'] ?? '919876543210'); ?>" target="_blank" onclick="event.stopPropagation()" class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-800/60 flex items-center justify-center hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors" title="Chat on WhatsApp">
                                         <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.67-1.616-.919-2.213-.242-.58-.487-.502-.67-.511l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c-.001 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413"/></svg>
                                     </a>

                                     <!-- Action CTA Button -->
                                     <button type="button" class="px-2.5 py-1.5 font-bold rounded-lg text-[10px] transition-all cursor-pointer flex items-center gap-1 shrink-0 <?php echo $stage_info['cta_style']; ?>" onclick="event.stopPropagation(); coraOpenLeadDetailDrawer('<?php echo esc_attr($lead['id']); ?>')">
                                         <?php echo $stage_info['cta_icon']; ?>
                                         <span><?php echo esc_html($stage_info['cta_label']); ?></span>
                                     </button>
                                 </div>
                             </div>

                             <!-- Progress Checklist Row -->
                             <div class="space-y-1 -mt-0.5">
                                 <div class="flex items-center justify-between text-[9px] font-semibold text-zinc-400">
                                     <span>Progress</span>
                                     <span class="text-zinc-500 dark:text-zinc-400 font-mono"><?php echo esc_html( $checklist ); ?></span>
                                 </div>
                                 <div class="w-full h-1 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                     <div class="h-full rounded-full transition-all <?php echo $style['progress_bg'] ?? 'bg-emerald-500'; ?>" style="width: <?php echo intval($checklist_pct); ?>%;"></div>
                                 </div>
                             </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Clean Notion-style Column Footer Add Button -->
                <div class="pt-2">
                    <button type="button" class="w-full py-2 px-3 text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-black/5 dark:hover:bg-white/5 rounded-xl transition-all flex items-center justify-center gap-1.5 border border-dashed border-zinc-300/80 dark:border-zinc-700/80 cursor-pointer" onclick="coraOpenCreateLeadDrawer('<?php echo esc_attr($stage_key); ?>')">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        <span>Add lead</span>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SUB-TAB 2: LEADS DIRECTORY (RESPONSIVE CARD GRID & VIEW SWITCHER) -->
    <div id="cora-lead-pane-directory" class="cora-lead-tab-pane <?php echo ($cora_initial_subtab === 'directory') ? '' : 'hidden'; ?> space-y-4">
        
        <!-- Directory Header Toolbar with View Mode Toggle + Column Density Picker -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 p-3.5 bg-white rounded-2xl border border-zinc-200/80 shadow-2xs">
            <div class="flex items-center gap-2">
                <span class="font-extrabold text-xs text-zinc-950 tracking-tight uppercase">Prospect Directory</span>
                <span class="px-2 py-0.5 rounded-full bg-zinc-100 text-[10.5px] font-bold text-zinc-600 border border-zinc-200/60 " id="cora-directory-total-badge"><?php echo count($cora_leads_raw); ?> Total Leads</span>
            </div>

            <!-- Col density + view switcher: desktop only (mobile always shows card grid) -->
            <div class="hidden sm:flex items-center gap-2 self-end sm:self-auto flex-wrap justify-end">

                <!-- Column Density Picker (only visible in card grid mode) -->
                <div id="cora-dir-col-picker" class="flex items-center gap-1 p-1 bg-zinc-100 rounded-xl text-xs font-semibold">
                    <span class="px-1.5 text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Cols</span>
                    <button type="button" id="cora-dir-col-btn-1" class="w-7 h-7 rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 font-bold text-[11px] flex items-center justify-center" onclick="coraSetGridColumns(1)" title="1 column">1</button>
                    <button type="button" id="cora-dir-col-btn-2" class="w-7 h-7 rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 font-bold text-[11px] flex items-center justify-center" onclick="coraSetGridColumns(2)" title="2 columns">2</button>
                    <button type="button" id="cora-dir-col-btn-3" class="w-7 h-7 rounded-lg transition-all cursor-pointer bg-white text-zinc-950 font-extrabold shadow-2xs flex items-center justify-center text-[11px]" onclick="coraSetGridColumns(3)" title="3 columns (max)">3</button>
                </div>

                <!-- View Mode Switcher: Cards Grid vs Table List -->
                <div class="flex items-center gap-1 p-1 bg-zinc-100 rounded-xl text-xs font-semibold">
                    <button type="button" id="cora-dir-view-btn-grid" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer bg-white text-zinc-950 font-extrabold shadow-2xs flex items-center gap-1.5" onclick="coraSwitchDirectoryViewMode('grid')">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect></svg>
                        <span>Card Grid</span>
                    </button>
                    <button type="button" id="cora-dir-view-btn-table" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5" onclick="coraSwitchDirectoryViewMode('table')">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                        <span>Table List</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- VIEW MODE 1: DESKTOP & MOBILE RESPONSIVE CARD GRID (DEFAULT, MAX 3 COLS) -->
        <div id="cora-directory-grid-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php if ( empty($cora_leads_raw) ) : ?>
                <div class="col-span-full p-8 text-center text-zinc-400 bg-white border border-zinc-200 rounded-2xl">
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
                        $pill_class = 'bg-emerald-50/80 text-emerald-800 border border-emerald-200/80 ';
                        $score_label = 'Won';
                        $score_icon = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                    } else if ($score === 'hot') {
                        $pill_class = 'bg-rose-50/80 text-rose-800 border border-rose-200/80 ';
                        $score_label = 'Hot';
                        $score_icon = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 2c.6 3.3 4 6 4 10a4 4 0 1 1-8 0c0-4 3.4-6.7 4-10z"></path></svg>';
                    } else if ($score === 'cold') {
                        $pill_class = 'bg-sky-50/80 text-sky-800 border border-sky-200/80 ';
                        $score_label = 'Cold';
                        $score_icon = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M12 2v20M17 5l-5 5-5-5M2 12h20M7 19l5-5 5 5"></path></svg>';
                    } else {
                        $pill_class = 'bg-amber-50/80 text-amber-800 border border-amber-200/80 ';
                        $score_label = 'Warm';
                        $score_icon = '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line></svg>';
                    }
                    $assigned_to_id = $lead['assigned_to'] ?? '';
                    $assignee_info = cora_get_clean_lead_assignee_info( $assigned_to_id, $lead['assignee_name'] ?? '', $cora_clean_users );
                    $assignee_display_name = $assignee_info['full_name'];
                    $assignee_first_name = $assignee_info['first_name'];
                    $assignee_initials = $assignee_info['initials'];
                    $assignee_role = 'Admin';
                    
                    $price_display = $lead['price'] ?? '0';
                    $num_price = (float) preg_replace('/[^0-9.]/', '', $price_display);

                    $stage_action_map = [
                        'New Lead' => [
                            'next_step' => 'Next: Contact & Pitch',
                            'cta_label' => 'Contact Client',
                            'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>',
                            'cta_style' => 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-zinc-100',
                        ],
                        'Contacted' => [
                            'next_step' => 'Next: Schedule Visit',
                            'cta_label' => 'Schedule Visit',
                            'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
                            'cta_style' => 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-zinc-100',
                        ],
                        'Site Visit' => [
                            'next_step' => 'Next: Send Quote',
                            'cta_label' => 'Negotiate',
                            'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M17 18a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2"></path><rect x="3" y="4" width="18" height="12" rx="2"></rect></svg>',
                            'cta_style' => 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 hover:bg-zinc-800 dark:hover:bg-zinc-100',
                        ],
                        'Negotiation' => [
                            'next_step' => 'Next: Close & Convert',
                            'cta_label' => 'Convert Deal',
                            'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>',
                            'cta_style' => 'bg-emerald-600 text-white hover:bg-emerald-700',
                        ],
                        'Converted' => [
                            'next_step' => 'Status: Deal Won',
                            'cta_label' => 'Converted',
                            'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>',
                            'cta_style' => 'bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800',
                        ],
                        'Lost' => [
                            'next_step' => 'Status: Closed / Lost',
                            'cta_label' => 'Closed',
                            'cta_icon'  => '<svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
                            'cta_style' => 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500',
                        ],
                    ];
                    $stage_info = $stage_action_map[$st] ?? $stage_action_map['New Lead'];
                ?>
                <div class="cora-lead-card bg-white dark:bg-zinc-900 p-4 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 hover:border-zinc-400 dark:hover:border-zinc-600 transition-all cursor-pointer flex flex-col justify-between gap-3 relative group select-none" 
                     data-id="<?php echo esc_attr($lead['id']); ?>" 
                     data-name="<?php echo esc_attr($lead['names']); ?>" 
                     data-email="<?php echo esc_attr($lead['email'] ?? ''); ?>" 
                     data-phone="<?php echo esc_attr($lead['phone'] ?? ''); ?>" 
                     data-city="<?php echo esc_attr($lead['city'] ?? ''); ?>" 
                     data-price="<?php echo esc_attr($lead['price'] ?? '0'); ?>" 
                     data-score="<?php echo esc_attr($score); ?>" 
                     data-status="<?php echo esc_attr($st); ?>" 
                     data-assigned-to="<?php echo esc_attr($assigned_to_id); ?>" 
                     onclick="coraOpenLeadDetailDrawer('<?php echo esc_attr($lead['id']); ?>')">
                    <!-- Top Row: Client Initial Avatar & Name + Score Badge -->
                    <div class="flex items-center justify-between gap-2 border-b border-zinc-100 dark:border-zinc-800 pb-2.5">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold text-xs flex items-center justify-center shrink-0">
                                <?php echo esc_html(strtoupper(substr($lead['names'] ?? 'C', 0, 1))); ?>
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-bold text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 leading-tight truncate">
                                    <?php echo esc_html($lead['names']); ?>
                                </h4>
                                <p class="text-[10.5px] text-zinc-400 font-medium truncate mt-0.5">
                                    <?php echo esc_html($lead['scale'] ?? 'Standard Shoot'); ?>
                                </p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9.5px] font-bold shrink-0 <?php echo $pill_class; ?>">
                            <?php echo $score_icon; ?>
                            <?php echo esc_html($score_label); ?>
                        </span>
                    </div>

                    <!-- Middle Row: Deal Value & Contact Email/Phone -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[10px] uppercase font-bold text-zinc-400 tracking-wider">Deal Value</span>
                            <span class="font-extrabold text-sm sm:text-base text-zinc-950 dark:text-zinc-100 font-mono tracking-tight">
                                ₹<?php echo number_format($num_price); ?>
                            </span>
                        </div>
                        <div class="p-2.5 bg-zinc-50 dark:bg-zinc-800/60 rounded-xl border border-zinc-200/60 dark:border-zinc-700/60 space-y-0.5">
                            <div class="text-[11px] font-medium text-zinc-800 dark:text-zinc-200 truncate flex items-center gap-1.5">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-400 shrink-0"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                <span class="truncate"><?php echo esc_html($lead['email']); ?></span>
                            </div>
                            <div class="text-[10.5px] font-medium text-zinc-500 dark:text-zinc-400 flex items-center justify-between gap-1 pt-0.5">
                                <span><?php echo esc_html($lead['phone'] ?? 'N/A'); ?></span>
                                <span class="px-1.5 py-0.5 rounded bg-zinc-200/60 dark:bg-zinc-700/60 text-[9.5px] font-semibold text-zinc-600 dark:text-zinc-300 truncate max-w-[90px]">
                                    <?php echo esc_html($lead['city'] ?? 'Mumbai'); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Next Step Milestone Banner -->
                    <div class="flex items-center justify-between text-[9.5px] px-2.5 py-1 rounded-lg bg-zinc-50 dark:bg-zinc-800/70 border border-zinc-200/60 dark:border-zinc-700/60">
                        <span class="text-zinc-400 uppercase tracking-wider text-[8.5px] font-bold">Next Action</span>
                        <span class="text-zinc-800 dark:text-zinc-200 font-semibold truncate max-w-[170px]"><?php echo esc_html($stage_info['next_step']); ?></span>
                    </div>

                    <!-- Footer Row: Stage Badge & Assignee + Quick Action -->
                    <div class="flex items-center justify-between gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-6 h-6 rounded-full bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 flex items-center justify-center font-bold text-[9px] shrink-0 border border-zinc-200 dark:border-zinc-700" title="Assigned to <?php echo esc_attr( $assignee_display_name ); ?>">
                                <?php echo esc_html( $assignee_initials ); ?>
                            </div>
                            <div class="min-w-0 flex flex-col">
                                <span class="font-bold text-zinc-900 dark:text-zinc-100 text-[11px] leading-none truncate"><?php echo esc_html( $assignee_first_name ); ?></span>
                                <span class="text-[9px] text-zinc-400 leading-none mt-0.5"><?php echo esc_html( $assignee_role ); ?></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0" onclick="event.stopPropagation()">
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $lead['phone'] ?? '919876543210'); ?>" target="_blank" class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-200/80 dark:border-emerald-800/60 flex items-center justify-center hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors" title="Chat on WhatsApp">
                                <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.67-1.616-.919-2.213-.242-.58-.487-.502-.67-.511l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c-.001 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413"/></svg>
                            </a>
                            <button type="button" class="px-2.5 py-1.5 font-bold rounded-lg text-[10px] transition-all cursor-pointer flex items-center gap-1 shrink-0 <?php echo $stage_info['cta_style']; ?>" onclick="coraOpenLeadDetailDrawer('<?php echo esc_attr($lead['id']); ?>')">
                                <?php echo $stage_info['cta_icon']; ?>
                                <span><?php echo esc_html($stage_info['cta_label']); ?></span>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <!-- Filter empty state (shown by JS when search/filter yields 0 results) -->
            <div id="cora-grid-empty-state" class="col-span-full hidden">
                <div class="flex flex-col items-center justify-center gap-4 py-16 px-6 bg-white border border-zinc-200/80 rounded-2xl">
                    <div class="w-12 h-12 rounded-2xl bg-zinc-100 flex items-center justify-center">
                        <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400 "><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-bold text-zinc-800 ">No leads found</p>
                        <p id="cora-grid-empty-msg" class="text-xs text-zinc-400 mt-1">Try adjusting your search or filters.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- VIEW MODE 2: TABLE LIST VIEW (OPTIONAL TOGGLE) -->
        <div id="cora-directory-table-container" class="hidden bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/90 dark:border-zinc-800 overflow-hidden">
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
                    <thead class="bg-zinc-50 dark:bg-zinc-800/80 text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider text-[10px] border-b border-zinc-200 dark:border-zinc-700">
                        <tr>
                            <th class="p-4 text-center">
                                <input type="checkbox" id="cora-leads-select-all" class="rounded border-zinc-300 text-zinc-950 focus:ring-0 cursor-pointer" onchange="coraToggleSelectAllLeads(this)">
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
                    <tbody id="cora-leads-table-body" class="divide-y divide-zinc-200/80 dark:divide-zinc-800">
                        <?php if ( empty($cora_leads_raw) ) : ?>
                            <tr>
                                <td colspan="9" class="p-8 text-center text-zinc-400">No leads registered in workspace yet. Click "Add Lead" to create your first inquiry.</td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ( $cora_leads_raw as $lead ) : 
                                $st = $lead['status'] ?? 'New Lead';
                                $badge = $stages_summary[$st]['badge'] ?? 'bg-zinc-100 text-zinc-800';
                                $assigned_to_id = $lead['assigned_to'] ?? '';
                                $assignee_info = cora_get_clean_lead_assignee_info( $assigned_to_id, $lead['assignee_name'] ?? '', $cora_clean_users );
                                $assignee_display_name = $assignee_info['full_name'];
                                $assignee_initials = $assignee_info['initials'];
                            ?>
                            <tr class="hover:bg-zinc-50/65 transition-colors cursor-pointer" 
                                data-id="<?php echo esc_attr($lead['id']); ?>" 
                                data-name="<?php echo esc_attr($lead['names']); ?>" 
                                data-email="<?php echo esc_attr($lead['email'] ?? ''); ?>" 
                                data-phone="<?php echo esc_attr($lead['phone'] ?? ''); ?>" 
                                data-city="<?php echo esc_attr($lead['city'] ?? ''); ?>" 
                                data-price="<?php echo esc_attr($lead['price'] ?? '0'); ?>" 
                                data-score="<?php echo esc_attr($score); ?>" 
                                data-status="<?php echo esc_attr($st); ?>" 
                                data-assigned-to="<?php echo esc_attr( $assigned_to_id ); ?>" 
                                onclick="coraOpenLeadDetailDrawer('<?php echo esc_attr($lead['id']); ?>')">
                                <td class="p-4 text-center" onclick="event.stopPropagation()">
                                    <input type="checkbox" class="cora-lead-row-checkbox rounded border-zinc-300 text-zinc-950 focus:ring-0 cursor-pointer" value="<?php echo esc_attr($lead['id']); ?>">
                                </td>
                                <td class="p-4 font-bold text-zinc-900 truncate">
                                    <div class="truncate"><?php echo esc_html( $lead['names'] ); ?></div>
                                    <span class="block text-[10px] font-normal text-zinc-400 mt-0.5 truncate"><?php echo esc_html( $lead['scale'] ?? 'Standard Shoot' ); ?></span>
                                </td>
                                <td class="p-4 truncate">
                                    <div class="font-medium text-zinc-800 truncate"><?php echo esc_html($lead['email']); ?></div>
                                    <div class="text-[10px] text-zinc-400 mt-0.5"><?php echo esc_html($lead['phone'] ?? 'N/A'); ?></div>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border <?php echo $badge; ?>">
                                        <?php echo esc_html( $st ); ?>
                                    </span>
                                </td>
                                <td class="p-4 font-bold text-zinc-900 ">
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
                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-200 "><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>Won</span>';
                                    } else if ($sc === 'hot') {
                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-600 border border-rose-200 "><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2c.6 3.3 4 6 4 10a4 4 0 1 1-8 0c0-4 3.4-6.7 4-10z"></path></svg>Hot</span>';
                                    } else if ($sc === 'cold') {
                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-sky-500/10 text-sky-600 border border-sky-200 "><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2v20M17 5l-5 5-5-5M2 12h20M7 19l5-5 5 5"></path></svg>Cold</span>';
                                    } else {
                                        echo '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-600 border border-amber-200 "><svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line></svg>Warm</span>';
                                    }
                                    ?>
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-full bg-zinc-950 text-white flex items-center justify-center font-bold text-[9px] shrink-0 border border-zinc-200 ">
                                            <?php echo esc_html( $assignee_initials ); ?>
                                        </div>
                                        <span class="font-bold text-zinc-900 text-xs truncate">
                                            <?php echo esc_html( $assignee_display_name ); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="p-4 font-medium text-zinc-700 truncate">
                                    <?php echo esc_html( $lead['city'] ?? 'Mumbai' ); ?>
                                </td>
                                <td class="p-4 text-right space-x-1" onclick="event.stopPropagation()">
                                    <button type="button" class="px-2.5 py-1 text-[11px] font-bold bg-zinc-100 text-zinc-800 hover:bg-zinc-200 rounded-md transition-all cursor-pointer" onclick="coraOpenLeadDetailDrawer('<?php echo esc_attr($lead['id']); ?>')">
                                        View Deal
                                    </button>
                                    <button type="button" class="px-2.5 py-1 text-[11px] font-bold bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-md border border-emerald-200 transition-all cursor-pointer" onclick="coraConvertLeadToClient('<?php echo esc_attr($lead['id']); ?>')">
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
                                    <div class="w-10 h-10 rounded-xl bg-zinc-100 flex items-center justify-center">
                                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="1.8" fill="none" class="text-zinc-400 "><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm font-bold text-zinc-800 ">No leads found</p>
                                        <p id="cora-table-empty-msg" class="text-xs text-zinc-400 mt-0.5">Try adjusting your search or filters.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div><!-- /#cora-directory-table-container -->
        <!-- SERVER-SIDE PAGINATION BAR (DESKTOP & MOBILE) -->
        <div id="cora-directory-pagination" class="mt-4 p-3.5 bg-white rounded-2xl border border-zinc-200/80 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-3 text-xs select-none">
            <div class="flex items-center gap-2 text-zinc-500 font-medium">
                <span id="cora-pagination-info">Showing 1–<?php echo min(25, count($cora_leads_raw)); ?> of <?php echo count($cora_leads_raw); ?> leads</span>
                <span class="hidden sm:inline text-zinc-300 ">|</span>
                <div class="flex items-center gap-1.5">
                    <span class="text-[11px] font-semibold text-zinc-400">Per Page:</span>
                    <select id="cora-pagination-per-page" class="px-2 py-1 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-800 font-bold text-xs cursor-pointer outline-none" onchange="coraChangePerPage(this.value)">
                        <option value="10">10</option>
                        <option value="25" selected>25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-1.5" id="cora-pagination-controls">
                <button type="button" id="cora-pagination-prev" class="px-3 py-1.5 bg-zinc-100 text-zinc-700 font-semibold rounded-xl text-xs hover:bg-zinc-200 disabled:opacity-40 disabled:cursor-not-allowed transition-all flex items-center gap-1 cursor-pointer" onclick="coraGoToPage('prev')" disabled>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    <span>Prev</span>
                </button>
                <span id="cora-pagination-page-label" class="px-2 text-xs font-bold text-zinc-900 ">Page 1 of 1</span>
                <button type="button" id="cora-pagination-next" class="px-3 py-1.5 bg-zinc-100 text-zinc-700 font-semibold rounded-xl text-xs hover:bg-zinc-200 disabled:opacity-40 disabled:cursor-not-allowed transition-all flex items-center gap-1 cursor-pointer" onclick="coraGoToPage('next')">
                    <span>Next</span>
                    <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </div>
        </div>
    </div>



    <!-- SUB-TAB 4: ACTIVITY & OUTREACH LOG -->
    <div id="cora-lead-pane-activity" class="cora-lead-tab-pane <?php echo ($cora_initial_subtab === 'activity') ? '' : 'hidden'; ?> space-y-4">
        <div class="bg-white p-5 rounded-2xl border border-zinc-200/80 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-zinc-200/80 ">
                <div>
                    <h3 class="font-bold text-sm text-zinc-900 ">Workspace Activity & Outreach Timeline</h3>
                    <p class="text-xs text-zinc-500 mt-0.5">Automated logging throttled periodically (5m interval) to prevent server bloat.</p>
                </div>
                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200/60 inline-flex items-center gap-1.5 shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> 7-Day Auto-Purge Schedule
                </span>
            </div>
            
            <div id="cora-lead-activity-timeline" class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-zinc-200 mt-4">
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
                    <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full bg-zinc-900 border-2 border-white "></div>
                    <div class="text-xs">
                        <span class="font-bold text-zinc-900 "><?php echo $act_title; ?>:</span>
                        <span class="text-zinc-600 "> <?php echo $act_desc; ?></span>
                        <div class="text-[10px] text-zinc-400 mt-0.5"><?php echo $act_time_str; ?> • <?php echo $act_user; ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else : ?>
                <div class="relative">
                    <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white "></div>
                    <div class="text-xs">
                        <span class="font-bold text-zinc-900 ">Workspace Activity Log Active:</span>
                        <span class="text-zinc-600 "> Logs are stored for 7 days on a periodic schedule and auto-purged to protect server resources.</span>
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
<aside id="cora-lead-detail-drawer" class="cora-prospect-detail-drawer cora-side-drawer hidden collapsed fixed top-0 right-0 w-full sm:w-[540px] md:w-[50vw] max-w-full sm:max-w-xl h-full bg-white shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 flex flex-col font-sans overflow-hidden">
    
    <!-- Drag Handle Bar on Left Edge (Desktop Only) -->
    <div id="cora-drawer-resize-handle" class="hidden sm:flex absolute top-0 bottom-0 -left-2 w-4 cursor-ew-resize group z-20 items-center justify-center" title="Drag left/right to resize drawer">
        <div class="w-1.5 h-14 rounded-full bg-zinc-300 group-hover:bg-zinc-950 group-hover:w-2 transition-all shadow-xs"></div>
    </div>

    <!-- Header: Clean, Uncluttered & Sticky Top -->
    <div class="p-4 sm:p-5 border-b border-zinc-200 flex items-center justify-between shrink-0 bg-white sticky top-0 z-30">
        <div class="flex items-center gap-3 min-w-0 pr-2">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-zinc-950 text-white font-black text-sm sm:text-base flex items-center justify-center shadow-sm shrink-0">
                <span id="cora-drawer-avatar-initial">C</span>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                    <h3 id="cora-drawer-lead-name" class="font-black text-base sm:text-lg text-zinc-900 leading-tight truncate">Corporate Brand Film</h3>
                    <span id="cora-drawer-lead-score" class="px-2.5 py-0.5 rounded-full text-[9.5px] font-extrabold uppercase tracking-wider bg-amber-500/10 text-amber-600 border border-amber-200 shrink-0">Warm</span>
                </div>
                <p id="cora-drawer-lead-email" class="text-xs text-zinc-500 mt-0.5 font-medium truncate">Shoot: Brand Film – Bengaluru</p>
            </div>
        </div>
        
        <button type="button" class="text-zinc-500 hover:text-zinc-900 w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-zinc-100 hover:bg-zinc-200 transition-all cursor-pointer shrink-0 flex items-center justify-center" onclick="window.coraCloseAllDrawers()" title="Close Drawer">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Segmented Tab Header Bar -->
    <div class="px-3 sm:px-6 py-2 bg-zinc-50/90 border-b border-zinc-200/80 shrink-0">
        <div class="overflow-x-auto no-scrollbar flex items-center gap-1 p-1 bg-zinc-200/70 rounded-2xl text-xs font-semibold max-w-full">
            <button type="button" id="cora-lead-detail-tab-btn-overview" class="cora-lead-detail-tab-btn shrink-0 py-2 px-3.5 rounded-xl transition-all cursor-pointer bg-white text-zinc-950 font-extrabold shadow-xs flex items-center justify-center gap-1.5 whitespace-nowrap text-xs" onclick="coraSwitchLeadDetailTab('overview')">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                <span>Details</span>
            </button>
            <button type="button" id="cora-lead-detail-tab-btn-automation" class="cora-lead-detail-tab-btn shrink-0 py-2 px-3.5 rounded-xl transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 flex items-center justify-center gap-1.5 whitespace-nowrap text-xs font-semibold" onclick="coraSwitchLeadDetailTab('automation')">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                <span>Automations</span>
            </button>
            <button type="button" id="cora-lead-detail-tab-btn-checklist" class="cora-lead-detail-tab-btn shrink-0 py-2 px-3.5 rounded-xl transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 flex items-center justify-center gap-1.5 whitespace-nowrap text-xs font-semibold" onclick="coraSwitchLeadDetailTab('checklist')">
                <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="shrink-0"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                <span>Tasks</span>
            </button>
            <button type="button" id="cora-lead-detail-tab-btn-audit" class="cora-lead-detail-tab-btn shrink-0 py-2 px-3.5 rounded-xl transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 flex items-center justify-center gap-1.5 whitespace-nowrap text-xs font-semibold" onclick="coraSwitchLeadDetailTab('audit')">
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
            <div class="p-3.5 bg-zinc-50 border border-zinc-200/80 rounded-2xl shadow-2xs space-y-2.5">
                <div class="flex items-center justify-between gap-2 border-b border-zinc-100 pb-2">
                    <span class="text-[10px] uppercase font-extrabold tracking-wider text-zinc-500 ">Quick Outreach & Actions</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white text-zinc-700 border border-zinc-200 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span>SLA: <strong id="cora-drawer-sla-timer" class="text-zinc-950 font-extrabold">18m remaining</strong></span>
                    </span>
                </div>
                <div class="grid grid-cols-3 gap-2 pt-0.5">
                    <a id="cora-drawer-whatsapp-btn" href="#" target="_blank" class="py-2.5 px-2 bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold rounded-xl text-xs hover:bg-emerald-100 transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" class="shrink-0 text-emerald-600 "><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.67-1.616-.919-2.213-.242-.58-.487-.502-.67-.511l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c-.001 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413"/></svg>
                        <span>WhatsApp</span>
                    </a>
                    <a id="cora-drawer-sla-email-btn" href="#" target="_blank" class="py-2.5 px-2 bg-white text-zinc-900 border border-zinc-200 font-bold rounded-xl text-xs hover:bg-zinc-100 transition-all cursor-pointer flex items-center justify-center gap-1.5 shadow-2xs">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500 "><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        <span>Email</span>
                    </a>
                    <button type="button" id="cora-convert-lead-btn" class="py-2.5 px-2 bg-zinc-950 text-white font-extrabold rounded-xl text-xs hover:bg-zinc-800 transition-all shadow-sm cursor-pointer flex items-center justify-center gap-1.5" onclick="coraConvertCurrentLeadToClient()">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>Convert</span>
                    </button>
                </div>
            </div>

            <!-- ACCORDION SECTION 1: DEAL STATUS & OWNER ASSIGNMENT (COLLAPSED BY DEFAULT) -->
            <div class="cora-accordion-card bg-white rounded-2xl border border-zinc-200 shadow-2xs overflow-hidden">
                <button type="button" class="w-full p-4 flex items-center justify-between gap-2 text-left cursor-pointer hover:bg-zinc-50 transition-colors" onclick="coraToggleDrawerAccordion(this)">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-xl bg-zinc-100 text-zinc-900 flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-xs text-zinc-900 ">Deal Status & Assignee</h4>
                            <p class="text-[10.5px] text-zinc-400 font-medium">Pipeline stage and team owner</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded-full text-[9.5px] font-bold bg-zinc-100 text-zinc-600 ">Tap to Edit</span>
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" class="cora-accordion-icon text-zinc-400 transition-transform duration-200"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </div>
                </button>
                <div class="cora-accordion-body hidden p-4 pt-0 border-t border-zinc-100 space-y-3 mt-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <div>
                            <label class="block font-bold text-[11px] text-zinc-600 mb-1">Pipeline Stage</label>
                            <select id="cora-drawer-stage-select" class="w-full text-xs font-extrabold bg-zinc-50 border border-zinc-200 px-3 py-2.5 rounded-xl text-zinc-900 focus:outline-none cursor-pointer" onchange="coraUpdateLeadStageFromDrawer()">
                                <option value="New Lead">New Lead</option>
                                <option value="Contacted">Proposal Sent</option>
                                <option value="Site Visit">Site Visit / Viewing</option>
                                <option value="Negotiation">Negotiation</option>
                                <option value="Converted">Converted</option>
                                <option value="Lost">Closed / Lost</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-[11px] text-zinc-600 mb-1">Assigned Team Member</label>
                            <select id="cora-drawer-input-assigned-to" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 text-xs font-semibold focus:outline-none transition-colors cursor-pointer" onchange="coraUpdateLeadAssignee(document.getElementById('cora-drawer-lead-id').value, this.value)">
                                <?php foreach ( $cora_users_list as $u ) : ?>
                                    <option value="<?php echo esc_attr( $u->ID ); ?>"><?php echo esc_html( $u->display_name ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCORDION SECTION 2: CONTACT DETAILS & LOCATION (OPEN BY DEFAULT FOR INSTANT COMFORT) -->
            <div class="cora-accordion-card bg-white rounded-2xl border border-zinc-200 shadow-2xs overflow-hidden">
                <button type="button" class="w-full p-4 flex items-center justify-between gap-2 text-left cursor-pointer hover:bg-zinc-50 transition-colors" onclick="coraToggleDrawerAccordion(this)">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-xl bg-zinc-100 text-zinc-900 flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-xs text-zinc-900 ">Contact & Client Information</h4>
                            <p class="text-[10.5px] text-zinc-400 font-medium">Name, email, phone & city</p>
                        </div>
                    </div>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" class="cora-accordion-icon text-zinc-400 transition-transform duration-200 transform rotate-180"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div class="cora-accordion-body p-4 pt-0 border-t border-zinc-100 space-y-3 mt-3">
                    <div class="pt-2">
                        <label class="block font-bold text-zinc-700 mb-1">Full Name / Prospect Title</label>
                        <input type="text" id="cora-drawer-input-names" class="w-full px-3.5 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none text-xs">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-zinc-700 mb-1">Email Address</label>
                            <input type="email" id="cora-drawer-input-email" class="w-full px-3.5 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-zinc-700 mb-1">Phone / WhatsApp</label>
                            <input type="text" id="cora-drawer-input-phone" class="w-full px-3.5 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none text-xs">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <label class="block font-bold text-zinc-700 ">Target City / Geo-Location</label>
                            <button type="button" class="text-[10px] font-bold text-zinc-600 hover:text-zinc-950 flex items-center gap-1 cursor-pointer" onclick="coraDetectCurrentGeoCity()">
                                <svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                <span>Auto-Detect Geo</span>
                            </button>
                        </div>
                        <input type="text" id="cora-drawer-input-city" class="w-full px-3.5 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none text-xs" placeholder="e.g. Mumbai, BKC / Bengaluru">
                        
                        <!-- Quick Studio Hub City Pills for Instant Selection -->
                        <div class="flex items-center gap-1.5 flex-wrap pt-2">
                            <span class="text-[9.5px] font-bold text-zinc-400 uppercase tracking-wider">Quick Hubs:</span>
                            <button type="button" class="px-2 py-0.5 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[10px] font-semibold transition-colors cursor-pointer" onclick="$('#cora-drawer-input-city').val('Mumbai')">Mumbai</button>
                            <button type="button" class="px-2 py-0.5 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[10px] font-semibold transition-colors cursor-pointer" onclick="$('#cora-drawer-input-city').val('Bengaluru')">Bengaluru</button>
                            <button type="button" class="px-2 py-0.5 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[10px] font-semibold transition-colors cursor-pointer" onclick="$('#cora-drawer-input-city').val('Goa')">Goa</button>
                            <button type="button" class="px-2 py-0.5 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[10px] font-semibold transition-colors cursor-pointer" onclick="$('#cora-drawer-input-city').val('Delhi NCR')">Delhi NCR</button>
                            <button type="button" class="px-2 py-0.5 rounded-lg bg-zinc-100 hover:bg-zinc-200 text-zinc-700 text-[10px] font-semibold transition-colors cursor-pointer" onclick="$('#cora-drawer-input-city').val('Hyderabad')">Hyderabad</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCORDION SECTION 3: DEAL BUDGET & PRIORITY (COLLAPSED BY DEFAULT) -->
            <div class="cora-accordion-card bg-white rounded-2xl border border-zinc-200 shadow-2xs overflow-hidden">
                <button type="button" class="w-full p-4 flex items-center justify-between gap-2 text-left cursor-pointer hover:bg-zinc-50 transition-colors" onclick="coraToggleDrawerAccordion(this)">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-xl bg-zinc-100 text-zinc-900 flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-xs text-zinc-900 ">Deal Budget & Intent Priority</h4>
                            <p class="text-[10.5px] text-zinc-400 font-medium">Value (₹) and priority level</p>
                        </div>
                    </div>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" class="cora-accordion-icon text-zinc-400 transition-transform duration-200"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div class="cora-accordion-body hidden p-4 pt-0 border-t border-zinc-100 space-y-3 mt-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <div>
                            <label class="block font-bold text-zinc-700 mb-1">Deal Budget (₹)</label>
                            <input type="text" id="cora-drawer-input-price" class="w-full px-3.5 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-zinc-700 mb-1">Priority Level</label>
                            <select id="cora-drawer-input-score" class="w-full px-3.5 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none text-xs">
                                <option value="hot">Hot (High Priority)</option>
                                <option value="warm">Warm (Standard Interest)</option>
                                <option value="cold">Cold (Low Priority)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCORDION SECTION 4: NOTES & SHOOT SPECIFICATIONS (COLLAPSED BY DEFAULT) -->
            <div class="cora-accordion-card bg-white rounded-2xl border border-zinc-200 shadow-2xs overflow-hidden">
                <button type="button" class="w-full p-4 flex items-center justify-between gap-2 text-left cursor-pointer hover:bg-zinc-50 transition-colors" onclick="coraToggleDrawerAccordion(this)">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-xl bg-zinc-100 text-zinc-900 flex items-center justify-center shrink-0">
                            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-xs text-zinc-900 ">Deal Notes & Shoot Specifications</h4>
                            <p class="text-[10.5px] text-zinc-400 font-medium">Inquiry requirements & dates</p>
                        </div>
                    </div>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" class="cora-accordion-icon text-zinc-400 transition-transform duration-200"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </button>
                <div class="cora-accordion-body hidden p-4 pt-0 border-t border-zinc-100 space-y-3 mt-3">
                    <div class="pt-2">
                        <textarea id="cora-drawer-input-notes" rows="3" class="w-full px-3.5 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none resize-none text-xs" placeholder="Client specifications, requested deliverables, shoot dates..."></textarea>
                    </div>
                </div>
            </div>

        </div>

        <!-- TAB 2: WORKFLOWS & AUTOMATION -->
        <div id="cora-lead-detail-tab-automation" class="cora-lead-detail-tab-pane hidden space-y-4 text-xs">
            <div class="p-3.5 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-1">
                <h4 class="font-bold text-xs text-zinc-900 flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
                    Automated Sequences & Drip Workflows
                </h4>
                <p class="text-[11px] text-zinc-500">Configure automated customer journeys and notification rules for this deal.</p>
            </div>

            <div class="space-y-3">
                <div class="p-3.5 rounded-xl border border-zinc-200 bg-white flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xs text-zinc-900 ">Instant Welcome WhatsApp & Email</div>
                        <div class="text-[11px] text-zinc-500 mt-0.5">Sends automated welcome portfolio deck when lead is created.</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" checked class="cora-toggle-checkbox sr-only">
                        <div class="cora-toggle-slider"></div>
                    </label>
                </div>

                <div class="p-3.5 rounded-xl border border-zinc-200 bg-white flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xs text-zinc-900 ">3-Day Auto Proposal Reminder Drip</div>
                        <div class="text-[11px] text-zinc-500 mt-0.5">Reminds client if proposal remains unreviewed for 72 hours.</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
                        <input type="checkbox" checked class="cora-toggle-checkbox sr-only">
                        <div class="cora-toggle-slider"></div>
                    </label>
                </div>

                <div class="p-3.5 rounded-xl border border-zinc-200 bg-white flex items-center justify-between">
                    <div>
                        <div class="font-bold text-xs text-zinc-900 ">High-Value VIP Alert (> ₹2,00,000)</div>
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
            <div class="p-3.5 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-2">
                <div class="flex items-center justify-between">
                    <h4 class="font-bold text-xs text-zinc-900 ">Deal Intake Checklist</h4>
                    <span class="text-[10px] font-bold text-emerald-600 font-mono">2/4 Completed (50%)</span>
                </div>
                <div class="w-full h-1.5 bg-zinc-200 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: 50%;"></div>
                </div>
            </div>

            <!-- Interactive Checklist Items Container -->
            <div id="cora-lead-checklist-container" class="space-y-2">
                <label class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-200 bg-white hover:border-zinc-300 transition-all cursor-pointer">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <input type="checkbox" checked class="w-4 h-4 text-emerald-600 rounded border-zinc-300 focus:ring-emerald-500">
                        <span class="text-xs font-semibold text-zinc-800 truncate line-through">Verify Shoot Date & Venue Licensing</span>
                    </div>
                    <span class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-200">Done</span>
                </label>

                <label class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-200 bg-white hover:border-zinc-300 transition-all cursor-pointer">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <input type="checkbox" checked class="w-4 h-4 text-emerald-600 rounded border-zinc-300 focus:ring-emerald-500">
                        <span class="text-xs font-semibold text-zinc-800 truncate line-through">Deliver Itemized Commercial Proposal</span>
                    </div>
                    <span class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-200">Done</span>
                </label>

                <label class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-200 bg-white hover:border-zinc-300 transition-all cursor-pointer">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <input type="checkbox" class="w-4 h-4 text-emerald-600 rounded border-zinc-300 focus:ring-emerald-500">
                        <span class="text-xs font-semibold text-zinc-800 truncate">Confirm 50% Booking Advance Deposit</span>
                    </div>
                    <span class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 border border-amber-200">Pending</span>
                </label>

                <label class="flex items-center justify-between p-2.5 rounded-xl border border-zinc-200 bg-white hover:border-zinc-300 transition-all cursor-pointer">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <input type="checkbox" class="w-4 h-4 text-emerald-600 rounded border-zinc-300 focus:ring-emerald-500">
                        <span class="text-xs font-semibold text-zinc-800 truncate">Assign Lead Videographer & Crew Roster</span>
                    </div>
                    <span class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 border border-amber-200">Pending</span>
                </label>
            </div>

            <!-- Dynamic Intake Task Adder -->
            <div class="pt-2 flex items-center gap-2">
                <input type="text" id="cora-new-checklist-input" class="flex-1 px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 text-xs focus:outline-none" placeholder="Add custom intake task...">
                <button type="button" class="px-3.5 py-2 bg-zinc-950 text-white font-bold rounded-xl text-xs hover:bg-zinc-800 transition-all cursor-pointer shrink-0" onclick="coraAddLeadChecklistItem()">
                    + Add Task
                </button>
            </div>
        </div>

        <!-- TAB 4: AUDIT TRAIL & CALL LOGS -->
        <div id="cora-lead-detail-tab-audit" class="cora-lead-detail-tab-pane hidden space-y-4 text-xs">
            <!-- Add Call Note Logger -->
            <div class="p-3.5 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-xs text-zinc-900 flex items-center gap-1.5">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        Log Prospect Call / Meeting Note
                    </span>
                    <span class="text-[9.5px] font-semibold text-zinc-500 bg-white px-2 py-0.5 rounded-md border border-zinc-200">AI Synced</span>
                </div>
                <textarea id="cora-audit-note-input" rows="3" class="w-full px-3 py-2 bg-white border border-zinc-200 rounded-xl text-zinc-900 text-xs focus:outline-none resize-none leading-relaxed" placeholder="Record raw meeting notes, budget mentioned, key objections, or requested deliverables..."></textarea>
                <div class="flex items-center justify-between gap-2 pt-1">
                    <button type="button" id="btn-cora-ai-synthesize-note" class="px-3 py-1.5 bg-white border border-zinc-300 text-zinc-800 font-bold rounded-xl text-xs hover:bg-zinc-100 transition-all cursor-pointer flex items-center gap-1.5" onclick="coraAiSummarizeCallNotes()">
                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                        <span>✨ AI Extract & Synthesize</span>
                    </button>
                    <button type="button" class="px-3.5 py-1.5 bg-zinc-950 text-white font-bold rounded-xl text-xs hover:bg-zinc-800 transition-all cursor-pointer shadow-xs" onclick="coraAddLeadAuditLogNote()">
                        + Save Raw Note
                    </button>
                </div>
                <div id="cora-ai-synthesized-preview" class="hidden p-3 bg-zinc-900 text-white rounded-xl text-xs space-y-2 border border-zinc-800">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-[11px] text-emerald-400 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            AI Synthesized Call Summary
                        </span>
                        <span id="cora-ai-sentiment-badge" class="px-2 py-0.5 rounded text-[9px] font-mono uppercase bg-zinc-800 text-zinc-300 border border-zinc-700">Positive</span>
                    </div>
                    <p id="cora-ai-summary-text" class="text-xs text-zinc-300 leading-relaxed"></p>
                    <div id="cora-ai-action-items-list" class="space-y-1 pt-1 border-t border-zinc-800"></div>
                </div>
            </div>

            <!-- Chronological Audit Timeline -->
            <div id="cora-lead-audit-timeline" class="space-y-4 pt-2">
                <div class="relative pl-6 pb-4 border-l-2 border-zinc-200 ml-3">
                    <div class="absolute -left-[7px] top-1 w-3 h-3 rounded-full bg-emerald-500 ring-2 ring-white "></div>
                    <div class="flex items-center justify-between gap-2 min-w-0">
                        <span class="font-bold text-xs text-zinc-900 ">Stage Moved to Negotiation</span>
                        <span class="text-[10px] text-zinc-400 font-mono shrink-0">Today, 2:15 PM</span>
                    </div>
                    <p class="text-xs text-zinc-500 mt-0.5">User moved deal stage from Proposal Sent to Negotiation.</p>
                </div>

                <div class="relative pl-6 pb-4 border-l-2 border-zinc-200 ml-3">
                    <div class="absolute -left-[7px] top-1 w-3 h-3 rounded-full bg-blue-500 ring-2 ring-white "></div>
                    <div class="flex items-center justify-between gap-2 min-w-0">
                        <span class="font-bold text-xs text-zinc-900 ">Proposal Estimate Sent</span>
                        <span class="text-[10px] text-zinc-400 font-mono shrink-0">Yesterday, 11:30 AM</span>
                    </div>
                    <p class="text-xs text-zinc-500 mt-0.5">Itemized commercial quotation PDF sent via WhatsApp.</p>
                </div>

                <div class="relative pl-6 border-l-2 border-zinc-200 ml-3">
                    <div class="absolute -left-[7px] top-1 w-3 h-3 rounded-full bg-zinc-400 ring-2 ring-white "></div>
                    <div class="flex items-center justify-between gap-2 min-w-0">
                        <span class="font-bold text-xs text-zinc-900 ">Lead Inquiry Registered</span>
                        <span class="text-[10px] text-zinc-400 font-mono shrink-0">2 days ago</span>
                    </div>
                    <p class="text-xs text-zinc-500 mt-0.5">Inquiry captured via Website Intake Form.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer: Sticky Bottom Action Bar -->
    <div class="p-3.5 sm:p-4 border-t border-zinc-200 flex items-center justify-between shrink-0 bg-white sticky bottom-0 z-30 shadow-lg">
        <button type="button" class="px-2.5 sm:px-3.5 py-2 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0" onclick="coraDeleteCurrentLead()">
            Delete Lead
        </button>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" class="px-3 sm:px-4 py-2 bg-zinc-100 text-zinc-800 font-semibold rounded-xl text-xs cursor-pointer hover:bg-zinc-200" onclick="window.coraCloseAllDrawers()">
                Cancel
            </button>
            <button type="button" class="px-3.5 sm:px-5 py-2 bg-zinc-950 text-white font-bold rounded-xl text-xs hover:bg-zinc-800 transition-all cursor-pointer shadow-sm" onclick="coraSaveLeadFromDrawer()">
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
<aside id="cora-create-lead-drawer" class="cora-side-drawer hidden collapsed fixed top-0 right-0 w-full sm:w-[540px] md:w-[50vw] max-w-full sm:max-w-xl h-full bg-white shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 flex flex-col font-sans overflow-hidden">
    
    <!-- Drawer Header with Live Stage Indicator -->
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between shrink-0 bg-zinc-50/50 ">
        <div>
            <div class="flex items-center gap-2">
                <h3 class="font-extrabold text-base text-zinc-900 ">Register New Lead Inquiry</h3>
                <span id="cora-create-lead-target-badge" class="px-2 py-0.5 rounded-full bg-zinc-900 text-white font-bold text-[9.5px] tracking-wider uppercase">
                    Stage: New Inquiries
                </span>
            </div>
            <p class="text-xs text-zinc-400 mt-0.5">3-Step Intelligent Lead Capture Wizard.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-800 p-2 rounded-lg hover:bg-zinc-100 transition-colors cursor-pointer" onclick="window.coraCloseAllDrawers()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Step Progress Indicator Bar -->
    <div class="px-6 py-3 bg-zinc-50/80 border-b border-zinc-200/80 flex items-center justify-between gap-2 shrink-0">
        <button type="button" onclick="coraGoToCreateLeadStep(1)" class="flex-1 flex items-center gap-2 text-left cursor-pointer">
            <span id="cora-lead-step-ind-1" class="w-6 h-6 rounded-full border flex items-center justify-center text-[10.5px] font-bold transition-all bg-zinc-950 text-white border-zinc-950 shadow-xs">1</span>
            <span class="text-[11px] font-bold text-zinc-800 hidden sm:inline">Client Contact</span>
        </button>
        <div class="w-4 h-px bg-zinc-200 shrink-0"></div>
        <button type="button" onclick="coraGoToCreateLeadStep(2)" class="flex-1 flex items-center gap-2 text-left cursor-pointer">
            <span id="cora-lead-step-ind-2" class="w-6 h-6 rounded-full border flex items-center justify-center text-[10.5px] font-bold transition-all bg-zinc-100 text-zinc-400 border-zinc-200 ">2</span>
            <span class="text-[11px] font-bold text-zinc-600 hidden sm:inline">Stage & Budget</span>
        </button>
        <div class="w-4 h-px bg-zinc-200 shrink-0"></div>
        <button type="button" onclick="coraGoToCreateLeadStep(3)" class="flex-1 flex items-center gap-2 text-left cursor-pointer">
            <span id="cora-lead-step-ind-3" class="w-6 h-6 rounded-full border flex items-center justify-center text-[10.5px] font-bold transition-all bg-zinc-100 text-zinc-400 border-zinc-200 ">3</span>
            <span class="text-[11px] font-bold text-zinc-600 hidden sm:inline">Scope & Notes</span>
        </button>
    </div>

    <!-- Main Form Container -->
    <form id="cora-create-lead-form" class="p-6 overflow-y-auto flex-1 text-xs" onsubmit="event.preventDefault(); coraSubmitNewLeadForm();">
        <input type="hidden" id="cora-new-lead-stage" value="New Lead">

        <!-- STEP 1: CLIENT CONTACT INFO -->
        <div id="cora-create-lead-step-1" class="cora-create-lead-step space-y-4">
            <div class="p-3.5 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-1">
                <h4 class="font-bold text-xs text-zinc-900 flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Step 1: Client Contact Information
                </h4>
                <p class="text-[11px] text-zinc-500">Provide primary contact details to initiate the lead record.</p>
            </div>

            <div>
                <label class="block font-bold text-zinc-700 mb-1">Full Name / Client Name <span class="text-rose-500">*</span></label>
                <input type="text" id="cora-new-lead-names" required class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none focus:border-zinc-900 transition-colors" placeholder="e.g. Vikramaditya Singhania">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-zinc-700 mb-1">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" id="cora-new-lead-email" required class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none focus:border-zinc-900 transition-colors" placeholder="vikram@singhania.com">
                </div>
                <div>
                    <label class="block font-bold text-zinc-700 mb-1">Phone / WhatsApp</label>
                    <input type="tel" id="cora-new-lead-phone" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none focus:border-zinc-900 transition-colors" placeholder="+91 98765 43210" oninput="this.value = this.value.replace(/[^0-9+\-\s()]/g, '')">
                </div>
            </div>

            <div class="pt-6 flex items-center justify-between border-t border-zinc-200 ">
                <button type="button" class="px-4 py-2 bg-zinc-100 text-zinc-700 font-semibold rounded-xl text-xs cursor-pointer hover:bg-zinc-200 transition-colors" onclick="window.coraCloseAllDrawers()">
                    Cancel
                </button>
                <button type="button" class="px-5 py-2.5 bg-zinc-950 text-white font-bold rounded-xl text-xs hover:bg-zinc-800 transition-all cursor-pointer flex items-center gap-1.5 shadow-sm" onclick="coraGoToCreateLeadStep(2)">
                    <span>Next: Stage & Budget</span>
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            </div>
        </div>

        <!-- STEP 2: STAGE & BUDGET STRATEGY -->
        <div id="cora-create-lead-step-2" class="cora-create-lead-step hidden space-y-4">
            <div class="p-3.5 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-1">
                <h4 class="font-bold text-xs text-zinc-900 flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                    Step 2: Initial Stage & Value Scoping
                </h4>
                <p class="text-[11px] text-zinc-500">Select the target pipeline stage and assign deal temperature priority.</p>
            </div>

            <!-- Dynamic Interactive Stage Cards Grid -->
            <div>
                <label class="block font-bold text-zinc-700 mb-1.5">Target Pipeline Stage <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-2">
                    <?php foreach ( $stages_config as $s_key => $s_val ) : 
                        if ( isset( $s_val['enabled'] ) && ! $s_val['enabled'] ) continue;
                        $s_label = $s_val['label'] ?? $s_key;
                        $s_badge = $s_val['badge'] ?? 'bg-zinc-500/10 text-zinc-600 border-zinc-200';
                    ?>
                    <button type="button" class="cora-stage-select-btn p-3 rounded-xl border border-zinc-200 bg-white text-left transition-all cursor-pointer flex flex-col justify-between gap-1.5 hover:border-zinc-400 group" data-stage="<?php echo esc_attr($s_key); ?>" onclick="coraSelectCreateLeadStage('<?php echo esc_attr($s_key); ?>')">
                        <div class="flex items-center justify-between gap-1 w-full">
                            <span class="cora-stage-title font-bold text-xs text-zinc-900 truncate group-hover:text-zinc-950"><?php echo esc_html($s_label); ?></span>
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
                    <label class="block font-bold text-zinc-700 mb-1">Estimated Deal Budget (₹)</label>
                    <input type="text" id="cora-new-lead-price" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none focus:border-zinc-900 transition-colors" placeholder="e.g. 150000" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                <div>
                    <label class="block font-bold text-zinc-700 mb-1">Temperature / Intent Priority</label>
                    <select id="cora-new-lead-score" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none transition-colors">
                        <option value="warm">Warm (Standard Interest)</option>
                        <option value="hot">Hot (High Intent / Urgency)</option>
                        <option value="cold">Cold (Low Priority)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-zinc-700 mb-1">Assign Team Member</label>
                <select id="cora-new-lead-assigned-to" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none transition-colors">
                    <?php foreach ( $cora_users_list as $u ) : ?>
                        <option value="<?php echo esc_attr( $u->ID ); ?>"><?php echo esc_html( $u->display_name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="pt-6 flex items-center justify-between border-t border-zinc-200 ">
                <button type="button" class="px-4 py-2 bg-zinc-100 text-zinc-700 font-semibold rounded-xl text-xs cursor-pointer hover:bg-zinc-200 transition-colors flex items-center gap-1.5" onclick="coraGoToCreateLeadStep(1)">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <span>Back</span>
                </button>
                <button type="button" class="px-5 py-2.5 bg-zinc-950 text-white font-bold rounded-xl text-xs hover:bg-zinc-800 transition-all cursor-pointer flex items-center gap-1.5 shadow-sm" onclick="coraGoToCreateLeadStep(3)">
                    <span>Next: Scope & Notes</span>
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </button>
            </div>
        </div>

        <!-- STEP 3: SHOOT SCOPE & NOTES -->
        <div id="cora-create-lead-step-3" class="cora-create-lead-step hidden space-y-4">
            <div class="p-3.5 bg-zinc-50 border border-zinc-200/80 rounded-xl space-y-1">
                <h4 class="font-bold text-xs text-zinc-900 flex items-center gap-1.5">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none" class="text-zinc-500"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    Step 3: Shoot Scope & Specific Notes
                </h4>
                <p class="text-[11px] text-zinc-500">Add project deliverable details, shoot location, and intake requirements.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-zinc-700 mb-1">Scope / Property Type</label>
                    <input type="text" id="cora-new-lead-scale" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none focus:border-zinc-900 transition-colors" placeholder="e.g. Commercial Villa / Studio Shoot">
                </div>
                <div>
                    <label class="block font-bold text-zinc-700 mb-1">City / Location</label>
                    <input type="text" id="cora-new-lead-city" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none focus:border-zinc-900 transition-colors" placeholder="e.g. Mumbai, BKC">
                </div>
            </div>

            <div>
                <label class="block font-bold text-zinc-700 mb-1">Inquiry Notes & Intake Requirements</label>
                <textarea id="cora-new-lead-notes" rows="4" class="w-full px-3 py-2.5 bg-zinc-50 border border-zinc-200 rounded-xl text-zinc-900 font-medium focus:outline-none focus:border-zinc-900 transition-colors resize-none" placeholder="Add any background notes, client preferences, or specific deliverables..."></textarea>
            </div>

            <div class="pt-6 flex items-center justify-between border-t border-zinc-200 ">
                <button type="button" class="px-4 py-2 bg-zinc-100 text-zinc-700 font-semibold rounded-xl text-xs cursor-pointer hover:bg-zinc-200 transition-colors flex items-center gap-1.5" onclick="coraGoToCreateLeadStep(2)">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2.5" fill="none"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <span>Back</span>
                </button>
                <button type="submit" class="px-6 py-2.5 bg-zinc-950 text-white font-bold rounded-xl text-xs hover:bg-zinc-800 transition-all cursor-pointer shadow-sm flex items-center gap-1.5">
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
<aside id="cora-lead-schedule-drawer" class="cora-side-drawer hidden collapsed fixed top-0 right-0 w-full sm:w-[480px] max-w-full sm:max-w-md h-full bg-white shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200 flex flex-col font-sans overflow-hidden">
    <div class="p-5 border-b border-zinc-200 flex items-center justify-between shrink-0 bg-zinc-50/50 ">
        <div>
            <h3 class="font-extrabold text-base text-zinc-900 ">Schedule Follow-Up Action</h3>
            <p class="text-xs text-zinc-400 mt-0.5">Set reminders or crew tasks for lead nurturing.</p>
        </div>
        <button type="button" class="text-zinc-400 hover:text-zinc-800 p-2 rounded-lg hover:bg-zinc-100 transition-colors cursor-pointer" onclick="window.coraCloseAllDrawers()">
            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <form class="p-6 overflow-y-auto flex-1 space-y-4 text-xs" onsubmit="event.preventDefault(); coraSubmitScheduleTask();">
        <input type="hidden" id="cora-task-lead-id" value="">

        <div>
            <label class="block font-bold text-zinc-700 mb-1">Follow-Up Action Type</label>
            <select id="cora-task-action" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-900 focus:outline-none">
                <option value="call">Phone Call Nurture</option>
                <option value="proposal">Send Proposal / Estimate</option>
                <option value="viewing">Site Visit / Viewing</option>
                <option value="whatsapp">WhatsApp Quick Ping</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-bold text-zinc-700 mb-1">Target Date</label>
                <input type="date" id="cora-task-date" required class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-900 focus:outline-none">
            </div>
            <div>
                <label class="block font-bold text-zinc-700 mb-1">Target Time</label>
                <input type="time" id="cora-task-time" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-900 focus:outline-none" value="11:00">
            </div>
        </div>

        <div>
            <label class="block font-bold text-zinc-700 mb-1">Assigned Team Member</label>
            <select id="cora-task-assignee" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-900 focus:outline-none">
                <option value="me">Assigned to Me</option>
                <?php foreach ($cora_users_list as $u) : ?>
                    <option value="<?php echo esc_attr($u->ID); ?>"><?php echo esc_html($u->display_name); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block font-bold text-zinc-700 mb-1">Reminder Notes</label>
            <textarea id="cora-task-note" rows="3" class="w-full px-3 py-2 bg-zinc-50 border border-zinc-200 rounded-lg text-zinc-900 focus:outline-none resize-none" placeholder="Details for follow up..."></textarea>
        </div>

        <div class="pt-4 flex items-center justify-end gap-2 border-t border-zinc-200 ">
            <button type="button" class="px-4 py-2 bg-zinc-100 text-zinc-800 font-semibold rounded-lg text-xs cursor-pointer" onclick="window.coraCloseAllDrawers()">
                Cancel
            </button>
            <button type="submit" class="px-5 py-2 bg-zinc-950 text-white font-bold rounded-lg text-xs hover:bg-zinc-800 transition-all cursor-pointer shadow-sm">
                Save Task
            </button>
        </div>
    </form>
</aside>

<!-- ========================================================================= -->
<!-- SLIDING SIDE DRAWER 4: CUSTOMIZE CRM PIPELINE & ANALYTICS LAYOUT           -->
<!-- ========================================================================= -->
<aside id="cora-lead-stages-drawer" 
       class="cora-side-drawer hidden collapsed fixed top-0 right-0 w-full sm:w-[540px] max-w-full sm:max-w-lg h-full bg-white dark:bg-zinc-900 shadow-2xl z-[9999] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-zinc-200/80 dark:border-zinc-800 flex flex-col font-sans select-none overflow-hidden"
       data-initial-kpis='<?php echo esc_attr( json_encode( $selected_kpi_keys ) ); ?>'>
    <!-- Header Bar -->
    <div class="p-4 px-5 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-white dark:bg-zinc-900">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 flex items-center justify-center shrink-0 border border-zinc-200/60 dark:border-zinc-700/60 shadow-2xs">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
            </div>
            <div>
                <h3 class="font-bold text-sm text-zinc-950 dark:text-zinc-100 tracking-tight">Customize CRM Layout</h3>
                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Manage pipeline workflow stages and select top KPI analytics cards.</p>
            </div>
        </div>
        <button type="button" class="w-8 h-8 rounded-lg text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors flex items-center justify-center cursor-pointer" onclick="window.coraCloseAllDrawers()" title="Close">
            <svg viewBox="0 0 24 24" width="15" height="15" stroke="currentColor" stroke-width="2" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    <!-- Segmented Tab Header -->
    <div class="px-5 pt-3 pb-2.5 bg-zinc-50/80 dark:bg-zinc-900/80 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center gap-2">
        <button type="button" class="cora-stages-drawer-tab-btn px-3.5 py-1.5 text-xs rounded-xl font-bold transition-all cursor-pointer bg-white dark:bg-zinc-800 text-zinc-950 dark:text-white shadow-2xs border border-zinc-200/80 dark:border-zinc-700 flex items-center gap-1.5" data-tab="cols" onclick="coraSwitchStagesDrawerTab('cols')">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="9" rx="1"></rect><rect x="3" y="14" width="7" height="7" rx="1"></rect><rect x="14" y="14" width="7" height="7" rx="1"></rect></svg>
            <span>Pipeline Columns</span>
        </button>
        <button type="button" class="cora-stages-drawer-tab-btn px-3.5 py-1.5 text-xs rounded-xl font-medium transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white hover:bg-zinc-200/50 dark:hover:bg-zinc-800 flex items-center gap-1.5" data-tab="kpis" onclick="coraSwitchStagesDrawerTab('kpis')">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
            <span>Analytics Cards</span>
            <span id="cora-kpis-count-pill" class="px-1.5 py-0.2 rounded-full text-[9px] font-bold bg-zinc-200/80 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200"><?php echo count($selected_kpi_keys); ?>/4</span>
        </button>
    </div>

    <!-- TAB PANE 1: PIPELINE STAGES FORM -->
    <div id="cora-stages-tab-pane-cols" class="cora-stages-drawer-pane p-5 overflow-y-auto flex-1 space-y-3 text-xs">
        <div class="flex items-center justify-between pb-2.5 mb-1 border-b border-zinc-200/80 dark:border-zinc-800">
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Pipeline Stage Workflow</span>
                <span class="px-2 py-0.5 rounded-full bg-zinc-100 dark:bg-zinc-800 text-[10px] font-bold font-mono text-zinc-600 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700/60 whitespace-nowrap" id="cora-stage-count-badge"><?php echo count($stages_config); ?> Stages</span>
            </div>
            <button type="button" class="h-8 px-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-900 font-semibold rounded-xl text-xs hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-all flex items-center gap-1.5 cursor-pointer shadow-2xs whitespace-nowrap active:scale-95" onclick="coraAddNewStageRow()">
                <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.2" fill="none"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Add Column</span>
            </button>
        </div>

        <div id="cora-stages-list-container" class="space-y-2">
            <?php foreach ( $stages_config as $s_key => $s_val ) : ?>
            <div class="cora-stage-config-row p-2.5 sm:p-3 rounded-xl border border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900/60 shadow-2xs hover:border-zinc-300 dark:hover:border-zinc-700 transition-all flex items-center justify-between gap-2.5 relative group"
                 draggable="true"
                 data-key="<?php echo esc_attr($s_key); ?>"
                 ondragstart="coraStageRowDragStart(event)"
                 ondragover="coraStageRowDragOver(event)"
                 ondrop="coraStageRowDrop(event)"
                 ondragend="coraStageRowDragEnd(event)">
                
                <!-- Left: Grip + Title Input -->
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    <div class="w-7 h-7 rounded-lg bg-zinc-50 dark:bg-zinc-800 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 flex items-center justify-center cursor-grab active:cursor-grabbing shrink-0 select-none transition-colors border border-zinc-200/60 dark:border-zinc-700/60" title="Drag to reorder">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none"><circle cx="9" cy="6" r="1.2"></circle><circle cx="15" cy="6" r="1.2"></circle><circle cx="9" cy="12" r="1.2"></circle><circle cx="15" cy="12" r="1.2"></circle><circle cx="9" cy="18" r="1.2"></circle><circle cx="15" cy="18" r="1.2"></circle></svg>
                    </div>
                    <input type="text" class="cora-stage-label-input h-9 px-3 bg-zinc-50/70 dark:bg-zinc-800/60 border border-zinc-200/80 dark:border-zinc-700 focus:border-zinc-900 dark:focus:border-white focus:bg-white dark:focus:bg-zinc-800 rounded-lg font-semibold text-zinc-900 dark:text-zinc-100 text-xs flex-1 min-w-0 outline-none transition-all" value="<?php echo esc_attr($s_val['label'] ?? $s_key); ?>" placeholder="Stage Title">
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
                        'emerald' => '#10b981', 'amber'   => '#f59e0b', 'blue'    => '#3b82f6',
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
                            <option value="bg-emerald-500/10 text-emerald-600 border-emerald-200 " <?php echo (strpos($badge_val,'emerald')!==false)?'selected':'';?>>Emerald</option>
                            <option value="bg-amber-500/10 text-amber-600 border-amber-200 " <?php echo (strpos($badge_val,'amber')!==false)?'selected':'';?>>Amber</option>
                            <option value="bg-blue-500/10 text-blue-600 border-blue-200 " <?php echo (strpos($badge_val,'blue')!==false && strpos($badge_val,'sky')===false)?'selected':'';?>>Blue</option>
                            <option value="bg-violet-500/10 text-violet-600 border-violet-200 " <?php echo (strpos($badge_val,'violet')!==false)?'selected':'';?>>Violet</option>
                            <option value="bg-pink-500/10 text-pink-600 border-pink-200 " <?php echo (strpos($badge_val,'pink')!==false)?'selected':'';?>>Pink</option>
                            <option value="bg-rose-500/10 text-rose-600 border-rose-200 " <?php echo (strpos($badge_val,'rose')!==false)?'selected':'';?>>Rose</option>
                            <option value="bg-sky-500/10 text-sky-600 border-sky-200 " <?php echo (strpos($badge_val,'sky')!==false)?'selected':'';?>>Sky</option>
                            <option value="bg-indigo-500/10 text-indigo-600 border-indigo-200 " <?php echo (strpos($badge_val,'indigo')!==false)?'selected':'';?>>Indigo</option>
                            <option value="bg-purple-500/10 text-purple-600 border-purple-200 " <?php echo (strpos($badge_val,'purple')!==false)?'selected':'';?>>Purple</option>
                            <option value="bg-orange-500/10 text-orange-600 border-orange-200 " <?php echo (strpos($badge_val,'orange')!==false)?'selected':'';?>>Orange</option>
                            <option value="bg-teal-500/10 text-teal-600 border-teal-200 " <?php echo (strpos($badge_val,'teal')!==false)?'selected':'';?>>Teal</option>
                            <option value="bg-lime-500/10 text-lime-600 border-lime-200 " <?php echo (strpos($badge_val,'lime')!==false)?'selected':'';?>>Lime</option>
                            <option value="bg-red-500/10 text-red-600 border-red-200 " <?php echo (strpos($badge_val,'red')!==false)?'selected':'';?>>Red</option>
                            <option value="bg-cyan-500/10 text-cyan-600 border-cyan-200 " <?php echo (strpos($badge_val,'cyan')!==false)?'selected':'';?>>Cyan</option>
                            <option value="bg-fuchsia-500/10 text-fuchsia-600 border-fuchsia-200 " <?php echo (strpos($badge_val,'fuchsia')!==false)?'selected':'';?>>Fuchsia</option>
                            <option value="bg-zinc-500/10 text-zinc-600 border-zinc-200 " <?php echo (strpos($badge_val,'zinc')!==false)?'selected':'';?>>Zinc</option>
                        </select>

                        <!-- Refined Minimal Swatch Trigger -->
                        <label for="<?php echo esc_attr($picker_id); ?>" class="cora-stage-color-swatch-label w-7 h-7 rounded-lg bg-zinc-50 dark:bg-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 flex items-center justify-center cursor-pointer transition-all shrink-0 hover:scale-105" title="Stage accent color">
                            <span class="cora-stage-color-swatch w-3.5 h-3.5 rounded-full shrink-0 ring-1 ring-black/15 shadow-2xs transition-transform" style="background:<?php echo esc_attr($swatch_hex); ?>;"></span>
                            <input
                                type="color"
                                id="<?php echo esc_attr($picker_id); ?>"
                                class="cora-stage-native-color-input sr-only"
                                value="<?php echo esc_attr($swatch_hex); ?>"
                                oninput="coraStageColorChange(this)"
                            >
                        </label>
                    </div>

                    <!-- Monochromatic Toggle Switch -->
                    <label class="inline-flex items-center gap-1.5 cursor-pointer select-none shrink-0" title="Toggle column visibility">
                        <input type="checkbox" class="cora-stage-enable-checkbox cora-toggle-checkbox sr-only" <?php echo ( ! isset($s_val['enabled']) || $s_val['enabled'] ) ? 'checked' : ''; ?> onchange="coraUpdateStageToggleLabel(this)">
                        <span class="cora-toggle-slider"></span>
                        <span class="cora-toggle-text text-[11px] font-bold text-zinc-600 dark:text-zinc-400 select-none w-8 text-left"><?php echo ( ! isset($s_val['enabled']) || $s_val['enabled'] ) ? 'Show' : 'Hide'; ?></span>
                    </label>

                    <button type="button" class="w-7 h-7 rounded-lg text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 border border-transparent transition-all flex items-center justify-center cursor-pointer shrink-0" onclick="coraRemoveStageRow(this)" title="Delete stage">
                        <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- TAB PANE 2: ANALYTICS KPI CARDS SELECTOR -->
    <div id="cora-stages-tab-pane-kpis" class="cora-stages-drawer-pane hidden p-5 overflow-y-auto flex-1 space-y-3.5 text-xs">
        <div class="p-3 bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200/70 dark:border-zinc-700 rounded-xl flex items-start gap-2.5">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="1.8" fill="none" class="shrink-0 text-zinc-500 mt-0.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <p class="text-[11px] text-zinc-600 dark:text-zinc-300 leading-relaxed">
                Select up to <strong class="font-bold text-zinc-950 dark:text-white">4 KPI cards</strong> for Desktop. The top 2 selected cards are automatically prioritized on Mobile screens.
            </p>
        </div>

        <div class="space-y-2.5" id="cora-kpi-picker-list">
            <?php foreach ( $all_crm_kpis as $kpi_id => $kpi_data ) : 
                $is_selected = in_array( $kpi_id, $selected_kpi_keys, true );
                $selected_pos = $is_selected ? ( array_search( $kpi_id, $selected_kpi_keys, true ) + 1 ) : 0;
            ?>
            <div class="cora-kpi-item-card p-3 rounded-xl border border-zinc-200/80 dark:border-zinc-800 transition-all flex items-center justify-between gap-3 cursor-pointer hover:border-zinc-300 dark:hover:border-zinc-700 <?php echo $is_selected ? 'bg-white dark:bg-zinc-900 shadow-2xs' : 'bg-zinc-50/50 dark:bg-zinc-900/30 opacity-60'; ?>"
                 data-kpi-key="<?php echo esc_attr( $kpi_id ); ?>"
                 onclick="coraToggleLeadKpiCard('<?php echo esc_attr( $kpi_id ); ?>')">
                <div class="flex items-center gap-3 min-w-0">
                    <?php echo $kpi_data['icon_svg']; ?>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-xs text-zinc-950 dark:text-zinc-100 truncate"><?php echo esc_html( $kpi_data['title'] ); ?></span>
                            <span class="cora-kpi-device-badge text-[9.5px] font-semibold px-2 py-0.5 rounded-full border <?php 
                                if ( $is_selected && $selected_pos <= 2 ) {
                                    echo 'bg-emerald-50 text-emerald-700 border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/60';
                                } elseif ( $is_selected ) {
                                    echo 'bg-zinc-100 text-zinc-600 border-zinc-200/60 dark:bg-zinc-800 dark:text-zinc-300 dark:border-zinc-700/60';
                                } else {
                                    echo 'hidden';
                                }
                            ?>">
                                <?php echo ( $selected_pos <= 2 ) ? 'Mobile & Desktop' : 'Desktop Only'; ?>
                            </span>
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate mt-0.5"><?php echo esc_html( $kpi_data['desc'] ); ?></p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 shrink-0">
                    <input type="checkbox" class="cora-kpi-select-checkbox cora-toggle-checkbox sr-only" <?php echo $is_selected ? 'checked' : ''; ?> tabindex="-1">
                    <span class="cora-toggle-slider"></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer Action Bar -->
    <div class="p-4 px-5 border-t border-zinc-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between shrink-0 shadow-lg">
        <button type="button" class="h-9 px-3.5 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold rounded-xl text-xs transition-all flex items-center gap-1.5 cursor-pointer whitespace-nowrap active:scale-95" onclick="coraResetDefaultStages()">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="1.8" fill="none"><path d="M23 4v6h-6"></path><path d="M1 20v-6h6"></path><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
            <span>Reset to Default</span>
        </button>
        <div class="flex items-center gap-2">
            <button type="button" class="h-9 px-3.5 bg-white hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-700 border border-zinc-200/80 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-semibold rounded-xl text-xs cursor-pointer transition-all whitespace-nowrap active:scale-95" onclick="window.coraCloseAllDrawers()">
                Cancel
            </button>
            <button type="button" onclick="coraSavePipelineStages()" class="h-9 px-4 bg-zinc-950 dark:bg-white text-white dark:text-zinc-900 font-bold rounded-xl text-xs hover:bg-zinc-800 dark:hover:bg-zinc-100 transition-all cursor-pointer shadow-sm whitespace-nowrap active:scale-95">
                Save Layout
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
            gridBtn.className = "px-3 py-1.5 rounded-lg transition-all cursor-pointer bg-white text-zinc-950 font-extrabold shadow-2xs flex items-center gap-1.5";
        }
        if (tableBtn) {
            tableBtn.className = "px-3 py-1.5 rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5";
        }
        // Restore saved column preference
        var savedCols = parseInt(localStorage.getItem('cora_grid_cols') || '3');
        window.coraSetGridColumns(savedCols, true);
    } else {
        gridContainer.classList.add('hidden');
        tableContainer.classList.remove('hidden');
        if (colPicker) colPicker.classList.add('hidden');
        if (tableBtn) {
            tableBtn.className = "px-3 py-1.5 rounded-lg transition-all cursor-pointer bg-white text-zinc-950 font-extrabold shadow-2xs flex items-center gap-1.5";
        }
        if (gridBtn) {
            gridBtn.className = "px-3 py-1.5 rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 flex items-center gap-1.5";
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
    grid.classList.remove('grid-cols-1', 'grid-cols-2', 'grid-cols-3', 'md:grid-cols-1', 'md:grid-cols-2', 'md:grid-cols-3', 'lg:grid-cols-1', 'lg:grid-cols-2', 'lg:grid-cols-3');

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
            btn.className = "w-7 h-7 rounded-lg transition-all cursor-pointer bg-white text-zinc-950 font-extrabold shadow-2xs flex items-center justify-center text-[11px]";
        } else {
            btn.className = "w-7 h-7 rounded-lg transition-all cursor-pointer text-zinc-500 hover:text-zinc-900 font-bold text-[11px] flex items-center justify-center";
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
