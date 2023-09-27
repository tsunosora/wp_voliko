<?php
class NBT_Solutions_Tera_Wallet_Template {
	function __construct() {
		add_action( 'woopanel_dashboard_tera-wallet_endpoint', array( $this, 'woopanel_tera_wallet_endpoint_content' ) );
        add_action( 'woopanel_dashboard_wallet-transaction_endpoint', array( $this, 'woopanel_wallet_transaction_endpoint_content' ) );

		add_action( 'woopanel_enqueue_scripts', array($this, 'woopanel_scripts'));


	}

	public function woopanel_tera_wallet_endpoint_content() {
		if( isset($_POST['save']) ) {
			$nonce = $_POST['_wpnonce'];

			if ( ! wp_verify_nonce( $nonce, 'update_options' ) ) {
				die( __( 'Security check', 'textdomain' ) ); 
			} else {
				// Do stuff here.
				if( isset($_POST['wallet_type']) && class_exists('Woo_Wallet_Frontend') ) {
					if( $_POST['wallet_type'] == '#wallet_topup' ) {
						$amount = sanitize_text_field($_POST['wallet_topup_amount']);
						$tera_wallet = new Woo_Wallet_Frontend();

						$is_valid = $this->is_valid_wallet_recharge_amount($amount);
						if ($is_valid['is_valid']) {
							add_filter('woocommerce_add_cart_item_data', array($this, 'add_woo_wallet_product_price_to_cart_item_data'), 10, 2);

							$product = get_wallet_rechargeable_product();
							if ($product) {
		                        if( ! is_wallet_rechargeable_cart() ) {
		                            woo_wallet_persistent_cart_update();
		                        }
		                        wc()->cart->empty_cart();
		                        wc()->cart->add_to_cart($product->get_id());
		                        $redirect_url = apply_filters('woo_wallet_redirect_to_checkout_after_added_amount', true) ? wc_get_checkout_url() : wc_get_cart_url();
		                        wp_safe_redirect($redirect_url);

		                        exit();
							}
				        }else {
				        	wc_add_notice($is_valid['message'], 'error');
				        }
					}

					if( $_POST['wallet_type'] == '#wallet_transfer' ) {
						$response = $this->wallet_transfer();
						if( ! empty($response['is_valid']) ) {
							echo woopanel_render_alert(
								sprintf('<div class="m-alert__icon"><i class="flaticon-danger"></i></div><div class="m-alert__text">%s</div>',
								$response['message']
							), 'success');
						}else {
							echo woopanel_render_alert(
								sprintf('<div class="m-alert__icon"><i class="flaticon-danger"></i></div><div class="m-alert__text">%s</div>',
								$response['message']
							));
						}
					}
				}
				


			}
		}

	    $fields = woopanel_tera_wallet_fields();
	    include NBT_TERA_WALLET_PATH . 'templates/settings.php';
	}

    public function woopanel_wallet_transaction_endpoint_content() {
        $transaction = new WooPanel_Template_Transaction_Listing();
        $transaction->lists();
    }

	private function wallet_transfer() {
        if (isset($_POST['woo_wallet_transfer_user_id'])) {
            $whom = $_POST['woo_wallet_transfer_user_id'];
        }
        if (isset($_POST['woo_wallet_transfer_amount'])) {
            $amount = $_POST['woo_wallet_transfer_amount'];
        }
        $whom = apply_filters('woo_wallet_transfer_user_id', $whom);
        $whom = get_userdata($whom);
        $current_user_obj = get_userdata(get_current_user_id());
        $credit_note = isset($_POST['woo_wallet_transfer_note']) && !empty($_POST['woo_wallet_transfer_note']) ? $_POST['woo_wallet_transfer_note'] : sprintf(__('Wallet funds received from %s', 'woo-wallet'), $current_user_obj->user_email);
        $debit_note = sprintf(__('Wallet funds transfer to %s', 'woo-wallet'), $whom->user_email);
        $credit_note = apply_filters('woo_wallet_transfer_credit_transaction_note', $credit_note, $whom, $amount);
        $debit_note = apply_filters('woo_wallet_transfer_debit_transaction_note', $debit_note, $whom, $amount);
        
        $transfer_charge_type = woo_wallet()->settings_api->get_option('transfer_charge_type', '_wallet_settings_general', 'percent');
        $transfer_charge_amount = woo_wallet()->settings_api->get_option('transfer_charge_amount', '_wallet_settings_general', 0);
        $transfer_charge = 0;
        if ('percent' === $transfer_charge_type) {
            $transfer_charge = ( $amount * $transfer_charge_amount ) / 100;
        } else {
            $transfer_charge = $transfer_charge_amount;
        }
        $transfer_charge = apply_filters('woo_wallet_transfer_charge_amount', $transfer_charge, $whom);
        $credit_amount = apply_filters('woo_wallet_transfer_credit_amount', $amount, $whom);
        $debit_amount = apply_filters('woo_wallet_transfer_debit_amount', $amount + $transfer_charge, $whom);
        if ( woo_wallet()->settings_api->get_option( 'min_transfer_amount', '_wallet_settings_general', 0 ) ) {
            if ( woo_wallet()->settings_api->get_option( 'min_transfer_amount', '_wallet_settings_general', 0 ) > $amount) {
                return array(
                    'is_valid' => false,
                    'message' => sprintf( __('Minimum transfer amount is %s', 'woo-wallet'), wc_price( woo_wallet()->settings_api->get_option( 'min_transfer_amount', '_wallet_settings_general', 0 ), woo_wallet_wc_price_args() ) )
                );
            }
        }
        if (!$whom) {
            return array(
                'is_valid' => false,
                'message' => __('Invalid user', 'woo-wallet')
            );
        }
        if (floatval($debit_amount) > woo_wallet()->wallet->get_wallet_balance(get_current_user_id(), 'edit')) {
            return array(
                'is_valid' => false,
                'message' => __('Entered amount is greater than current wallet amount.', 'woo-wallet')
            );
        }

        if ($credit_transaction_id = woo_wallet()->wallet->credit($whom->ID, $credit_amount, $credit_note)) {
            do_action('woo_wallet_transfer_amount_credited', $credit_transaction_id, $whom->ID, get_current_user_id());
            $debit_transaction_id = woo_wallet()->wallet->debit(get_current_user_id(), $debit_amount, $debit_note);
            do_action('woo_wallet_transfer_amount_debited', $debit_transaction_id, get_current_user_id(), $whom->ID);
            update_wallet_transaction_meta($debit_transaction_id, '_wallet_transfer_charge', $transfer_charge, get_current_user_id());
            $response = array(
                'is_valid' => true,
                'message' => __('Amount transferred successfully!', 'woo-wallet')
            );
        }

        return $response;
	}

	/**
	 * Check wallet recharge amount.
	 * @param float $amount
	 * @return array
	 */
	private function is_valid_wallet_recharge_amount($amount = 0) {
	    $response = array('is_valid' => true);
	    $min_topup_amount = woo_wallet()->settings_api->get_option('min_topup_amount', '_wallet_settings_general', 0);
	    $max_topup_amount = woo_wallet()->settings_api->get_option('max_topup_amount', '_wallet_settings_general', 0);

        if ($min_topup_amount && $amount < $min_topup_amount) {
            $response = array(
                'is_valid' => false,
                'message' => sprintf(__('The minimum amount needed for wallet top up is %s', 'woo-wallet'), wc_price($min_topup_amount, woo_wallet_wc_price_args()))
            );
        }
        if ($max_topup_amount && $amount > $max_topup_amount) {
            $response = array(
                'is_valid' => false,
                'message' => sprintf(__('Wallet top up amount should be less than %s', 'woo-wallet'), wc_price($max_topup_amount, woo_wallet_wc_price_args()))
            );
        }
        if ($min_topup_amount && $max_topup_amount && ( $amount < $min_topup_amount || $amount > $max_topup_amount )) {
            $response = array(
                'is_valid' => false,
                'message' => sprintf(__('Wallet top up amount should be between %s and %s', 'woo-wallet'), wc_price($min_topup_amount, woo_wallet_wc_price_args()), wc_price($max_topup_amount, woo_wallet_wc_price_args()))
            );
        }

	    return apply_filters('woo_wallet_is_valid_wallet_recharge_amount', $response, $amount);
	}

    /**
     * WooCommerce add cart item data
     * @param array $cart_item_data
     * @param int $product_id
     * @return array
     */
    public function add_woo_wallet_product_price_to_cart_item_data($cart_item_data, $product_id) {
        $product = wc_get_product($product_id);

        if (isset($_POST['wallet_topup_amount']) && $product) {
            $recharge_amount = apply_filters('woo_wallet_rechargeable_amount', round($_POST['wallet_topup_amount'], 2));
            $cart_item_data['recharge_amount'] = $recharge_amount;
        }

        return $cart_item_data;
    }

    public function woopanel_scripts() {
    		wp_enqueue_style('select2', WOODASHBOARD_URL . 'vendors/select2/select2.min.css', array());
    		wp_enqueue_script('select2', WOODASHBOARD_URL . 'vendors/select2/select2.full.min.js', array(), WC_VERSION);	
    }
}

new NBT_Solutions_Tera_Wallet_Template();