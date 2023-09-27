<?php

/**
 * Order Delivery Date for WooCommerce Lite
 *
 * GDPR related fixes. 
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Privacy
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}



/**
 * GDPR related fixes. 
 *
 * @class nbtodd_privacy
 */
class nbtodd_privacy {
	/**
	 * Default Constructor
	 *
	 * @since 3.5
	 */

	public function __construct() {
		add_filter( "woocommerce_privacy_export_order_personal_data_props", array( &$this, "nbtodd_privacy_export_order_personal_data_props" ), 10, 2 );
        add_filter( "woocommerce_privacy_export_order_personal_data_prop", array( &$this, "nbtodd_privacy_export_order_personal_data_prop_callback" ), 10, 3 );
	}

	function nbtodd_privacy_export_order_personal_data_props( $props_to_export, $order ) {
        $my_key_value   = array( 'delivery_details' => __( 'Delivery Date', 'order-delivery-date' ) );
        $key_pos        = array_search( 'items', array_keys( $props_to_export ) );
        
        if ( $key_pos !== false ) {
            $key_pos++;
            
            $second_array       = array_splice( $props_to_export, $key_pos );        
            $props_to_export    = array_merge( $props_to_export, $my_key_value, $second_array );
        }

        return $props_to_export;
    }  

    function nbtodd_privacy_export_order_personal_data_prop_callback( $value, $prop, $order ) {
        if ( $prop == "delivery_details" ) {
            $delivery_date = NBT_Solutions_Order_Delivery_Date::nbtodd_get_order_delivery_date( $order->get_id() );
            $value = $delivery_date;           
        }
        return $value;
    }
}

$nbtodd_privacy = new nbtodd_privacy();