(function($) {
'use strict';

	if( typeof WooPanel_Checkout != 'undefined' ) {
		$('#billing_address_1').val(WooPanel_Checkout.address);
		$('#billing_city').val(WooPanel_Checkout.city);
		$('#billing_phone').val(WooPanel_Checkout.phone);
		$('#billing_country').val(WooPanel_Checkout.country);
	}
	
})(jQuery);