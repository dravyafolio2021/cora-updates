import re

file_path = "/Users/shrutian/Desktop/cora/app/public/wp-content/plugins/cora-workspace/views/view-content-suite.php"
with open(file_path, "r") as f:
    content = f.read()

new_js = """// ============================================================
// CALENDAR: Week Navigation (client-side, no page reload)
// ============================================================
let _calWeekOffset = 0;

window.coraNavWeek = function(delta) {
    // delta: -1 = prev, +1 = next, 0 = reset to today
    if (delta === 0) {
        _calWeekOffset = 0;
    } else {
        _calWeekOffset += delta;
    }
    _coraRebuildWeekGrid();
};

// Rebuild the week columns based on _calWeekOffset
function _coraRebuildWeekGrid() {
    const weekView = document.getElementById('cora-cal-week-view');
    if (!weekView) return;

    // Get current PHP-rendered dates from data attributes (already rendered server-side)
    // The grid just slides: show the columns for the right week offset
    // Since we are PHP-rendered, week navigation triggers a URL param update + page reload
    const url = new URL(window.location);
    url.searchParams.set('cal_week_offset', _calWeekOffset);
    window.location.href = url.toString();
}

// ============================================================
// CALENDAR: View Toggle (Week <-> Month)
// ============================================================
window.coraToggleCalendarView = function(mode) {
    const weekView = document.getElementById('cora-cal-week-view');
    const monthView = document.getElementById('cora-cal-month-view');
    const weekBtn = document.getElementById('btn-cal-view-week');
    const monthBtn = document.getElementById('btn-cal-view-month');

    const activeClass = 'h-7 px-3.5 rounded-md text-xs font-bold bg-white text-zinc-950 shadow-sm transition-all cursor-pointer';
    const inactiveClass = 'h-7 px-3.5 rounded-md text-xs font-semibold text-zinc-500 hover:text-zinc-900 transition-all cursor-pointer';

    if (mode === 'week') {
        if (weekView) weekView.classList.remove('hidden');
        if (monthView) monthView.classList.add('hidden');
        if (weekBtn) weekBtn.className = activeClass;
        if (monthBtn) monthBtn.className = inactiveClass;
    } else {
        if (weekView) weekView.classList.add('hidden');
        if (monthView) monthView.classList.remove('hidden');
        if (monthBtn) monthBtn.className = activeClass;
        if (weekBtn) weekBtn.className = inactiveClass;
    }
};

// ============================================================
// CALENDAR: Collapsible Filter Bar Toggle
// ============================================================
window.coraToggleFilterBar = function() {
    const bar = document.getElementById('cal-filters-collapsible-bar');
    const btn = document.getElementById('btn-cal-toggle-filters');
    if (!bar) return;
    const isHidden = bar.classList.contains('hidden');
    if (isHidden) {
        bar.classList.remove('hidden');
        bar.style.display = 'flex';
        if (btn) btn.classList.add('bg-zinc-100', 'border-zinc-400');
    } else {
        bar.classList.add('hidden');
        bar.style.display = 'none';
        if (btn) btn.classList.remove('bg-zinc-100', 'border-zinc-400');
    }
};

// ============================================================
// CALENDAR: Live Client-Side Filtering
// ============================================================
window.coraFilterCalendar = function() {
    const statusFilter = (document.getElementById('cal-filter-status')?.value || '').toLowerCase();
    const typeFilter = (document.getElementById('cal-filter-type')?.value || '').toLowerCase();
    const channelFilter = (document.getElementById('cal-filter-channel')?.value || '').toLowerCase();
    const ownerFilter = document.getElementById('cal-filter-owner')?.value || '';

    document.querySelectorAll('.cora-cal-event-card').forEach(card => {
        const status = (card.dataset.status || '').toLowerCase();
        const type = (card.dataset.type || '').toLowerCase();
        const owner = card.dataset.owner || '';
        
        let show = true;
        if (statusFilter && !status.includes(statusFilter)) show = false;
        if (typeFilter && !type.includes(typeFilter)) show = false;
        if (ownerFilter && owner !== ownerFilter) show = false;

        card.style.display = show ? '' : 'none';
    });

    // Hide entire empty columns after filter
    document.querySelectorAll('.cora-cal-day-cell').forEach(cell => {
        const visibleCards = [...cell.querySelectorAll('.cora-cal-event-card')].filter(c => c.style.display !== 'none');
        const emptyPlaceholder = cell.querySelector('.cora-cal-empty-placeholder');
        if (emptyPlaceholder) {
            emptyPlaceholder.style.display = visibleCards.length === 0 ? '' : 'none';
        }
    });
};

window.coraResetCalendarFilters = function() {
    ['cal-filter-type', 'cal-filter-status', 'cal-filter-channel', 'cal-filter-owner'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    document.querySelectorAll('.cora-cal-event-card').forEach(card => {
        card.style.display = '';
    });
    document.querySelectorAll('.cora-cal-empty-placeholder').forEach(ph => {
        ph.style.display = '';
    });
};

// ============================================================
// CALENDAR: Drag & Drop Rescheduling
// ============================================================
let _calDragPostId = null;
let _calDragOriginDate = null;

window.coraCalDragStart = function(e, postId, originDate) {
    _calDragPostId = postId;
    _calDragOriginDate = originDate;
    e.dataTransfer.setData('text/plain', postId);
    e.dataTransfer.effectAllowed = 'move';
    e.currentTarget.classList.add('opacity-50', 'scale-95');
};

window.coraCalDragEnd = function(e) {
    e.currentTarget.classList.remove('opacity-50', 'scale-95');
};

window.coraCalDragOver = function(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
};

window.coraCalDragEnter = function(e) {
    e.preventDefault();
    const cell = e.currentTarget;
    cell.classList.add('ring-2', 'ring-zinc-900', 'ring-inset', 'bg-zinc-50');
};

window.coraCalDragLeave = function(e) {
    const cell = e.currentTarget;
    if (!cell.contains(e.relatedTarget)) {
        cell.classList.remove('ring-2', 'ring-zinc-900', 'ring-inset', 'bg-zinc-50');
    }
};

window.coraCalDrop = function(e, dayNum, dateStr) {
    e.preventDefault();
    const cell = e.currentTarget;
    cell.classList.remove('ring-2', 'ring-zinc-900', 'ring-inset', 'bg-zinc-50');

    const postId = e.dataTransfer.getData('text/plain') || _calDragPostId;
    if (!postId) return;
    if (dateStr === _calDragOriginDate) return; // dropped on same day, no-op

    // AJAX reschedule
    if (typeof $ !== 'undefined' && window.coraREWPData) {
        $.post(coraREWPData.ajaxUrl, {
            action: 'cora_update_article_date',
            nonce: coraREWPData.ajaxNonce,
            post_id: postId,
            target_date: dateStr
        }, function(r) {
            if (window.coraShowToast) {
                if (r.success) {
                    window.coraShowToast('Article rescheduled to ' + dateStr, 'success');
                } else {
                    window.coraShowToast('Failed to reschedule article', 'error');
                }
            }
            if (r.success) {
                setTimeout(() => window.location.reload(), 400);
            }
        }).fail(function() {
            if (window.coraShowToast) window.coraShowToast('Network error while rescheduling', 'error');
        });
    }
};

// ============================================================
// CALENDAR: Click Empty Day Cell -> Open Create Drawer w/ Date Pre-filled
// ============================================================
window.coraCalDayClick = function(e, dateStr) {
    if (e.target.closest('.cora-cal-event-card') || e.target.closest('button')) return;
    if (typeof window.openCreateArticleDrawer === 'function') {
        window.openCreateArticleDrawer();
    }
    const dateInput = document.getElementById('ca-date');
    if (dateInput) dateInput.value = dateStr;
};

// ============================================================
// CALENDAR: Board Tab -> Switch to Workflow View
// ============================================================
window.coraCalSwitchToBoard = function() {
    if (typeof window.switchContentTab === 'function') {
        window.switchContentTab('ct-workflow');
    }
};

// ============================================================
// DRAWER CLOSE (scoped to content-suite drawers only)
// ============================================================
window.closeCreateArticleDrawer = function() {
    const sheet = document.getElementById('cora-create-article-sheet');
    if (sheet) sheet.classList.add('collapsed');
    const seoSheet = document.getElementById('cora-seo-detail-sheet');
    if (seoSheet) seoSheet.classList.add('collapsed');
    const bd = document.getElementById('cora-drawer-backdrop');
    if (bd) { bd.classList.add('hidden'); }
    if (typeof window.coraCloseAllDrawers === 'function') window.coraCloseAllDrawers();
};"""

lines = content.split('\n')
start_idx = -1
end_idx = -1

for i, line in enumerate(lines):
    if line.strip() == "// --- Calendar Drag & Drop + Day Events Drawer Logic ---":
        start_idx = i
        break

if start_idx != -1:
    for i in range(start_idx, len(lines)):
        if "window.switchContentTab = function(tabId) {" in lines[i]:
            end_idx = i
            break

if start_idx != -1 and end_idx != -1:
    # Indent the new JS
    indented_new_js = "\n".join(["    " + l if l else "" for l in new_js.split("\n")])
    
    new_lines = lines[:start_idx] + indented_new_js.split("\n") + [""] + lines[end_idx:]
    with open(file_path, "w") as f:
        f.write("\n".join(new_lines))
    print("Replaced successfully!")
else:
    print(f"Could not find bounds. start: {start_idx}, end: {end_idx}")

