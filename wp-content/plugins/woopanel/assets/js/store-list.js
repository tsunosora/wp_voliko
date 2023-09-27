/* global Cookies */
jQuery( function( $ ) {
  // Orderby
	$( '.wpl-store-ordering' ).on( 'change', 'select.orderby', function() {
		$( this ).closest( 'form' ).submit();
	});

	$(document).on('click', 'body.woopanel-style1-layout .addr-sec', function() {
	    $('html,body').animate({
	        scrollTop: $("#asl-map-canv").offset().top
	    });
	});
});
