
(function($) {
    if (typeof coraAutoSave !== 'undefined') {
        coraAutoSave.attachForm('#cora-settings-suite-form', 'settings_suite', 'cora_save_system_settings_suite');
    }

    $('#cora-purge-legacy-options').on('click', function(e) {
        e.preventDefault();
        
        var $btn = $(this);
        $btn.prop('disabled', true).text('Purging options...');
        
        $.post(coraREData.ajaxUrl, {
            action: 'cora_purge_options_data',
            nonce: coraREData.ajaxNonce
        }, function(res) {
            if (res.success) {
                window.coraShowToast(res.data || 'Old options cache database tables purged successfully!');
                setTimeout(function() { window.location.reload(); }, 1200);
            } else {
                window.coraShowToast(res.data || 'Failed to purge database options.');
                $btn.prop('disabled', false).text('Purge Old wp_options Cache');
            }
        }).fail(function() {
            window.coraShowToast('A system error occurred during purge.');
            $btn.prop('disabled', false).text('Purge Old wp_options Cache');
        });
    });
})(jQuery);
