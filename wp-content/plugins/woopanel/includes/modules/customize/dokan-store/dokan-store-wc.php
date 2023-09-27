<?php
class WooPanel_Customize_Dokan_Store_WC {
	private $dokan_layout_slug = 'dokan-store';
	
	function __construct() {
		add_action ( 'woocommerce_after_shop_loop_item', array( $this, 'additional_product_data' ), 99999 );

        $this->wc_ordering();
	}

    public function wc_ordering() {
        if( isset($_GET['orderby']) ) {
            $order = '';
            $orderby_value = isset($_GET['orderby']) ? wc_clean( (string) wp_unslash( $_GET['orderby'] ) ) : '';

            $orderby_value = is_array( $orderby_value ) ? $orderby_value : explode( '-', $orderby_value );
            $orderby       = esc_attr( $orderby_value[0] );
            $order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : $order;


            // Convert to correct format.
            $orderby = strtolower( is_array( $orderby ) ? (string) current( $orderby ) : (string) $orderby );
            $order   = strtoupper( is_array( $order ) ? (string) current( $order ) : (string) $order );


            switch ($orderby) {
                case 'menu_order':
                    $args['orderby'] = 'menu_order title';
                    break;
                case 'date':
                    $args['orderby'] = 'date ID';
                    $args['order']   = ( 'ASC' === $order ) ? 'ASC' : 'DESC';
                    break;
                case 'popularity':
                    add_filter( 'posts_clauses', array( $this, 'order_by_popularity_post_clauses' ) );
                    break;
                case 'price':
                    $callback = 'DESC' === $order ? 'order_by_price_desc_post_clauses' : 'order_by_price_asc_post_clauses';
                    add_filter( 'posts_clauses', array( $this, $callback ) );
                    break;
                case 'rating':
                    add_filter( 'posts_clauses', array( $this, 'order_by_rating_post_clauses' ) );
                    break;
                default:
                    # code...
                    break;
            }
        }
    }


    /**
     * Handle numeric price sorting.
     *
     * @param array $args Query args.
     * @return array
     */
    public function order_by_price_asc_post_clauses( $args ) {
        $args['join']    = $this->append_product_sorting_table_join( $args['join'] );
        $args['orderby'] = ' wc_product_meta_lookup.min_price ASC, wc_product_meta_lookup.product_id ASC ';
        return $args;
    }

    /**
     * Handle numeric price sorting.
     *
     * @param array $args Query args.
     * @return array
     */
    public function order_by_price_desc_post_clauses( $args ) {
        $args['join']    = $this->append_product_sorting_table_join( $args['join'] );
        $args['orderby'] = ' wc_product_meta_lookup.max_price DESC, wc_product_meta_lookup.product_id DESC ';
        return $args;
    }
    
    public function order_by_popularity_post_clauses( $args ) {
        $args['join']    = $this->append_product_sorting_table_join( $args['join'] );
        $args['orderby'] = ' wc_product_meta_lookup.total_sales DESC, wc_product_meta_lookup.product_id DESC ';
        return $args;
    }


    /**
     * Order by rating post clauses.
     *
     * @param array $args Query args.
     * @return array
     */
    public function order_by_rating_post_clauses( $args ) {
        $args['join']    = $this->append_product_sorting_table_join( $args['join'] );
        $args['orderby'] = ' wc_product_meta_lookup.average_rating DESC, wc_product_meta_lookup.product_id DESC ';
        return $args;
    }

    /**
     * Join wc_product_meta_lookup to posts if not already joined.
     *
     * @param string $sql SQL join.
     * @return string
     */
    private function append_product_sorting_table_join( $sql ) {
        global $wpdb;

        if ( ! strstr( $sql, 'wc_product_meta_lookup' ) ) {
            $sql .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
        }
        return $sql;
    }


	public function additional_product_data() {
        global $wp_query;

		if ( ! empty($wp_query->query['store']) && ! is_admin() ) {
            $post_id = get_the_ID();
            $post_type = get_post_type( $post_id );
            $is_add = true;
            if( is_product() ) {
                global $wp_query;
                $check_post = get_post($post_id);
                if( ! empty($wp_query->queried_object_id) && $wp_query->queried_object_id == $post_id ) {
                    $is_add = false;
                }
            }

            if ( $post_type == 'product' && $is_add ) {
            	set_query_var( 'post_id', $post_id );
				$template = WOODASHBOARD_TEMPLATE_DIR . 'dokan-store/store-wc-list.php';
				load_template( $template, false );
            }
		}
	}
}

new WooPanel_Customize_Dokan_Store_WC();