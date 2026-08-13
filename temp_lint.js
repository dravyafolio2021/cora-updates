
// PWA Installation & Push Subscription Logic
let coraPwaDeferredPrompt;

function coraShowPwaPrompt() {
    const neverPrompt = localStorage.getItem('cora_pwa_never_prompt') === 'true';
    const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    
    if (neverPrompt || isStandalone) return;
    
    setTimeout(() => {
        const banner = document.getElementById('cora-pwa-prompt-banner');
        if (banner) {
            banner.classList.remove('translate-y-12', 'opacity-0', 'pointer-events-none');
            banner.classList.add('translate-y-0', 'opacity-100');
        }
    }, 2000);
}

function coraHidePwaPrompt() {
    const banner = document.getElementById('cora-pwa-prompt-banner');
    if (banner) {
        banner.classList.remove('translate-y-0', 'opacity-100');
        banner.classList.add('translate-y-12', 'opacity-0', 'pointer-events-none');
    }
}

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    coraPwaDeferredPrompt = e;
    window.coraPwaDeferredPrompt = e; // Expose globally for manual PWA install button in Settings
    const installBtn = document.getElementById('cora-pwa-install-btn');
    if (installBtn) {
        installBtn.classList.remove('hidden');
    }
    coraShowPwaPrompt();
});

function coraUrlB64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

function coraRequestPushSubscription() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        if (window.coraShowToast) {
            window.coraShowToast('Push notifications are not supported in this browser.', 'error');
        }
        return;
    }
    
    Notification.requestPermission().then(permission => {
        if (permission !== 'granted') {
            if (window.coraShowToast) {
                window.coraShowToast('Notification permission denied.', 'error');
            }
            return;
        }
        
        navigator.serviceWorker.ready.then(registration => {
            if (!window.coraPwaVapidPublicKey) {
                if (window.coraShowToast) {
                    window.coraShowToast('Push services not configured yet. Try reloading.', 'error');
                }
                return;
            }
            
            const applicationServerKey = coraUrlB64ToUint8Array(window.coraPwaVapidPublicKey);
            registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: applicationServerKey
            })
            .then(subscription => {
                fetch('/wp-json/cora-pwa/v1/save-subscription', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': window.coraPwaNonce
                    },
                    body: JSON.stringify(subscription)
                })
                .then(res => res.json())
                .then(resData => {
                    if (resData.success) {
                        if (window.coraShowToast) {
                            window.coraShowToast('Notifications enabled successfully!', 'success');
                        }
                        
                        const token = resData.data.token;
                        navigator.serviceWorker.register('/* PHP_CODE */?v=/* PHP_CODE */&token=' + token, { scope: '/' })
                            .then(() => {
                                const badge = document.getElementById('cora-pwa-badge');
                                if (badge) {
                                    badge.innerText = 'Active';
                                    badge.className = 'text-[9px] font-bold px-1.5 py-0.5 bg-emerald-600 text-white rounded uppercase';
                                }
                                const pushBtn = document.getElementById('cora-pwa-push-btn');
                                if (pushBtn) pushBtn.classList.add('hidden');
                                const testBtn = document.getElementById('cora-pwa-test-btn');
                                if (testBtn) testBtn.classList.remove('hidden');
                                const statusText = document.getElementById('cora-pwa-status-text');
                                if (statusText) statusText.innerText = 'Notifications are active on this device.';
                            });
                    } else {
                        if (window.coraShowToast) {
                            window.coraShowToast('Failed to save subscription on server.', 'error');
                        }
                    }
                })
                .catch(err => {
                    console.error('Subscription save error:', err);
                    if (window.coraShowToast) {
                        window.coraShowToast('Error connecting to notification server.', 'error');
                    }
                });
            })
            .catch(err => {
                console.error('Push registration error:', err);
                if (window.coraShowToast) {
                    window.coraShowToast('Failed to subscribe to browser push notifications.', 'error');
                }
            });
        });
    });
}

function coraSendTestPushNotification() {
    jQuery.ajax({
        url: '/* PHP_CODE */',
        type: 'POST',
        data: {
            action: 'cora_pwa_send_test_push',
            nonce: window.coraAjaxNonce
        },
        success: function(res) {
            if (res.success) {
                if (window.coraShowToast) {
                    window.coraShowToast(res.data, 'success');
                }
            } else {
                if (window.coraShowToast) {
                    window.coraShowToast(res.data, 'error');
                }
            }
        },
        error: function() {
            if (window.coraShowToast) {
                window.coraShowToast('Failed to trigger test notification.', 'error');
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const installBtn = document.getElementById('cora-pwa-install-btn');
    if (installBtn) {
        installBtn.addEventListener('click', (e) => {
            if (!coraPwaDeferredPrompt) return;
            coraPwaDeferredPrompt.prompt();
            coraPwaDeferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    installBtn.classList.add('hidden');
                }
                coraPwaDeferredPrompt = null;
            });
        });
    }

    // Prompt banner buttons
    const promptInstall = document.getElementById('cora-pwa-prompt-install');
    if (promptInstall) {
        promptInstall.addEventListener('click', () => {
            if (!coraPwaDeferredPrompt) return;
            coraPwaDeferredPrompt.prompt();
            coraPwaDeferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    if (installBtn) installBtn.classList.add('hidden');
                }
                coraHidePwaPrompt();
                coraPwaDeferredPrompt = null;
            });
        });
    }

    const promptDismiss = document.getElementById('cora-pwa-prompt-dismiss');
    if (promptDismiss) {
        promptDismiss.addEventListener('click', () => {
            coraHidePwaPrompt();
        });
    }

    const promptNever = document.getElementById('cora-pwa-prompt-never');
    if (promptNever) {
        promptNever.addEventListener('click', () => {
            localStorage.setItem('cora_pwa_never_prompt', 'true');
            coraHidePwaPrompt();
        });
    }
    
    // Auto check if running inside standalone PWA
    if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
        if (installBtn) installBtn.classList.add('hidden');
        const statusText = document.getElementById('cora-pwa-status-text');
        if (statusText) statusText.innerText = 'App is installed and running.';
    }
    
    // Auto check push status
    if ('serviceWorker' in navigator && 'PushManager' in window) {
        navigator.serviceWorker.ready.then(registration => {
            registration.pushManager.getSubscription().then(subscription => {
                if (subscription) {
                    const badge = document.getElementById('cora-pwa-badge');
                    if (badge) {
                        badge.innerText = 'Active';
                        badge.className = 'text-[9px] font-bold px-1.5 py-0.5 bg-emerald-600 text-white rounded uppercase';
                    }
                    const pushBtn = document.getElementById('cora-pwa-push-btn');
                    if (pushBtn) pushBtn.classList.add('hidden');
                    const testBtn = document.getElementById('cora-pwa-test-btn');
                    if (testBtn) testBtn.classList.remove('hidden');
                    const statusText = document.getElementById('cora-pwa-status-text');
                    if (statusText) statusText.innerText = 'Notifications are active on this device.';
                }
            });
        });
    }
});
