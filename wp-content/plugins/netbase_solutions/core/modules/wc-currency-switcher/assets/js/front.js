var nbtwccs_loading_first_time = true;
var sumbit_currency_changing = true;

jQuery(function ($) {

    jQuery.fn.life = function (types, data, fn) {
	jQuery(this.context).on(types, this.selector, data, fn);
	return this;
    };

    nbtwccs_array_of_get = jQuery.parseJSON(nbtwccs_array_of_get);

    
    if (Object.keys(nbtwccs_array_of_get).length !== 0) {
	if ('currency' in nbtwccs_array_of_get) {
	
	    $('body.woocommerce-cart .shop_table.cart').closest('form').find('input[name="update_cart"]').prop('disabled', false);
	    $('body.woocommerce-cart .shop_table.cart').closest('form').find('input[name="update_cart"]').trigger('click');
	}
    }

    

    if (Object.keys(nbtwccs_array_of_get).length == 0) {
	nbtwccs_array_of_get = {};
    }

    
    nbtwccs_array_no_cents = jQuery.parseJSON(nbtwccs_array_no_cents);

    
    if (!parseInt(nbtwccs_get_cookie('woocommerce_items_in_cart'), 10)) {
	$('.widget_shopping_cart_content').empty();
	$(document.body).trigger('wc_fragment_refresh');
    }

    if (nbtwccs_array_of_get.currency != undefined || nbtwccs_array_of_get.removed_item != undefined || nbtwccs_array_of_get.key != undefined)
    {
	nbtwccs_refresh_mini_cart(555);
    }
    
    jQuery(document).on("adding_to_cart", function () {
	nbtwccs_refresh_mini_cart(999);
    });

    
    jQuery('.nbtwccs_price_info').life('click', function () {
	return false;
    });

 	nbtwccs_loading_first_time = false;

    
    jQuery('.nbtwccs_flag_view_item').click(function () {
	if (sumbit_currency_changing) {
	    if (jQuery(this).hasClass('nbtwccs_flag_view_item_current')) {
		return false;
	    }
	
	    if (Object.keys(nbtwccs_array_of_get).length == 0) {
		window.location = window.location.href + '?currency=' + jQuery(this).data('currency');
	    } else {

		nbtwccs_redirect(jQuery(this).data('currency'));

	    }
	}

	return false;
    });

    
    if (jQuery('.nbtwccs_converter_shortcode').length) {
	jQuery('.nbtwccs_converter_shortcode_button').click(function () {
	    var amount = jQuery(this).parent('.nbtwccs_converter_shortcode').find('.nbtwccs_converter_shortcode_amount').eq(0).val();
	    var from = jQuery(this).parent('.nbtwccs_converter_shortcode').find('.nbtwccs_converter_shortcode_from').eq(0).val();
	    var to = jQuery(this).parent('.nbtwccs_converter_shortcode').find('.nbtwccs_converter_shortcode_to').eq(0).val();
	    var precision = jQuery(this).parent('.nbtwccs_converter_shortcode').find('.nbtwccs_converter_shortcode_precision').eq(0).val();
	    var results_obj = jQuery(this).parent('.nbtwccs_converter_shortcode').find('.nbtwccs_converter_shortcode_results').eq(0);
	    jQuery(results_obj).val(nbtwccs_lang_loading + ' ...');
	    var data = {
		action: "nbtwccs_convert_currency",
		amount: amount,
		from: from,
		to: to,
		precision: precision
	    };

	    jQuery.post(nbtwccs_ajaxurl, data, function (value) {
		jQuery(results_obj).val(value);
	    });

	    return false;

	});
    }

    
    if (jQuery('.nbtwccs_rates_shortcode').length) {
	jQuery('.nbtwccs_rates_current_currency').life('change', function () {
	    var _this = this;
	    var data = {
		action: "nbtwccs_rates_current_currency",
		current_currency: jQuery(this).val(),
		precision: jQuery(this).data('precision'),
		exclude: jQuery(this).data('exclude')
	    };

	    jQuery.post(nbtwccs_ajaxurl, data, function (html) {
		jQuery(_this).parent('.nbtwccs_rates_shortcode').html(html);
	    });

	    return false;

	});
    }

    
    if (typeof nbtwccs_shop_is_cached !== 'undefined') {
	if (nbtwccs_shop_is_cached) {
	    sumbit_currency_changing = false;
	    if (typeof nbtwccs_array_of_get.currency === 'undefined') {

		if (jQuery('body').hasClass('single')) {
		    jQuery('.nbtwccs_price_info').remove();
		}

		
		var products_ids = [];
		jQuery.each(jQuery('.nbtwccs_price_code'), function (index, item) {
		    products_ids.push(jQuery(item).data('product-id'));
		});

		//if no prices on the page - do nothing
		if (products_ids.length === 0) {
                    sumbit_currency_changing = true;
		    return;
		}

		var data = {
		    action: "nbtwccs_get_products_price_html",
		    products_ids: products_ids
		};
		jQuery.post(nbtwccs_ajaxurl, data, function (data) {

		    data = jQuery.parseJSON(data);

		    if (!jQuery.isEmptyObject(data)) {
			jQuery('.nbtwccs_price_info').remove();
			jQuery.each(jQuery('.nbtwccs_price_code'), function (index, item) {
                            
                        if(data.ids[jQuery(item).data('product-id')]!=undefined){
                            jQuery(item).replaceWith(data.ids[jQuery(item).data('product-id')]);
                        }   
			   
			});
			
			jQuery('.woocommerce-currency-switcher').val(data.current_currency);
			
			
			sumbit_currency_changing = true;
		    }

		});

	    } else {
		sumbit_currency_changing = true;
	    }
	}
    }

    setTimeout(function () {

    }, 300);

    
   
});


function nbtwccs_redirect(currency) {
    if (!sumbit_currency_changing) {
	return;
    }

    var l = window.location.href;

    l = l.split('?');
    l = l[0];
    var string_of_get = '?';
    nbtwccs_array_of_get.currency = currency;
    

    if (Object.keys(nbtwccs_array_of_get).length > 0) {
	jQuery.each(nbtwccs_array_of_get, function (index, value) {
	    string_of_get = string_of_get + "&" + index + "=" + value;
	});
        
    }
    window.location = l + string_of_get;
}


function nbtwccs_refresh_mini_cart(delay) {
    /** Cart Handling */
    setTimeout(function () {
	try {
	    
	    $fragment_refresh = {
		url: wc_cart_fragments_params.ajax_url,
		type: 'POST',
		data: {action: 'woocommerce_get_refreshed_fragments', nbtwccs_woocommerce_before_mini_cart: 'mini_cart_refreshing'},
		success: function (data) {
		    if (data && data.fragments) {

			jQuery.each(data.fragments, function (key, value) {
			    jQuery(key).replaceWith(value);
			});

			try {
			    if ($supports_html5_storage) {
				sessionStorage.setItem(wc_cart_fragments_params.fragment_name, JSON.stringify(data.fragments));
				sessionStorage.setItem('wc_cart_hash', data.cart_hash);
			    }
			} catch (e) {

			}

			jQuery('body').trigger('wc_fragments_refreshed');
		    }
		}
	    };

	    jQuery.ajax($fragment_refresh);


	    /* Cart hiding */
	    try {
		
		if (nbtwccs_get_cookie('woocommerce_items_in_cart') > 0)
		{
		    jQuery('.hide_cart_widget_if_empty').closest('.widget_shopping_cart').show();
		} else {
		    jQuery('.hide_cart_widget_if_empty').closest('.widget_shopping_cart').hide();
		}
	    } catch (e) {
		
	    }


	    jQuery('body').bind('adding_to_cart', function () {
		jQuery('.hide_cart_widget_if_empty').closest('.widget_shopping_cart').show();
	    });

	} catch (e) {
	    
	}

    }, delay);

}

function nbtwccs_get_cookie(name) {
    var matches = document.cookie.match(new RegExp(
	    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
	    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}
