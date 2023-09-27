<?php
/**
 * @version    1.0
 * @package    Package Name
 * @author     Your Team <support@yourdomain.com>
 * @copyright  Copyright (C) 2014 yourdomain.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

/**
 * Plug additional sidebars into WordPress.
 *
 * @package  Package Name
 * @since    1.0
 */
define('NBT_REG_PATH', plugin_dir_path( __FILE__ ));
define('NBT_REG_URL', plugin_dir_url( __FILE__ ));
class NBT_Solutions_Register {
    /**
     * Variable to hold the initialization state.
     *
     * @var  boolean
     */
    protected static $initialized = false;

    private static $settings_saved;
    private static $score_settings;

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

        self::$settings_saved = false;

        /**
         * Load modules
         */
        add_action( 'widgets_init', array(__CLASS__, 'register_widgets') );

        self::$score_settings = (is_array(get_option('solutions_core_settings'))?get_option('solutions_core_settings'):array());
        if( in_array('brands', self::$score_settings) ) {
            include NBT_CORE_PATH.'modules/brands/inc/metabox.php';
            include NBT_CORE_PATH.'modules/brands/inc/widgets.php';
            NBT_Solutions_Brands_Metabox::initialize();
        }


        if( in_array('ajax-cart', self::$score_settings) ) {
            include NBT_CORE_PATH.'modules/ajax-cart/inc/widgets.php';
        }
		
        if( in_array('ajax-search', self::$score_settings) ) {
            include NBT_CORE_PATH.'modules/ajax-search/inc/widgets.php';
        }

        
        if( in_array('wc-currency-switcher', self::$score_settings) ) {
            include NBT_CORE_PATH.'modules/wc-currency-switcher/inc/widgets.php';
        }

        if( in_array('social-login', self::$score_settings) ) {
            include NBT_CORE_PATH.'modules/social-login/inc/widgets.php';
        }

        self::$initialized = true;
    }

    public static function register_widgets(){
        if( in_array('brands', self::$score_settings) ) {
            register_widget( 'NBT_Brands_Thumbnail_Widget' );
            register_widget( 'NBT_Brands_Slider_Widget' );
        }
        if( in_array('ajax-cart', self::$score_settings) ) {
            register_widget( 'NBT_Ajax_Cart_Widget' );
        }        
		
        if( in_array('ajax-search', self::$score_settings) ) {
            register_widget( 'NBT_Ajax_Search_Widget' );
        }
        
        if( in_array('wc-currency-switcher', self::$score_settings) ) {
        register_widget( 'NBT_WC_Currency_Switcher_Widgets' );
        }

        if( in_array('social-login', self::$score_settings) ) {
        register_widget( 'NBT_Social_Login_Widgets' );
        }

    }

}

