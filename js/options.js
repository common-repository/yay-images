(function($) {

    $('#yay_save_settings').bind('click', function() {
         
        $('.yay_save_loader').show();
        
        var dataS = {
            action: 'my_action',
            method: 'savesettings',
            formdata: $('#yay_form_settings').serializeArray()
        };
        
        $.post(ajaxurl, dataS, function(r) {
            $('.yay_save_loader').hide();
            $('.yay_settings_saved').show();

            window.setTimeout(function() {
                $('.yay_settings_saved').fadeOut(400);
            }, 4000);

        });
    });
  
})(jQuery);
