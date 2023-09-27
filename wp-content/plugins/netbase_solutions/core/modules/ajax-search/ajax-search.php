<?php
/*
  Plugin Name: NBT Ajax Search
  Plugin URI: http://netbaseteam.com
  Description: This is package of solutions core.
  Version: 1.5
  Author: Netbaseteam
  Author URI: ttps://netbaseteam.com/
 */
define('AJAX_SEARCH_PATH', plugin_dir_path( __FILE__ ));
define('AJAX_SEARCH_URL', plugin_dir_url( __FILE__ ));
class NBT_Solutions_Ajax_Search {

  static $plugin_id = 'ajax-search';
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

        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            add_action( 'admin_notices', array( __CLASS__, 'install_woocommerce_admin_notice') );
        }else{
            include(AJAX_SEARCH_PATH . '/inc/frontend.php');
            include(AJAX_SEARCH_PATH . '/inc/functions.init.php');

            if( ! defined('PREFIX_NBT_SOL')){
                include(AJAX_SEARCH_PATH . '/inc/settings.php');
            }
            if ( !class_exists('NBT_Solutions_Metabox') ) {
                require_once AJAX_SEARCH_PATH . '/inc/metabox.php';
                NBT_Solutions_Metabox::initialize();
            }
        }
        // Register actions to do something.
        //add_action( 'action_name'   , array( __CLASS__, 'method_name'    ) );
        // State that initialization completed.
        self::$initialized = true;
    }


    public static function _register_widgets(){
        register_widget( 'NBT_AjaxSearch_Widget' );
    }
    /**
     * Method Featured.
     *
     * @return  array
     */
    public static function install_woocommerce_admin_notice() {?>
        <div class="error">
            <p><?php _e( 'WooCommerce plugin is not activated. Please install and activate it to use for plugin <strong>NBT WooCommerce Price Matrix</strong>.', 'nbt-solution' ); ?></p>
        </div>
        <?php    
    }


    public static function set_ajaxcart_icon(){
        $set_icon = array(
            'nbt-icon-basket',
            'nbt-icon-basket-1',
            'nbt-icon-basket-2',
            'nbt-icon-basket-3',
            'nbt-icon-basket-4',
            'nbt-icon-basket-alt',
            'nbt-icon-shopping-basket',
        );

        return $set_icon;
    } 
}



if( ! defined('PREFIX_NBT_SOL')){
    NBT_Solutions_Ajax_Search::initialize();
}