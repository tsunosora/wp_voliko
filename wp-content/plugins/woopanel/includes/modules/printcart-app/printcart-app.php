<?php

define('NBT_PRINTCART_APP_PATH', plugin_dir_path( __FILE__ ));
define('NBT_PRINTCART_APP_URL', plugin_dir_url( __FILE__ ));

/**
 * WooPanel PrintCart App class
 *
 * @package WooPanel_Printcart_App
 */
class WooPanel_PrintCart_App {

  /**
   * Set module id
   *
   * @var string
   */
    static $plugin_id = 'printcart-app';

    /**
     * Variable to hold the initialization state.
     *
     * @var  boolean
     */
    protected static $initialized = false;
    
    /**
     * Initialize functions.
     *
     * @return  void
     */
    public static function initialize() {
        // Do nothing if pluggable functions already initialized.
        if ( self::$initialized ) {
            return;
        }


        add_filter( 'woopanel_menus', array(__CLASS__, 'woopanel_menus') );
        add_filter( 'woopanel_submenus', array(__CLASS__, 'woopanel_submenus') );
        add_filter( 'woopanel_query_var_filter', array(__CLASS__, 'add_printcart_app_query_vars'), 20, 1 );

        add_action( 'woopanel_dashboard_shopify-app_endpoint', array(__CLASS__, 'shopify_app_endpoint' ) );
        add_action( 'woopanel_dashboard_printfull-app_endpoint', array(__CLASS__, 'printfull_app_endpoint' ) );
        add_action( 'woopanel_dashboard_woocommerce-app_endpoint', array(__CLASS__, 'woocommerce_app_endpoint' ) );

        // State that initialization completed.
        self::$initialized = true;
    }

    /**
     * Add menu for WooPanel
     *
     * @var array
     */
    public static function woopanel_menus( $woopanel_menus ) {
        $woopanel_menus[29] = array(
            'id'         => 'printcart-app',
            'menu_slug'  => 'shopify-app',
            'menu_title' => esc_html__( 'PrintCart Apps', 'woopanel' ),
            'capability' => '',
            'page_title' => '',
            'icon'       => 'flaticon-responsive',
            'classes'    => '',
        );
        $woopanel_menus[35] = array(
            'id'         => 'pricing',
            'menu_slug'  => 'pricing',
            'menu_title' => esc_html__( 'Pricing', 'woopanel' ),
            'capability' => '',
            'page_title' => '',
            'icon'       => 'flaticon-responsive',
            'classes'    => '',
            'link'      => 'https://printcart.com/pricing'
        );
        return $woopanel_menus;
    }

    public static function woopanel_submenus( $woopanel_submenus ) {
        $woopanel_submenus['printcart-app'] = array(
            5 => array(
                'id'         => 'printcart_shopify',
                'menu_slug'  => 'shopify-app',
                'label'      => esc_html__( 'Shopify App', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ),
            6 => array(
                'id'         => 'printcart_printfull',
                'menu_slug'  => 'printfull-app',
                'label'      => esc_html__( 'PrintFull App', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ),
            7 => array(
                'id'         => 'printcart_woocommerce',
                'menu_slug'  => 'woocommerce-app',
                'label'      => esc_html__( 'WooCommerce App', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            )
        );

        return $woopanel_submenus;
    }

    public static function add_printcart_app_query_vars( $query_vars ) {
        $query_vars[] = 'printcart-app';
        $query_vars[] = 'shopify-app';
        $query_vars[] = 'printfull-app';
        $query_vars[] = 'woocommerce-app';

        return $query_vars;
    }

    public static function shopify_app_endpoint() {
        // woopanel_get_template('customize.php');

        echo 'Sử dụng function shopify_app_endpoint trên để nhúng template';
    }

    public static function printfull_app_endpoint() {
        // woopanel_get_template('customize.php');

        echo 'Sử dụng function printfull_app_endpoint trên để nhúng template';
    }

    public static function woocommerce_app_endpoint() {
        // woopanel_get_template('customize.php');

        echo 'Sử dụng function woocommerce_app_endpoint trên để nhúng template';
    }
}

WooPanel_PrintCart_App::initialize();