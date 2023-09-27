<?php

add_action('init', 'do_output_buffer');
function do_output_buffer(){ ob_start(); }


/**
 * Login redirect WooCommerce
 *
 * @since 1.0.0
 * @hook woocommerce_login_redirect
 * @param {string} $url Redirect to
 * @param {object} $user User object
 * @return void
 */
add_filter( 'woocommerce_login_redirect', 'woopanel_login_redirect', 520, 2 );
/**
 * Login redirect WooCommerce
 *
 * @param string $url Redirect to
 * @param object $user User object
 * @return string $link
 */
function woopanel_login_redirect( $redirect_to, $user ) {
    if ( in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')) ) {
        $redirect_to = admin_url();
    } else {
        if( is_shop_staff( $user ) ) {
            $redirect_to = woopanel_current_url();
        } else if( is_woo_installed() && isset( $user->roles ) && in_array( 'customer', $user->roles ) ) {
            $redirect_to = get_permalink( wc_get_page_id( 'myaccount' ) );
        } else {
            $redirect_to = get_home_url();
        }
    }
    if ( $user && isset($user->ID) ) {
        $current_login = get_user_meta( $user->ID, '_current_login', true );
        update_user_meta( $user->ID, '_previous_login', $current_login );
        update_user_meta( $user->ID, '_current_login', current_time( 'timestamp' ) );
    }
    return apply_filters( 'woopanel_login_redirect', $redirect_to, $user );
}
function woopanel_wp_login_redirect( $redirect_to, $request = '', $user = '' ) {
    woopanel_login_redirect( $redirect_to, $user );
}

add_filter( 'map_meta_cap', 'woopanel_edit_post_cap', 10, 4 );
function woopanel_edit_post_cap( $caps, $cap, $user_id, $args ) {
    if ( 'edit_post' == $cap && ! is_shop_staff(false, true) )
    {
        $caps = array();
        $caps[] = 'edit_post';
    }
    
    return $caps;
}

add_filter( 'edit_post_link', 'woopanel_edit_post_link' );
/**
 * Filters the post edit link anchor tag.
 *
 * @param string $link Anchor tag for the edit link.
 * @return string $link
 */
function woopanel_edit_post_link( $link ) {
    $matches = array();
    preg_match( '~<a(.*?)href="([^"]+)"(.*?)>~', $link, $matches );

    return str_replace( $matches[2], woopanel_post_edit_url( get_the_ID() ), $link );
}


add_filter( 'display_post_states', 'woopanel_add_post_state', 10, 2 );
/**
 * Filters the default post display states used in the posts list table.
 *
 * @param string $post_states An array of post display states.
 * @param object $post The current post object.
 * @return array $post_states
 */
function woopanel_add_post_state( $post_states, $post ) {
    if ( absint( WooPanel_Admin_Options::get_option('dashboard_page_id') ) === $post->ID ) {
        $post_states[] = esc_html__( 'Seller Center Page', 'woopanel' );
    }
    return $post_states;
}

/**
 * Add role default
 *
 * @since 1.0.0
 * @hook init
 * @param null
 * @return void
 */
add_action( 'init', 'woopanel_set_roles_caps');
/**
 * Add role default
 *
 * @since 1.0.0
 * @param null
 * @return void
 */
function woopanel_set_roles_caps() {
    // For dokan
    if( woopanel_is_marketplace() == 'dokan' ) {
        $role = get_role( 'seller' );
        $role->add_cap( 'edit_shop_coupons' );
        $role->add_cap( 'edit_post' );
        $role->add_cap( 'upload_files' );
    }
}



add_filter( 'woocommerce_admin_order_preview_get_order_details', 'add_order_details_preview', 10, 2 );
function add_order_details_preview( $data, $order ) {
    global $woopanel_order_status, $woopanel_post_types;

    $order_url = esc_url( woopanel_dashboard_url( $woopanel_post_types['shop_order']['slug'] ) .'/?id='. esc_attr($data['order_number']) );
    $data['order_url'] = $order_url;
    $data['status_color'] = $woopanel_order_status["wc-{$data['status']}"]['color'];

    return $data;
}