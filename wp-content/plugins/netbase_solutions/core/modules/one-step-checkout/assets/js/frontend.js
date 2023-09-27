jQuery( function( $ ) {

	var nb_checkout_load = {
		/**
		 * Init jQuery.BlockUI
		 */
		block: function($el) {
			$el.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		/**
		 * Remove jQuery.BlockUI
		 */
		unblock: function($el) {
			$el.unblock();
		}
	}
	
	var $supports_html5_storage = true,
		cart_hash_key           = wc_cart_fragments_params.cart_hash_key;
	try {
		$supports_html5_storage = ( 'sessionStorage' in window && window.sessionStorage !== null );
		window.sessionStorage.setItem( 'wc', 'test' );
		window.sessionStorage.removeItem( 'wc' );
		window.localStorage.setItem( 'wc', 'test' );
		window.localStorage.removeItem( 'wc' );
	} catch( err ) {
		$supports_html5_storage = false;
	}
	
	/* Cart session creation time to base expiration on */
	function set_cart_creation_timestamp() {
		if ( $supports_html5_storage ) {
			sessionStorage.setItem( 'wc_cart_created', ( new Date() ).getTime() );
		}
	}
	
	/** Set the cart hash in both session and local storage */
	function set_cart_hash( cart_hash ) {
		if ( $supports_html5_storage ) {
			localStorage.setItem( cart_hash_key, cart_hash );
			sessionStorage.setItem( cart_hash_key, cart_hash );
		}
	}
	
	var $fragment_refresh = {
		url: wc_cart_fragments_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'get_refreshed_fragments' ),
		type: 'POST',
		success: function( data ) {
			if ( data && data.fragments ) {

				$.each( data.fragments, function( key, value ) {
					$( key ).replaceWith( value );
				});

				if ( $supports_html5_storage ) {
					sessionStorage.setItem( wc_cart_fragments_params.fragment_name, JSON.stringify( data.fragments ) );
					set_cart_hash( data.cart_hash );

					if ( data.cart_hash ) {
						set_cart_creation_timestamp();
					}
				}

				$( document.body ).trigger( 'wc_fragments_refreshed' );
			}
		}
	};

	var nb_one_checkout = {
		_init: function() {
			$(document).on('click', '.nb-checkout-row .remove', this.remove_cart);
			$(document).on('click', '.nb-checkout-row .restore-item', this.restore_item);
			$(document).on('change', '.shop-table_responsive .input-text.qty', this.change_qty);
			
			/* ------------------------------------- */
			/*  woocommerce
			/* ------------------------------------- */
			$(document).on('click', '.nb-qty-plus', nb_one_checkout.qty_plus);
			$(document).on('click', '.nb-qty-minus', nb_one_checkout.qty_minus);
			
			this.woocommerce();
		},
		
		woocommerce: function() {
			/* Add Product Quantity Up Down icon */
			$('.product-quantity .quantity').each(function() {
				$(this).append('<div class="qty-buttons"> <span class="quantity-icon nb-qty-plus"></span> <span class="quantity-icon nb-qty-minus"></span></div>');
			});
			
			// if( $('.woocommerce-checkout-payment').length ) {
				// $('.woocommerce-checkout-payment').find('ul.wc_payment_methods').before('<h3>Payment Methods</h3>');
			// }

		},
		
		change_qty: function() {
			var qty = $(this).val();;
			
			nb_one_checkout.re_calculator( qty, $(this).closest('.cart_item').attr('id') );
		},
		
		qty_plus: function() {
            var parent = $(this).closest('.quantity');
			var qty = parseInt($('input.qty', parent).val()) + 1;
			$('input.qty', parent).val( qty );
			
			nb_one_checkout.re_calculator( qty, $(this).closest('.cart_item').attr('id') );
		},
		
		qty_minus: function() {
            var parent = $(this).closest('.quantity');
            if( parseInt($('input.qty', parent).val()) > 1) {
				var qty = parseInt($('input.qty', parent).val()) - 1;
                $('input.qty', parent).val( qty );
				nb_one_checkout.re_calculator( qty, $(this).closest('.cart_item').attr('id') );
            }
		},
		
		refresh_cart_fragment: function() {
			$.ajax( $fragment_refresh );
		},
		
		re_calculator: function(qty, id) {
			nb_checkout_load.block( $('.shop_table') );
			$.ajax({
				url: nbt_solutions.ajax_url,
				type: 'POST',
				data: { 'action': 'nb_solution_change_qty', 'qty': qty, 'cart_item_key': id },
				success: function( rs ) {
					
					if( rs.complete != undefined) {
						$('#' + id).find('.product-subtotal').html(rs.price);
						$('.cart-subtotal > td:last-child').html(rs.subtotal);
						$('.order-total > td:last-child').html(rs.total);
						
						nb_checkout_load.unblock( $('.shop_table') );
					}else {
						nb_checkout_load.unblock( $('.shop_table') );
					}
					
					nb_one_checkout.refresh_cart_fragment();
				}
			});
			
			
		},
		
		remove_cart: function(e) {
			e.preventDefault();
			
			nb_checkout_load.block( $('.shop_table') );
			
			var cart_item_key = $(this).closest('tr').attr('id');

			$.ajax({
				url: nbt_solutions.ajax_url,
				type: 'POST',
				data: { 'action': 'nb_solution_remove', 'cart_item_key': cart_item_key },
				success: function( rs ) {
					
					if( rs.complete != undefined) {
						if( rs.empty != undefined) {
							$('.nb-checkout-row').html('<div class="nb-checkout-col-12">' + rs.cart_template + '</div>');
							nb_one_checkout.refresh_cart_fragment();
						}else {
							$('#nb-checkout-cart').html(rs.cart_template);
							$('#message-nb-checkout').html('<div class="woocommerce-message">' + rs.message + '</div>');
							nb_one_checkout.re_calculator(0, cart_item_key);
							nb_one_checkout.woocommerce();
							$( document.body ).trigger( 'wc_fragments_refreshed' );
						}
					}else {
						nb_checkout_load.unblock( $('.shop_table') );
					}
				}
			});
		},
		
		restore_item: function(e) {
			e.preventDefault();
			
			var cart_item_key = $(this).attr('data-id');
			$.ajax({
				url: nbt_solutions.ajax_url,
				type: 'POST',
				data: { 'action': 'nb_solution_restore', 'cart_item_key': cart_item_key },
				success: function( rs ) {
					if( rs.complete != undefined) {
						$('#nb-checkout-cart').html(rs.cart_template);
						$('#message-nb-checkout').html('<div class="woocommerce-message">' + rs.message + '</div>');
					}
				},
				complete: function() {
				}
			});
		}
	}
	
	if( $('body').hasClass('nb-woocommerce-checkout') ) {
		nb_one_checkout._init();
	}
});