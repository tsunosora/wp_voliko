(function($) {
'use strict';
	function getQueryVariable(query, variable) {
		var vars = query.split('&');
		for (var i = 0; i < vars.length; i++) {
			var pair = vars[i].split('=');
			if (decodeURIComponent(pair[0]) == variable) {
				return decodeURIComponent(pair[1]);
			}
		}
	}

	var count = 0;
	jQuery(document).ajaxSend(function(evt, req, set) {
		if( getQueryVariable(set.data, 'action') == 'woopanel_dashboard_save_order' ) {
			count++;
		}	   
	});
	
	jQuery(document).ajaxComplete(function(evt, req, set) {
		if(count == 1) {
			NProgress.done();
		}

		if( getQueryVariable(set.data, 'action') == 'woopanel_dashboard_save_order' ) {
			count--;
		}
	});
	function dashboard_sort() {
		var dashboard_box = [];
		jQuery( "#dashboard-wrapper .dashboard-widget" ).each(function( index ) {
			var $widget = jQuery(this).attr('data-widget');
			dashboard_box.push($widget);
		});
		
		return dashboard_box;
	}
		
	jQuery('.dashboard-box').sortable({
		revert: true,
		placeholder: 'sortable-placeholder',
		connectWith: '.dashboard-box',
		items: '.dashboard-widget',
		handle: '.m-portlet__head',
		cursor: 'move',
		delay: 0,
		distance: 2,
		tolerance: 'pointer',
		forcePlaceholderSize: true,
		revert: true,
		sort: function(e, ui) {
			var $box = jQuery(ui.item).closest('.dashboard-box');
			if( $box.find('.dashboard-widget:not(.ui-sortable-helper)').length <= 0 ) {
				$box.addClass('empty-container');
			}
		},
		stop: function(e, ui) {
			var $box = jQuery(ui.item).closest('.dashboard-box');
			if( $box.find('.dashboard-widget').length > 0 ) {
				$box.removeClass('empty-container');
			}
		},
		
		receive: function(e, ui) {
			NProgress.remove();
			NProgress.start();
			
			_wpl_dashboard_mark_area();
			_wpl_dashboard_save_order();
		}
	});
	
	function _wpl_dashboard_save_order() {
		var postVars = {
			action: 'woopanel_dashboard_save_order',
			widgets: dashboard_sort()
		};
		
		jQuery.post( WooPanel.ajaxurl, postVars );
	}
	
	function _wpl_dashboard_mark_area() {
		var visible = jQuery('div.dashboard-widget:visible').length;
		
		jQuery( '#dashboard-wrapper .dashboard-box:visible' ).each( function() {
			var t = jQuery(this);
			
			if ( visible == 1 || t.children('.dashboard-widget:visible').length ) {
				t.removeClass('empty-container');
			}
			else {
				t.addClass('empty-container');
			}
		});
	}
})(jQuery);