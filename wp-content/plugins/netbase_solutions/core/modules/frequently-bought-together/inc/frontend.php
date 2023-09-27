<?php
class NBT_FBT_Frontend {
	protected $currency = array();
	protected $current_currency = '';
	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'embed_style' ));
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'fbt_add_product_from' ), 1 );
		add_action( 'wp_loaded', array( $this, 'fbt_add_product_to_cart' ), 20 );
	}

	public function embed_style() {
		if( ! defined('PREFIX_NBT_SOL') ) {
			wp_enqueue_style( 'frontend-solutions', NBT_FBT_URL .'assets/css/frontend.css', array(), '20171003' );
			wp_enqueue_script( 'frontend-solutions', NBT_FBT_URL . 'assets/js/frontend.js', array( 'jquery' ), time(), true );
		}
			wp_localize_script( 'frontend-solutions', 'nb_fbt', array( 
				'ajax_url' 			=> admin_url('admin-ajax.php'),
				'currency_symbol' 	=> get_woocommerce_currency_symbol(),
				'currency_pos'      => get_option( 'woocommerce_currency_pos' ),
			));
	}

	public function fbt_add_product_from() {
        $product = wc_get_product(get_the_ID());

        if(! $product || empty($product->get_cross_sell_ids()) || $product->is_type( array( 'grouped', 'external' ))) {
        	return '';
        }

        if($product->is_type('variable')) {
        	$default_attributes = $product->get_default_attributes();
        	if(!empty($default_attributes)) {
				
				$product_id = $this->fbt_get_product_by_variation(array('default_attributes' => $default_attributes, 'product' => $product));
				
        		if($product_id == 0) {
        			$variations = $product->get_children();

        			if( empty( $variations ) ) {
        			    return '';
        			}
        			// get first product variation
        			$product_id = array_shift( $variations );
        		}
        	}
        	else {
	        	$variations = $product->get_children();

	        	if( empty( $variations ) ) {
	        	    return '';
	        	}
	        	// get first product variation
	        	$product_id = array_shift( $variations );
        	}
        	$product = wc_get_product( $product_id );
        }


        $products[]  = $product;
        foreach ($product->get_cross_sell_ids() as $value) {
            $products[] = wc_get_product($value);
        }

        
		return wc_get_template('nbt-fbt-form.php', array('products' => $products), '', NBT_FBT_PATH . 'tpl/' );
	}

	public function fbt_add_product_to_cart() {
		if( ! (isset($_REQUEST['action']) && $_REQUEST['action'] == 'nb_cross_sell' && wp_verify_nonce($_REQUEST['_wpnonce'], 'nb_cross_sell') ))
			return ;

		if(! isset($_REQUEST['offeringID']))
			return;

		$mess = array();

		foreach ($_REQUEST['offeringID'] as $id) {
			$product 	= wc_get_product($id);
			$variation_att = array();
			$variation_id = '';
			if(is_object($product)) {				
				if($product->post_type == 'product_variation') {					
					$product_id 		= $product->parent_id;
					$variation_att      = $product->get_variation_attributes();
					$variation_id 		= $id;
				}
				else {
					$product_id = $id;
				}
			}
			if( WC()->cart->add_to_cart( $product_id, 1, $variation_id, $variation_att ) ) {
				if( version_compare( WC()->version, '2.6', '>=' ) ) {
					$mess[$product_id] = 1;
				}
				else {
					$mess[] = $product_id;
				}
			}
		}

		if( ! empty( $mess ) ) {
			wc_add_to_cart_message( $mess );
		}

		if( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {
			$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : WC()->cart->get_cart_url();
			wp_safe_redirect( $cart_url );
			exit;
		}
		else {
			//redirect to product page
			$dest = remove_query_arg( array( 'action', '_wpnonce' ) );
			wp_redirect( esc_url( $dest ) );
			exit;
		}

	}

	public function fbt_get_product_by_variation($options = array()) {
		
		global  $wpdb;
		$sql = '';
		$df_table_alias = 'postmeta';
		$sql .= 'select a.ID, a.post_parent from ' . $wpdb->prefix . 'posts as a';
		$count = 0;
		$count1 = 0;

		foreach($options['default_attributes'] as $attr) {
			$table_alias = $df_table_alias . $count;
			$sql .= ' INNER JOIN ' . $wpdb->prefix . 'postmeta as ' . $table_alias . ' on a.ID = ' . $table_alias . '.post_id';
			$count++;
		}

		$sql .= ' WHERE';

		foreach($options['default_attributes'] as $index => $attr) {
			$table_alias = $df_table_alias . $count1;
			$and_condition = $count1 == 0 ? ' ' :  ' AND ';
			$sql .= $and_condition . $table_alias . '.meta_key = "attribute_' . sanitize_text_field($index) . '" AND ' . $table_alias . '.meta_value = "' . sanitize_text_field($attr) . '"';
			$count1++;
		}

		$sql .= ' AND a.post_status = "publish"';
		$sql .= ' AND a.post_parent = ' . $options['product']->get_id();

		$query = $wpdb->get_results($sql);
		$product_id = 0;

		if(is_object($query[0]))
		{
			$product_id = $query[0]->ID;
		}

		return $product_id;
	}
}
new NBT_FBT_Frontend();