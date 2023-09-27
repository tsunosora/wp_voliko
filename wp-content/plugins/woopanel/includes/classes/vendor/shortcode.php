<?php

class WooPanel_Seller_Shortcode {

    /**
     * Total vendors found
     *
     * @var integer
     */
    private $total_users;

    function __construct()
    {

        if( ! is_admin() ) {
            add_shortcode( 'woopanel_stores', array( $this, 'add_shortcode' ) );
        }
    	 

    }

    public function add_shortcode( $atts ) {

        $defaults = array(
            'per_page' => 9,
            'search'   => 'yes',
            'per_row'  => 3,
            'featured' => 'no'
        );

        /**
         * Filter return the number of store listing number per page.
         *
         * @since 2.2
         *
         * @param array
         */
        $attr   = shortcode_atts( apply_filters( 'woopanel_store_listing_per_page', $defaults ), $atts );
        $paged  = (int) is_front_page() ? max( 1, get_query_var( 'page' ) ) : max( 1, get_query_var( 'paged' ) );
        $limit  = $attr['per_page'];
        $offset = ( $paged - 1 ) * $limit;

        $seller_args = array(
            'number' => $limit,
            'offset' => $offset
        );

        $_get_data = wp_unslash( $_GET );


        ob_start();
        woopanel_get_template_part( 'vendor/store-lists', false, array(
            'results' => $this->get_stores($offset, $limit),
            'per_row' => $attr['per_row'],
            'paged' => $paged,
            'count' => $this->get_count(),
            'limit' => $limit

        ) );
        $content = ob_get_clean();

        return apply_filters( 'woopanel_seller_listing', $content, $attr );

    }

    public function get_stores( $paged, $limit ) {
        global $wpdb;

        $query = $this->get_query(0, $paged, $limit);

        $sql = implode(' ', $query);

        return $wpdb->get_results($sql);
    }

    public function get_count() {
        global $wpdb;

        $query = $this->get_query(1);
        $sql = implode(' ', $query);


        return $wpdb->get_var($sql);
    }

    public function get_query( $count = 0, $paged = 0, $limit = 10) {
        global $wpdb;

        $prefix = WOOPANEL_STORE_LOCATOR_PREFIX;

        $query = array();
        if( empty($count) ) {
            $query['select'] = "SELECT s.*, u.user_login FROM " . $prefix . "stores as s";
        }else {
            $query['select'] = "SELECT COUNT(*) as total FROM " . $prefix . "stores as s";
        }
        
        $query['join'] = "LEFT JOIN {$prefix}stores_categories as sc ON s.id = sc.store_id ";
        $query['join'] .= "LEFT JOIN {$wpdb->prefix}users as u ON s.user_id = u.ID";
        $query['where'] = "WHERE (is_disabled is NULL || is_disabled = 0) ";

        if( ! empty($_GET['store_category']) ) {
            $store_category = absint($_GET['store_category']);
            $query['where'] .= "AND sc.category_id = {$store_category}";
        }
        //$query['where'] .= "AND s.user_id > 0";
        
        if( empty($count) ) {
            $query['group_by'] = "GROUP BY s.id";
            $query['limit'] = "LIMIT {$paged}, {$limit}";
        }

        return $query;
    }
    /**
     * Get vendors
     *
     * @param array $args
     *
     * @return array
     */
    public function get_vendors( $args = array() ) {
        $vendors = array();

        $defaults = array(
            'role__in'   => array( 'wpl_seller', 'administrator' ),
            'number'     => 10,
            'offset'     => 0,
            'orderby'    => 'registered',
            'order'      => 'ASC',
            'status'     => 'approved',
            'featured'   => '', // yes or no
            'meta_query' => array(),
        );

        $args   = wp_parse_args( $args, $defaults );
        $status = $args['status'];

        // check if the user has permission to see pending vendors
        if ( 'approved' != $args['status'] && current_user_can( 'manage_woocommerce' ) ) {
            $status = $args['status'];
        }

        if ( in_array( $status, array( 'approved', 'pending' ) ) ) {
            $operator = ( $status == 'approved' ) ? '=' : '!=';

            $args['meta_query'][] = array(
                'key'     => 'woopanel_enable_selling',
                'value'   => 'yes',
                'compare' => $operator
            );
        }

        // if featured
        if ( 'yes' == $args['featured'] ) {
            $args['meta_query'][] = array(
                'key'     => 'woopanel_feature_seller',
                'value'   => 'yes',
                'compare' => '='
            );
        }

        unset( $args['status'] );
        unset( $args['featured'] );

        $user_query = new WP_User_Query( $args );
        $results    = $user_query->get_results();

        $this->total_users = $user_query->total_users;

        foreach ( $results as $key => $result ) {
        	$vendors[] = new WooPanel_Query_Vendor( $result );
        }

        return $vendors;
    }

     /**
     * Get total user according to query
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_total() {
        return $this->total_users;
    }
}

new WooPanel_Seller_Shortcode();