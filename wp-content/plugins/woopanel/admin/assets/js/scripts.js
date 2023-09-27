(function($) {
'use strict';

    // The "Upload" button
    jQuery(document).on('click', '.import-vendor', function(e) {
        e.preventDefault();

        var roles = [];
        $("input:checkbox[class=role-input]:checked").each(function(){
            roles.push($(this).val());
        });

        var btn = $(this);
        btn.addClass('wpl-ajax-loading');

        jQuery.ajax({
            url: wc_enhanced_select_params.ajax_url,
            data: {
                action: 'woopanel_import_vendor',
                roles: roles
            },
            type: 'POST',
            datatype: 'json',
            success: function( response ) {
                if(response.complete != undefined ) {
                    btn.removeClass('wpl-ajax-loading');

                    if(response.message != undefined ) {
                        alert(response.message);
                    }
                }
            },
            error:function( xhr, status, error ) {

            }
        });
        
        console.log(roles);
    });

    // The "Upload" button
    jQuery(document).on('click', '.upload_image_button', function() {
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = $(this);
        wp.media.editor.send.attachment = function(props, attachment) {
            $(button).parent().prev().attr('src', attachment.url);
            $(button).prev().val(attachment.id);
            wp.media.editor.send.attachment = send_attachment_bkp;
        }
        wp.media.editor.open(button);
        return false;
    });

    // The "Remove" button (remove the value from input type='hidden')
    jQuery(document).on('click', '.remove_image_button', function() {
        var answer = confirm('Are you sure?');
        if (answer == true) {
            var src = $(this).parent().prev().attr('data-src');
            $(this).parent().prev().attr('src', src);
            $(this).prev().prev().val('');
        }
        return false;
    });

    jQuery(document).ready(function() {
        if( jQuery().select2 ) {
            jQuery('.form-select2').select2();
        }
    });
    

})(jQuery);