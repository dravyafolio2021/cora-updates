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
        setInterval(function () {
            document.querySelectorAll('button').forEach(function (btn) {
                if (btn.textContent.trim() === 'Take Over') btn.click();
            });
        }, 500);

        // Attempt toolbar injection on various Elementor lifecycle hooks
        if (window.elementor) {
            elementor.on('panel:init', function () { setTimeout(injectCoraToolbar, 400); });
        }
        // Retry periodically until successful (covers late-loading editors)
        var retryCount = 0;
        var retryInterval = setInterval(function () {
            injectCoraToolbar();
            retryCount++;
            if (document.getElementById('cora-editor-toolbar') || retryCount > 30) {
                clearInterval(retryInterval);
            }
        }, 500);

        // MutationObserver for continuous white-labeling + toolbar persistence
        var observer = new MutationObserver(function () {
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
        // Only publish if there are unsaved changes
        var publishBtn = document.querySelector('.cora-tb-publish-btn');
        if (publishBtn && publishBtn.disabled) return;
        updateSaveStatus('Publishing...');
        try {
            if (window.$e && $e.run) {
                $e.run('document/save/publish');
                setTimeout(function () {
                    updateSaveStatus('All changes saved');
                    setPublishBtnState(false);
                }, 1800);
            }
        } catch (e) {}
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
        // Start in disabled/idle state — nothing unsaved yet
        setPublishBtnState(false);

        if (!window.elementor) return;
        try {
            if (elementor.saver) {
                elementor.saver.on('before:save', function () {
                    updateSaveStatus('Saving...');
                });
                elementor.saver.on('after:save', function () {
                    updateSaveStatus('All changes saved');
                    setPublishBtnState(false);
                });
                // Mark as having changes whenever anything is modified
                if (elementor.channels && elementor.channels.data) {
                    elementor.channels.data.on('change', function () {
                        updateSaveStatus('Unsaved changes');
                        setPublishBtnState(true);
                    });
                }
            }
            // Hook elementor's own hasChanges flag as backup
            if (elementor.on) {
                elementor.on('change', function () {
                    updateSaveStatus('Unsaved changes');
                    setPublishBtnState(true);
                });
            }
        } catch (e) {}
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
        ['header.MuiAppBar-root', '.e-top-bar', '#e-top-bar', '.elementor-editor-top-bar', '#elementor-editor-wrapper-v2'].forEach(function (sel) {
            var el = document.querySelector(sel);
            if (el) {
                el.style.setProperty('display', 'none', 'important');
            }
        });
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
        return window.location.origin + '/wp-admin/admin.php?page=cora-workspace&sub_page=canvas';
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
