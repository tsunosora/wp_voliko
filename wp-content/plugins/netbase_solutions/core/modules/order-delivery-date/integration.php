<?php 
/**
* Order Delivery Date for WooCommerce Lite
*
* Integration of the WooCommerce Order Delivery Date plugin with various 3rd party plugins 
*
* @author      Tyche Softwares
* @package     Order-Delivery-Date-Lite-for-WooCommerce/Integrate-Delivery-Date
* @since       3.5
*/


/** 
 * Integration class to integrate the WooCommerce Order Delivery Date plugin with various 3rd party plugins
 * 
 * @class nbtodd_integration
 */
class nbtodd_integration {
	 /**
     * Default Constructor
     * 
     * @since 3.5
     */    
	public function __construct() {		
	    // WooCommerce PDF Invoices & Packing Slips
		if ( version_compare( get_option( 'wpo_wcpdf_version' ), '2.0.0', ">=" ) ) {
			add_action( 'wpo_wcpdf_after_order_details', array( &$this, 'nbtodd_plugins_packing_slip' ), 10, 2 );
		} else {
			add_action( 'wpo_wcpdf_after_order_details', array( &$this, 'nbtodd_plugins_packing_slip' ) );
		}
		
		// add custom columns headers to csv when Order/Customer CSV Export Plugin is activate
		add_filter( 'wc_customer_order_csv_export_order_headers', array( &$this, 'nbtodd_csv_export_modify_column_headers' ) );
		add_filter( 'wc_customer_order_csv_export_order_row', array( &$this, 'nbtodd_csv_export_modify_row_data' ), 10, 3 );
		
		//WooCommerce Print Invoice & Delivery Note		
		add_filter( 'wcdn_order_info_fields', array( &$this, 'nbtodd_print_invoice_delivery_note' ), 10, 2 );
		
		add_action( 'woocommerce_cloudprint_internaloutput_footer', array( &$this, 'nbtodd_cloud_print_fields' ) );
		
		//WooCommerce Print Invoice/Packing list plugin
		add_action( 'wc_pip_after_body', array( &$this, 'nbtodd_woocommerce_pip' ), 10, 4 );
	}

	/**
	 * Adds delivery date and time selected for an order in the PDF invoices
	 * and Packing slips from WooCommerce PDF Invoices & Packing Slips plugin.
	 *
	 * @hook wpo_wcpdf_after_order_details
	 * @since 1.7
	 */
	function nbtodd_plugins_packing_slip( $template_type = "", $order = array() ) {
		global $nbtodd_date_formats;
		$options = get_option( NBTODD_SETTINGS );
		if ( version_compare( get_option( 'wpo_wcpdf_version' ), '2.0.0', ">=" ) ) {
			$order_id = ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">="  ) ) ? $order->get_id() : $order->id;
		} else { 
			global $wpo_wcpdf;
			$order_export = $wpo_wcpdf->export;
			$order_obj = $order_export->order;
			$order_id = $order_obj->id;
		}

		$delivery_date_formatted = NBT_Solutions_Order_Delivery_Date::nbtodd_get_order_delivery_date( $order_id );
		if( $delivery_date_formatted != '' ) {
		    echo '<p><strong>' . $options['nbt_order-delivery-date_field_label'] . ': </strong>' . $delivery_date_formatted;
		}
	}

	/**
	 * Adds delivery date and time column headings to CSV when order
	 * is exported from Order/Customer CSV Export Plugin.
	 *
	 * @param array $column_headers - List of Column Names
	 * @return array $column_headers - The list of column names
	 *
	 * @hook wc_customer_order_csv_export_order_headers
	 * @since 1.7
	 */
	function nbtodd_csv_export_modify_column_headers( $column_headers ) { 
		$options = get_option( NBTODD_SETTINGS );
		$new_headers = array(
			'column_1' => __( $options['nbt_order-delivery-date_field_label'], 'order-delivery-date' )
		);
		return array_merge( $column_headers, $new_headers );
	}

	/**
	 * Adds delivery date and time column content to CSV when order
	 * is exported from Order/Customer CSV Export Plugin.
	 *
	 * @param array $order_data -
	 * @param object $order - Order Details
	 * @param object $csv_generator
	 * @return array $new_order_data - Delivery data
	 *
	 * @hook wc_customer_order_csv_export_order_row
	 * @since 1.7
	 */
	public static function nbtodd_csv_export_modify_row_data( $order_data, $order, $csv_generator ) {
	    $new_order_data = $custom_data = array();
		$order_id = $order->id;
		$delivery_date_formatted = NBT_Solutions_Order_Delivery_Date::nbtodd_get_order_delivery_date( $order_id );
		
		$custom_data = array(
		    'column_1' => $delivery_date_formatted,
		);
			
		if ( isset( $csv_generator->order_format ) && ( 'default_one_row_per_item' == $csv_generator->order_format || 'legacy_one_row_per_item' == $csv_generator->order_format ) ) {
			foreach ( $order_data as $data ) {
				$new_order_data[] = array_merge( (array) $data, $custom_data );
			}
		} else {
			$new_order_data = array_merge( $order_data, $custom_data );
		}
		
		return $new_order_data;
	}

	/**
	 * Adds delivery date and time selected for an order in the invoices
	 * and delivery notes from WooCommerce Print Invoice & Delivery Note plugin.
	 *
	 * @param array $fields - List of fields
	 * @param object $order
	 * @return array $fields - with the delivery data added
	 *
	 * @hook wcdn_order_info_fields
	 * @since 1.7
	 */
	function nbtodd_print_invoice_delivery_note( $fields, $order ) {
		$options = get_option( NBTODD_SETTINGS );
		$new_fields = array();
        $order_id = $order->id;
        $delivery_date_formatted = NBT_Solutions_Order_Delivery_Date::nbtodd_get_order_delivery_date( $order_id );
        if( $delivery_date_formatted != '' ) {
            $new_fields[ $options['nbt_order-delivery-date_field_label'] ] = array(
                'label' => __( $options['nbt_order-delivery-date_field_label'], 'order-delivery-date' ),
                'value' => $delivery_date_formatted
            );
        }
        return array_merge( $fields, $new_fields );
	}
	
	/**
	 * Adds delivery date and time selected for an order
	 * in the prints from WooCommerce Print Orders plugin.
	 *
	 * @param object $order - Order Details
	 *
	 * @hook woocommerce_cloudprint_internaloutput_footer
	 * @since 1.7
	 */
	function nbtodd_cloud_print_fields( $order ) { 
		$options = get_option( NBTODD_SETTINGS );
	    $field_date_label =$options['nbt_order-delivery-date_field_label'];
	    $order_id = $order->id;
	    
	    $delivery_date_formatted = NBT_Solutions_Order_Delivery_Date::nbtodd_get_order_delivery_date( $order_id );
	    echo '<p><strong>'.__( $field_date_label, 'order-delivery-date' ) . ': </strong>' . $delivery_date_formatted;
	}
	
	/**
	 * Adds delivery date and time selected for an order in the invoices
	 * and delivery notes from WooCommerce Print Invoice/Packing list plugin.
	 *
	 * @param $type
	 * @param $action
	 * @param $document
	 * @param object $order - Order Details
	 *
	 * @hook wc_pip_after_body
	 * @since 1.7
	 */
	function nbtodd_woocommerce_pip( $type, $action, $document, $order ) {
	    global $orddd_date_formats;
	    $options = get_option( NBTODD_SETTINGS );
	    $delivery_date = $options['nbt_order-delivery-date_field_label'];
	    
        $order_id = $order->id;
        $delivery_date_formatted = NBT_Solutions_Order_Delivery_Date::nbtodd_get_order_delivery_date( $order_id );
        if( $delivery_date_formatted != '' ) {
            echo '<p><strong>' . __( $delivery_date, 'order-delivery-date' ) . ': </strong>' . $delivery_date_formatted;
        }
	}
}
$nbtodd_integration = new nbtodd_integration();
?>