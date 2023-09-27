(function($) {
'use strict';
    jQuery.nbMediaUploader({
        target : '#featured_image_container',
        action : 'get_featured',
        inputId : '#_thumbnail_id',
        uploaderTitle : WooPanel.label.i18n_featured_image,
        btnSelect : WooPanel.label.i18n_set_featured_image,
        btnSetId : '#set-post-thumbnail',
        btnSetText : WooPanel.label.i18n_set_featured_image,
        btnRemoveId : '#remove-post-thumbnail',
        multiple : false,
    });

    jQuery.nbMediaUploader({
        uploaderTitle : WooPanel.label.i18n_add_gallery,
        btnSelect : WooPanel.label.i18n_add_gallery,
        target : '#gallery_images_container',
        btnSetId : '#add_gallery_images:not(.disable-link)',
        action: 'get_gallery',
        inputId : '#_image_gallery',
        multiple : 'add',
    });
	
	if( jQuery('.tax_tags').length > 0 ) {
		jQuery.nbTagsBox({
			inputId: '#' + jQuery('.tax_tags').attr('data-id')
		});
	}
})(jQuery);