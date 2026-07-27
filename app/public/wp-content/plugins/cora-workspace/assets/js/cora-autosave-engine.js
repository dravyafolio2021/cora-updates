/**
 * Cora Auto-Save Engine
 * Provides client-side local caching and debounced server-side saving.
 */
(function($) {
    'use strict';

    window.coraAutoSave = {
        debounceTimer: null,

        saveLocalDraft: function(moduleKey, data) {
            const timestamp = new Date().getTime();
            const draft = {
                timestamp: timestamp,
                data: data
            };
            try {
                localStorage.setItem('cora_draft_' + moduleKey, JSON.stringify(draft));
            } catch (e) {
                console.warn('Cora AutoSave: localStorage error', e);
            }
        },

        loadLocalDraft: function(moduleKey) {
            try {
                const draftStr = localStorage.getItem('cora_draft_' + moduleKey);
                if (draftStr) {
                    return JSON.parse(draftStr);
                }
            } catch (e) {
                console.warn('Cora AutoSave: parse error', e);
            }
            return null;
        },

        clearLocalDraft: function(moduleKey) {
            try {
                localStorage.removeItem('cora_draft_' + moduleKey);
            } catch (e) {}
        },

        attachForm: function(formIdOrElement, moduleKey, ajaxAction, options) {
            const $form = $(formIdOrElement);
            if (!$form.length) return;
            
            const showToast = (msg) => {
                if (typeof window.coraShowToast === 'function') {
                    window.coraShowToast(msg);
                } else if ($('#cora-autosave-indicator').length) {
                    $('#cora-autosave-indicator').text(msg).show();
                } else {
                    console.log(msg);
                }
            };

            $form.on('input change keyup', 'input, textarea, select, checkbox', (e) => {
                const formDataStr = $form.serialize();
                
                // Save locally instantly (<5ms)
                this.saveLocalDraft(moduleKey, formDataStr);

                if (this.debounceTimer) {
                    clearTimeout(this.debounceTimer);
                }
                
                showToast('• Saving draft...');

                this.debounceTimer = setTimeout(() => {
                    const nonce = typeof coraGetAJAXNonce === 'function' ? coraGetAJAXNonce() : (typeof coraREWPData !== 'undefined' ? coraREWPData.ajaxNonce : '');
                    const ajaxUrl = typeof coraGetAJAXUrl === 'function' ? coraGetAJAXUrl() : (typeof coraREWPData !== 'undefined' ? coraREWPData.ajaxUrl : '/wp-admin/admin-ajax.php');

                    const data = {
                        action: ajaxAction,
                        module_key: moduleKey,
                        draft_data: formDataStr,
                        nonce: nonce
                    };
                    
                    $.post(ajaxUrl, data, (response) => {
                        if (response && response.success) {
                            showToast('✓ Auto-saved');
                        }
                    });
                }, 600);
            });
        },

        autoRestoreForm: function(formIdOrElement, moduleKey) {
            const $form = $(formIdOrElement);
            if (!$form.length) return;
            
            const draft = this.loadLocalDraft(moduleKey);
            if (draft && draft.data) {
                const urlParams = new URLSearchParams(draft.data);
                let restored = false;
                
                urlParams.forEach((value, key) => {
                    const $field = $form.find('[name="' + key + '"]');
                    if ($field.length) {
                        if ($field.is(':checkbox') || $field.is(':radio')) {
                            const $specificField = $field.filter('[value="' + value + '"]');
                            if ($specificField.length && !$specificField.is(':checked')) {
                                $specificField.prop('checked', true);
                                restored = true;
                            }
                        } else {
                            if (!$field.val()) { // Only populate if empty
                                $field.val(value);
                                restored = true;
                            }
                        }
                    }
                });
                
                if (restored && typeof window.coraShowToast === 'function') {
                    window.coraShowToast('Restored from draft');
                }
            }
        }
    };

    $(document).ready(function() {
        $('form[data-autosave-module]').each(function() {
            const $form = $(this);
            const moduleKey = $form.attr('data-autosave-module');
            const ajaxAction = $form.attr('data-autosave-action') || 'cora_save_module_draft';
            
            window.coraAutoSave.autoRestoreForm($form, moduleKey);
            window.coraAutoSave.attachForm($form, moduleKey, ajaxAction);
        });
    });

})(jQuery);
