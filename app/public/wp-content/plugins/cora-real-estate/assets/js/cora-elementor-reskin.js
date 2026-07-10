/**
 * Cora Elementor Reskin JS
 * Intercepts Elementor UI events and routes them through the Cora Toast System.
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
    
    // Hide Elementor specific UI popups that can't be caught via pure CSS
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            // Check for Elementor Dialogs (Upsells, Promos)
            const dialogs = document.querySelectorAll('.elementor-dialog-widget-promotion, .elementor-dialog-pro-badge');
            dialogs.forEach(dialog => {
                if (dialog.style.display !== 'none') {
                    dialog.style.display = 'none';
                }
            });
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });

    // Wait for Elementor topbar to render, then inject custom breadcrumbs and exit button
    const checkTopbar = setInterval(() => {
        const topbar = document.querySelector('#elementor-editor-wrapper-v2 header, #editor-one-top-bar, #e-top-bar, .e-top-bar');
        if (topbar) {
            clearInterval(checkTopbar);
            
            // Check if our custom navigation is already injected
            if (!document.getElementById('cora-editor-injected-nav')) {
                // Create a container for our custom navigation
                const navContainer = document.createElement('div');
                navContainer.id = 'cora-editor-injected-nav';
                navContainer.className = 'cora-editor-injected-nav';
                
                // 1. Exit Button
                const exitBtn = document.createElement('button');
                exitBtn.className = 'cora-injected-exit-btn';
                exitBtn.innerHTML = `
                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    Theme Dashboard
                `;
                exitBtn.onclick = function() {
                    // Call the parent window's closeElementorEditor function
                    if (window.parent && window.parent.closeElementorEditor) {
                        window.parent.closeElementorEditor();
                    }
                };

                // 2. Separator
                const separator = document.createElement('div');
                separator.className = 'cora-injected-separator';

                // 3. Breadcrumbs
                const breadcrumbs = document.createElement('div');
                breadcrumbs.className = 'cora-injected-breadcrumbs';
                
                // Get page title and theme name from parent window
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

                // Insert at the very beginning of the topbar (on the left)
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
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCoraReskin);
} else {
    initCoraReskin();
}
