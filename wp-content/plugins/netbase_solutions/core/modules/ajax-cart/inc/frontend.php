<?php

class NBT_WooCommerce_AjaxCart_Frontend
{
    protected $args;

    function __construct()
    {
		add_action( 'wp_ajax_nopriv_nbt_add_to_cart', array($this, 'nbt_add_to_cart'));
		add_action( 'wp_ajax_nbt_add_to_cart', array($this, 'nbt_add_to_cart') );
		
		add_filter( 'woocommerce_add_to_cart_fragments', array($this, 'set_ajax_fragments'), 10, 1);
		add_filter( 'nbt_ajax_cart_icon', array($this, 'nbt_ajax_cart_icon_callback'), 10, 4);
		
        add_action( 'wp_ajax_nopriv_nbt_remove_cart', array($this, 'nbt_remove_to_cart') );
        add_action( 'wp_ajax_nbt_remove_cart', array($this, 'nbt_remove_to_cart') );
		
		add_shortcode('nbt_ajax_cart', array($this, 'shortcode_ajax_cart'), 10, 1);
		
        add_action( 'wp_enqueue_scripts', array($this, 'embed_style') );
    }
	
    public function shortcode_ajax_cart()
    {
        if (function_exists('nbt_ajax_template')) {
            nbt_ajax_template();
        }
    }
	
	public function nbt_remove_to_cart() {
        global $woocommerce, $currency;
        $ajax = new WC_AJAX();
        $cart = WC()->instance()->cart;

        $total = 0;
        $product_id = intval($_REQUEST['product_id']);
        $variation_id = intval($_REQUEST['variation_id']);
        $item_count = WC()->cart->cart_contents_count;
        if ($item_count > 0) {
            $get_cart = WC()->cart->get_cart();
            foreach ($get_cart as $item_key => $item) {
                if ($item['product_id'] == $product_id && empty($variation_id) ) {
                    $woocommerce->cart->set_quantity($item_key, 0);
                }

                if ($item['variation_id'] == $variation_id && !empty($variation_id) ) {
                    $woocommerce->cart->set_quantity($item_key, 0);
                }
            }

        }

		wc_clear_notices(); // clear other notice
		WC_AJAX::get_refreshed_fragments();	
	}
	
	public function nbt_ajax_cart_icon_callback($output, $icon, $count, $price)
    {
        return sprintf('<i class="%s"></i><span class="nbt-ajax-cart-count">%d</span>', $icon, $count);
    }
	
	public function nbt_add_to_cart() {
		// wc_clear_notices();
		if( !isset($_POST['action']) || $_POST['action'] != 'nbt_add_to_cart' || !isset($_POST['add-to-cart']) ){
			die();
		}
		
		// get woocommerce error notice
		$errors = wc_get_notices( 'error' );
		if( $errors ) {
			wc_print_notice( $errors[0]['notice'], 'error' );
		}else {
			do_action( 'woocommerce_ajax_added_to_cart', intval( $_POST['add-to-cart'] ) );
			wc_clear_notices(); // clear other notice
			WC_AJAX::get_refreshed_fragments();	
			
		}
		
		die();
	}
	
	public function set_ajax_fragments( $fragments ) {
		if( isset($_POST['add-to-cart']) ) {
			$quantity = 0;
			$carts = WC()->cart->get_cart();
			foreach ($carts as $key => $cart) {
				if( empty($cart['variation_id']) && $cart['product_id'] == $_POST['add-to-cart'] ) {
					$quantity = $cart['quantity'];
				}

				if( ! empty($cart['variation_id']) && $cart['variation_id'] == $_POST['variation_id'] ) {
					$quantity = $cart['quantity'];
				}
			}


			$stock = 0;
			$product = wc_get_product($_POST['add-to-cart']);
			if( empty($_POST['variation_id']) ) {
				$stock = $product->get_stock_quantity();
			}else {
				$variation_obj = new WC_Product_variation($_POST['variation_id']);
				$stock = $variation_obj->get_stock_quantity();
			}

			$title = '';
			$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : WC()->cart->get_cart_url();

			if( empty($stock) ) {
				$fragments['ajax_completed'] = true;

				$fragments['url'] = esc_url($cart_url) ;
				$fragments['title'] = '<strong>'.$product->get_name().'</strong>';
		
			}else {
				if( $quantity <= $stock ) {
					$fragments['ajax_completed'] = true;
					$fragments['url'] = esc_url($cart_url) ;
					$fragments['title'] = '<strong>'.$product->get_name().'</strong>';
			
				}
			}

		}

		$count_value = WC()->cart->get_cart_contents_count();
		ob_start();
		nbt_ajax_cart_popup();
		$cart_content = ob_get_clean();
		
		//Cart content
		$fragments['div.nbt-ajax-cart-popup'] = '<div class="nbt-ajax-cart-popup">'.$cart_content.'</div>';

		//Total Count
		$fragments['span.nbt-ajax-cart-count'] = '<span class="nbt-ajax-cart-count">'.$count_value.'</span>';
		$fragments['ajax_count'] = $count_value;

		$fragments['ajax_popup'] = '<div class="nbt-ajax-cart-popup open" style="display: block;">'.$cart_content.'</div>';
		
		return $fragments;
	}
	
	public function custom_css() {
		$css = '';
		$ajaxcart_settings = get_option('ajax-cart_settings');

		if( isset($ajaxcart_settings['wc_ajax_cart_color_icon']) ) {
			$css .= ".nbt-ajax-cart-icon i {
				color: {$ajaxcart_settings['wc_ajax_cart_color_icon']} !important;
			}\n";
		}else {
			$css .= ".nbt-ajax-cart-icon i {
				color: {NBT_Ajax_Cart_Settings::show_settings('wc_ajax_cart_color_icon')};
			}\n";
		}
		
		if( isset($ajaxcart_settings['wc_ajax_cart_color_count']) ) {
			$css .= ".nbt-ajax-cart .nbt-ajax-cart-icon .nbt-ajax-cart-count {
				background: {$ajaxcart_settings['wc_ajax_cart_color_count']} !important;
				color: {$ajaxcart_settings['wc_ajax_cart_color_count_text']} !important;
			}";
		}else {
			$css .= ".nbt-ajax-cart .nbt-ajax-cart-icon .nbt-ajax-cart-count {
				background: {$ajaxcart_settings['wc_ajax_cart_color_count']} !important;
				color: {$ajaxcart_settings['wc_ajax_cart_color_count_text']} !important;
			}";
		}
		
		if( isset($ajaxcart_settings['wc_ajax_cart_primary_color']) ) {
			$css .= ".nbt-ajax-cart .nbt-ajax-cart-popup {
				border-top-color: {$ajaxcart_settings['wc_ajax_cart_primary_color']} !important;
			}\n";
			$css .= ".nbt-ajax-cart .nbt-ajax-cart-icon:after {
				border-bottom-color: {$ajaxcart_settings['wc_ajax_cart_primary_color']} !important;
			}\n";
			
			$css .= ".nbt-ajax-cart .nbt-ajax-cart-popup ul li .nbt-ajax-cart-right h4 a {
				color: {$ajaxcart_settings['wc_ajax_cart_primary_color']} !important;
			}\n";
			$css .= ".nbt-ajax-cart .nbt-ajax-cart-popup .buttons a:hover {
				background: {$ajaxcart_settings['wc_ajax_cart_primary_color']} !important;
			}\n";
		}else {
			$css .= ".nbt-ajax-cart .nbt-ajax-cart-popup {
				border-top-color: ". NBT_Ajax_Cart_Settings::show_settings('primary_color')." !important;
			}\n";
			$css .= ".nbt-ajax-cart .nbt-ajax-cart-icon:after {
				border-bottom-color: ". NBT_Ajax_Cart_Settings::show_settings('primary_color')." !important;
			}\n";
			$css .= ".nbt-ajax-cart .nbt-ajax-cart-popup ul li .nbt-ajax-cart-right h4 a {
				color: ". NBT_Ajax_Cart_Settings::show_settings('primary_color')." !important;
			}\n";
			$css .= ".nbt-ajax-cart .nbt-ajax-cart-popup .buttons a {
				background: ". NBT_Ajax_Cart_Settings::show_settings('background_button')." !important;
			}\n";
			$css .= ".nbt-ajax-cart .nbt-ajax-cart-popup .buttons a:hover {
				background: ". NBT_Ajax_Cart_Settings::show_settings('background_button_hover')." !important;
			}\n";
			
		}
		
		if( isset($ajaxcart_settings['wc_ajax_cart_top_popup']) ) {
			$top_default = $ajaxcart_settings['wc_ajax_cart_top_popup'] - 0;
			$default = 30 - $top_default;
			$css .= ".nbt-ajax-cart .nbt-ajax-cart-popup {
				top: {$ajaxcart_settings['wc_ajax_cart_top_popup']}px !important;
			}\n";
			$css .= ".nbt-ajax-cart .nbt-ajax-cart-icon:after {
				bottom: {$default}px !important;
			}\n";
		}

		if( isset($ajaxcart_settings['wc_ajax_cart_count_color_border']) ) {
			$css .= ".nbt-ajax-cart .nbt-ajax-cart-icon .nbt-ajax-cart-count {
				border-color: {$ajaxcart_settings['wc_ajax_cart_count_color_border']} !important;
			}\n";
		}
		
		return $css;		
	}

    public function embed_style()
    {
        if (!defined('PREFIX_NBT_SOL')) {
            wp_enqueue_style('ntb-fonts', NB_AJAXCART_URL . 'assets/css/ntb-fonts.css', false, '1.1', 'all');
        }

        wp_enqueue_style('mCustomScrollbar', NB_AJAXCART_URL . 'assets/css/jquery.mCustomScrollbar.min.css', false, '1.1', 'all');
        wp_enqueue_style('jquery.growl', NB_AJAXCART_URL . 'assets/css/jquery.growl.css', false, '1.1', 'all');
        if ( ! defined('PREFIX_NBT_SOL') ) {
            wp_enqueue_style('ajax-cart-front', NB_AJAXCART_URL . 'assets/css/frontend.css', false, false, 'all');
			wp_add_inline_style( 'ajax-cart-front', $this->custom_css() );
        }else {
			wp_add_inline_style( 'frontend-solutions', $this->custom_css() );
		}

        wp_enqueue_script('jquery.mCustomScrollbar', NB_AJAXCART_URL . 'assets/js/jquery.mCustomScrollbar.min.js', null, null, true);
        if (!defined('PREFIX_NBT_SOL')) {
            wp_enqueue_script('ajax-cart', NB_AJAXCART_URL . 'assets/js/frontend.js?v=' . time(), null, null, true);
        }
		$js_settings = array(
			'enable_ajax' => get_option('woocommerce_enable_ajax_add_to_cart'),
			'wc_ajax_url' => WC_AJAX::get_endpoint( "%%endpoint%%" ),
			'ajax_count' => WC()->cart->get_cart_contents_count(),
			'loader' => NB_AJAXCART_URL . '/assets/img/qv-loader.gif',
			'label' => array(
				'view_cart' => __('View cart', 'woocommerce'),
				'message_success' => __('has been added to your cart.', 'nbt-solution')
			)
		);
		
		$ajaxcart_settings = get_option('ajax-cart_settings');
		if( isset($ajaxcart_settings['wc_ajax_cart_top']) ) {
			$js_settings['top_notification'] = $ajaxcart_settings['wc_ajax_cart_top'];
		}
		
		

		if ( !defined('PREFIX_NBT_SOL') ) {
			wp_localize_script( 'nbt-ajax-cart', 'nbt_ajaxcart_params', $js_settings);
		}else {
			wp_localize_script( 'frontend-solutions', 'nbt_ajaxcart_params', $js_settings);
		}
    }
}

new NBT_WooCommerce_AjaxCart_Frontend();