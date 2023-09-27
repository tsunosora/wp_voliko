jQuery( function( $ ) {
	
	var nbt_ajaxcart_load = {
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
	var nbt_ajaxcart = {

		/**
		 * Initialize variations actions
		 */
		init: function() {
			
			$(window).load(function() {
				nbt_ajaxcart.load_cal();
			});
			
			if( $('body').hasClass('single-product') && $('.single-product .js_open_desginer_in_new_page').length == 0 && $('.single-product .js_is_edit_mode').length == 0 ) {
				if( $('[name="variation_id"]').length || $('[name="add-to-cart"]').length ) {
					$(document).on( 'submit', '.single-product form.cart', this.add_to_cart );
				}
				
			}
			
        	
			$(document).on( 'added_to_cart', this.added_to_cart );
			$(document).on( 'click', '.nbt-ajax-cart-icon', this.show_ajaxcart_popup );
			$(document).on('click', '.nbt-ajax-cart-popup .remove', this.remove_to_cart);
			
			$(document).mouseup(function(e) 
			{
				if($(".nbt-ajax-cart").length){
				    var container = $(".nbt-ajax-cart-popup");
				    var icon = $(".nbt-ajax-cart-icon");

				    if (!container.is(e.target) && container.has(e.target).length === 0 && !icon.is(e.target) && icon.has(e.target).length === 0) 
				    {
						container.hide();
						container.removeClass('open');
						container.closest('.nbt-ajax-cart').removeClass('active');
				    }
				}
			});
			
			$(document).on('click', '.button.product_type_simple', this.trigger_add_to_cart);
			
			 
		},
		
		trigger_add_to_cart: function(event) {
			event.preventDefault();

			var $this = $(this);
			
			$this.block({
				message: null,
				overlayCSS  : {
					background: '#fff',
					opacity   : 0.5,
					cursor    : 'none'
				}
			});
			
			var product_data = [];
	        product_data.push({ name: 'action', value: 'nbt_add_to_cart' });
			product_data.push({ name: 'add-to-cart', value: $this.attr('data-product_id') });
			product_data.push({ name: 'quantity', value: 1 });
			

			$.ajax({
				url: nbt_solutions.ajax_url,
				data: $.param(product_data),
				type: 'POST',
				datatype: 'json',
				success: function( response ) {
					$this.unblock();
					if(response.fragments){
						$( document.body ).trigger( 'added_to_cart', [ response.fragments ] );
						if( response.fragments.ajax_completed != undefined) {
							Cookies.set( 'ajax_count', response.fragments.ajax_count );
							nbt_ajaxcart.notification('<div class="text-notice"><a href="' + response.fragments.url + '" class="button wc-forward nbt-ac-carturl">' + nbt_ajaxcart_params.label.view_cart + '</a> <div>' + response.fragments.title + ' ' + nbt_ajaxcart_params.label.message_success + '</div></div>');
						}
					}

					if( response.error != undefined ) {
						alert(response.error);
					}

					$(".cart_list").mCustomScrollbar({
						theme: "dark"
					});
				},
				error:function(){
					alert('There was an error when processing data, please try again !');
					nbt_ajaxcart_load.unblock($li);
				}
			});
		},
		
		load_cal: function(){
			if( $(".nbt-ajax-cart").length ){
				var $width = $(window).width() - 300;
				
				$('.nbt-ajax-cart').each(function() {
					var $wrap = $(this);
					var position = $wrap.offset();

					if($width < position.left){
						$wrap.find(".nbt-ajax-cart-popup").addClass('nbt-ajaxcart-right');
						$wrap.find(".nbt-ajax-cart-popup").css({right: '-20px'});
					}else{
						$wrap.find(".nbt-ajax-cart-popup").addClass('nbt-ajaxcart-left');
						$wrap.find(".nbt-ajax-cart-popup").css({left: 0});
					}
				});
			}
		},
		
		show_ajaxcart_popup: function(){
			var $el = $(this).closest('.nbt-ajax-cart-icon').next();
			var $wrap = $(this).closest('.nbt-ajax-cart');
			
			if($el.hasClass('open')){
				$el.hide();
				$el.removeClass('open');
				$wrap.removeClass('active');
			}else{
				$el.show();
				$el.addClass('open');
				$wrap.addClass('active');
				nbt_ajaxcart.load_cal();
				$wrap.find(".cart_list").mCustomScrollbar({
					theme:"dark"
				});
			}
		},
		
		add_to_cart: function(event){
			
			if( nbt_ajaxcart_params.enable_ajax != 'yes') {
				return;
			}
			
			event.preventDefault();
			
			var product_data = $(this).serializeArray();
			var btn_submit  = $(this).find( 'button[type="submit"]');
			
			// if button as name add-to-cart get it and add to form
	        if( btn_submit.attr('name') && btn_submit.attr('name') == 'add-to-cart' && btn_submit.attr('value') ){
	            product_data.push({ name: 'add-to-cart', value: btn_submit.attr('value') });
	        }

	        product_data.push({name: 'action', value: 'nbt_add_to_cart'});

			if( Cookies.get('ajax_count') == undefined ) {
				Cookies.set('ajax_count', nbt_ajaxcart_params.ajax_count);
			}
			
			nbt_ajaxcart_load.block(btn_submit);
		
			$.ajax({
				url: nbt_solutions.ajax_url,
				data: $.param(product_data),
				type: 'POST',
				datatype: 'json',
				success: function( response ) {
					
					if(response.fragments) {
						$( document.body ).trigger( 'added_to_cart', [ response.fragments, btn_submit ] );
						console.log(response);
						if( response.fragments.ajax_completed != undefined) {
							btn_submit.prop('disabled', false);
							nbt_ajaxcart_load.unblock(btn_submit);
							Cookies.set( 'ajax_count', response.fragments.ajax_count );
							nbt_ajaxcart.notification('<div class="text-notice"><a href="' + response.fragments.url + '" class="button wc-forward nbt-ac-carturl">' + nbt_ajaxcart_params.label.view_cart + '</a> <div>' + response.fragments.title + ' ' + nbt_ajaxcart_params.label.message_success + '</div></div>');
						}else {
							btn_submit.prop('disabled', true);
							nbt_ajaxcart_load.unblock(btn_submit);
							alert('You can\'t add to cart this product!');
						}


						$(".cart_list").mCustomScrollbar({
							theme: "dark"
						});
					}else {
						location.reload();
					}

				},
				error:function(){
					alert('There was an error when processing data, please try again !');
					nbt_ajaxcart_load.unblock($li);
				}
			});
		},
		
		added_to_cart: function(event, fragments, btn_submit) {
			

		},
		
		remove_to_cart: function(){
			var $this = $(this).closest('li');
			var $wrap = $(this).closest('.nbt-ajax-cart');
			
			nbt_ajaxcart_load.block( $wrap.find('.nbt-ajax-cart-popup') );
			
			var product_data = [];
			product_data.push({name: 'action', value: 'nbt_remove_cart'});
			product_data.push({name: 'product_id', value: $(this).attr('data-product_id')});
			product_data.push({name: 'variation_id', value: $(this).attr('data-variation_id')});
			
			$.ajax({
				url: nbt_solutions.ajax_url,
				data: $.param(product_data),
				type: 'POST',
				datatype: 'json',
				success: function( response ) {
					
					if(response.fragments){
						$wrap.find('.nbt-ajax-cart-count, .counter-number').text(response.fragments.ajax_count);
						$wrap.find('div.nbt-ajax-cart-popup').replaceWith(response.fragments.ajax_popup);
						nbt_ajaxcart.load_cal();
						$wrap.find(".cart_list").mCustomScrollbar({
							theme:"dark"
						});
					}
					nbt_ajaxcart_load.unblock( $wrap.find('.nbt-ajax-cart-popup') );
				},
				error:function(){
					alert('There was an error when processing data, please try again !');
					nbt_ajaxcart_load.unblock();
				}
			});
			return false;
		},

		notification: function(content){
			$('.ajaxcart-notification').remove();

			$('body').append('<div id="growls" class="ajaxcart-notification default" style="top: ' + nbt_ajaxcart_params.top_notification + 'px;"><div class="growl growl-notice growl-medium"><div class="growl-message"><div class="growl-close">×</div>' + content + '</div></div>');

			$('.ajaxcart-notification').hide().show('slow').delay(2000).hide('slow');
		}
	}
	nbt_ajaxcart.init();
	
});
