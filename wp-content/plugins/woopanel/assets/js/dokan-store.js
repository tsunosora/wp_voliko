(function($) {
'use strict';
	var xhr_view = null;
	
	if( typeof Cookies.get('woopanel_wc_layout') != 'undefined' ) {
		if( $('.woopanel-wc-store-layout').length > 0 && Cookies.get('woopanel_wc_layout') == 'list' ) {
			switch_list_layout('list');
			$('.woopanel-wc-store-layout .wpl-icon-store').removeClass('active');
			$('.woopanel-wc-store-layout .wpl-icon-store.list-layout').addClass('active');
		}
	}

	$(document).on('change', '.woopanel-wc-store-filter .wpl-orderby', function(e) {
		e.preventDefault();
		var $val = $(this).val(),
			$store_url = $('.woopanel-wc-store').attr('data-store_url');

		window.location.href = $store_url + '?orderby=' + $val;
	});

	$(document).on('click', '.woopanel-wc-store-layout .wpl-icon-store', function(e) {
		e.preventDefault();
		
		var $this = $(this),
			layout = $this.attr('data-layout');

		switch_list_layout(layout);
		$('.woopanel-wc-store-layout .wpl-icon-store').removeClass('active');
		$(this).addClass('active');
	});
	
	function switch_list_layout(name) {
		if( name == 'list') {
			$( ".products .product" ).each(function( index ) {
				var $each = $(this),
					$html = $each.find('.woopanel-list-wc-wrap')[0].outerHTML;

				if( $each.find('>.woopanel-list-wc-wrap').length <= 0 ) {
					console.log('outerHTML');
					$each.append($html);
				}
			});

			$( '.products' ).addClass('product-list');
			$( '.product > .woopanel-list-wc-wrap' ).css('display', 'flex');
		}else {
			$( '.products' ).removeClass('product-list');
			$( '.product > .woopanel-list-wc-wrap' ).hide();
		}

		Cookies.set( 'woopanel_wc_layout', name );
	}
})(jQuery);