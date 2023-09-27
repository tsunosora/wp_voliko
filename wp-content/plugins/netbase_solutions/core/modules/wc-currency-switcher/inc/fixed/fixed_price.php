<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');


final class NBTWCCS_FIXED_PRICE extends NBTWCCS_FIXED_AMOUNT {

    public function __construct() {
        $this->key="_price_";
	add_action('woocommerce_product_options_general_product_data', array($this, 'woocommerce_product_options_general_product_data'), 9999);
	add_action('woocommerce_process_product_meta', array($this, 'woocommerce_process_product_meta'), 9999, 1);
	add_action('woocommerce_product_after_variable_attributes', array($this, 'woocommerce_product_after_variable_attributes'), 9999, 3);
	add_action('woocommerce_process_product_meta_variable', array($this, 'woocommerce_process_product_meta_variable'), 9999, 1);
	add_action('woocommerce_save_product_variation', array($this, 'woocommerce_process_product_meta_variable'), 9999, 1);
    }

    public function woocommerce_product_options_general_product_data() {
	global $NBTWCCS;
	global $post;
	$_product = wc_get_product($post->ID);
	add_action('admin_footer', array($this, 'admin_footer'));
	if ($_product->is_type('simple') OR $_product->is_type('external')) {
	    $data = array();
	    $data['currencies'] = $NBTWCCS->get_currencies();
	    $data['default_currency'] = $NBTWCCS->default_currency;
	    $data['is_fixed_enabled'] = $NBTWCCS->is_fixed_enabled;
	    $data['is_geoip_manipulation'] = $NBTWCCS->is_geoip_manipulation;
	    $data['post_id'] = $post->ID;
	    $data['type'] = 'simple';
	    $data['product_geo_data'] = $this->get_product_geo_data($post->ID);

	    echo $this->render_html(NBTWCCS_PATH . 'tpl/fixed/product_price_data.php', $data);
	}
    }

    //saving data for simple product
    public function woocommerce_process_product_meta($post_id) {
	$this->save_product_prices($post_id);
    }

    public function woocommerce_product_after_variable_attributes($loop, $variation_data, $variation) {
	global $NBTWCCS;
	$data = array();
	$data['currencies'] = $NBTWCCS->get_currencies();
	$data['default_currency'] = $NBTWCCS->default_currency;
	$data['is_fixed_enabled'] = $NBTWCCS->is_fixed_enabled;
	$data['is_geoip_manipulation'] = $NBTWCCS->is_geoip_manipulation;
	$data['post_id'] = $variation->ID;
	$data['type'] = 'var';
	$data['product_geo_data'] = $this->get_product_geo_data($variation->ID);
	echo $this->render_html(NBTWCCS_PATH . 'tpl/fixed/product_price_data.php', $data);
    }

    //saving data for variable product
    public function woocommerce_process_product_meta_variable($post_id) {
	if (isset($_POST['variable_post_id']) AND ! empty($_POST['variable_post_id'])) {
	    foreach ($_POST['variable_post_id'] as $key => $p_id) {
		$this->save_product_prices($p_id);
	    }
	}
    }

    public function save_product_prices($post_id) {
	if (!current_user_can('manage_options')) {
	    return;
	}

	global $NBTWCCS;
	$currencies = $NBTWCCS->get_currencies();

	if (isset($_POST['nbtwccs_regular_price'][$post_id])) {
	    unset($_POST['nbtwccs_regular_price'][0]);
	    unset($_POST['nbtwccs_regular_price']['__POST_ID__']);
	    unset($_POST['nbtwccs_sale_price'][0]);
	    unset($_POST['nbtwccs_sale_price']['__POST_ID__']);

	    //clean all data before apply new selected data
	    foreach ($currencies as $code => $curr) {
		delete_post_meta($post_id, '_nbtwccs_regular_price_' . $code);
		delete_post_meta($post_id, '_nbtwccs_sale_price_' . $code);
	    }
	    
	    if (is_array($_POST['nbtwccs_regular_price'][$post_id])) {
		foreach ($_POST['nbtwccs_regular_price'][$post_id] as $code => $price) {
		    $price = floatval($price);
		    if ($price > 0) {
			update_post_meta($post_id, '_nbtwccs_regular_price_' . $code, $price);
		    } else {
			update_post_meta($post_id, '_nbtwccs_regular_price_' . $code, -1);
		    }
		}
	    }
	    
	    if (is_array($_POST['nbtwccs_sale_price'][$post_id])) {
		foreach ($_POST['nbtwccs_sale_price'][$post_id] as $code => $price) {
		    $price = floatval($price);
		    if ($price > 0) {
			update_post_meta($post_id, '_nbtwccs_sale_price_' . $code, $price);
		    } else {
			update_post_meta($post_id, '_nbtwccs_sale_price_' . $code, -1);
		    }
		}
	    }
	}
	
	if (isset($_POST['nbtwccs_price_geo_countries'])) {
	    update_post_meta($post_id, '_nbtwccs_price_geo_countries', '');
	    update_post_meta($post_id, '_nbtwccs_regular_price_geo', '');
	    update_post_meta($post_id, '_nbtwccs_sale_price_geo', '');
	    if (is_array($_POST['nbtwccs_price_geo_countries'])) {
		foreach ($_POST['nbtwccs_price_geo_countries'] as $post_id => $rules) {
		    update_post_meta($post_id, '_nbtwccs_price_geo_countries', $rules);
		}

		foreach ($_POST['nbtwccs_regular_price_geo'] as $post_id => $rules) {
		    update_post_meta($post_id, '_nbtwccs_regular_price_geo', $rules);
		}

		foreach ($_POST['nbtwccs_sale_price_geo'] as $post_id => $rules) {
		    update_post_meta($post_id, '_nbtwccs_sale_price_geo', $rules);
		}
	    }
	}
    }

    public function get_product_geo_data($post_id) {
	$data = array();
	$data['price_geo_countries'] = (array) get_post_meta($post_id, '_nbtwccs_price_geo_countries', true);
	$data['regular_price_geo'] = (array) get_post_meta($post_id, '_nbtwccs_regular_price_geo', true);
	$data['sale_price_geo'] = (array) get_post_meta($post_id, '_nbtwccs_sale_price_geo', true);

	//*** some corrections
	if (!empty($data['regular_price_geo'])) {
	    foreach ($data['regular_price_geo'] as $key => $value) {
		if (empty($data['sale_price_geo'][$key])) {
		    //for example sale price should be, but user leave it empty for any currency, in such
		    //case without such correction price will be free for the product, what is wrong behaviour
		    $data['sale_price_geo'][$key] = $value;
		}
	    }
	}

	return $data;
    }

    /*     * ********************************************************* */


    public function get_price_type($product, $price) {
	$type = 'regular';

	static $products_data = array();
	$product_id = 0;
	

	if (version_compare(WOOCOMMERCE_VERSION, '2.7', '>=')) {
	    global $NBTWCCS;
	    $p_id = 0;
	    if (method_exists($product, 'get_id')) {
		$p_id = $product->get_id();
	    } else {
		$p_id = $product->id;
	    }

	    if (method_exists($product, 'get_sale_price')) {
		if ($this->is_exists($p_id, $NBTWCCS->current_currency, 'sale') AND ! $product->get_sale_price('edit')) {
		    return 'sale';
		}
	    } else {
		if (isset($product->sale_price)) {
		    if ($this->is_exists($p_id, $NBTWCCS->current_currency, 'sale') AND ! $product->sale_price) {
			return 'sale';
		    }
		}
	    }
	}

	if (version_compare(WOOCOMMERCE_VERSION, '2.7', '>=')) {
	    if (method_exists($product, 'get_sale_price')) {
		$sale_price = $product->get_sale_price('edit');
		$product_id = $product->get_id();
	    } else {
		if (isset($product->sale_price)) {
		    $sale_price = $product->sale_price;
		}
		$product_id = $product->id;
	    }
	} else {
	    if (isset($product->sale_price)) {
		$sale_price = $product->sale_price;
	    }
	    $product_id = $product->id;
	}


	if (version_compare(WOOCOMMERCE_VERSION, '2.7', '>=')) {
	    if (method_exists($product, 'get_regular_price')) {
		$regular_price = $product->get_regular_price('edit');
		$product_id = $product->get_id();
	    } else {
		if (isset($product->regular_price)) {
		    $regular_price = $product->regular_price;
		}
		$product_id = $product->id;
	    }
	} else {
	    if (isset($product->regular_price)) {
		$regular_price = $product->regular_price;
	    }
	    $product_id = $product->id;
	}
	if (isset($products_data[$product_id])) {
	    if ($products_data[$product_id] < $price) {
		$type = 'regular';
	    } else {
		$type = 'sale';
	    }
	} else {
	    $products_data[$product_id] = $price;
	    $type = 'sale';
	}	
	return $type;
    }
}
