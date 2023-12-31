<?php

/**
 * Exit if accessed directly
 */
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Frontend Theme class
 */
if ( !class_exists( 'NBT_Order_Delivery_Note_Theme' ) ) {

	class NBT_Order_Delivery_Note_Theme {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Load the hooks
			add_action( 'wp_loaded', array( $this, 'load_hooks' ) );
		}

		/**
		 * Load the hooks at the end when
		 * the theme and plugins are ready.
		 */
		public function load_hooks() {
			// hooks
			add_filter( 'woocommerce_my_account_my_orders_actions', array( $this, 'create_print_button_account_page' ), 10, 2 );
			add_action( 'woocommerce_view_order', array( $this, 'create_print_button_order_page' ) );
			add_action( 'woocommerce_thankyou', array( $this, 'create_print_button_order_page' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
			add_action( 'woocommerce_email_after_order_table', array( $this, 'add_email_print_url' ), 100, 3 );
		}

		/**
		 * Add the scripts
		 */
		public function add_scripts() {
			if ( is_account_page() || is_order_received_page() || $this->is_woocommerce_tracking_page() ) {
				wp_enqueue_script( 'order-delivery-note-print-link', NBT_Solutions_Order_Delivery_Note::$plugin_url . 'js/jquery.print-link.js', array( 'jquery' ) );
				wp_enqueue_script( 'order-delivery-note-theme', NBT_Solutions_Order_Delivery_Note::$plugin_url . 'js/theme.js', array( 'jquery', 'order-delivery-note-print-link' ) );
			}
		}

		/**
		 * Create a print button for the 'My Account' page
		 */
		public function create_print_button_account_page( $actions, $order ) {
			if( get_option( 'wc_order_delivery_note_print_my_account' ) == 'yes' ) {
				// Add the print button
				$wdn_order_id =  ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">="  ) ) ? $order->get_id() : $order->id;
				$actions['print'] = array(
					'url'  => wcdn_get_print_link( $wdn_order_id, $this->get_template_type( $order ) ),
					'name' => __( 'Print', 'order-delivery-note' )
				);
			}
			return $actions;
		}

		/**
		 * Create a print button for the 'View Order' page
		 */
		public function create_print_button_order_page( $order_id ) {
			$order = new WC_Order( $order_id );
			$wdn_order_billing_id  =  ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">="  ) ) ? $order->get_billing_email() : $order->billing_email;
			// Output the button only when the option is enabled
			if( get_option( 'wc_order_delivery_note_print_view_order' ) == 'yes' ) {
				// Default button for all pages and logged in users
				$print_url = wcdn_get_print_link( $order_id, $this->get_template_type( $order ) );

				// Pass the email to the url for the tracking
				// and thank you page. This allows to view the
				// print page without logging in.
				if( $this->is_woocommerce_tracking_page() ) {
					//changed
					$wdn_order_email = sanitize_email( $_REQUEST['order_email'] ) ;
					$print_url = wcdn_get_print_link( $order_id, $this->get_template_type( $order ), $wdn_order_email );
				}

				// Thank you page
				if( is_order_received_page() && !is_user_logged_in() ) {
					// Don't output the butten when there is no email
					if( !$wdn_order_billing_id ) {
						return;
					}
					$print_url = wcdn_get_print_link( $order_id, $this->get_template_type( $order ), $wdn_order_billing_id );
				}

				?>
				<p class="order-print">
					<a href="<?php echo $print_url; ?>" class="button print"><?php _e( 'Print', 'order-delivery-note' ); ?></a>
				</p>
				<?php
			}
		}

		/**
		 * Add a print url to the emails that are sent to the customer
		 */
		public function add_email_print_url( $order, $sent_to_admin = true, $plain_text = false ) {
			if( NB_Solution::get_setting('order-delivery-note')['wc_order_delivery_note_print_link' ]) {
			    $wdn_order_billing_id  =  ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">="  ) ) ? $order->get_billing_email() : $order->billing_email;
				if( $wdn_order_billing_id && !$sent_to_admin ) {
				    $wdn_order_id =  ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">="  ) ) ? $order->get_id() : $order->id;

					$url = wcdn_get_print_link( $wdn_order_id, $this->get_template_type( $order ), $wdn_order_billing_id, true );

					if( $plain_text ) :
echo __( 'Print your order', 'order-delivery-note' ) . "\n\n";

echo $url . "\n";

echo "\n****************************************************\n\n";
					else : ?>
					<?php // changed ?>
						<p><strong><?php _e( 'Print:', 'order-delivery-note' ); ?></strong> <a href="<?php echo esc_url_raw( $url ); ?>"><?php _e( 'Open print view in browser', 'order-delivery-note' ); ?></a></p>
					<?php endif;
				}
			}
		}

		/**
		 * Get the print button template type depnding on order status
		 */
		public function get_template_type( $order ) {

		    $wdn_order_status =  ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">="  ) ) ? $order->get_status() : $order->status;

			if( $wdn_order_status == 'completed' ) {
				$type = apply_filters( 'wcdn_theme_print_button_template_type_complete_status', 'invoice' );
			} else {
				$type = apply_filters( 'wcdn_theme_print_button_template_type', 'order' );
			}
			return $type;
		}

		/**
		 * Is WooCommerce 'Order Tracking' page
		 */
		public function is_woocommerce_tracking_page() {
	        return ( is_page( wc_get_page_id( 'order_tracking' ) ) && isset( $_REQUEST['order_email'] ) ) ? true : false;
		}

	}

}

?>