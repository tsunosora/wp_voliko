<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class
 */
if ( !class_exists( 'NBT_Order_Delivery_Note_Settings' ) ) {

	/**
	 * WooCommerce Print Delivery Notes
	 * 
	 * @author Tyche Softwares
	 * @package WooCommerce-Delivery-Notes/Settings
	 */
	class NBT_Order_Delivery_Note_Settings {

		static $id = 'order_delivery_note';

        protected static $initialized = false;

		/**
		 * Constructor
		 */
		public static function initialize() {
            // Define default variables
            if ( self::$initialized ) {
                return;
			}
			
			
			self::$initialized = true;
		}

		
		public static function get_settings() {
			 
		    $settings = array(
					'logo' => array(
						'name' =>__( 'Shop Logo', 'order-delivery-note' ),
						'desc'         => '',
						'id'   => 'wc_'.self::$id.'_logo',
						'default' => 'http://netbasejsc.com/images/logo.png',
						'type'         => 'image',
						'desc_tip'     =>  __( 'A shop logo representing your business. When the image is printed, its pixel density will automatically be eight times higher than the original. This means, 1 printed inch will correspond to about 288 pixels on the screen.', 'order-delivery-note' )
					),

					'name' => array(
						'name' =>__( 'Shop Name', 'order-delivery-note' ),
						'desc'     => '',
						'id'   => 'wc_'.self::$id.'_name',
						'default'  => '',
						'type'     => 'textarea',
						'desc_tip'     => __( 'The shop name. Leave blank to use the default Website or Blog title defined in WordPress settings. The name will be ignored when a Logo is set.', 'order-delivery-note' ),
					),

					'addres' => array(
						'name' =>__( 'Shop Address', 'order-delivery-note' ),
						'desc'     => __( 'The postal address of the shop or even e-mail or telephone.', 'order-delivery-note' ),
						'id'   => 'wc_'.self::$id.'_addres',
						'default'  => '',
						'type'     => 'textarea',
					),

					'complimentary' => array(
						'name' =>__( 'Complimentary Close', 'order-delivery-note' ),
						'desc'     => __( 'Add a personal close, notes or season greetings.', 'order-delivery-note' ),
						'id'   => 'wc_'.self::$id.'_complimentary',
						'default'  => '',
						'type'     => 'textarea',
					),

					'conditions' => array(
						'name' =>__( 'Policies', 'order-delivery-note' ),
						'desc'     => __( 'Add the shop policies, conditions, etc.', 'order-delivery-note' ),
						'id'   => 'wc_'.self::$id.'_conditions',
						'default'  => '',
						'type'     => 'textarea',
					),

					'footer' => array(
						'name' =>__( 'Footer', 'order-delivery-note' ),
						'desc'     => __( 'Add a footer imprint, instructions, copyright notes, e-mail, telephone, etc.', 'order-delivery-note' ),
						'id'   => 'wc_'.self::$id.'_footer',
						'default'  => '',
						'type'     => 'textarea',
					),
			        // 'page_endpoint' => array(
					// 	'name' =>__( 'Print Page Endpoint', 'order-delivery-note' ),
					// 	'desc'     => '',
					// 	'id'   => 'wc_'.self::$id.'_page_endpoint',
					// 	'default'  => 'print-order',
					// 	'type'     => 'textarea',
					// 	'desc_tip' => __( 'The endpoint is appended to the accounts page URL to print the order. It should be unique.', 'order-delivery-note' ),
					// ),

					// 'print_link' => array(
					// 	'name' =>__( 'Email', 'order-delivery-note' ),
					// 	'label'            => __( 'Show print link in customer emails', 'order-delivery-note' ),
					// 	'id'   => 'wc_'.self::$id.'_print_link',
					// 	'default'         => '',
					// 	'type'            => 'checkbox',
					// 	'desc'        => __( 'This includes the emails for a new, processing and completed order. On top of that the customer invoice email also includes the link.', 'order-delivery-note' )
					// ),

					// 'print_view_order' => array(
					// 	'name' =>__( 'My Account', 'order-delivery-note' ),
					// 	'label'            => __( 'Show print button on the "View Order" page', 'order-delivery-note' ),
					// 	'id'   => 'wc_'.self::$id.'_print_view_order',
					// 	'default'         => 'no',
					// 	'type'            => 'checkbox',
					// ),

					// 'print_my_account' => array(
					// 	'name' =>__( 'Show button', 'order-delivery-note' ),
					// 	'label'            => __( 'Show print buttons on the "My Account" page', 'order-delivery-note' ),
					// 	'id'   => 'wc_'.self::$id.'_print_my_account',
					// 	'default'         => 'no',
					// 	'type'            => 'checkbox',
						
					// ),
					// 'invoice_option' => array(
					// 	'name' =>__( 'Invoice', 'order-delivery-note' ),
				    //     'type'  => 'title',
					// 	'desc'  => '',
					// 	'default' => '',
				    //     'id'   => 'wc_'.self::$id.'_invoice_option',
			        // ),

			        // 'numbering' => array(
					// 	'name' =>__( 'Numbering', 'order-delivery-note' ),
					// 	'label'            => __( 'Create invoice numbers', 'order-delivery-note' ),
					// 	'id'   => 'wc_'.self::$id.'_numbering',
					// 	'default'         => 'no',
					// 	'type'            => 'checkbox',
					// 	'desc_tip'        => ''
					// ),

					// 'next_number' => array(
					// 	'name' =>__( 'Next Number', 'order-delivery-note' ),
					// 	'desc'     => '',
					// 	'id'   => 'wc_'.self::$id.'_next_number',
					// 	'default'  => 1,
					// 	'type'     => 'number',
					// 	'desc_tip' =>  __( 'The next invoice number.', 'order-delivery-note' )
					// ),
					// 'number_prefix' => array(
					// 	'name' =>__( 'Number Prefix', 'order-delivery-note' ),
					// 	'desc'     => '',
					// 	'id'   => 'wc_'.self::$id.'_number_prefix',
					// 	'class'    => 'create-invoice',
					// 	'default'  => '',
					// 	'type'     => 'textarea',
					// 	'desc_tip' =>  __( 'This text will be prepended to the invoice number.', 'order-delivery-note' )
					// ),

					// 'suffix' => array(
					// 	'name' =>__( 'Number Suffix', 'order-delivery-note' ),
					// 	'desc'     => '',
					// 	'id'   => 'wc_'.self::$id.'_suffix',
					// 	'default'  => '',
					// 	'type'     => 'textarea',
					// 	'desc_tip' =>  __( 'This text will be appended to the invoice number.', 'order-delivery-note' )
					// ),
		    );

		    return $settings;
		}

		
		public static function show_settings($name) {
			
			
			$settings = self::get_settings();
	
			if( isset($settings[$name]) ) {
				return $settings[$name]['default'];
			}
		}

		
	}

}
?>