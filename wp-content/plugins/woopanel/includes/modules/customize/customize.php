<?php

define('NBT_CUSTOMIZE_PATH', plugin_dir_path( __FILE__ ));
define('NBT_CUSTOMIZE_URL', plugin_dir_url( __FILE__ ));

/**
 * WooPanel Customize class
 *
 * @package WooPanel_Customize
 */
class WooPanel_Customize {

  /**
   * Set module id
   *
   * @var string
   */
    static $plugin_id = 'dokan-review';

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


        if ( class_exists( 'WeDevs_Dokan' ) ) {
            add_filter( 'woopanel_menus', array(__CLASS__, 'woopanel_menus') );
            add_filter( 'woopanel_query_var_filter', array(__CLASS__, 'add_customize_query_vars'), 20, 1 );
            add_action( 'woopanel_dashboard_customize_endpoint', array(__CLASS__, 'customize_endpoint' ) );
            add_action( 'wp_enqueue_scripts', array( __CLASS__, 'frontend_enqueue_scripts') );
            

            include_once NBT_CUSTOMIZE_PATH . 'dokan-store/dokan-store.php';
        }

        // State that initialization completed.
        self::$initialized = true;
    }

    /**
     * Add menu for WooPanel
     *
     * @var array
     */
    public static function woopanel_menus( $woopanel_menus ) {
        $woopanel_menus[28] = array(
            'id'         => 'customize',
            'menu_slug'  => 'customize',
            'menu_title' => esc_html__( 'Customize', 'woopanel' ),
            'capability' => '',
            'page_title' => '',
            'icon'       => 'flaticon-responsive',
            'classes'    => '',
        );

        return $woopanel_menus;
    }

    public static function add_customize_query_vars( $query_vars ) {
        $query_vars[] = 'customize';

        return $query_vars;
    }

    public static function customize_endpoint() {
        woopanel_get_template('customize.php');
    }

    public static function settings() {
    	return apply_filters( 'woopanel_customize_settings', array() );
    }

    public static function frontend_enqueue_scripts() {
      global $wp_query;


      // Only show on Store Vendor
      if( ! isset( $wp_query->query['store'] ) ) {
        return;
      }


      wp_add_inline_style( 'dokan-style', apply_filters( 'woopanel_frontend_inline_style', false ) );
    }

}

WooPanel_Customize::initialize();