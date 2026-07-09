/**
 * Cora Elementor Reskin JS
 * Intercepts Elementor UI events and routes them through the Cora Toast System.
 */
document.addEventListener('DOMContentLoaded', function() {
    
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

});
