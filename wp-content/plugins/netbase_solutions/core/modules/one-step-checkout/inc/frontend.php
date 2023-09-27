<?php
class NBT_OSC_Frontend {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		if( NB_Solution::is_page('nb-checkout') ) {
			add_shortcode( 'nb_checkout', array( $this, 'add_shortcode_nb_checkout') );
			add_action( 'wp_enqueue_scripts', array($this, 'embed_style'));
			add_filter( 'body_class', array($this, 'nb_checkout_body_classes'), 10, 1 );
			remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
			if( defined('PREFIX_NBT_SOL') ) {
				add_filter('nbt_solutions_localize', array($this, 'nbt_solutions_localize'), 10, 1);
			}
			add_action('woocommerce_checkout_order_review', array($this, 'woocommerce_checkout_order_review'));
		}
		add_filter('woocommerce_get_cart_url', array($this, 'woocommerce_get_checkout_url'), 9999, 1);
		add_filter('woocommerce_get_checkout_url', array($this, 'woocommerce_get_checkout_url'), 9999, 1);
		add_filter('woocommerce_continue_shopping_redirect', array($this, 'woocommerce_get_checkout_url'), 9999, 1);
		add_filter('woocommerce_get_cart_page_permalink', array($this, 'woocommerce_get_checkout_url'), 9999, 1);

		
		add_action( 'rest_api_init', array( $this, 'register_rest_route') );
		add_action( 'wp_ajax_nopriv_nb_solution_remove', array($this, 'nb_solution_remove') );
		add_action( 'wp_ajax_nb_solution_remove', array($this, 'nb_solution_remove') );
		
		add_action( 'wp_ajax_nopriv_nb_solution_restore', array($this, 'nb_solution_restore') );
		add_action( 'wp_ajax_nb_solution_restore', array($this, 'nb_solution_restore') );
		
		add_action( 'wp_ajax_nopriv_nb_solution_change_qty', array($this, 'nb_solution_change_qty') );
		add_action( 'wp_ajax_nb_solution_change_qty', array($this, 'nb_solution_change_qty') );
		add_filter( 'woocommerce_get_return_url', array($this, 'woocommerce_get_return_url'), 20, 2 );
	}
	
	public function woocommerce_get_checkout_url($link) {
		return NBT_Solutions_One_Step_Checkout::nb_get_checkout_url();
	}
	
	public function woocommerce_checkout_order_review() {
		include(NBT_OSC_PATH . 'templates/order.php');
	}
	
	public function refresh_cart() {
		ob_start();
		if( WC()->cart->is_empty() ) {
			wc_get_template('cart/cart-empty.php');
		}else {
			include(NBT_OSC_PATH . 'templates/cart.php');
		}
		return ob_get_clean();
	}
	
	public function nb_solution_change_qty() {
		$json = array();
		$quantity  = sanitize_text_field($_REQUEST['qty']);
		$cart_item_key  = sanitize_text_field($_REQUEST['cart_item_key']);
		
		$cart_item		= WC()->cart->get_cart_item( $cart_item_key );

		if ( $cart_item && !empty($quantity) ) {
			WC()->cart->set_quantity( $cart_item_key, $quantity);
			
			$price = $cart_item['data']->get_price() * 1;
			//
		}
		
		$cart = WC()->cart->get_cart();
		
		if( isset( $cart[$cart_item_key] ) ) {
			$json['complete'] = true;
			$cart_item = $cart[$cart_item_key];
			$json['price'] = wc_price($cart_item['line_total']);
			
			ob_start();
			wc_cart_totals_subtotal_html();
			$json['subtotal'] = ob_get_clean();
			
			ob_start();
			wc_cart_totals_order_total_html();
			$json['total'] = ob_get_clean();
		}else {
			$json['complete'] = true;

			ob_start();
			wc_cart_totals_subtotal_html();
			$json['subtotal'] = ob_get_clean();
			
			ob_start();
			wc_cart_totals_order_total_html();
			$json['total'] = ob_get_clean();
		}

		
		wp_send_json($json);
	}
	
	public function nb_solution_restore() {
		$json = array();
		$product_id  = sanitize_text_field($_REQUEST['cart_item_key']);
		$cart_item_key = md5($product_id);
		
		$product = wc_get_product( $product_id );
		if( $product ) {
			echo $cart_item_key;
			WC()->cart->restore_cart_item( $cart_item_key );
			$item_title = sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce' ), $product->get_name() );
			$undo_notice = sprintf( __( '%s restore.', 'woocommerce' ), $item_title );

			$json['complete'] = true;
			$json['message'] = $undo_notice;
			$json['item_id'] = $cart_item_key;
			$json['cart_template'] = $this->refresh_cart();
		}

		
		wp_send_json($json);
	}
	
	public function nb_solution_remove() {
		$cart_item_key  = sanitize_text_field($_REQUEST['cart_item_key']);
		$cart_item		= WC()->cart->get_cart_item( $cart_item_key );
		
		if ( $cart_item ) {
			WC()->cart->remove_cart_item( $cart_item_key );
			
			$product = wc_get_product( $cart_item['product_id'] );
			$item_removed_title = apply_filters( 'woocommerce_cart_item_removed_title', $product ? sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce' ), $product->get_name() ) : __( 'Item', 'woocommerce' ), $cart_item );

			// Don't show undo link if removed item is out of stock.
			if ( $product && $product->is_in_stock() && $product->has_enough_stock( $cart_item['quantity'] ) ) {
				/* Translators: %s Product title. */
				$removed_notice  = sprintf( __( '%s removed.', 'woocommerce' ), $item_removed_title );
				//$removed_notice .= ' <a href="' . esc_url( wc_get_cart_undo_url( $cart_item_key ) ) . '" class="restore-item" data-id="'. $cart_item['product_id'] .'">' . __( 'Undo?', 'woocommerce' ) . '</a>';
			} else {
				/* Translators: %s Product title. */
				$removed_notice = sprintf( __( '%s removed.', 'woocommerce' ), $item_removed_title );
			}

			$json['complete'] = true;
			$json['message'] = $removed_notice;
			if( ! WC()->cart->is_empty() ) {
				$json['item_id'] = $cart_item_key;
				$json['cart_template'] = $this->refresh_cart();
				
			}else {
				$json['empty'] = true;
				$json['cart_template'] = $this->refresh_cart();
			}
		}
		
		wp_send_json($json);
		
		die();
	}
	
	public function nbt_solutions_localize($array) {
		$array['nb_checkout_url'] = get_rest_url() .'nb_checkout/v1/%%endpoint%%/';

		return $array;
	}
	
	public function register_rest_route() {
        register_rest_route(
            'nb_checkout/v1/',
            '/remove_cart',
            array(
                'methods'  => 'POST',
                'callback' => array(__CLASS__, 'rest_remove_cart_callback'),
            )
        );
	}
	
	public static function rest_remove_cart_callback(WP_REST_Request $request) {
		$json = array();
		$cart_item_key = sanitize_text_field($request->get_param( 'cart_item_key' ));
		$cart_item  = WC()->cart->get_cart_item( $cart_item_key );

		if ( $cart_item ) {

			//WC()->cart->set_quantity( $cart_item_key, 0, false );

			$rs = WC()->cart->remove_cart_item( $cart_item_key );
			if( $rs ) {
				$product = wc_get_product( $cart_item['product_id'] );
				$item_removed_title = apply_filters( 'woocommerce_cart_item_removed_title', $product ? sprintf( _x( '&ldquo;%s&rdquo;', 'Item name in quotes', 'woocommerce' ), $product->get_name() ) : __( 'Item', 'woocommerce' ), $cart_item );

				// Don't show undo link if removed item is out of stock.
				if ( $product && $product->is_in_stock() && $product->has_enough_stock( $cart_item['quantity'] ) ) {
					/* Translators: %s Product title. */
					$removed_notice  = sprintf( __( '%s removed.', 'woocommerce' ), $item_removed_title );
					$removed_notice .= ' <a href="' . esc_url( wc_get_cart_undo_url( $cart_item_key ) ) . '" class="restore-item">' . __( 'Undo?', 'woocommerce' ) . '</a>';
				} else {
					/* Translators: %s Product title. */
					$removed_notice = sprintf( __( '%s removed.', 'woocommerce' ), $item_removed_title );
				}

				$json['complete'] = true;
				$json['message'] = $removed_notice;
				wc_add_notice( $removed_notice );
			}else {
				$json['message'] = 'Not found';
			}


		}
		
		wp_send_json($json);
	}
	
	public function nb_checkout_body_classes($classes) {
		$classes[] = 'woocommerce-checkout nb-woocommerce-checkout';
		
		return $classes;
	}

	public function add_shortcode_nb_checkout() {
		echo '<div class="woocommerce">';
		if( WC()->cart->is_empty() ) {
			wc_get_template('cart/cart-empty.php');
		}else {
			// Get checkout object.
			$checkout = WC()->checkout();

			
			include( NBT_OSC_PATH . 'templates/checkout.php' );
		}
		echo '</div>';
	}


	public function woocommerce_get_return_url($url, $order) {
		$url = str_replace('nb-checkout', 'checkout', $url);
		return $url;
	}
	/**
	 * Enqueue scripts and stylesheets
	 */
	public function embed_style() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		
		wp_enqueue_style( 'select2', plugins_url( 'assets/css/select2.css', WC_PLUGIN_FILE ), array(), '20160615' );
		wp_enqueue_style( 'woocommerce-general', plugins_url( 'assets/css/woocommerce.css', WC_PLUGIN_FILE ), array(), '20160615' );
		
		
		//wp_enqueue_script('wc-add-payment-method', plugins_url( 'assets/js/frontend/add-payment-method' . $suffix . '.js', WC_PLUGIN_FILE ), null, null, true);
		wp_enqueue_script('selectWoo', plugins_url( 'assets/js/selectWoo/selectWoo.full' . $suffix . '.js', WC_PLUGIN_FILE ), null, null, true);
		wp_enqueue_script('wc-checkout', plugins_url( 'assets/js/frontend/checkout' . $suffix . '.js', WC_PLUGIN_FILE ), null, null, true);
		
		
		wp_enqueue_script('nb-form', NBT_OSC_URL .  'assets/js/jquery.form.min.js', null, null, true);
	}
}
new NBT_OSC_Frontend();