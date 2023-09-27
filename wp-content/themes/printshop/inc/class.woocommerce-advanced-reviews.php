<?php
if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists ( 'Wpnetbase_WooCommerce_Advanced_Reviews' ) ) {

    class Wpnetbase_WooCommerce_Advanced_Reviews {

        protected static $instance;
        
        public static function get_instance () {
            if ( is_null ( self::$instance ) ) {
                self::$instance = new self();
            }

            return self::$instance;
        }
       
        protected function __construct () {
            if ( ! function_exists ( 'WC' ) ) {
                return;
            }
             //  Load reviews template
            add_filter ( 'comments_template', array ( $this, 'show_advanced_reviews_template' ), 99 );
            /**
             * Add summary bars for product rating
             */
            add_action ( 'wpnetbase_advanced_reviews_before_reviews', array ( $this, 'load_reviews_summary' ) );

            
        }

        public function show_advanced_reviews_template ( $template ) {

            if ( get_post_type () === 'product' ) {
                
                return wc_locate_template ( "wpnetbase-product-reviews.php", '', '' );
            }

            return $template;
        }
        public function load_reviews_summary ( $template ) {
            if ( ! is_product () ) {
                return $template;
            }

            global $product;
            global $review_stats;

            $review_stats = array (
                '1'     =>  $this->wc_product_reviews_pro_get_product_rating_count ( $product->get_id(), 1 ) ,
                '2'     =>  $this->wc_product_reviews_pro_get_product_rating_count ( $product->get_id(), 2 ) ,
                '3'     =>  $this->wc_product_reviews_pro_get_product_rating_count ( $product->get_id(), 3 ) ,
                '4'     =>  $this->wc_product_reviews_pro_get_product_rating_count ( $product->get_id(), 4 ) ,
                '5'     =>  $this->wc_product_reviews_pro_get_product_rating_count ( $product->get_id(), 5 ) ,
                'total' =>  $this->wc_product_reviews_pro_get_product_rating_count ( $product->get_id() ) ,
            );

            wc_get_template ( 'wpnetbase-single-product-reviews.php', null, '', '' );
        }

        function wc_product_reviews_pro_get_product_rating_count( $product_id, $rating = null ) {
            global $wpdb;
            $where_meta_value   = $rating ? $wpdb->prepare( " AND meta_value = %d", $rating ) : " AND meta_value > 0";
            $count              = $wpdb->get_var( $wpdb->prepare("
                                          SELECT COUNT(meta_value) FROM $wpdb->commentmeta
                                          LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
                                          WHERE meta_key = 'rating'
                                          AND comment_post_ID = %d
                                          AND comment_approved = '1'
                                          ", $product_id ) . $where_meta_value );
            return $count;
        }
              

    }
}
$WPNETBASE_AdvancedReview = Wpnetbase_WooCommerce_Advanced_Reviews::get_instance ();