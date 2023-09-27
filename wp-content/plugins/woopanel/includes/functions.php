<?php

if ( !function_exists( 'is_woopanel_endpoint_url' ) ) {

    /**
     * Check endpoint
     * @since 1.0.0
     *
     * @param string $endpoint Default is false.
     * @return boolean
     */
    function is_woopanel_endpoint_url( $endpoint = false ) {
        global $wp;
        if( isset( $wp->query_vars[ $endpoint ] ) ||
            ($endpoint=='dashboard' || $endpoint==false) && ( isset( $wp->query_vars['page'] ) || empty( $wp->query_vars ) ) )
            return true;

        return false;
    }
}

if ( !function_exists( 'is_woo_installed' ) ) {

    /**
     * Check if WooPanel installed
     * @since 1.0.0
     * @return boolean
     */
	function is_woo_installed() {
		return function_exists( 'WC' );
	}
}

if ( !function_exists( 'is_woo_available' ) ) {

    /**
     * Check if WooPanel installed and enable
     * @since 1.0.0
     * @return boolean
     */
    function is_woo_available() {
        return is_woo_installed();
    }
}

if ( !function_exists( 'is_woopanel' ) ) {
    /**
     * Check if is woopanel
     * @since 1.0.0
     * @return boolean
     */
    function is_woopanel()
    {
        global $post;
        if (isset($post->post_type) &&
            'page' === $post->post_type &&
            'publish' === $post->post_status &&
            $post->post_content &&
            has_shortcode($post->post_content, WooDashboard_Shortcodes::$shortcodes['dashboard'])) {
            return true;
        }
        return false;
    }
}

if ( !function_exists( 'is_shop_staff' ) ) {

    /**
     * Check if user has role staff
     * @since 1.0.0
     *
     * @param int $user Default is null.
     * @param array $prerogative  Arguments to role
     * @return array
     */
    function is_shop_staff( $user = '', $prerogative = false ) {
        if( !is_user_logged_in() ) false;
        if(!$user) $user = wp_get_current_user();

        $prerogative_role = array( 'administrator', 'shop_manager' );
        $seller_role = array('vendor', 'seller', 'wcfm_vendor', 'dc_vendor', 'wc_product_vendors_admin_vendor', 'wc_product_vendors_manager_vendor', 'shop_staff' );
        $current_role = $user->roles;

        if( $prerogative ) {
            $staff_role = $prerogative_role;
        } else {
            $staff_role = array_unique( array_merge( $prerogative_role, $seller_role, NBWooCommerceDashboard::$role ) );
        }

        return !empty( array_intersect( $staff_role, $current_role ) );
    }
}

if ( !function_exists( 'redirect_no_permission' ) ) {
    /**
     * Redirect if user not have role staff
     * @since 1.0.0
     * @return boolean
     */
    function redirect_no_permission() {
        if(!is_shop_staff()) woopanel_redirect(home_url());
    }
}

if ( !function_exists( 'redirect_no_permission_ajax' ) ) {
    /**
     * Redirect if user not have role staff, request ajax
     * @since 1.0.0
     * @return boolean
     */
    function redirect_no_permission_ajax() {
        if(!is_shop_staff())
            echo '<script>alert("'. esc_html__('You need a higher level of permission.', 'woopanel' ) .'"); window.location.replace("'. home_url() .'");</script>';
    }
}

if ( !function_exists( 'woopanel_is_marketplace' ) ) {
    /**
     * Check if support marketplace plugin
     * @since 1.0.0
     * @return boolean
     */
    function woopanel_is_marketplace() {
        $active_plugins = (array) get_option( 'active_plugins', array() );
        if ( is_multisite() ) {
            $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }

        // WCfM Multivendor Marketplace Check
        $is_marketplace = ( in_array( 'wc-multivendor-marketplace/wc-multivendor-marketplace.php', $active_plugins ) || array_key_exists( 'wc-multivendor-marketplace/wc-multivendor-marketplace.php', $active_plugins ) || class_exists( 'WCFMmp' ) ) ? 'wcfmmarketplace' : false;

        // WC Vendors Check
        if( !$is_marketplace )
            $is_marketplace = ( in_array( 'wc-vendors/class-wc-vendors.php', $active_plugins ) || array_key_exists( 'wc-vendors/class-wc-vendors.php', $active_plugins ) || class_exists( 'WC_Vendors' ) ) ? 'wcvendors' : false;

        // WC Marketplace Check
        if( !$is_marketplace )
            $is_marketplace = ( in_array( 'dc-woocommerce-multi-vendor/dc_product_vendor.php', $active_plugins ) || array_key_exists( 'dc-woocommerce-multi-vendor/dc_product_vendor.php', $active_plugins ) || class_exists( 'WCMp' ) ) ? 'wcmarketplace' : false;

        // WC Product Vendors Check
        if( !$is_marketplace )
            $is_marketplace = ( in_array( 'woocommerce-product-vendors/woocommerce-product-vendors.php', $active_plugins ) || array_key_exists( 'woocommerce-product-vendors/woocommerce-product-vendors.php', $active_plugins ) ) ? 'wcpvendors' : false;

        // Dokan Lite Check
        if( !$is_marketplace )
            $is_marketplace = ( in_array( 'dokan-lite/dokan.php', $active_plugins ) || array_key_exists( 'dokan-lite/dokan.php', $active_plugins ) || class_exists( 'WeDevs_Dokan' ) ) ? 'dokan' : false;

        return $is_marketplace;
    }
}

if( !function_exists( 'woopanel_is_vendor' ) ) {
    /**
     * Check if user has role in marketplace
     * @since 1.0.0
     * @return boolean
     */
    function woopanel_is_vendor( $user_id = '' ) {
        if( !$user_id ) {
            if( !is_user_logged_in() ) return false;
            $user_id = get_current_user_id();
        }

        $is_marketplace = woopanel_is_marketplace();

        if( $is_marketplace ) {
            if( 'wcvendors' == $is_marketplace ) {
                if ( WCV_Vendors::is_vendor( $user_id ) ) return true;
            } elseif( 'wcmarketplace' == $is_marketplace ) {
                if( is_user_wcmp_vendor( $user_id ) ) return true;
            } elseif( 'wcpvendors' == $is_marketplace ) {
                if( WC_Product_Vendors_Utils::is_vendor( $user_id ) && !WC_Product_Vendors_Utils::is_pending_vendor( $user_id ) ) return true;
            } elseif( in_array( $is_marketplace, array( 'dokan', 'wcfmmarketplace' ) ) ) {
                $user = get_userdata( $user_id );
                $vendor_role = array('seller', 'wcfm_vendor');

                return !empty( array_intersect( $vendor_role, (array) $user->roles ) );
            }
        }

        return apply_filters( 'woopanel_is_vendor', false );
    }
}

if ( !function_exists( 'woopanel_redirect' ) ) {
    /**
     * Redirect URL
     * @since 1.0.0
     *
     * @param string $url
     * @return string
     */
    function woopanel_redirect($url) {
        wp_safe_redirect($url);
        exit;
    }
}

if ( !function_exists( 'woopanel_current_url' ) ) {
    /**
     * Get current url
     * @since 1.0.0
     * @return string
     */
    function woopanel_current_url() {
        if (isset($_SERVER['HTTPS']) &&
            ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        return $protocol . esc_attr( $_SERVER['HTTP_HOST'] ) . esc_attr( $_SERVER['REQUEST_URI'] );
    }
}


if ( !function_exists( 'woopanel_dashboard_url' ) ) {
    /**
     * Get url woopanel
     * @return string
     */
    function woopanel_dashboard_url($path = null)
    {
        $page_id = absint(WooPanel_Admin_Options::get_option('dashboard_page_id'));

        if ( get_post_status ( $page_id ) != 'publish' ) return null;
        $dashboard_url = get_permalink($page_id);
        return esc_url( $dashboard_url . esc_attr( $path ) );
    }
}

if ( !function_exists( 'woopanel_dashboard_pagename' ) ) {
    /**
     * Get url woopanel
     * @return string
     */
    function woopanel_dashboard_pagename()
    {
        $page_id = absint(WooPanel_Admin_Options::get_option('dashboard_page_id'));

        if ( get_post_status ( $page_id ) != 'publish' ) return null;
        $dashboard = get_post($page_id);
        return empty($dashboard) ? '' : esc_attr($dashboard->post_name);
    }
}


if ( !function_exists( 'woopanel_logout_url' ) ) {
    /**
     * Get url logout woopanel
     * @return string
     */
    function woopanel_logout_url($redirect = '')
    {
        $redirect = $redirect ? $redirect : home_url();
        return wp_logout_url($redirect);
    }
}


if ( !function_exists( 'woopanel_logo_src' ) ) {
    /**
     * Get url logo woopanel
     * @return string
     */
    function woopanel_logo_src( $type = '' ) {

        if( $type == 'header' ) {
            $default_src = WOODASHBOARD_URL .'assets/images/logo_header.png';
            $imageID = WooPanel_Admin_Options::get_option( 'dashboard_header_logo' );

            if( WooPanel_Admin_Options::get_option( 'customize_dashboard' ) == 'yes' && woopanel_get_option( 'dashboard_header_logo' ) != '-1' ) {
                $imageID = woopanel_get_option('dashboard_header_logo');
            }

        } else {
            $default_src = WOODASHBOARD_URL .'assets/images/logo_shop.png';
            $imageID = WooPanel_Admin_Options::get_option('shop_logo');
            if( woopanel_get_option( 'shop_logo' ) != '-1' ){
                $imageID = woopanel_get_option('shop_logo');
            }
        }
        $image_src = wp_get_attachment_image_src($imageID, 'full');

        return ! empty( $image_src ) ? $image_src[0] : $default_src;
    }
}


if( ! function_exists('woopanel_nice_number') ) {
    /**
     * Get nice number
     * @return string
     */
    function woopanel_nice_number($n) {
        // first strip any formatting;
        $n = (0+str_replace(",", "", $n));

        // is this a number?
        if (!is_numeric($n)) return false;

        // now filter it;
        if ($n > 1000000000000) return round(($n/1000000000000), 2).'T';
        elseif ($n > 1000000000) return round(($n/1000000000), 2).'G';
        elseif ($n > 1000000) return round(($n/1000000), 2).'M';
        elseif ($n > 1000) return round(($n/1000), 2).'K';

        return number_format($n);
    }
}


if( ! function_exists('woopanel_current_user') ) {
    /**
     * Get current user
     * @return array
     */
    function woopanel_current_user() {
        $user = wp_get_current_user();

        return array_merge(
            (array)$user->data,
            array(
                'roles' => $user->roles[0]
            )
        );
    }
}

/**
 * Get user by userid
 * @return array
 */
function woopanel_get_user_to_edit( $user_id ) {
    $user = get_userdata( $user_id );

    if ( $user )
        $user->filter = 'edit';

    return $user;
}

/**
 * Get template no items
 * @return array
 */
function woopanel_no_content( $args = array() ){
    $defaults = array(
        'icon'     => 'flaticon-open-box',
        'title'    => esc_html__( 'No items found.', 'woopanel' ),
        'subtitle' => '',
        'class'    => '',
        'style'    => '',
    );
    $r = wp_parse_args($args, $defaults);

    woopanel_get_template_part('global/content', 'empty', array(
        'icon'     => $r['icon'],
        'title'    => $r['title'],
        'subtitle' => $r['subtitle'],
        'class'    => $r['class'],
        'style'    => $r['style'],
    ));
}

if( ! function_exists('woopanel_sanitize') ) {
    /**
     * Sanitize an input.
     * @param array
     * @return array
     */
    function woopanel_sanitize($array) {
        foreach( (array) $array as $k => $v) {
            if ( is_array( $v ) ) {
                $array[$k] = woopanel_sanitize( $v );
            } else {
                $array[$k] = sanitize_text_field( $v );
            }
        }
   
        return $array;
    }
}


if( ! function_exists('woopanel_clean') ) {
    /**
     * Sanitization is the process of cleaning or filtering your input data. Whether the data is from a user or an API or web service, you use sanitizing when you don’t know what to expect or you don’t want to be strict with data validation.
     */
    function woopanel_clean( $var ) {
        if ( is_array( $var ) ) {
            return array_map( 'wc_clean', $var );
        } else {
            return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
        }
    }
}

function woopanel_wc_orderby($name = false) {
    $array = array(
        'menu_order'=> esc_html__( 'Custom ordering', 'woopanel' ),
        'name'      => esc_html__( 'Name', 'woopanel' ),
        'name_num'  => esc_html__( 'Name (numeric)', 'woopanel' ),
        'id'        => esc_html__( 'Term ID', 'woopanel' )
    );

    if( isset($array[$name]) ) {
        return $array[$name];
    }else {
        return $name;
    }
}

function woopanel_withdraw_request($section_id) {
	global $current_user;
	
	if ( ! current_user_can( 'dokan_manage_withdraw' ) ) {
		return;
	}
	
	$post_data = wp_unslash( $_POST );
	$amount          = '';
	$withdraw_method = '';
	
	
	if( isset($post_data['witdraw_amount']) && isset($post_data['withdraw_method']) ) {
		$amount          = sanitize_text_field( $post_data['witdraw_amount'] );
		$withdraw_method = sanitize_text_field( $post_data['withdraw_method'] );

        $errors           = new WP_Error();
        $limit           = (new Dokan_Withdraw)->get_withdraw_limit();
        $balance         = round( dokan_get_seller_balance( dokan_get_current_user_id(), false ), 2 );
        $withdraw_amount = (float) $post_data['witdraw_amount'];

        if ( empty( $withdraw_amount ) ) {
            $errors->add( 'errors', esc_html__( 'Withdraw amount required ', 'woopanel' ) );
        } elseif ( $withdraw_amount > $balance ) {
            $errors->add( 'errors', esc_html__( 'You don\'t have enough balance for this request', 'woopanel' ) );
        } elseif ( $withdraw_amount < $limit ) {
            $errors->add( 'errors', sprintf( esc_html__( 'Withdraw amount must be greater than %d', 'woopanel' ), (new Dokan_Withdraw)->get_withdraw_limit() ) );
        }

        if ( empty( sanitize_text_field( $post_data['withdraw_method'] ) ) ) {
            $errors->add( 'errors', esc_html__( 'withdraw method required', 'woopanel' ) );
        }
	
		if ( $errors->get_error_codes() ) {
			$error_msg = '';
			foreach( $errors->errors['errors'] as $k => $error ) {
				$error_msg .= $error;		
			}

			if( ! empty($error_msg) ) {
				echo woopanel_render_alert($error_msg, 'error');
			}
		}else {
			$data_info = array(
				'user_id' => $current_user->ID,
				'amount'  => $amount,
				'status'  => 0,
				'method'  => $withdraw_method,
				'ip'      => dokan_get_client_ip(),
				'notes'   => '',
			);

			$update = (new Dokan_Withdraw)->insert_withdraw( $data_info );
			echo woopanel_render_alert( esc_html__('Your request has been received successfully and being reviewed!', 'woopanel' ), 'success');
			
			return;
		}
		
	}	
	
	
	if ( (new Dokan_Withdraw)->has_pending_request( $current_user->ID ) ) {
		
		if( ! isset($post_data['witdraw_amount']) && ! isset($post_data['withdraw_method']) ) {
			echo woopanel_render_alert( sprintf( '<p>%s</p><p>%s</p>', esc_html__( 'You already have pending withdraw request(s).', 'woopanel' ), esc_html__( 'Please submit your request after approval or cancellation of your previous request.', 'woopanel' ) ), 'error' );
		}
		
		$withdraw_requests = (new Dokan_Withdraw)->get_withdraw_requests( $current_user->ID );
			
		if ( $withdraw_requests ) {
		?>
			<table class="dokan-table dokan-table-striped">
				<tr>
					<th><?php esc_html_e( 'Amount', 'woopanel' ); ?></th>
					<th><?php esc_html_e( 'Method', 'woopanel' ); ?></th>
					<th><?php esc_html_e( 'Date', 'woopanel' ); ?></th>
					<th><?php esc_html_e( 'Cancel', 'dwoopanel' ); ?></th>
					<th><?php esc_html_e( 'Status', 'woopanel' ); ?></th>
				</tr>

				<?php foreach ( $withdraw_requests as $request ) { ?>

					<tr>
						<td><?php echo wc_price( $request->amount ); ?></td>
						<td><?php echo esc_html( dokan_withdraw_get_method_title( $request->method ) ); ?></td>
						<td><?php echo esc_html( dokan_format_time( $request->date ) ); ?></td>
						<td>
							<?php
							$url = add_query_arg( array(
								'action' => 'dokan_cancel_withdrow',
								'id'     => $request->id
							), dokan_get_navigation_url( 'withdraw' ) );
							?>
							<a href="<?php echo esc_url( wp_nonce_url( $url, 'dokan_cancel_withdrow' ) ); ?>">
								<?php esc_html_e( 'Cancel', 'woopanel' ); ?>
							</a>
						</td>
						<td>
							<?php
								if ( $request->status == 0 ) {
									echo '<span class="label label-danger">' . esc_html__( 'Pending Review', 'woopanel' ) . '</span>';
								} elseif ( $request->status == 1 ) {
									echo '<span class="label label-warning">' . esc_html__( 'Accepted', 'woopanel' ) . '</span>';
								}
							?>
						</td>
					</tr>

				<?php } ?>

			</table>
		<?php
		}
		
		return;
	}else if( ! (new Dokan_Withdraw)->has_withdraw_balance( $current_user->ID ) ) {
		echo woopanel_render_alert( esc_html__('You don\'t have sufficient balance for a withdraw request!', 'woopanel' ), 'error');
		
		return;
	}
	
	$payment_methods = array_intersect( dokan_get_seller_active_withdraw_methods(), dokan_withdraw_get_active_methods() );
	
	woopanel_form_field(
		'witdraw_amount',
		array(
			'id'          => 'witdraw_amount',
			'type'		  => 'number',
			'label'       => esc_html__( 'Withdraw Amount', 'woopanel' ),
			'form_inline' => true,
			'custom_attributes' => array(
				'min'		  => esc_attr( dokan_get_option( 'withdraw_limit', 'dokan_withdraw', 0 ) ),
				'placeholder' => '0.00'
			)
		),
		$amount
	);
	
	$payment_options = array();
	foreach ( $payment_methods as $method_name ) {
		$payment_options[$method_name] = dokan_withdraw_get_method_title( $method_name );
	}

	woopanel_form_field(
		'withdraw_method',
		array(
			'id'          => 'withdraw_method',
			'type'		  => 'select',
			'label'       => esc_html__( 'Payment Method', 'woopanel' ),
			'form_inline' => true,
			'options' => $payment_options

		),
		$withdraw_method
	);
}

/**
 * Display template withdraw approved Dokan
 * @return void
 */
function woopanel_withdraw_approved($section_id) {
	global $current_user;
	
	if ( ! current_user_can( 'dokan_manage_withdraw' ) ) {
		return;
	}
	
	$requests = (new Dokan_Withdraw)->get_withdraw_requests( $current_user->ID, 1, 100 );
	if ( $requests ) {
		?>
		<table class="dokan-table dokan-table-striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Amount', 'woopanel' ); ?></th>
					<th><?php esc_html_e( 'Method', 'woopanel' ); ?></th>
					<th><?php esc_html_e( 'Date', 'woopanel' ); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php foreach ( $requests as $row ) { ?>
				<tr>
					<td><?php echo wc_price( $row->amount ); ?></td>
					<td><?php echo esc_html( dokan_withdraw_get_method_title( $row->method ) ); ?></td>
					<td><?php echo esc_html( date_i18n( 'M j, Y g:ia', strtotime( $row->date ) ) ); ?></td>
				</tr>
			<?php } ?>

			</tbody>
		</table>
		<?php
	}else {
		esc_html_e( 'Sorry, no transactions were found!', 'woopanel' );
	}
}

/**
 * Display template withdraw cancelled Dokan
 * @return void
 */
function woopanel_withdraw_cancelled($section_id) {
	global $current_user;
	
	if ( ! current_user_can( 'dokan_manage_withdraw' ) ) {
		return;
	}
	
	$requests = (new Dokan_Withdraw)->get_withdraw_requests( $current_user->ID, 2, 100 );

	if ( $requests ) {
		?>
		<table class="dokan-table dokan-table-striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Amount', 'woopanel' ); ?></th>
					<th><?php esc_html_e( 'Method', 'woopanel' ); ?></th>
					<th><?php esc_html_e( 'Date', 'woopanel' ); ?></th>
					<th><?php esc_html_e( 'Note', 'woopanel' ); ?></th>
				</tr>
			</thead>
			<tbody>

			<?php foreach ( $requests as $row ) { ?>
				<tr>
					<td><?php echo wc_price( $row->amount ); ?></td>
					<td><?php echo esc_html( dokan_withdraw_get_method_title( $row->method ) ); ?></td>
					<td><?php echo esc_html( date_i18n( 'M j, Y g:ia', strtotime( $row->date ) ) ); ?></td>
					<td><?php echo wp_kses_post( $row->note ); ?></td>
				</tr>
			<?php } ?>

			</tbody>
		</table>
		<?php
	}else {
		esc_html_e( 'Sorry, no transactions were found!', 'woopanel' );
	}

}

function woopanel_render_alert( $msg, $type = 'warrning') {
	return '<div class="m-alert m-alert--air m-alert--square alert m-alert--icon m-alert-single m-alert-'. esc_attr($type) .'">'. wp_kses( $msg, array(
                        'div' => array(
                            'class' => array()
                        ),
                        'i' => array(
                            'class' => array()
                        ),
                    ) ) .'</div>';
}

/**
 * Get layout switch
 * @return boolean
 */
function woopanel_get_layout() {
    global $woopanel_admin_options, $current_user;

    $customize_layout = isset($woopanel_admin_options['customize_layout']) ? $woopanel_admin_options['customize_layout'] : 'fullwidth';


    $user_customize_layout = get_user_meta($current_user->ID, '_shop_layout', true);

    $result = empty($user_customize_layout) ? $customize_layout : $user_customize_layout;

    if( isset($_POST['shop_layout']) ) {
        $result = esc_attr($_POST['shop_layout']);
    }

    if( isset($_COOKIE['switch_layout'] ) && ! isset($_POST['shop_layout']) ) {
        $result = $_COOKIE['switch_layout'];
        update_user_meta($current_user->ID, '_shop_layout', $_COOKIE['switch_layout']);
        unset($_COOKIE['switch_layout']);
        setcookie('switch_layout', null, -1, '/'); 
    }

    return $result;

}

function woopanel_render_metaboxes( $metabox, $post ) {
    if( isset($metabox['panel']) ) {
        call_user_func_array( $metabox['content'], array(&$post) );
    }else { ?>
        <div id="<?php echo esc_attr( $metabox['id'] );?>" class="m-portlet">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text"><?php echo esc_attr( $metabox['title'] );?></h3>
                    </div>
                </div>
            </div>
            <div class="m-portlet__body">
                <?php call_user_func_array( $metabox['content'], array(&$post) );?>
            </div>
        </div>
        <?php
    }
}

/**
 * Get total order by customer
 * @param int $user_id Customer ID
 * @return int
 */
function woopanel_get_customer_order_count( $user_id ) {
    return woopanel_get_customer_query( $user_id, true );
}

/**
 * Get total price by customer
 * @param int $user_id Customer ID
 * @return int
 */
function woopanel_get_customer_total_spent( $user_id ) {
    global $wpdb, $current_user;
    $order_status = array( 'completed', 'processing', 'on-hold' );

    $query = [];


    $query['select'] = "SELECT SUM( DISTINCT od_meta_price.meta_value ) as total FROM {$wpdb->posts} as od";
    
    $query['join']   = "INNER JOIN {$wpdb->postmeta} AS od_meta ON od.ID = od_meta.post_id ";
    $query['join']   .= "INNER JOIN {$wpdb->postmeta} AS od_meta_price ON od.ID = od_meta_price.post_id ";
    $query['join'] .= "INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON (od.ID = order_items.order_id) ";

    $query[ "join" ] .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta ON (order_items.order_item_id = order_item_meta.order_item_id)  AND (order_item_meta.meta_key = '_product_id')";

    $query['join']   .= "INNER JOIN {$wpdb->posts} AS product ON order_item_meta.meta_value = product.ID";

    $query['where'] = "WHERE od.post_type = 'shop_order' ";
    $query['where'] .= "AND od.post_status IN ( 'wc-" . implode( "','wc-", $order_status ) . "') ";
    $query['where'] .= sprintf( "AND od_meta.meta_key = '_customer_user' AND od_meta.meta_value = %d ", $user_id );

    $query['where'] .= sprintf( "AND product.post_author = '%s' AND product.post_status = 'publish'", $current_user->ID );

    $query['where'] .= "AND od_meta_price.meta_key = '_order_total' AND od_meta_price.meta_value > 0";
    $query['order'] = "ORDER BY od.post_date DESC";

    return $wpdb->get_var( implode(' ', $query) );
}

/**
 * Get result order by customer
 * @param int $user_id Customer ID
 * @param int $offset Start on record 
 * @param int $per_page Return number records per page
 * @return object
 */
function woopanel_get_customer_order( $user_id, $offset, $per_page ) {
    return woopanel_get_customer_query( $user_id, false, $offset, $per_page );
}

function woopanel_get_customer_query( $user_id, $count = null, $offset = null, $per_page = null ) {
    global $wpdb, $current_user;
    $order_status = array( 'completed', 'processing', 'on-hold' );

    $query = [];

    if( $count ) {
        $query['select'] = "SELECT COUNT( DISTINCT od.ID ) as total FROM {$wpdb->posts} as od";
    }else {
        $query['select'] = "SELECT od.* FROM {$wpdb->posts} as od";
    }
    
    $query['join']   = "INNER JOIN {$wpdb->postmeta} AS od_meta ON od.ID = od_meta.post_id ";
    $query['join'] .= "INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON (od.ID = order_items.order_id) ";

    $query[ "join" ] .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta ON (order_items.order_item_id = order_item_meta.order_item_id)  AND (order_item_meta.meta_key = '_product_id')";

    $query['join']   .= "INNER JOIN {$wpdb->posts} AS product ON order_item_meta.meta_value = product.ID";

    $query['where'] = "WHERE od.post_type = 'shop_order' ";
    $query['where'] .= "AND od.post_status IN ( 'wc-" . implode( "','wc-", $order_status ) . "') ";
    $query['where'] .= sprintf( "AND od_meta.meta_key = '_customer_user' AND od_meta.meta_value = %d ", $user_id );

    $query['where'] .= sprintf( "AND product.post_author = '%s' AND product.post_status = 'publish'", $current_user->ID );
    $query['order'] = "ORDER BY od.post_date DESC";

    if( ! $count ) {
        $query['limit'] = sprintf( "LIMIT %d, %d", $offset, $per_page );
    }

    if( $count ) {
        return $wpdb->get_var( implode(' ', $query) );
    }else {
        return $wpdb->get_results( implode(' ', $query) );
    }
}

function woopanel_deactive_modules( $modules = array() ) {
    $data = get_option('woopanel_modules');
    $data = empty($data) ? array() : $data;

    foreach ($modules as $module_id ) {
        if( isset($data[$module_id]) ) {
            unset($data[$module_id]);
        }
    }

    update_option('woopanel_modules', $data);
    return $data;
}


function woopanel_check_usermeta( $user_id, $meta_key ) {
    global $wpdb;

    return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->usermeta WHERE user_id = %d AND meta_key = %s", $user_id, $meta_key ) );
}


if ( ! function_exists( 'woodashboard_post_thumbnail' ) ) {
    function woodashboard_post_thumbnail( $post_id, $size = 'thumbnail' ) {
        global $_wp_additional_image_sizes;


        $_thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true);

        if( empty($_thumbnail_id) && ! is_numeric($_thumbnail_id) ) {
            $source = WC()->plugin_url() . '/assets/images/placeholder.png';
        }else {
            $source = get_post_meta($_thumbnail_id, '_nb_offload_media_url', true);

            if( empty($source) ) {
                $upload_dir = wp_upload_dir();
                $source = get_post_meta( $_thumbnail_id, '_wp_attached_file', true );

                $filename   = $upload_dir['basedir'] . '/' . $source;
                if ( file_exists( $filename ) ) {
                    $source = $upload_dir['baseurl'] . '/' . $source;
                }else {
                    $source = WC()->plugin_url() . '/assets/images/placeholder.png';
                }
            }
        }

        if( isset($_wp_additional_image_sizes[$size]) ) {
            $img = sprintf( '<img src="%s" width="%d" height="%d" />', esc_url($source), $_wp_additional_image_sizes[$size]['width'], $_wp_additional_image_sizes[$size]['height']);
        }else {
            $img = sprintf( '<img src="%s" />', esc_url($source) );
        }

        return $img;
    }
}


/**
 * Dokan get seller short formatted address
 *
 * @since  2.5.7
 *
 * @param  integer $store_id
 *
 * @return string
 */
function wpl_get_seller_short_address( $store_id, $line_break = true ) {
    $store_address = wpl_get_seller_address( $store_id, true );
    $address_classes = array(
        'street_1',
        'street_2',
        'city',
        'state',
        'country',
    );
    $short_address = array();
    $formatted_address = '';

    if ( ! empty( $store_address['street_1'] ) && empty( $store_address['street_2'] ) ) {
        $short_address[] = "<span class='{$address_classes[0]}'> {$store_address['street_1']},</span>";
    } else if ( empty( $store_address['street_1'] ) && ! empty( $store_address['street_2'] ) ) {
        $short_address[] = "<span class='{$address_classes[1]}'> {$store_address['street_2']},</span>";
    } else if ( ! empty( $store_address['street_1'] ) && ! empty( $store_address['street_2'] ) ) {
        $short_address[] = "<span class='{$address_classes[0]} {$address_classes[1]}'> {$store_address['street_1']}, {$store_address['street_2']}</span>";
    }

    if ( ! empty( $store_address['city'] ) && ! empty( $store_address['city'] ) ) {
        $short_address[] = "<span class='{$address_classes[2]}'> {$store_address['city']},</span>";
    }

    if ( ! empty( $store_address['state'] ) && ! empty( $store_address['country'] ) ) {
        $short_address[] = "<span class='{$address_classes[3]}'> {$store_address['state']},</span>" . "<span class='{$address_classes[4]}'> {$store_address['country']} </span>";
    } else if ( ! empty( $store_address['country'] ) ) {
        $short_address[] = "<span class='{$address_classes[4]}'> {$store_address['country']} </span>";
    }

    if ( ! empty( $short_address ) && $line_break ) {
        $formatted_address = implode( '<br>', $short_address );
    } else {
        $formatted_address = implode( ' ', $short_address );
    }

    return apply_filters( 'woopanel_store_header_adress', $formatted_address, $store_address, $short_address );
}


/**
 * Generate Address string | array for given seller id or current user
 *
 * @since 2.3
 *
 * @param int seller_id, defaults to current_user_id
 * @param boolean get_array, if true returns array instead of string
 *
 * @return String|array Address | array Address
 */
function wpl_get_seller_address( $seller_id = '', $get_array = false ) {

    if ( $seller_id == '' ) {
        $seller_id = dokan_get_current_user_id();
    }

    $profile_info = woopanel_get_store_info( $seller_id );

    if ( isset( $profile_info['address'] ) ) {

        $address = $profile_info['address'];

        $country_obj = new WC_Countries();
        $countries   = $country_obj->countries;
        $states      = $country_obj->states;

        $street_1     = isset( $address['street_1'] ) ? $address['street_1'] : '';
        $street_2     = isset( $address['street_2'] ) ? $address['street_2'] : '';
        $city         = isset( $address['city'] ) ? $address['city'] : '';

        $zip          = isset( $address['zip'] ) ? $address['zip'] : '';
        $country_code = isset( $address['country'] ) ? $address['country'] : '';
        $state_code   = isset( $address['state'] ) ? $address['state'] : '';
        $state_code   = isset( $address['state'] ) ? ( $address['state'] == 'N/A' ) ? '' : $address['state'] : '';

        $country_name = isset( $countries[ $country_code ] ) ? $countries[ $country_code ] : '';
        $state_name   = isset( $states[ $country_code ][ $state_code ] ) ? $states[ $country_code ][ $state_code ] : $state_code;

    } else {
        return 'N/A';
    }

    if ( $get_array == true ) {
        $address = array(
            'street_1' => $street_1,
            'street_2' => $street_2,
            'city'     => $city,
            'zip'      => $zip,
            'country'  => $country_name,
            'state'    => isset( $states[ $country_code ][ $state_code ] ) ? $states[ $country_code ][ $state_code ] : $state_code,
        );

        return apply_filters( 'woopanel_get_seller_address', $address, $profile_info );
    }

    $country           = new WC_Countries();
    $formatted_address = $country->get_formatted_address( array(
        'address_1' => $street_1,
        'address_2' => $street_2,
        'city'      => $city,
        'postcode'  => $zip,
        'state'     => $state_code,
        'country'   => $country_code,
    ) );

    return apply_filters( 'woopanel_get_seller_formatted_address', $formatted_address, $profile_info );
}


/**
 * Get store page url of a seller
 *
 * @param int $user_id
 * @return string
 */
function woopanel_get_store_url( $user_id ) {
    if ( ! $user_id ) {
        return '';
    }

    $userdata         = get_userdata( $user_id );
    $user_nicename    = ( ! false == $userdata ) ? $userdata->user_nicename : '';
    $custom_store_url = 'store-profile';

    return sprintf( '%s/%s/', home_url( '/' . $custom_store_url ), $user_nicename );
}


/**
 * Get store info based on seller ID
 *
 * @param int $seller_id
 *
 * @return array
 */
function woopanel_get_store_info( $seller_id ) {
    return WooDashboard()->vendor->get( $seller_id )->get_shop_info();
}

function woopanel_array_equal($a, $b) {
    return (
         is_array($a) 
         && is_array($b) 
         && count($a) == count($b) 
         && array_diff($a, $b) === array_diff($b, $a)
    );
}


function woopanel_is_vendor() {
    global $current_user;

    return array_intersect(NBWooCommerceDashboard::$role_seller, $current_user->roles);
}

function woopanel_is_super_admin() {
    global $current_user;
    return array_intersect(NBWooCommerceDashboard::$role_super_admin, $current_user->roles);
}