jQuery(document).ready(function($) {
	jQuery('#number-box .widget-title').each(function(){
		jQuery("#number-box .widget-title").lettering();
	});   
	jQuery('.widget_maxmegamenu h3.widget-title').toggle(function() {
		  jQuery('.widget_maxmegamenu .mega-menu-wrap').fadeOut('fast');
	}, function() {
		jQuery('.widget_maxmegamenu .mega-menu-wrap').fadeIn('fast');
	});
});
