(function($) {
'use strict';
    jQuery.nbMediaUploader({
        action : 'get_image',
        uploaderTitle : WooPanel.label.i18n_image_title,
        btnSelect : WooPanel.label.i18n_set_image,
        btnSetText : WooPanel.label.i18n_set_image,
        multiple : false,
    });
	
	jQuery(document).on('click', '#m_nav .m-nav__link', function() {
		var $tab = jQuery(this).attr('href');
		Cookies.set('setting_current_tab', $tab);
	});
	
	var currentTab = Cookies.get('setting_current_tab');
	if( typeof currentTab != 'undefined'  ) {
		jQuery('#m_nav a[href="' + currentTab + '"]').trigger('click');
	}
	
	if( jQuery().timepicker ) {
		jQuery('input.timepicker').timepicker({});
	}
})(jQuery);