<?php

/**
 * WooDashboard Rewrite class
 *
 * @package WooDashboard_Rewrites
 */
class WooDashboard_Rewrites {

    /**
     * Query var
     * @var array
     */
    public $query_vars = array();
    
    /**
     * Query var
     * @var array
     */
    public $vendor_query_vars = array();

    /**
     * WooDashboard_Rewrites Constructor.
     */
    public function __construct() {
        /**
         * Fires after WordPress has finished loading but before any headers are sent.
         *
         * @since 1.0.0
         * @hook init
         * @function register_rule
         * @param null
         */
        add_action( 'init', array( $this, 'register_rule' ) );
        add_action('wp', array( $this, 'permission_pages') );
    }

    /**
     * Main WooDashboard_Rewrites Instance.
     *
     * Ensures only one instance of WooPanel is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WooPanel()
     * @return WooPanel - Main instance.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new WooDashboard_Rewrites();
        }

        return $instance;
    }

    /**
     * Register query vars
     */
    public function init_query_vars( $vendor = true ) {
        /**
         * Add quere var menu
         *
         * @since 1.0.0
         * @hook woopanel_query_var_filter
         * @type apply_filters
         * @param {array} $query_vars
         */
        $this->vendor_query_vars = apply_filters( 'woopanel_vendor_query_var', array(
            'articles',
            'article',
            'categories',
            'products',
            'product',
            'product-categories',
            'product-tags',
            'product-attributes',
            'product-orders',
            'order',
            'coupons',
            'coupon',
            'customers',
            'customer',
            'faqs',
            'faq',
            'comments',
            'comment',
            'reviews',
            'review',
            'settings',
        ));

        $this->query_vars = apply_filters( 'woopanel_query_var_filter', array(
            'dashboard',
            'edit-account',
            'nblogout',
            'profile'
        ));

        if( $vendor ) {
            $this->query_vars = array_merge($this->query_vars, $this->vendor_query_vars);
        }

        return $this->query_vars;
    }

    public function permission_pages() {
        global $wp_query;

        if( isset($wp_query->query['pagename']) && $wp_query->query['pagename'] == woopanel_dashboard_pagename() ) {
            unset($wp_query->query['pagename']);
            $page = array_keys($wp_query->query);

            $unsets = array( 'coupon', 'product-categories', 'product-tags', 'product-attributes');
            $unset_alls = array('coupons');

            if( ! is_super_admin() ) {
                

                if( isset($page[0]) && in_array($page[0], $unsets)) {
                    wp_safe_redirect( woopanel_dashboard_url() );
                    die();
                }
            }


            if( isset($page[0]) && in_array($page[0], $unset_alls)) {
                wp_safe_redirect( woopanel_dashboard_url() );
                die();
            }
        }


    }


    /**
     * Register rewrite rules
     */
    function register_rule() {
        $woopanel_dashboard_url = woopanel_dashboard_url();
        $woopanel_query_vars = $this->init_query_vars( is_shop_staff() );

        foreach ( $woopanel_query_vars as $var ) {
            add_rewrite_endpoint( $var, EP_PAGES );
        }


        /**
         * Rewrite rules loaded
         *
         * @since 1.0.0
         * @hook woopanel_rewrite_rules_loaded
         * @param {string} $url Dashboard URL
         */
        do_action( 'woopanel_rewrite_rules_loaded', $woopanel_dashboard_url );

        flush_rewrite_rules();
    }
}