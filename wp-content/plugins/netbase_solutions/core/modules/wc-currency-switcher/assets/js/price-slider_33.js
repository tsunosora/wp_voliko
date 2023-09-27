/* global woocommerce_price_slider_params, accounting */
jQuery( function( $ ) {

	// woocommerce_price_slider_params is required to continue, ensure the object exists
	if ( typeof woocommerce_price_slider_params === 'undefined' ) {
		return false;
	}

	$( document.body ).bind( 'price_slider_create price_slider_slide', function( event, min, max ) {

                min=nbtwccs_convert_price_slider(min);
                max=nbtwccs_convert_price_slider(max);
                var nbtwccs_format="%v %s";
                if (nbtwccs_current_currency.position === 'left') {
                    nbtwccs_format="%s%v";
                } else if (nbtwccs_current_currency.position === 'left_space') {
                   nbtwccs_format="%s %v";
                } else if (nbtwccs_current_currency.position === 'right') {
                   nbtwccs_format="%v%s";

                } else if (nbtwccs_current_currency.position === 'right_space') {
                   nbtwccs_format="%v %s";
                }
                //woocommerce_price_slider_params.currency_format_num_decimals =nbtwccs_current_currency.decimals;

		$( '.price_slider_amount span.from' ).html( accounting.formatMoney( min, {
			symbol:    woocommerce_price_slider_params.currency_format_symbol,
			decimal:   woocommerce_price_slider_params.currency_format_decimal_sep,
			thousand:  woocommerce_price_slider_params.currency_format_thousand_sep,
			precision: woocommerce_price_slider_params.currency_format_num_decimals,
			format:    nbtwccs_format
		} ) );

		$( '.price_slider_amount span.to' ).html( accounting.formatMoney( max, {
			symbol:    woocommerce_price_slider_params.currency_format_symbol,
			decimal:   woocommerce_price_slider_params.currency_format_decimal_sep,
			thousand:  woocommerce_price_slider_params.currency_format_thousand_sep,
			precision: woocommerce_price_slider_params.currency_format_num_decimals,
			format:    nbtwccs_format
		} ) );

		$( document.body ).trigger( 'price_slider_updated', [ min, max ] );
	});

	function init_price_filter() {
		$( 'input#min_price, input#max_price' ).hide();
		$( '.price_slider, .price_label' ).show();

		var min_price = $( '.price_slider_amount #min_price' ).data( 'min' ),
			max_price = $( '.price_slider_amount #max_price' ).data( 'max' ),
			current_min_price = $( '.price_slider_amount #min_price' ).val(),
			current_max_price = $( '.price_slider_amount #max_price' ).val();

		$( '.price_slider:not(.ui-slider)' ).slider({
			range: true,
			animate: true,
			min: min_price,
			max: max_price,
			values: [ current_min_price, current_max_price ],
			create: function() {

				$( '.price_slider_amount #min_price' ).val( current_min_price );
				$( '.price_slider_amount #max_price' ).val( current_max_price );

				$( document.body ).trigger( 'price_slider_create', [ current_min_price, current_max_price ] );
			},
			slide: function( event, ui ) {

				$( 'input#min_price' ).val( ui.values[0] );
				$( 'input#max_price' ).val( ui.values[1] );

				$( document.body ).trigger( 'price_slider_slide', [ nbtwccs_convert_price_slider(ui.values[0]), nbtwccs_convert_price_slider(ui.values[1])] );
			},
			change: function( event, ui ) {

				$( document.body ).trigger( 'price_slider_change', [ ui.values[0], ui.values[1] ] );
			}
		});
	}

	init_price_filter();

	var hasSelectiveRefresh = (
		'undefined' !== typeof wp &&
		wp.customize &&
		wp.customize.selectiveRefresh &&
		wp.customize.widgetsPreview &&
		wp.customize.widgetsPreview.WidgetPartial
	);
	if ( hasSelectiveRefresh ) {
		wp.customize.selectiveRefresh.bind( 'partial-content-rendered', function() {
			init_price_filter();
		} );
	}
});

function  nbtwccs_convert_price_slider(price){
	var label = price;

	if (nbtwccs_current_currency.rate !== 1) {
	    label = Math.ceil(label * parseFloat(nbtwccs_current_currency.rate));
	}

	//+++
        return label;
}
