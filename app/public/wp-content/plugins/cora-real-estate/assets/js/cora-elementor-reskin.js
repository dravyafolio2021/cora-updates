/**
 * Cora Elementor Reskin JS
 * Restores default Elementor panels, widgets, tabs, and titles.
 * Injects only the custom topbar navigation and breadcrumbs layout.
 */
function initCoraReskin() {
    // Override the default browser alert inside the editor iframe
    window.alert = function(msg) {
        if (window.parent && window.parent.coraShowToast) {
            window.parent.coraShowToast(msg);
        } else {
            console.log("Intercepted Elementor Alert: ", msg);
        }
    };

    // Wait for Elementor topbar to render, then inject custom breadcrumbs and exit button
    const checkTopbar = setInterval(() => {
        const topbar = document.querySelector('#elementor-editor-wrapper-v2 header, #editor-one-top-bar, #e-top-bar, .e-top-bar');
        if (topbar) {
            clearInterval(checkTopbar);
            
            if (!document.getElementById('cora-editor-injected-nav')) {
                const navContainer = document.createElement('div');
                navContainer.id = 'cora-editor-injected-nav';
                navContainer.className = 'cora-editor-injected-nav';
                
                const exitBtn = document.createElement('button');
                exitBtn.className = 'cora-injected-exit-btn';
                exitBtn.innerHTML = `
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    Theme Dashboard
                `;
                exitBtn.onclick = function() {
                    if (window.parent && window.parent.closeElementorEditor) {
                        window.parent.closeElementorEditor();
                    }
                };

                const separator = document.createElement('div');
                separator.className = 'cora-injected-separator';

                const breadcrumbs = document.createElement('div');
                breadcrumbs.className = 'cora-injected-breadcrumbs';
                
                const themeTitle = (window.parent && window.parent.canvasState && window.parent.canvasState.activeThemeName) 
                    ? window.parent.canvasState.activeThemeName 
                    : 'Theme';
                const pageTitle = (window.parent && window.parent.document.getElementById('editor-page-title')) 
                    ? window.parent.document.getElementById('editor-page-title').innerText 
                    : 'Page';

                breadcrumbs.innerHTML = `
                    <span>Canvas</span>
                    <span>/</span>
                    <span class="cora-injected-crumb-theme">${themeTitle}</span>
                    <span>/</span>
                    <span class="cora-injected-crumb-page">${pageTitle}</span>
                `;

                navContainer.appendChild(exitBtn);
                navContainer.appendChild(separator);
                navContainer.appendChild(breadcrumbs);

                topbar.insertBefore(navContainer, topbar.firstChild);
            }
        }
    }, 200);

    // Auto-click "Take Over" modal button if it appears
    const checkTakeOver = setInterval(() => {
        const dialogButtons = document.querySelectorAll('button');
        dialogButtons.forEach(btn => {
            if (btn.textContent.trim() === 'Take Over') {
                btn.click();
            }
        });
    }, 500);

    // MutationObserver to white-label WordPress references and logos dynamically
    const observer = new MutationObserver(() => {
        // Hide panel footer exit options
        document.querySelectorAll('#elementor-panel-footer-exit-to-dashboard, .elementor-panel-menu-item-exit, .elementor-panel-menu-item-exit-to-dashboard').forEach(el => {
            el.style.setProperty('display', 'none', 'important');
        });
        
        // Hide menu items or text blocks containing WordPress/Exit to references
        document.querySelectorAll('.elementor-panel-menu-item, .elementor-panel-menu-item-title, [class*="menu-item"]').forEach(el => {
            const text = el.textContent || '';
            if (text.toLowerCase().includes('wordpress') || text.toLowerCase().includes('exit to')) {
                el.style.setProperty('display', 'none', 'important');
                const parentLi = el.closest('li');
                if (parentLi) {
                    parentLi.style.setProperty('display', 'none', 'important');
                }
            }
        });

        // Hide specific top bar features: Angie, Checklist, What's New
        document.querySelectorAll('[data-tooltip="Angie"], [aria-label="Angie"], [title="Angie"], [data-tooltip="Checklist"], [aria-label="Checklist"], [title="Checklist"], [data-tooltip="What\'s New"], [aria-label="What\'s New"], [title="What\'s New"]').forEach(el => {
            el.style.setProperty('display', 'none', 'important');
            const wrapper = el.closest('span');
            if (wrapper) {
                wrapper.style.setProperty('display', 'none', 'important');
            }
        });
        // Hide native Elementor header topbar
        const nativeHeader = document.querySelector('header.MuiAppBar-root, .e-top-bar, #e-top-bar, .elementor-editor-top-bar');
        if (nativeHeader) {
            nativeHeader.style.setProperty('display', 'none', 'important');
        }
        // Replace Elementor Logo button with Cora logo
        document.querySelectorAll('button').forEach(btn => {
            const svg = btn.querySelector('svg');
            if (svg) {
                const title = svg.querySelector('title');
                if (title && title.textContent === 'Elementor Logo') {
                    btn.setAttribute('title', 'Cora Logo');
                    btn.setAttribute('aria-label', 'Cora Logo');
                    btn.setAttribute('data-tooltip', 'Cora Logo');
                    const logoUrl = window.location.origin + '/wp-content/plugins/cora-real-estate/assets/images/cora-favicon.png';
                    btn.innerHTML = `<img src="${logoUrl}" style="width: 20px; height: 20px; object-fit: contain;" alt="Cora Logo" />`;
                }
            }
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCoraReskin);
} else {
    initCoraReskin();
}
