<?php

if ( ! function_exists( 'woopanel_dokan_get_review_url' ) ) {
    /**
     * Get review page url of a seller
     *
     * @param int $user_id
     * @return string
     */
    function woopanel_dokan_get_review_url( $user_id ) {
        if ( ! $user_id ) {
            return '';
        }
    
        $userstore = dokan_get_store_url( $user_id );
    
        return apply_filters( 'dokan_get_seller_review_url', $userstore . 'reviews' );
    }
}

if ( ! function_exists( 'woopanel_dokan_review_query' ) ) {
    /**
     * Query for dokan review
     * @return string
     */
    function woopanel_dokan_review_query( $vendor_id, $count = false ) {
        global $wpdb, $current_user;

        $query = array();

		if( $count ) {
            $query['select'] = "SELECT COUNT( DISTINCT od.ID ) as total FROM {$wpdb->posts} as od";
		}else {
            $query['select'] = "SELECT product.* FROM {$wpdb->posts} as od";
        }

        $query['join']   = "INNER JOIN {$wpdb->postmeta} AS od_meta ON od.ID = od_meta.post_id ";
        $query['join'] .= "INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON (od.ID = order_items.order_id) ";

        $query[ "join" ] .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta ON (order_items.order_item_id = order_item_meta.order_item_id)  AND (order_item_meta.meta_key = '_product_id')";

        $query['join']   .= "INNER JOIN {$wpdb->posts} AS product ON order_item_meta.meta_value = product.ID";

        $query['where'] = "WHERE od.post_type = 'shop_order' ";
        $query['where'] .= "AND od.post_status = 'wc-completed' ";
        $query['where'] .= sprintf( "AND od_meta.meta_key = '_customer_user' AND od_meta.meta_value = %d ", $current_user->ID );

        $query['where'] .= sprintf( "AND product.post_author = '%s' AND product.post_status = 'publish'", $vendor_id );
        $query['order'] = "ORDER BY od.post_date DESC";

        if( $count ) {
            return  $wpdb->get_var( implode(' ', $query) );
        }else {
            return  $wpdb->get_results( implode(' ', $query) );
        }
    }
}

/**
 * Display curent user ip address
 * @return string
 */
function woopanel_get_ip() {
    if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
    //check ip from share internet
    $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
    //to check ip is pass from proxy
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
    $ip = $_SERVER['REMOTE_ADDR'];
    }
    return apply_filters( 'wpb_get_ip', $ip );
}