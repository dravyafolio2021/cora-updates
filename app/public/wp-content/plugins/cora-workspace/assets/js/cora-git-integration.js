/**
 * Cora Git Integration — Elementor Editor
 * Injects the Git button into the top bar, renders the drawer UI,
 * and auto-commits to GitHub on every Elementor Publish action.
 *
 * Runs inside the Elementor editor iframe.
 */
(function () {
    'use strict';

    var DATA = window.coraGitData || {};
    var AJAX = DATA.ajax_url || '/wp-admin/admin-ajax.php';
    var NONCE = DATA.nonce || '';

    var state = {
        connected: false,
        username: '',
        repo: '',
        has_repo: false,
        has_clientid: true,
        branches: ['main'],
        currentBranch: 'main',
        recentCommits: [],
        drawerOpen: false,
        autoCommit: true,
        pollingTimer: null,
    };

    /* ── AJAX ──────────────────────────────────────────────────────────────── */
    function ajax(action, data) {
        var params = new URLSearchParams(Object.assign({ action: action, nonce: NONCE }, data || {}));
        return fetch(AJAX, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: params.toString() }).then(function (r) { return r.json(); });
    }

    /* ── Toast (forward to parent Cora toast) ────────────────────────────── */
    function toast(msg, type) {
        if (window.parent && window.parent.coraShowToast) {
            window.parent.coraShowToast(msg, type || 'info');
        }
    }

    /* ── Inject Git button (replace Kit) ─────────────────────────────────── */
    function injectGitButton() {
        var interval = setInterval(function () {
            // Hide Kit button
            var kitBtn = null;
            document.querySelectorAll('button').forEach(function (btn) {
                var label = (btn.getAttribute('data-tooltip') || btn.getAttribute('aria-label') || btn.textContent || '').trim().toLowerCase();
                if (label === 'kit') kitBtn = btn;
            });
            if (kitBtn) {
                kitBtn.style.setProperty('display', 'none', 'important');
                var kitWrap = kitBtn.closest('li, [class*="ToolbarItem"], [class*="toolbar-item"]');
                if (kitWrap) kitWrap.style.setProperty('display', 'none', 'important');
            }

            // Inject only once
            if (document.getElementById('cora-git-topbar-btn')) { clearInterval(interval); return; }

            // Find the toolbar area (Elementor v3 uses MUI toolbar)
            var toolbar = document.querySelector('[class*="e-top-bar__main-area"], .e-top-bar .MuiToolbar-root, header .MuiToolbar-root');
            if (!toolbar) return;

            clearInterval(interval);

            var btn = document.createElement('button');
            btn.id = 'cora-git-topbar-btn';
            btn.className = 'cora-git-topbar-btn';
            btn.setAttribute('aria-label', 'Git Version Control');
            btn.innerHTML =
                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
                '<circle cx="18" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/>' +
                '<path d="M6 9v6"/><path d="M15.5 5.5 18 9"/><path d="M15.5 18.5 18 15"/>' +
                '</svg>' +
                '<span>Git</span>';
            btn.addEventListener('click', toggleDrawer);

            // Insert before the device-switcher group or at end of toolbar
            var devSwitcher = toolbar.querySelector('[class*="DeviceSwitcher"], [class*="device-switcher"]');
            if (devSwitcher) {
                toolbar.insertBefore(btn, devSwitcher);
            } else {
                toolbar.appendChild(btn);
            }
        }, 400);
        setTimeout(function () { clearInterval(interval); }, 20000);
    }

    /* ── Drawer shell ────────────────────────────────────────────────────── */
    function buildDrawer() {
        if (document.getElementById('cora-git-drawer')) return;
        var d = document.createElement('div');
        d.id = 'cora-git-drawer';
        d.className = 'cora-git-drawer';
        d.innerHTML =
            '<div class="cora-git-drawer-header">' +
            '  <div class="cora-git-drawer-title">' +
            '    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M6 9v6"/><path d="M15.5 5.5 18 9"/><path d="M15.5 18.5 18 15"/></svg>' +
            '    Git' +
            '  </div>' +
            '  <button id="cora-git-close" class="cora-git-close-btn" aria-label="Close Git drawer">' +
            '    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
            '  </button>' +
            '</div>' +
            '<div id="cora-git-body" class="cora-git-body"><div class="cora-git-loading"><div class="cora-git-spinner"></div><span>Loading…</span></div></div>';
        document.body.appendChild(d);
        document.getElementById('cora-git-close').addEventListener('click', closeDrawer);
    }

    function toggleDrawer() { state.drawerOpen ? closeDrawer() : openDrawer(); }
    function openDrawer() {
        buildDrawer();
        document.getElementById('cora-git-drawer').classList.add('cora-git-drawer--open');
        state.drawerOpen = true;
        loadStatus();
    }
    function closeDrawer() {
        var d = document.getElementById('cora-git-drawer');
        if (d) d.classList.remove('cora-git-drawer--open');
        state.drawerOpen = false;
        clearInterval(state.pollingTimer);
    }

    /* ── Load status ─────────────────────────────────────────────────────── */
    function loadStatus() {
        setBody('<div class="cora-git-loading"><div class="cora-git-spinner"></div><span>Checking connection…</span></div>');
        ajax('cora_github_get_status').then(function (res) {
            if (!res.success) { renderError(res.data && res.data.message ? res.data.message : 'Could not load status.'); return; }
            var d = res.data;
            state.connected = !!d.connected;
            state.username = d.username || '';
            state.repo = d.repo || '';
            state.has_repo = !!d.has_repo;
            state.has_clientid = !!d.has_clientid;
            state.branches = d.branches || ['main'];
            state.recentCommits = d.recent_commits || [];
            if (!d.connected) renderNotConnected();
            else if (!d.has_repo) renderCreateRepo();
            else renderDashboard();
        }).catch(function () { renderError('Network error. Check your connection.'); });
    }

    /* ── Not connected ───────────────────────────────────────────────────── */
    function renderNotConnected() {
        var setupNote = !state.has_clientid
            ? '<p class="cora-git-warn-note">⚠ GitHub OAuth Client ID is not configured yet. Ask your admin to add it in WordPress Settings.</p>'
            : '';
        setBody(
            '<div class="cora-git-connect-state">' +
            '  <div class="cora-git-big-icon">' +
            '    <svg width="36" height="36" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>' +
            '  </div>' +
            '  <h3 class="cora-git-title">Connect GitHub</h3>' +
            '  <p class="cora-git-desc">Link your site to GitHub for automatic version control. Every publish creates a commit.</p>' +
            setupNote +
            '  <button id="cora-git-start-connect" class="cora-git-primary-btn">Connect with GitHub</button>' +
            '</div>'
        );
        document.getElementById('cora-git-start-connect').addEventListener('click', startDeviceFlow);
    }

    /* ── Device Flow ─────────────────────────────────────────────────────── */
    function startDeviceFlow() {
        setBody('<div class="cora-git-loading"><div class="cora-git-spinner"></div><span>Connecting to GitHub…</span></div>');
        ajax('cora_github_initiate_device_flow').then(function (res) {
            if (!res.success) {
                var msg = (res.data && res.data.message) ? res.data.message : 'Failed to connect.';
                renderError(msg);
                return;
            }
            var d = res.data;
            var interval = Math.max((d.interval || 5), 5) * 1000;
            renderDeviceCode(d.user_code, d.verification_uri, interval);
        });
    }

    function renderDeviceCode(code, uri, interval) {
        setBody(
            '<div class="cora-git-device-flow">' +
            '  <h3 class="cora-git-title">Authorize on GitHub</h3>' +
            '  <p class="cora-git-desc">Go to GitHub and enter this code:</p>' +
            '  <div class="cora-git-code-box">' +
            '    <span id="cora-git-code-text">' + code + '</span>' +
            '    <button id="cora-git-copy-code" class="cora-git-copy-btn" title="Copy">' +
            '      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>' +
            '    </button>' +
            '  </div>' +
            '  <a href="' + uri + '" target="_blank" id="cora-git-open-github" class="cora-git-primary-btn">' +
            '    Open github.com/login/device' +
            '  </a>' +
            '  <div class="cora-git-polling-row">' +
            '    <div class="cora-git-spinner cora-git-spinner--sm"></div>' +
            '    <span>Waiting for your approval…</span>' +
            '  </div>' +
            '  <button id="cora-git-cancel-flow" class="cora-git-link-btn">Cancel</button>' +
            '</div>'
        );

        document.getElementById('cora-git-copy-code').addEventListener('click', function () {
            if (navigator.clipboard) navigator.clipboard.writeText(code);
            this.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>';
        });

        document.getElementById('cora-git-cancel-flow').addEventListener('click', function () {
            clearInterval(state.pollingTimer);
            renderNotConnected();
        });

        state.pollingTimer = setInterval(function () {
            ajax('cora_github_poll_device_token').then(function (res) {
                if (!res.success) { clearInterval(state.pollingTimer); renderError((res.data && res.data.message) || 'Authorization failed.'); return; }
                if (res.data.status === 'connected') {
                    clearInterval(state.pollingTimer);
                    state.connected = true;
                    state.username = res.data.username || '';
                    renderCreateRepo();
                }
            });
        }, interval);
    }

    /* ── Create repo ─────────────────────────────────────────────────────── */
    function renderCreateRepo() {
        var suggested = 'cora-' + (document.title.split(/[—|·]/)[0] || 'website').trim().toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        setBody(
            '<div class="cora-git-connect-state">' +
            '  <div class="cora-git-success-icon">' +
            '    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>' +
            '  </div>' +
            '  <h3 class="cora-git-title">Connected as @' + state.username + '</h3>' +
            '  <p class="cora-git-desc">Create a private repository to start versioning your site automatically.</p>' +
            '  <div class="cora-git-field">' +
            '    <label class="cora-git-label">Repository name</label>' +
            '    <input id="cora-git-repo-name-input" class="cora-git-input" type="text" value="' + suggested + '" placeholder="cora-my-website" />' +
            '  </div>' +
            '  <button id="cora-git-create-repo-btn" class="cora-git-primary-btn">Create Private Repository</button>' +
            '  <p class="cora-git-footnote">A private repo will be created in your GitHub account. No public access.</p>' +
            '</div>'
        );
        document.getElementById('cora-git-create-repo-btn').addEventListener('click', function () {
            var name = document.getElementById('cora-git-repo-name-input').value.trim();
            var btn  = document.getElementById('cora-git-create-repo-btn');
            btn.disabled = true; btn.textContent = 'Creating…';
            ajax('cora_github_create_repo', { repo_name: name }).then(function (res) {
                if (!res.success) { btn.disabled = false; btn.textContent = 'Create Private Repository'; toast((res.data && res.data.message) || 'Failed.', 'error'); return; }
                state.repo = res.data.repo; state.has_repo = true; state.branches = ['main']; state.recentCommits = [];
                toast('Repository "' + res.data.repo + '" created!', 'success');
                renderDashboard();
            });
        });
    }

    /* ── Dashboard ───────────────────────────────────────────────────────── */
    function renderDashboard() {
        var repoName = state.repo ? state.repo.split('/')[1] : '';
        var branchOpts = (state.branches.length ? state.branches : ['main']).map(function (b) {
            return '<option value="' + b + '"' + (b === state.currentBranch ? ' selected' : '') + '>' + b + '</option>';
        }).join('');

        var commitsHtml = state.recentCommits.length
            ? state.recentCommits.map(function (c) {
                return '<a href="' + c.url + '" target="_blank" class="cora-git-commit-row">' +
                    '<code class="cora-git-sha">' + c.sha + '</code>' +
                    '<span class="cora-git-commit-msg">' + (c.message.split('\n')[0] || '').substring(0, 60) + '</span>' +
                    '</a>';
            }).join('')
            : '<p class="cora-git-empty">No commits yet. Publish a page to create your first commit.</p>';

        setBody(
            '<div class="cora-git-dashboard">' +

            '  <div class="cora-git-status-row">' +
            '    <span class="cora-git-dot"></span>' +
            '    <span class="cora-git-username">@' + state.username + '</span>' +
            '    <span class="cora-git-sep">·</span>' +
            '    <a href="https://github.com/' + state.repo + '" target="_blank" class="cora-git-repo-link">' + repoName + '</a>' +
            '  </div>' +

            '  <div class="cora-git-section">' +
            '    <div class="cora-git-row">' +
            '      <label class="cora-git-label">Branch</label>' +
            '      <div class="cora-git-branch-controls">' +
            '        <select id="cora-git-branch-sel" class="cora-git-select">' + branchOpts + '</select>' +
            '        <button id="cora-git-new-branch" class="cora-git-icon-btn" title="Create branch"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg></button>' +
            '      </div>' +
            '    </div>' +
            '    <div id="cora-git-new-branch-row" style="display:none" class="cora-git-new-branch-row">' +
            '      <input id="cora-git-new-branch-name" class="cora-git-input" placeholder="feature/my-branch" />' +
            '      <button id="cora-git-create-branch-btn" class="cora-git-sm-btn">Create</button>' +
            '      <button id="cora-git-cancel-branch" class="cora-git-sm-btn cora-git-sm-btn--ghost">✕</button>' +
            '    </div>' +
            '  </div>' +

            '  <div class="cora-git-section">' +
            '    <label class="cora-git-label">Commit now</label>' +
            '    <input id="cora-git-commit-msg-input" class="cora-git-input" type="text" placeholder="Describe your changes (optional)…" />' +
            '    <button id="cora-git-commit-now-btn" class="cora-git-primary-btn cora-git-mt6">' +
            '      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M2 12h8"/><path d="M14 12h8"/></svg>' +
            '      Commit Now' +
            '    </button>' +
            '  </div>' +

            '  <div class="cora-git-section">' +
            '    <div class="cora-git-toggle-row">' +
            '      <span class="cora-git-label">Auto-commit on Publish</span>' +
            '      <label class="cora-git-toggle">' +
            '        <input type="checkbox" id="cora-git-auto-toggle"' + (state.autoCommit ? ' checked' : '') + '>' +
            '        <span class="cora-git-toggle-track"></span>' +
            '      </label>' +
            '    </div>' +
            '  </div>' +

            '  <div class="cora-git-section">' +
            '    <label class="cora-git-label">Recent commits</label>' +
            '    <div class="cora-git-commits">' + commitsHtml + '</div>' +
            '  </div>' +

            '  <div class="cora-git-section cora-git-footer-section">' +
            '    <button id="cora-git-disconnect-btn" class="cora-git-link-btn cora-git-danger">Disconnect GitHub</button>' +
            '  </div>' +

            '</div>'
        );

        document.getElementById('cora-git-branch-sel').addEventListener('change', function () { state.currentBranch = this.value; });

        document.getElementById('cora-git-new-branch').addEventListener('click', function () {
            document.getElementById('cora-git-new-branch-row').style.display = 'flex';
        });
        document.getElementById('cora-git-cancel-branch').addEventListener('click', function () {
            document.getElementById('cora-git-new-branch-row').style.display = 'none';
        });
        document.getElementById('cora-git-create-branch-btn').addEventListener('click', function () {
            var name = document.getElementById('cora-git-new-branch-name').value.trim();
            if (!name) return;
            ajax('cora_github_create_branch', { branch_name: name, from_branch: state.currentBranch }).then(function (res) {
                if (res.success) { state.branches.push(name); state.currentBranch = name; toast('Branch "' + name + '" created.', 'success'); renderDashboard(); }
                else toast((res.data && res.data.message) || 'Failed.', 'error');
            });
        });

        document.getElementById('cora-git-commit-now-btn').addEventListener('click', function () {
            var msg = document.getElementById('cora-git-commit-msg-input').value.trim();
            doCommit(getPageId(), msg, this);
        });

        document.getElementById('cora-git-auto-toggle').addEventListener('change', function () { state.autoCommit = this.checked; });

        document.getElementById('cora-git-disconnect-btn').addEventListener('click', function () {
            if (!window.confirm('Disconnect GitHub? You can reconnect any time.')) return;
            ajax('cora_github_disconnect').then(function () {
                state.connected = false; state.username = ''; state.repo = ''; state.has_repo = false;
                renderNotConnected();
            });
        });
    }

    /* ── Commit helper ───────────────────────────────────────────────────── */
    function doCommit(pageId, msg, btn) {
        if (!state.connected || !state.has_repo) return;
        if (!pageId) { toast('Could not detect current page ID.', 'error'); return; }
        if (btn) { btn.disabled = true; btn.textContent = 'Committing…'; }
        ajax('cora_github_commit_page', { page_id: pageId, message: msg || '', branch: state.currentBranch }).then(function (res) {
            if (btn) { btn.disabled = false; btn.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M2 12h8"/><path d="M14 12h8"/></svg> Commit Now'; }
            if (res.success) {
                toast('Committed to GitHub ✓', 'success');
                // Refresh commit list in 1s if drawer is open
                if (state.drawerOpen) setTimeout(loadStatus, 1000);
            } else {
                toast((res.data && res.data.message) || 'Commit failed.', 'error');
            }
        });
    }

    /* ── Page ID helper ──────────────────────────────────────────────────── */
    function getPageId() {
        try {
            var cfg = window.elementorFrontend && window.elementorFrontend.config;
            if (!cfg) cfg = window.elementor && window.elementor.config;
            return (cfg && (cfg.document && cfg.document.id || cfg.post_id)) || null;
        } catch (e) { return null; }
    }

    /* ── Hook into Publish ───────────────────────────────────────────────── */
    function hookPublish() {
        // Method 1: $e API hook (Elementor 3.x)
        var eReady = setInterval(function () {
            if (window.$e && window.$e.hooks) {
                clearInterval(eReady);
                try {
                    window.$e.hooks.registerAfterAfter('document/save/save', function () {
                        if (!state.autoCommit) return;
                        setTimeout(function () { doCommit(getPageId(), '', null); }, 800);
                    });
                } catch (e) { /* unsupported */ }
            }
        }, 500);
        setTimeout(function () { clearInterval(eReady); }, 20000);

        // Method 2: MutationObserver watching "All changes saved" text
        var lastSaved = false;
        var saveObs = new MutationObserver(function () {
            var saved = !!document.querySelector('[class*="DocumentSaved"]:not([class*="hidden"]), .e-top-bar [class*="saved"]');
            if (saved && !lastSaved && state.autoCommit && state.connected && state.has_repo) {
                doCommit(getPageId(), '', null);
            }
            lastSaved = saved;
        });
        saveObs.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['class'] });
    }

    /* ── Add button blank panel fix ──────────────────────────────────────── */
    function fixAddButton() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('button');
            if (!btn) return;
            var label = (btn.getAttribute('data-tooltip') || btn.getAttribute('aria-label') || btn.textContent || '').trim();
            if (label === 'Add' || label === '+ Add') {
                setTimeout(function () {
                    if (window.$e) {
                        try { window.$e.route('panel/elements'); } catch (err) {}
                    }
                }, 60);
            }
        }, true);
    }

    /* ── Remove unwanted top bar items ───────────────────────────────────── */
    function removeUnwantedItems() {
        var selectors = [
            // Notes
            '[data-tooltip="Notes"]', '[aria-label="Notes"]', '#elementor-panel-footer-tool-notes',
            // Avatar / user button
            'button[class*="Avatar"]', '[class*="MuiAvatar-root"]',
            // Help (optional — keep if user wants)
        ];
        selectors.forEach(function (sel) {
            document.querySelectorAll(sel).forEach(function (el) {
                var parent = el.closest('li, button, [class*="ToolbarItem"]');
                (parent || el).style.setProperty('display', 'none', 'important');
            });
        });

        // Avatar: find button that wraps an img with src containing gravatar/avatar
        document.querySelectorAll('button').forEach(function (btn) {
            var img = btn.querySelector('img');
            if (img && (img.src.includes('gravatar') || img.src.includes('avatar') || btn.className.toLowerCase().includes('avatar'))) {
                btn.style.setProperty('display', 'none', 'important');
                var wrap = btn.closest('li, [class*="ToolbarItem"]');
                if (wrap) wrap.style.setProperty('display', 'none', 'important');
            }
        });
    }

    /* ── Utilities ───────────────────────────────────────────────────────── */
    function setBody(html) {
        var el = document.getElementById('cora-git-body');
        if (el) el.innerHTML = html;
    }
    function renderError(msg) {
        setBody(
            '<div class="cora-git-error-state">' +
            '  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' +
            '  <p>' + msg + '</p>' +
            '  <button class="cora-git-link-btn" onclick="(function(){document.getElementById && document.getElementById(\'cora-git-body\') && coraGitLoadStatus && coraGitLoadStatus()})()">Try again</button>' +
            '</div>'
        );
        window.coraGitLoadStatus = loadStatus;
    }

    /* ── MutationObserver for dynamic removal ────────────────────────────── */
    function observeAndClean() {
        var obs = new MutationObserver(removeUnwantedItems);
        obs.observe(document.body, { childList: true, subtree: true });
        removeUnwantedItems(); // initial pass
    }

    /* ── Init ────────────────────────────────────────────────────────────── */
    function init() {
        fixAddButton();
        injectGitButton();
        hookPublish();
        observeAndClean();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
