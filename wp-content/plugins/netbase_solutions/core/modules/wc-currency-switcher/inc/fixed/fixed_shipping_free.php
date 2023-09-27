<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

class NBTWCCS_FIXED_SHIPPING_FREE extends NBTWCCS_FIXED_AMOUNT {

    protected $key = "";

    public function __construct() {
        $this->key = "_min_shipping_";
        add_filter('woocommerce_shipping_instance_form_fields_free_shipping', array($this, 'add_fixed_free_rate'), 9999, 1);
        add_filter('woocommerce_shipping_free_shipping_instance_settings_values', array($this, 'save_fixed_free_rate'), 9999, 2);
    }

    public function add_fixed_free_rate($fields) {

        global $NBTWCCS;
        $currencies = $NBTWCCS->get_currencies();
        $default_currency = $NBTWCCS->default_currency;
        $is_fixed_enabled = $NBTWCCS->is_fixed_shipping;

        foreach ($currencies as $code => $data) {
            if ($code == $default_currency) {
                continue;
            }
            $fields['nbtwccs_fixed' . $this->key . $code] = array(
                'title' => sprintf(__('Minimum order amount in %s', 'netbase_solutions'), $code),
                'type' => 'price',
                'placeholder' => __("auto", 'netbase_solutions'),
                'description' => $code,
                'default' => '',
                'desc_tip' => true
            );
        }
        wc_enqueue_js("
        		jQuery( function( $ ) {
                            function wcFreeShippingShowHideMinAmountFieldNBTWCCS( el ) {
				var form = $( el ).closest( 'form' );
				var minAmountField = $( 'input[id^=woocommerce_free_shipping_nbtwccs_fixed_min_shipping_]', form ).closest( 'tr' );
				if ( 'coupon' === $( el ).val() || '' === $( el ).val() ) {
                                    minAmountField.hide();
				} else {
                                    minAmountField.show();
				}
			}

			$( document.body ).on( 'change', '#woocommerce_free_shipping_requires', function() {
                            wcFreeShippingShowHideMinAmountFieldNBTWCCS( this );
			});

			// Change while load.
			$( '#woocommerce_free_shipping_requires' ).change();
                            $( document.body ).on( 'wc_backbone_modal_loaded', function( evt, target ) {
				if ( 'wc-modal-shipping-method-settings' === target ) {
                                    wcFreeShippingShowHideMinAmountFieldNBTWCCS( $( '#wc-backbone-modal-dialog #woocommerce_free_shipping_requires', evt.currentTarget ) );
				}
                            } );
			});
	");

        return $fields;
    }

    public function save_fixed_free_rate($options, $method) {
        return $options;
    }

    public function get_value($method_key, $code, $type) {

        $settings = get_option($method_key, null);
        if ($settings == null OR ! is_array($settings)) {
            return -1;
        }
        $array_key = sprintf('nbtwccs_fixed%s%s%s', $type, $this->key, $code);
        if (!isset($settings[$array_key])) {
            return -1;
        }
        return $settings[$array_key];
    }

}
