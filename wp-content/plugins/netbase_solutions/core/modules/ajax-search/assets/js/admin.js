jQuery( function( $ ) {

	var $el = $( '#woocommerce-product-data' );
	/**
	 * Variations Price Matrix actions
	 */
	var wc_ajaxcart_admin = {

		/**
		 * Initialize variations actions
		 */
		init: function() {
        	$(document).on('click', '.wc_ajax_cart_icon li', this.select_icon);
		},
		
		/**
		 * Initial load variations
		 *
		 * @return {Bool}
		 */
		select_icon: function() {
			$('.wc_ajax_cart_icon li').removeClass('active');
			$(this).find('[type="radio"]').prop("checked", true);
			$(this).addClass('active');

		}
	}
	
	wc_ajaxcart_admin.init();

});