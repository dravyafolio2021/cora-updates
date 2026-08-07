/**
 * Lovable Studio — Drawer + Wizard + Prompt Builder + Page Mapper
 * Enqueued on the Canvas admin page.
 * Depends on: lovable-prompts.js, jQuery, window.CORA_LOVABLE_ROUTES, window.CORA_PAGE_MAPPINGS, window.CORA_GIT_CONFIG
 */
(function($) {
    'use strict';

    var lsState = {
        step: 1,
        style: 'modern',
        layout: 'homepage',
        selected: {}
    };

    // ── Open / Close ────────────────────────────────────────────────────

    window.openLovableStudio = function() {
        var drawer = document.getElementById('lovable-studio-drawer');
        if (!drawer) return;
        drawer.style.display = 'flex';
        lsGoToStep(1);
        lsRenderComponentGrid();
        lsRenderMapperStep();
    };

    window.closeLovableStudio = function() {
        var drawer = document.getElementById('lovable-studio-drawer');
        if (!drawer) return;
        drawer.style.display = 'none';
        if (typeof window.switchTab === 'function') {
            window.switchTab('pages');
        }
    };

    // ── Step navigation ─────────────────────────────────────────────────

    window.lsGoToStep = function(n) {
        lsState.step = n;
        for (var i = 1; i <= 5; i++) {
            var el = document.getElementById('ls-step-' + i);
            if (el) el.style.display = (i === n) ? '' : 'none';
        }

        // Wizard bar highlight
        document.querySelectorAll('.ls-step-btn').forEach(function(btn) {
            var s   = parseInt(btn.getAttribute('data-step'), 10);
            var num = btn.querySelector('.ls-step-num');
            if (s === n) {
                btn.style.color = '#09090b';
                btn.style.borderBottomColor = '#09090b';
                if (num) { num.style.background = '#09090b'; num.style.color = '#fff'; num.textContent = s; }
            } else if (s < n) {
                btn.style.color = '#22c55e';
                btn.style.borderBottomColor = 'transparent';
                if (num) { num.style.background = '#f0fdf4'; num.style.color = '#16a34a'; num.textContent = '\u2713'; }
            } else {
                btn.style.color = '#71717a';
                btn.style.borderBottomColor = 'transparent';
                if (num) { num.style.background = '#f4f4f5'; num.style.color = '#71717a'; num.textContent = s; }
            }
        });

        var scroll = document.getElementById('ls-step-scroll');
        if (scroll) scroll.scrollTop = 0;

        if (n === 2) lsBuildPrompt();
        if (n === 5) lsRenderMapperStep();
    };

    // ── Style & Layout pickers ──────────────────────────────────────────

    window.lsSetStyle = function(style) {
        lsState.style = style;
        document.querySelectorAll('#ls-style-picker button').forEach(function(btn) {
            var active = btn.getAttribute('data-style') === style;
            btn.style.background  = active ? '#18181b' : '#fff';
            btn.style.color       = active ? '#fff'    : '#3f3f46';
            btn.style.borderColor = active ? '#18181b' : '#e4e4e7';
        });
    };

    window.lsSetLayout = function(layout) {
        lsState.layout = layout;
        document.querySelectorAll('#ls-layout-picker button').forEach(function(btn) {
            var active = btn.getAttribute('data-layout') === layout;
            btn.style.background  = active ? '#18181b' : '#fff';
            btn.style.color       = active ? '#fff'    : '#3f3f46';
            btn.style.borderColor = active ? '#18181b' : '#e4e4e7';
        });
    };

    // ── Component Grid ──────────────────────────────────────────────────

    function lsRenderComponentGrid() {
        var grid = document.getElementById('ls-component-grid');
        if (!grid || !window.CORA_PROMPT_LIBRARY) return;
        var comps = window.CORA_PROMPT_LIBRARY.COMPONENTS;
        var html  = '';
        Object.keys(comps).forEach(function(id) {
            var c       = comps[id];
            var checked = !!lsState.selected[id];
            var border  = checked ? '#09090b' : '#e4e4e7';
            var bg      = checked ? '#f9f9f9' : '#fff';
            var iconBg  = checked ? '#09090b' : '#f4f4f5';
            var iconCol = checked ? '#fff'    : '#71717a';
            var chkBg   = checked ? '#09090b' : '#fff';
            var chkBdr  = checked ? '#09090b' : '#d4d4d8';
            var tick    = checked ? '<svg viewBox="0 0 12 12" width="10" height="10" stroke="white" stroke-width="2.5" fill="none"><polyline points="2 6 5 9 10 3"/></svg>' : '';
            var attrLine = c.coraAttr
                ? '<div style="margin-top:5px;font-size:9.5px;font-family:monospace;background:' + (checked?'#e4e4e7':'#f4f4f5') + ';padding:2px 7px;border-radius:4px;color:#3f3f46;display:inline-block;">' + c.coraAttr + '</div>'
                : '<div style="margin-top:5px;font-size:9.5px;color:#a1a1aa;">Static &mdash; no backend needed</div>';
            html +=
                '<div onclick="lsToggleComponent(\'' + id + '\')" id="ls-comp-' + id + '" style="border:1.5px solid ' + border + ';border-radius:10px;padding:12px;cursor:pointer;background:' + bg + ';transition:all .15s;user-select:none;">' +
                  '<div style="display:flex;align-items:flex-start;gap:10px;">' +
                    '<div style="width:34px;height:34px;background:' + iconBg + ';border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:' + iconCol + ';transition:all .15s;">' + c.icon + '</div>' +
                    '<div style="flex:1;min-width:0;">' +
                      '<div style="font-size:12px;font-weight:700;color:#09090b;margin-bottom:2px;">' + c.title + '</div>' +
                      '<div style="font-size:11px;color:#71717a;line-height:1.4;">' + c.description + '</div>' +
                      attrLine +
                    '</div>' +
                    '<div style="width:16px;height:16px;border-radius:4px;border:1.5px solid ' + chkBdr + ';background:' + chkBg + ';flex-shrink:0;display:flex;align-items:center;justify-content:center;margin-top:2px;">' + tick + '</div>' +
                  '</div>' +
                '</div>';
        });
        grid.innerHTML = html;
    }

    window.lsToggleComponent = function(id) {
        lsState.selected[id] = !lsState.selected[id];
        lsRenderComponentGrid();
    };

    // ── Prompt Builder ──────────────────────────────────────────────────

    function lsBuildPrompt() {
        if (!window.CORA_PROMPT_LIBRARY) return;
        var selected = Object.keys(lsState.selected).filter(function(k) { return lsState.selected[k]; });
        var prompt   = window.CORA_PROMPT_LIBRARY.buildPrompt(selected, lsState.style, lsState.layout);
        var ta       = document.getElementById('ls-prompt-output');
        if (ta) ta.value = prompt;

        // Static note visibility
        var hasAttrs = selected.some(function(id) { return !!(window.CORA_PROMPT_LIBRARY.COMPONENTS[id] && window.CORA_PROMPT_LIBRARY.COMPONENTS[id].coraAttr); });
        var note = document.getElementById('ls-prompt-static-note');
        if (note) note.style.display = (selected.length && !hasAttrs) ? '' : 'none';

        // Tech chips
        var chips = document.getElementById('ls-tech-chips');
        if (chips) {
            var chipHtml = '';
            selected.forEach(function(id) {
                var c = window.CORA_PROMPT_LIBRARY.COMPONENTS[id];
                if (c && c.coraAttr) {
                    chipHtml += '<span style="padding:3px 10px;background:#f4f4f5;border:1px solid #e4e4e7;border-radius:12px;font-size:10px;font-weight:600;color:#3f3f46;font-family:monospace;">' + c.coraAttr + '</span>';
                }
            });
            chips.innerHTML = chipHtml || '<span style="font-size:11px;color:#a1a1aa;">No backend attributes required for selected components.</span>';
        }
    }

    window.lsCopyPrompt = function() {
        var ta = document.getElementById('ls-prompt-output');
        if (!ta) return;
        var btn = document.getElementById('ls-copy-btn');
        try {
            navigator.clipboard.writeText(ta.value).then(function() {
                if (btn) { var o = btn.textContent; btn.textContent = '\u2713 Copied!'; btn.style.color = '#22c55e'; setTimeout(function() { btn.textContent = o; btn.style.color = '#d4d4d8'; }, 2000); }
            });
        } catch(e) {
            ta.select();
            document.execCommand('copy');
            if (window.coraShowToast) window.coraShowToast('Prompt copied!', 'success');
        }
    };

    // ── Sync Handler ────────────────────────────────────────────────────

    window.lsTriggerSync = function() {
        var repo   = (document.getElementById('ls-repo-url')    || {value: ''}).value.trim();
        var branch = (document.getElementById('ls-repo-branch') || {value: 'main'}).value.trim() || 'main';
        var token  = (document.getElementById('ls-repo-token')  || {value: ''}).value.trim();

        if (!repo) {
            if (window.coraShowToast) window.coraShowToast('Please enter a GitHub repository URL in Step 3.', 'error');
            lsGoToStep(3);
            return;
        }

        var btn  = document.getElementById('ls-sync-btn');
        var prog = document.getElementById('ls-sync-progress');
        var bar  = document.getElementById('ls-sync-progress-bar');

        if (btn)  { btn.disabled = true; btn.textContent = 'Syncing\u2026'; }
        if (prog) prog.style.display = '';
        if (bar)  { bar.style.width = '0%'; setTimeout(function() { bar.style.width = '70%'; }, 80); }

        $.post(coraREData.ajaxUrl, {
            action:  'cora_trigger_git_sync',
            nonce:   coraREData.ajaxNonce,
            theme_id: window.canvasState ? window.canvasState.activeThemeId : 0,
            repo:    repo,
            branch:  branch,
            token:   token,
            enabled: '1'
        }, function(res) {
            if (bar) bar.style.width = '100%';
            setTimeout(function() {
                if (btn)  { btn.disabled = false; btn.textContent = '\u27f3 Sync Now'; }
                if (prog) prog.style.display = 'none';
                if (bar)  bar.style.width = '0%';

                if (res.success) {
                    var msg = 'Repository synced!';
                    if (res.data && res.data.route_count) msg += ' ' + res.data.route_count + ' routes found.';
                    if (window.coraShowToast) window.coraShowToast(msg, 'success');

                    // Update compat chips in Step 4
                    if (res.data && res.data.compat_flags && res.data.compat_flags.length) {
                        var c = document.getElementById('ls-compat-chips');
                        if (c) c.innerHTML = res.data.compat_flags.map(function(f) {
                            return '<span style="padding:3px 9px;background:#f0fdf4;border:1px solid #86efac;border-radius:12px;font-size:10px;font-weight:600;color:#166534;">\u2713 ' + f + '</span>';
                        }).join('');
                        // Update global
                        if (window.CORA_GIT_CONFIG) window.CORA_GIT_CONFIG.compat_flags = res.data.compat_flags;
                    }

                    // Auto-advance to map step
                    setTimeout(function() { lsGoToStep(5); }, 700);
                } else {
                    var errMsg = (res.data && res.data.message) ? res.data.message : 'Sync failed. Please check your repo details.';
                    if (window.coraShowToast) window.coraShowToast(errMsg, 'error');
                }
            }, 500);
        }).fail(function() {
            if (btn)  { btn.disabled = false; btn.textContent = '\u27f3 Sync Now'; }
            if (prog) prog.style.display = 'none';
            if (window.coraShowToast) window.coraShowToast('Network error during sync.', 'error');
        });
    };

    // ── Page Mapper ─────────────────────────────────────────────────────

    function lsRenderMapperStep() {
        var routes   = window.CORA_LOVABLE_ROUTES || [];
        var mappings = window.CORA_PAGE_MAPPINGS  || {};
        var pages    = (window.canvasState && window.canvasState.pages) || [];

        var emptyEl  = document.getElementById('ls-mapper-empty');
        var tableEl  = document.getElementById('ls-mapper-table');
        var rowsEl   = document.getElementById('ls-mapper-rows');
        var compatEl = document.getElementById('ls-compat-summary');

        if (!routes || !routes.length) {
            if (emptyEl) emptyEl.style.display = '';
            if (tableEl) tableEl.style.display = 'none';
            return;
        }
        if (emptyEl) emptyEl.style.display = 'none';
        if (tableEl) tableEl.style.display = '';

        if (!rowsEl) return;

        var routeOptions = '<option value="">\u2014 Not mapped \u2014</option>' + routes.map(function(r) {
            return '<option value="' + r.path + '">' + r.path + (r.title ? ' (' + r.title + ')' : '') + '</option>';
        }).join('');

        var rows = '';
        var hasPages = false;
        if (pages.length === 0) {
            var trs = document.querySelectorAll('#pages-table-body tr');
            if (trs.length > 0) {
                trs.forEach(function(tr) {
                    var idEl = tr.querySelector('[data-page-id]');
                    if (!idEl) return;
                    var pid  = idEl.getAttribute('data-page-id');
                    var name = tr.querySelector('.page-name-cell') ? tr.querySelector('.page-name-cell').textContent.trim() : 'Page ' + pid;
                    var slug = tr.getAttribute('data-slug') || '';
                    rows += lsMapperRow(pid, name, slug, mappings[pid], routeOptions);
                    hasPages = true;
                });
            }
        } else {
            pages.forEach(function(page) {
                rows += lsMapperRow(page.id, page.name || 'Page', page.slug || '', mappings[page.id], routeOptions);
                hasPages = true;
            });
        }

        if (!hasPages) {
            rows = '<div id="ls-auto-import-panel" style="padding:20px;text-align:center;border:1.5px dashed #ddd6fe;background:#f5f3ff;border-radius:12px;margin-bottom:14px;box-shadow:0 1px 3px rgba(124,58,237,0.05);">' +
                   '  <div style="font-size:13px;font-weight:700;color:#7c3aed;margin-bottom:4px;">Import ' + routes.length + ' Pages Automatically</div>' +
                   '  <p style="font-size:10.5px;color:#6d28d9;line-height:1.45;margin-bottom:12px;">We found ' + routes.length + ' page routes in your repository. Let Cora automatically create and map these pages in WordPress for you.</p>' +
                   '  <button onclick="lsAutoCreatePages()" id="ls-auto-create-btn" style="padding:8px 20px;background:#7c3aed;color:#fff;border:none;border-radius:8px;font-size:11.5px;font-weight:700;cursor:pointer;box-shadow:0 2px 8px rgba(124,58,237,0.18);transition:all 0.15s;" onmouseover="this.style.background=\'#6d28d9\'" onmouseout="this.style.background=\'#7c3aed\'">' +
                   '    Auto-Create & Map Pages' +
                   '  </button>' +
                   '</div>';
        }

        rowsEl.innerHTML = rows;

        // Compat summary
        if (compatEl) {
            var flags = (window.CORA_GIT_CONFIG && window.CORA_GIT_CONFIG.compat_flags) || [];
            if (flags.length) {
                compatEl.innerHTML = '<strong>Bridge Active:</strong> ' + flags.join(', ') + ' &mdash; Cora data attributes detected. Backend features will wire up automatically.';
                compatEl.style.borderColor = '#86efac';
                compatEl.style.background  = '#f0fdf4';
            } else if (routes.length) {
                compatEl.innerHTML = '<strong>Static Mode:</strong> No <code>data-cora-*</code> attributes detected in your repository. Pages will serve as static HTML. To enable live data, use the <strong>Prompt Builder</strong> (Step 2) next time you build in Lovable.';
                compatEl.style.borderColor = '#fde047';
                compatEl.style.background  = '#fefce8';
            } else {
                compatEl.innerHTML = '';
            }
        }
    }

    function lsMapperRow(pid, name, slug, currentRoute, routeOptions) {
        var selected = routeOptions.replace('value="' + (currentRoute || '') + '"', 'value="' + (currentRoute || '') + '" selected');
        return '<div style="display:grid;grid-template-columns:1fr 24px 1fr;gap:8px;align-items:center;">' +
            '<div style="padding:7px 11px;background:#f9f9f9;border:1px solid #e4e4e7;border-radius:8px;font-size:12px;font-weight:600;color:#09090b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="/' + slug + '">' +
                name + (slug ? '<span style="font-size:10px;font-weight:400;color:#a1a1aa;margin-left:4px;">/' + slug + '</span>' : '') +
            '</div>' +
            '<div style="text-align:center;color:#d4d4d8;font-size:16px;">\u2192</div>' +
            '<select onchange="lsMapPage(' + pid + ', this.value)" style="width:100%;padding:7px 10px;border:1px solid #e4e4e7;border-radius:8px;font-size:11px;color:#09090b;background:#fff;outline:none;cursor:pointer;">' + selected + '</select>' +
        '</div>';
    }

    window.lsMapPage = function(pageId, route) {
        if (typeof window.savePageLovableMapping === 'function') {
            window.savePageLovableMapping(pageId, route);
        }
        if (!window.CORA_PAGE_MAPPINGS) window.CORA_PAGE_MAPPINGS = {};
        if (route) { window.CORA_PAGE_MAPPINGS[pageId] = route; }
        else { delete window.CORA_PAGE_MAPPINGS[pageId]; }
    };

    window.lsAutoCreatePages = function() {
        var btn = document.getElementById('ls-auto-create-btn');
        if (btn) { btn.disabled = true; btn.textContent = 'Creating Pages\u2026'; }
        
        var themeId = window.canvasState ? window.canvasState.activeThemeId : 0;
        
        $.post(coraREData.ajaxUrl, {
            action: 'cora_ajax_auto_create_lovable_pages',
            nonce: coraREData.ajaxNonce,
            theme_id: themeId
        }, function(res) {
            if (res.success) {
                if (window.coraShowToast) window.coraShowToast(res.data.message || 'Pages created successfully!', 'success');
                setTimeout(function() { window.location.reload(); }, 1000);
            } else {
                var err = (res.data && res.data.message) ? res.data.message : 'Failed to auto-create pages.';
                if (window.coraShowToast) window.coraShowToast(err, 'error');
                if (btn) { btn.disabled = false; btn.textContent = 'Auto-Create & Map Pages'; }
            }
        }).fail(function() {
            if (window.coraShowToast) window.coraShowToast('Network error while creating pages.', 'error');
            if (btn) { btn.disabled = false; btn.textContent = 'Auto-Create & Map Pages'; }
        });
    };

    // ── Init on DOMReady ────────────────────────────────────────────────

    $(document).ready(function() {
        // Pre-select popular components for first-time users
        if (!Object.keys(lsState.selected).length) {
            lsState.selected = { 'property-grid': true, 'lead-form': true, 'hero-banner': true };
        }

        // Restore saved step from sessionStorage
        var savedStep = parseInt(sessionStorage.getItem('ls_step') || '1', 10);
        if (savedStep >= 1 && savedStep <= 5) lsState.step = savedStep;
    });

})(jQuery);
