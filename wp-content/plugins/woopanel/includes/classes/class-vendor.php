<?php
global $wpdb;

/**
 * WooPanel Template class
 *
 * @package WooPanel_Template
 */
define( 'WOOPANEL_STORE_LOCATOR_PREFIX', $wpdb->prefix . "wplsl_" );

class WooPanel_Vendor {

    /**
     * WooPanel_Template Constructor.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'register_rule' ) );
        add_filter( 'query_vars', array( $this, 'register_query_var' ) );
        add_filter( 'template_include', array( $this, 'vendor_template' ) );
        add_filter('media_view_settings', array( $this, 'media_view_settings'), 999, 2 );


        add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 999, 1 );
        add_action( 'do_meta_boxes', array( $this, 'remove_dokan_metaboxes' ) );


        add_action( 'wp_enqueue_scripts', array($this, 'store_scripts'), 99, 1 );


        add_action('init', function() {
            global $current_user;

            update_user_meta($current_user->ID, 'woopanel_enable_selling', 'yes');
        });


        add_action( 'woocommerce_created_customer', array($this, 'wc_add_role_vendor'), 20, 3 );
        $this->user_roles();
        $this->includes();
    }

    function includes() {
        include_once WOODASHBOARD_INC_DIR . 'classes/vendor/admin/user-profile.php';
        include_once WOODASHBOARD_INC_DIR . 'classes/vendor/query-vendor.php';
        include_once WOODASHBOARD_INC_DIR . 'classes/vendor/map-widgets.php';
        include_once WOODASHBOARD_INC_DIR . 'classes/vendor/shortcode.php';
        include_once WOODASHBOARD_INC_DIR . 'classes/vendor/ajax.php';
        include_once WOODASHBOARD_INC_DIR . 'classes/vendor/template.php';
    }

    function register_rule() {
        global $admin_options;

        $profile_store_permalink = isset($admin_options->options['profile_store_permalink']) ? esc_attr($admin_options->options['profile_store_permalink']) : 'store-profile';
        
        add_rewrite_endpoint( 'wpl_seller', EP_PAGES );
        add_rewrite_rule( $profile_store_permalink . '/([^/]+)?$', 'index.php?wpl_seller=vendor_profile&author_name=$matches[1]', 'top' );
        add_rewrite_rule( $profile_store_permalink . '/([^/]+)/tos?$', 'index.php?wpl_seller=vendor_profile&author_name=$matches[1]&section=tos', 'top' );

    }

    function register_query_var( $vars ) {
        $vars[] = 'wpl_seller';
        $vars[] = 'vendor';
        $vars[] = 'section';

        return $vars;
    }

    function vendor_template( $template ) {
        global $wp_query, $current_user, $admin_options;

        if( isset($wp_query->query['wpl_seller']) ) {

            $path = str_replace( '_', '/', $wp_query->query['wpl_seller'] );
            $_template = WOODASHBOARD_TEMPLATE_DIR . $path . '.php';

            if( file_exists($_template) ) {
                $wp_query->is_404 = false;
                $template = $_template;
            }
        }


        return $template;
    }

    public function store_scripts() {
        global $wp_query;

        if( isset($wp_query->query['wpl_seller']) || isset($wp_query->query_vars['store_list']) ) {
            wp_enqueue_style('wpl-store', WOODASHBOARD_URL .'assets/css/store.css', false, '1.4.0', 'all' );

            if(isset($wp_query->query_vars['store_list']) ) {
                wp_enqueue_script( 'wpl-store-list', WOODASHBOARD_URL . 'assets/js/store-list.js', array('jquery'), '1.0', true );
            }

            if( isset($wp_query->query['wpl_seller']) ) {
               wp_enqueue_script( 'wpl-store-profile', WOODASHBOARD_URL . 'assets/js/store-profile.js', array('jquery'), '1.0', true ); 
            }
       }
    }

    /**
     * Init woopanel user roles
     *
     * @since Dokan 1.0
     *
     * @global WP_Roles $wp_roles
     */
    function user_roles() {
        global $wp_roles;

        if ( class_exists( 'WP_Roles' ) && ! isset( $wp_roles ) ) {
            $wp_roles = new \WP_Roles();
        }

        add_role( 'wpl_seller', __( 'WPL Seller', 'woopanel' ), array(
            'read'                      => true,
            'publish_posts'             => true,
            'edit_post'                 => true,
            'edit_posts'                => true,
            'delete_published_posts'    => true,
            'edit_published_posts'      => true,
            'delete_posts'              => true,
            'manage_categories'         => true,
            'moderate_comments'         => true,
            'unfiltered_html'           => true,
            'upload_files'              => true,
            'edit_shop_orders'          => true,
            'edit_product'              => true,
            'read_product'              => true,
            'delete_product'            => true,
            'edit_products'             => true,
            'publish_products'          => true,
            'read_private_products'     => true,
            'delete_products'           => true,
            'delete_products'           => true,
            'delete_private_products'   => true,
            'delete_published_products' => true,
            'delete_published_products' => true,
            'edit_private_products'     => true,
            'edit_published_products'   => true,
            'manage_product_terms'      => true,
            'delete_product_terms'      => true,
            'assign_product_terms'      => true,
            'upload_files'              => true
        ) );

        $capabilities = array();
        $all_cap      = $this->get_all_caps();

        foreach ( $all_cap as $key => $cap ) {
            $capabilities = array_merge( $capabilities, array_keys( $cap ) );
        }

        foreach ( $capabilities as $key => $capability ) {
            $wp_roles->add_cap( 'seller', $capability );
            $wp_roles->add_cap( 'administrator', $capability );
            $wp_roles->add_cap( 'shop_manager', $capability );
        }
    }


    /**
     * Get all cap related to seller
     *
     * @since 2.7.3
     *
     * @return array
     */
    function get_all_caps() {
        return array();
    }

    function pre_get_posts( $query ) {
        global $admin_options;

        if ( is_admin() ) {
            return;
        }

        $store_listing_page = $admin_options->options['woopanel_page_stores'];

        if( isset($query->query['wpl_seller']) && isset($query->query['author_name']) ) {
            $store_user = WooDashboard()->store->get( get_query_var( 'author_name' ) );
            $query->set( 'store_user', $store_user );
        }

        if( isset($query->query['store']) && ! empty($_GET['id']) ) {
            $store_id = absint($_GET['id']);
            $store_user = WooDashboard()->store->get( $store_id );
            $query->set( 'store_user', $store_user );
        }

      if( get_query_var('pagename') == $store_listing_page ) {
            $query->set( 'store_list', $admin_options->options['woopanel_page_stores'] );
        }


        return $query;

    }

    public function media_view_settings($settings) {
        global $wp_query;
        
        if( isset($wp_query->query_vars['pagename']) && $wp_query->query_vars['pagename'] == 'sellercenter' && isset($wp_query->query['settings']) || isset($wp_query->query['store']) ) {
            $settings['post']['id'] = 0;
        }

        return $settings;
    }

    public function wc_add_role_vendor( $user_id, $data, $password ) {
        $user = new WP_User( $user_id );

        foreach (NBWooCommerceDashboard::$role as $role) {
            $user->add_role( $role );
        }
    }

    public function remove_dokan_metaboxes() {
        remove_meta_box( 'sellerdiv', 'product', 'normal' );
    }
}

/**
 * Returns the main instance of WooPanel_Vendor.
 *
 * @since  1.0.0
 * @return WooPanel_Vendor
 */
new WooPanel_Vendor();