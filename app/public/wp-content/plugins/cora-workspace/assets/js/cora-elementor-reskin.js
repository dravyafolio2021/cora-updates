/**
 * Cora Elementor Reskin JS
 * Injects a custom two-row Cora-branded toolbar into the Elementor editor,
 * matching the Canvas editor control panel experience.
 * Also white-labels WordPress/Elementor branding references.
 */

(function () {
    'use strict';

    function initCoraReskin() {
        if (window.self !== window.top && document.body) {
            document.body.classList.add('cora-in-iframe');
        }

        // Override browser alert inside the editor
        window.alert = function (msg) {
            if (window.coraShowToast) {
                window.coraShowToast(msg);
            } else if (window.parent && window.parent.coraShowToast) {
                window.parent.coraShowToast(msg);
            } else {
                console.log('Intercepted Elementor Alert:', msg);
            }
        };

        // Override browser confirm inside the editor
        window.confirm = function (msg) {
            if (window.coraShowToast) {
                window.coraShowToast(msg);
            } else if (window.parent && window.parent.coraShowToast) {
                window.parent.coraShowToast(msg);
            } else {
                console.log('Intercepted Elementor Confirm:', msg);
            }
            return true; // Auto-confirm
        };

        // Override browser prompt inside the editor
        window.prompt = function (msg, defaultText) {
            if (window.coraShowToast) {
                window.coraShowToast(msg);
            } else if (window.parent && window.parent.coraShowToast) {
                window.parent.coraShowToast(msg);
            } else {
                console.log('Intercepted Elementor Prompt:', msg);
            }
            return defaultText || '';
        };

        // Auto-click "Take Over" modal button if it appears
        function handleTakeOver() {
            var buttons = document.querySelectorAll('button');
            for (var i = 0; i < buttons.length; i++) {
                if (buttons[i].textContent.trim() === 'Take Over') {
                    buttons[i].click();
                    break;
                }
            }
        }
        handleTakeOver();

        // Auto-intercept preview error dialogs and transform into solutions
        function handlePreviewErrorDialog() {
            var dialogs = document.querySelectorAll('.elementor-dialog-type-alert, .dialog-widget');
            for (var i = 0; i < dialogs.length; i++) {
                var d = dialogs[i];
                if (d.offsetWidth > 0 || d.offsetHeight > 0 || (d.style && d.style.display !== 'none')) {
                    var msg = d.querySelector('.dialog-message, .dialog-content');
                    if (msg && (msg.innerText.indexOf('The preview could not be loaded') !== -1 || msg.innerText.indexOf('error 404') !== -1 || msg.innerText.indexOf('error 403') !== -1 || msg.innerText.indexOf('preview debug') !== -1)) {
                        var header = d.querySelector('.dialog-header, .dialog-title, h3');
                        if (header) header.textContent = 'Canvas Live Sync Ready';
                        var postId = (window.location.search.match(/post=(\d+)/) || [])[1] || '';
                        var directUrl = window.location.pathname + '?post=' + postId + '&action=elementor';
                        msg.innerHTML = '<div style="font-family:Inter,sans-serif;color:#52525b;font-size:13px;line-height:1.5;margin-bottom:14px;">' +
                            '<p style="margin-bottom:6px;font-weight:600;color:#18181b;">Page canvas has been provisioned and saved.</p>' +
                            '<p style="margin:0;">To edit without iframe sandbox restrictions, launch direct canvas mode below.</p>' +
                            '</div>';
                        var btns = d.querySelector('.dialog-buttons-wrapper');
                        if (btns) {
                            btns.innerHTML = '<div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px;">' +
                            '<button onclick="location.reload()" style="display:inline-flex;align-items:center;background:#f4f4f5;color:#18181b;border:1px solid #e4e4e7;font-size:12px;font-weight:500;padding:8px 14px;border-radius:8px;cursor:pointer;font-family:Inter,sans-serif;">Retry Sync</button>' +
                            '<a href="' + directUrl + '" target="_blank" style="display:inline-flex;align-items:center;gap:6px;background:#000;color:#fff;font-size:12px;font-weight:600;padding:8px 16px;border-radius:8px;text-decoration:none;font-family:Inter,sans-serif;">Open Direct Canvas ↗</a>' +
                            '</div>';
                        }
                    }
                }
            }
        }
        handlePreviewErrorDialog();

        // Auto-sanitize Elementor Page Layout select dropdown to strictly 2 options
        function sanitizePageLayoutSelect() {
            var selects = document.querySelectorAll('select[data-setting="template"], select[name="template"], .elementor-control-template select');
            for (var i = 0; i < selects.length; i++) {
                var sel = selects[i];
                if (sel.getAttribute('data-cora-layout-sanitized') === 'true') continue;
                
                var currentVal = sel.value;
                var isCanvas = currentVal === 'elementor_canvas' || currentVal === 'canvas';
                
                sel.innerHTML = '<option value="default">Default (Header &amp; Footer)</option>' +
                                '<option value="elementor_canvas">Canvas (No Header / Footer)</option>';
                
                sel.value = isCanvas ? 'elementor_canvas' : 'default';
                sel.setAttribute('data-cora-layout-sanitized', 'true');
            }
        }
        sanitizePageLayoutSelect();

        var takeOverAttempts = 0;
        var takeOverInterval = setInterval(function () {
            handleTakeOver();
            handlePreviewErrorDialog();
            sanitizePageLayoutSelect();
            takeOverAttempts++;
            if (takeOverAttempts > 30) {
                clearInterval(takeOverInterval);
            }
        }, 500);

        // Attempt toolbar injection on various Elementor lifecycle hooks
        if (window.elementor) {
            elementor.on('panel:init', function () { setTimeout(injectCoraToolbar, 400); });
        }
        // Retry periodically until successful (covers late-loading editors)
        var retryCount = 0;
        var retryInterval = setInterval(function () {
            injectCoraToolbar();
            handlePreviewErrorDialog();
            sanitizePageLayoutSelect();
            retryCount++;
            if (document.getElementById('cora-editor-toolbar') || retryCount > 30) {
                clearInterval(retryInterval);
            }
        }, 500);

        // MutationObserver for continuous white-labeling + toolbar persistence
        var observer = new MutationObserver(function () {
            handleTakeOver();
            handlePreviewErrorDialog();
            sanitizePageLayoutSelect();
            whiteLabelElementor();
            // Re-inject toolbar if it was removed by Elementor re-renders
            if (!document.getElementById('cora-editor-toolbar')) {
                injectCoraToolbar();
            }
            // Continuously ensure native top bar is hidden
            hideNativeTopBar();
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    /* ═══════════════════════════════════════════════════════
     * TOOLBAR INJECTION
     * ═══════════════════════════════════════════════════════ */

    function injectCoraToolbar() {
        // Skip if inside an iframe (Canvas wraps its own toolbar)
        if (window.self !== window.top) return;

        // If toolbar already exists in DOM, just ensure native bar is hidden
        if (document.getElementById('cora-editor-toolbar')) {
            hideNativeTopBar();
            return;
        }

        // Need body to be present
        if (!document.body) return;

        // Get document info
        var docTitle  = getDocTitle();
        var docType   = getDocType();
        var typeBadge = getTypeBadge(docType);

        var isThemeBuilder = ['header', 'footer', 'single', 'archive', 'error-404'].indexOf(docType) >= 0;

        // Get Cora theme context (passed via wp_localize_script)
        var ctx = window.coraEditorContext || {};
        var themeIsLive = (ctx.themeStatus === 'live');
        var themeLabel = themeIsLive ? 'LIVE' : 'DRAFT';
        var themeBadgeCls = themeIsLive ? 'cora-tb-theme-live' : 'cora-tb-theme-draft';
        var themeStatusText = themeIsLive ? 'Live Theme' : 'Draft Theme';
        var saveStatusDefault = ctx.themeName ? (themeStatusText + ' — ' + escHtml(ctx.themeName)) : 'Ready';

        // Build toolbar HTML
        var toolbar = document.createElement('div');
        toolbar.id = 'cora-editor-toolbar';
        toolbar.innerHTML =
            '<div class="cora-tb-row cora-tb-row1">' +
                '<div class="cora-tb-group">' +
                    '<div class="cora-tb-logo">' +
                        '<span class="cora-tb-logo-text">CORA</span>' +
                    '</div>' +
                    '<button class="cora-tb-back-btn" onclick="window.location.href=\'' + getBackUrl() + '\'">' +
                        '<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>' +
                        'Theme Dashboard' +
                    '</button>' +
                '</div>' +
                '<div class="cora-tb-group">' +
                    '<div class="cora-tb-breadcrumbs">' +
                        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>' +
                        '<span>/</span>' +
                        '<span>Theme Builder</span>' +
                        '<span>/</span>' +
                        '<span class="cora-tb-crumb-active">' + escHtml(docTitle) + '</span>' +
                        '<span class="cora-tb-badge ' + typeBadge.cls + '">' + typeBadge.label + '</span>' +
                    '</div>' +
                    '<div class="cora-tb-divider-v"></div>' +
                    '<div class="cora-tb-save-status">' +
                        '<span class="cora-tb-status-dot' + (themeIsLive ? '' : ' draft') + '" id="cora-tb-status-dot"></span>' +
                        '<span id="cora-tb-status-text">' + saveStatusDefault + '</span>' +
                    '</div>' +
                '</div>' +
                '<div class="cora-tb-group">' +
                    '<button class="cora-tb-text-btn" onclick="coraPreviewTemplate()" title="Preview">' +
                        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>' +
                        'Preview' +
                    '</button>' +
                '</div>' +
            '</div>' +
            '<div class="cora-tb-row cora-tb-row2">' +
                '<div class="cora-tb-group">' +
                    '<button id="cora-tb-add-elements-btn" class="cora-tb-add-elements-btn" onclick="coraOpenElementsPanel()" title="Add Element — open widgets panel">' +
                        '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>' +
                        'Add' +
                    '</button>' +
                    '<div class="cora-tb-divider-v"></div>' +
                    '<button class="cora-tb-text-btn" onclick="coraOpenTemplates()" title="Templates">' +
                        '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>' +
                        'Templates' +
                    '</button>' +
                    '<button class="cora-tb-text-btn" onclick="coraOpenGitDrawer()" title="Git">' +
                        '<svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>' +
                        'Git' +
                    '</button>' +
                    '<button class="cora-tb-text-btn" onclick="coraOpenSettings()" title="Settings">' +
                        '<svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>' +
                        'Settings' +
                    '</button>' +
                    '<div class="cora-tb-divider-v"></div>' +
                    '<button class="cora-tb-icon-btn" onclick="coraRunCmd(\'document/history/undo\')" title="Undo">' +
                        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v6h6"></path><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"></path></svg>' +
                    '</button>' +
                    '<button class="cora-tb-icon-btn" onclick="coraRunCmd(\'document/history/redo\')" title="Redo">' +
                        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 7v6h-6"></path><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3l3 2.7"></path></svg>' +
                    '</button>' +
                '</div>' +
                '<div class="cora-tb-group">' +
                    '<span class="cora-tb-doc-title">' + escHtml(docTitle) + '</span>' +
                    '<div class="cora-tb-divider-v"></div>' +
                    '<div class="cora-tb-devices" id="cora-tb-devices">' +
                        '<button class="cora-tb-device active" data-device="desktop" onclick="coraSwitchDevice(\'desktop\')" title="Desktop">' +
                            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>' +
                        '</button>' +
                        '<button class="cora-tb-device" data-device="tablet" onclick="coraSwitchDevice(\'tablet\')" title="Tablet">' +
                            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>' +
                        '</button>' +
                        '<button class="cora-tb-device" data-device="mobile" onclick="coraSwitchDevice(\'mobile\')" title="Mobile">' +
                            '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>' +
                        '</button>' +
                    '</div>' +
                    (isThemeBuilder ? '' : '<div class="cora-tb-divider-v"></div><span class="cora-tb-theme-badge ' + themeBadgeCls + '">' + themeLabel + '</span>') +
                '</div>' +
                '<div class="cora-tb-group">' +
                    '<button class="cora-tb-text-btn" onclick="coraToggleNavigator()" title="Navigator">' +
                        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="6" y1="3" x2="6" y2="15"></line><circle cx="18" cy="6" r="3"></circle><circle cx="6" cy="18" r="3"></circle><path d="M18 9a9 9 0 0 1-9 9"></path></svg>' +
                        'Navigator' +
                    '</button>' +
                    '<div class="cora-tb-publish-split">' +
                        '<button class="cora-tb-publish-btn" onclick="coraPublishTemplate()">Publish</button>' +
                        '<div class="cora-tb-publish-divider"></div>' +
                        '<button class="cora-tb-publish-chevron" onclick="coraTogglePublishMenu(event)">' +
                            '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>' +
                        '</button>' +
                        '<div class="cora-tb-publish-dropdown" id="cora-tb-publish-dropdown">' +
                            '<button onclick="coraRunCmd(\'document/save/draft\'); coraTogglePublishMenu(event);">Save Draft</button>' +
                            '<button onclick="coraRunCmd(\'document/save/default\'); coraTogglePublishMenu(event);">Save</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        // Insert as first child of body — outside Elementor's editor wrapper so it can't be removed
        document.body.insertBefore(toolbar, document.body.firstChild);

        // Hide the native Elementor top bar
        hideNativeTopBar();

        // Listen for save status changes
        observeSaveStatus();
    }

    /* ═══════════════════════════════════════════════════════
     * TOOLBAR COMMAND HELPERS (exposed globally)
     * ═══════════════════════════════════════════════════════ */

    /* ── Add Elements Panel ─────────────────────────────────────── */
    window.coraOpenElementsPanel = function () {
        var btn = document.getElementById('cora-tb-add-elements-btn');

        // ─────────────────────────────────────────────────────────────
        // ROOT CAUSE: $e.route() and $e.run('panel/open-default') only
        // change the route state — they don't call render() on the
        // categoriesView child. Pressing ESC works because it fires
        // $e.run('document/elements/deselect-all'), which goes through
        // Elementor's editor deselect lifecycle and forces a full render.
        //
        // So our primary strategy is: do exactly what ESC does.
        // ─────────────────────────────────────────────────────────────

        var rendered = false;

        // Strategy 1 — $e API: deselect-all triggers the same render path as ESC
        try {
            if (window.$e && $e.run) {
                // Deselect all elements → triggers panel to re-render widget categories
                $e.run('document/elements/deselect-all');

                // Give the deselect event 80ms to propagate and render,
                // then explicitly route to the Elements tab (not Globals).
                setTimeout(function () {
                    try { $e.route('panel/elements/elements'); } catch (x) {}
                    if (btn) btn.classList.add('active');
                }, 80);

                rendered = true;
            }
        } catch (e1) { rendered = false; }

        // Strategy 2 — Elementor v2: setPage() still triggers a render
        if (!rendered) {
            try {
                if (window.elementor && elementor.getPanelView) {
                    var panelView = elementor.getPanelView();
                    if (panelView) {
                        panelView.setPage('elements');
                        rendered = true;
                        if (btn) btn.classList.add('active');
                    }
                }
            } catch (e2) {}
        }

        // Strategy 3 — Dispatch a real ESC keydown (browser-level, always works)
        if (!rendered) {
            try {
                document.dispatchEvent(new KeyboardEvent('keydown', {
                    key: 'Escape', code: 'Escape', keyCode: 27, which: 27,
                    bubbles: true, cancelable: true
                }));
                setTimeout(function () {
                    if (btn) btn.classList.add('active');
                }, 150);
                rendered = true;
            } catch (e3) {}
        }

        // Register route-change hook ONCE to keep button active state in sync
        if (window.$e && $e.hooks) {
            try {
                if (!window._coraElemPanelHookDone) {
                    window._coraElemPanelHookDone = true;
                    $e.hooks.registerUIAfter('route/run', function () {
                        try {
                            var r = $e.routes.getCurrent('panel') || '';
                            if (btn) btn.classList.toggle('active', r === 'panel/elements/elements');
                        } catch (x) {}
                    });
                }
            } catch (ex) {}
        }
    };



    window.coraOpenTemplates = function () {
        try {
            if (window.$e && $e.run) {
                $e.run('library/open');
            } else {
                var btn = document.querySelector('.elementor-template-library-button, [data-dialog-class="elementor-templates-modal"]');
                if (btn) btn.click();
            }
        } catch (e) {}
    };

    window.coraOpenSettings = function () {
        var selectors = [
            '#elementor-panel-footer-settings',
            '.elementor-panel-footer-settings',
            '.eicon-cog',
            'i.eicon-cog',
            '[data-tooltip="Settings"]',
            'button[aria-label="Settings"]'
        ];
        for (var i = 0; i < selectors.length; i++) {
            try {
                var btn = document.querySelector(selectors[i]);
                if (btn) { btn.click(); return; }
            } catch (e) {}
        }
    };

    window.coraOpenGitDrawer = function () {
        if (window.coraToggleGitDrawer) {
            window.coraToggleGitDrawer();
        } else {
            var btn = document.getElementById('cora-git-topbar-btn');
            if (btn) btn.click();
        }
    };

    window.coraRunCmd = function (command, args) {
        try {
            if (window.$e && window.$e.run) {
                args ? $e.run(command, args) : $e.run(command);
            }
        } catch (e) { /* silent */ }
    };

    window.coraSwitchDevice = function (device) {
        var wrap = document.getElementById('cora-tb-devices');
        if (wrap) {
            wrap.querySelectorAll('.cora-tb-device').forEach(function (btn) {
                btn.classList.toggle('active', btn.getAttribute('data-device') === device);
            });
        }
        try { if ($e && $e.run) { $e.run('editor/responsive/change', { device: device }); return; } } catch (e) {}
        try { if (elementor && elementor.changeDeviceMode) { elementor.changeDeviceMode(device); return; } } catch (e) {}
        try { if (elementor && elementor.channels && elementor.channels.deviceMode) { elementor.channels.deviceMode.trigger('change', device); } } catch (e) {}
    };

    window.coraToggleNavigator = function () {
        // Try Elementor v3 panel route first
        var opened = false;
        try {
            if (window.$e && $e.run) {
                // Elementor v3.x: toggle the navigator panel
                var route = $e.routes ? $e.routes.current : null;
                if (route && route['panel/page'] === 'navigator') {
                    $e.run('panel/open-default', { autoFocusSearch: false });
                } else {
                    $e.run('panel/page-views/navigator');
                }
                opened = true;
            }
        } catch (e1) {
            opened = false;
        }
        if (!opened) {
            // Elementor v2 / fallback: click the native footer navigator button
            var selectors = [
                '.elementor-panel-footer-tool[data-tooltip="Structure"]',
                '.elementor-panel-footer-tool[data-tooltip="Navigator"]',
                '[aria-label="Structure"]',
                '[aria-label="Navigator"]',
                '[data-tooltip="Structure"]',
                '[data-tooltip="Navigator"]',
                '#elementor-panel-footer-navigator',
                '.elementor-panel-footer-navigator'
            ];
            for (var i = 0; i < selectors.length; i++) {
                try {
                    var btn = document.querySelector(selectors[i]);
                    if (btn) { btn.click(); opened = true; break; }
                } catch (e2) {}
            }
        }
    };

    window.coraPreviewTemplate = function () {
        var postId = getPostId();
        if (postId) {
            window.open(window.location.origin + '/?p=' + postId + '&preview=true', '_blank');
        }
    };

    window.coraPublishTemplate = function () {
        updateSaveStatus('Publishing...');
        try {
            if (window.$e && $e.run) {
                $e.run('document/save/publish');
                setTimeout(function () {
                    updateSaveStatus('All changes saved');
                    setPublishBtnState(false);
                }, 1800);
                return;
            }
        } catch (e) {}
        // Fallback: use elementor.saver directly
        try {
            if (window.elementor && elementor.saver) {
                elementor.saver.saveDocument({ status: 'publish' });
                setTimeout(function () {
                    updateSaveStatus('All changes saved');
                    setPublishBtnState(false);
                }, 1800);
            }
        } catch (e2) {}
    };


    window.coraToggleProfileMenu = function (e) {
        if (e) e.stopPropagation();
        var dd = document.getElementById('cora-tb-profile-dropdown');
        if (!dd) return;
        var isVisible = dd.classList.contains('visible');
        // Close any other open dropdowns first
        document.querySelectorAll('.cora-tb-publish-dropdown, .cora-tb-profile-dropdown').forEach(function(d) {
            d.classList.remove('visible');
        });
        if (!isVisible) {
            dd.classList.add('visible');
            setTimeout(function () {
                document.addEventListener('click', function closeDD(evt) {
                    var wrap = document.getElementById('cora-tb-profile-wrap');
                    if (wrap && !wrap.contains(evt.target)) {
                        dd.classList.remove('visible');
                        document.removeEventListener('click', closeDD);
                    }
                });
            }, 10);
        }
    };

    window.coraTogglePublishMenu = function (e) {
        if (e) e.stopPropagation();
        var dd = document.getElementById('cora-tb-publish-dropdown');
        if (dd) dd.classList.toggle('visible');
        setTimeout(function () {
            document.addEventListener('click', function closeDD() {
                if (dd) dd.classList.remove('visible');
                document.removeEventListener('click', closeDD);
            }, { once: true });
        }, 10);
    };

    /* ═══════════════════════════════════════════════════════
     * STATUS & HELPERS
     * ═══════════════════════════════════════════════════════ */

    /* Publish button enabled/disabled state */
    function setPublishBtnState(hasChanges) {
        var publishBtn  = document.querySelector('.cora-tb-publish-btn');
        var chevronBtn  = document.querySelector('.cora-tb-publish-chevron');
        var splitWrap   = document.querySelector('.cora-tb-publish-split');
        if (!publishBtn) return;
        publishBtn.disabled  = !hasChanges;
        if (chevronBtn) chevronBtn.disabled = !hasChanges;
        if (splitWrap) splitWrap.classList.toggle('cora-publish-idle', !hasChanges);
    }

    function observeSaveStatus() {
        // Start ENABLED — always let the user publish.
        // We'll gray it out only after a successful save,
        // and re-enable it again when a change is detected.
        setPublishBtnState(true);

        // Bind Elementor change listeners. Elementor may not be ready
        // at toolbar-inject time (especially in Theme Builder), so we
        // retry every 500ms until listeners are attached.
        var _bound = false;
        var _attempts = 0;
        var _maxAttempts = 30; // give up after ~15 s

        function tryBind() {
            if (_bound || _attempts++ > _maxAttempts) return;

            if (!window.elementor) return; // retry next tick

            try {
                // ── Saver events (before/after save) ──
                if (elementor.saver && !elementor.saver._coraBound) {
                    elementor.saver._coraBound = true;
                    elementor.saver.on('before:save', function () {
                        updateSaveStatus('Saving...');
                    });
                    elementor.saver.on('after:save', function () {
                        updateSaveStatus('All changes saved');
                        setPublishBtnState(false);
                    });
                }

                // ── Data channel: marks button enabled on any change ──
                if (elementor.channels && elementor.channels.data && !elementor.channels.data._coraBound) {
                    elementor.channels.data._coraBound = true;
                    elementor.channels.data.on('change', function () {
                        updateSaveStatus('Unsaved changes');
                        setPublishBtnState(true);
                    });
                    _bound = true; // primary listener attached
                }

                // ── elementor.on('change') — Elementor v3 backup ──
                if (elementor.on && !elementor._coraChangeBound) {
                    elementor._coraChangeBound = true;
                    elementor.on('change', function () {
                        updateSaveStatus('Unsaved changes');
                        setPublishBtnState(true);
                    });
                    _bound = true;
                }
            } catch (e) {}
        }

        // Try immediately, then retry every 500 ms until successful
        tryBind();
        var bindInterval = setInterval(function () {
            tryBind();
            if (_bound || _attempts > _maxAttempts) clearInterval(bindInterval);
        }, 500);
    }


    function updateSaveStatus(text) {
        var dot = document.getElementById('cora-tb-status-dot');
        var el  = document.getElementById('cora-tb-status-text');
        if (el) el.textContent = text;
        if (dot) {
            dot.classList.toggle('saving', text.indexOf('Saving') >= 0 || text.indexOf('Publishing') >= 0);
        }
    }

    function hideNativeTopBar() {
        // Hide native Elementor top bar elements.
        // IMPORTANT: Do NOT hide #elementor-editor-wrapper-v2 itself — in Elementor 3.24+,
        // the floating "+" add-widget button lives inside that wrapper. Only hide the
        // top-bar/header direct children inside it, not the wrapper container.
        var topBarSelectors = [
            'header.MuiAppBar-root',
            '.e-top-bar',
            '#e-top-bar',
            '.elementor-editor-top-bar'
        ];
        topBarSelectors.forEach(function (sel) {
            var el = document.querySelector(sel);
            if (el) {
                el.style.setProperty('display', 'none', 'important');
            }
        });

        // Target only the top-bar direct children inside #elementor-editor-wrapper-v2
        var v2Wrapper = document.querySelector('#elementor-editor-wrapper-v2');
        if (v2Wrapper) {
            // Ensure the wrapper itself is visible (it houses the add-widget button)
            v2Wrapper.style.removeProperty('display');
            // Hide only the direct top-bar header children
            var v2ChildSelectors = ['> header', '> .e-top-bar', '> .elementor-editor-top-bar'];
            v2ChildSelectors.forEach(function (childSel) {
                var child = v2Wrapper.querySelector(':scope ' + childSel);
                if (child) {
                    child.style.setProperty('display', 'none', 'important');
                }
            });
            // Also hide MUI AppBar direct children
            Array.from(v2Wrapper.children).forEach(function (child) {
                if (child.classList && (
                    child.classList.contains('MuiAppBar-root') ||
                    Array.from(child.classList).some(function(c) { return c.indexOf('MuiAppBar') === 0; })
                )) {
                    child.style.setProperty('display', 'none', 'important');
                }
            });
        }
    }


    function getDocTitle() {
        try {
            if (window.elementor && elementor.config) {
                var doc = elementor.config.initial_document || elementor.config.document;
                if (doc) {
                    return (doc.settings && doc.settings.settings && doc.settings.settings.post_title) ||
                           doc.post_title || 'Untitled';
                }
            }
        } catch (e) {}
        // Fallback: parse the page title
        var t = document.title.replace(/\s*[\-–—]\s*Elementor.*$/i, '').trim();
        return t || 'Untitled';
    }

    function getDocType() {
        try {
            if (window.elementor && elementor.config) {
                var doc = elementor.config.initial_document || elementor.config.document;
                if (doc && doc.type) return doc.type;
            }
        } catch (e) {}
        var title = document.title.toLowerCase();
        if (title.indexOf('header') >= 0) return 'header';
        if (title.indexOf('footer') >= 0) return 'footer';
        if (title.indexOf('single') >= 0) return 'single';
        if (title.indexOf('archive') >= 0) return 'archive';
        if (title.indexOf('404') >= 0) return 'error-404';
        return 'page';
    }

    function getTypeBadge(docType) {
        var map = {
            'header':    { label: 'HEADER',  cls: 'cora-tb-badge-header' },
            'footer':    { label: 'FOOTER',  cls: 'cora-tb-badge-footer' },
            'single':    { label: 'SINGLE',  cls: 'cora-tb-badge-single' },
            'archive':   { label: 'ARCHIVE', cls: 'cora-tb-badge-archive' },
            'error-404': { label: '404',     cls: 'cora-tb-badge-404' },
            'page':      { label: 'PAGE',    cls: 'cora-tb-badge-page' },
            'wp-page':   { label: 'PAGE',    cls: 'cora-tb-badge-page' },
            'wp-post':   { label: 'POST',    cls: 'cora-tb-badge-page' },
        };
        return map[docType] || { label: docType.toUpperCase(), cls: 'cora-tb-badge-page' };
    }

    function getPostId() {
        var params = new URLSearchParams(window.location.search);
        return params.get('post') || '';
    }

    function getBackUrl() {
        if (typeof coraEditorContext !== 'undefined' && coraEditorContext.canvasUrl) {
            return coraEditorContext.canvasUrl;
        }
        var wsSlug = (typeof coraEditorContext !== 'undefined' && coraEditorContext.wsSlug) ? coraEditorContext.wsSlug : 'workspace';
        return window.location.origin + '/' + wsSlug + '/canvas';
    }

    function escHtml(str) {
        var div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    /* ═══════════════════════════════════════════════════════
     * WHITE-LABELING
     * ═══════════════════════════════════════════════════════ */

    function whiteLabelElementor() {
        // Hide panel footer exit options
        document.querySelectorAll('#elementor-panel-footer-exit-to-dashboard, .elementor-panel-menu-item-exit, .elementor-panel-menu-item-exit-to-dashboard').forEach(function (el) {
            el.style.setProperty('display', 'none', 'important');
        });

        // Hide menu items containing WordPress/Exit to references
        document.querySelectorAll('.elementor-panel-menu-item, .elementor-panel-menu-item-title, [class*="menu-item"]').forEach(function (el) {
            var text = el.textContent || '';
            if (text.toLowerCase().indexOf('wordpress') >= 0 || text.toLowerCase().indexOf('exit to') >= 0) {
                el.style.setProperty('display', 'none', 'important');
                var parentLi = el.closest('li');
                if (parentLi) parentLi.style.setProperty('display', 'none', 'important');
            }
        });

        // Hide Angie, Checklist, What's New
        document.querySelectorAll('[data-tooltip="Angie"], [aria-label="Angie"], [title="Angie"], [data-tooltip="Checklist"], [aria-label="Checklist"], [title="Checklist"]').forEach(function (el) {
            el.style.setProperty('display', 'none', 'important');
            var wrapper = el.closest('span');
            if (wrapper) wrapper.style.setProperty('display', 'none', 'important');
        });

        // Hide onboarding toasts
        var toast = document.getElementById('elementor-toast');
        if (toast) toast.style.setProperty('display', 'none', 'important');

        // Remove "Edit with AI" sparkle buttons
        document.querySelectorAll('.e-ai-button, [class*="e-ai-button"]').forEach(function (el) { el.remove(); });

        // Block plugin install / upsell notice banners
        document.querySelectorAll(
            '.elementor-control-notice, [class*="elementor-control-notice"], ' +
            '.e-notice, .e-notice-bar, [class*="e-notice"], ' +
            '.elementor-promotion, .elementor-upgrade-notice, ' +
            '.elementor-go-pro, [class*="go-pro"]'
        ).forEach(function (el) { el.remove(); });

        // Scrub install-plugin CTAs
        document.querySelectorAll('a[href*="install-plugin"], button[data-settings*="install-plugin"]').forEach(function (el) {
            var notice = el.closest('.elementor-control-notice, [class*="elementor-control-notice"]');
            if (notice) notice.remove(); else el.remove();
        });
    }

    /* ═══════════════════════════════════════════════════════
     * INIT
     * ═══════════════════════════════════════════════════════ */

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCoraReskin);
    } else {
        initCoraReskin();
    }

})();
