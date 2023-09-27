<?php
/*
  Plugin Name: NBT FAQs
  Plugin URI: http://abc.com/woocommerce-price-matrix
  Description: Change the default behavior of WooCommerce Cart page, making AJAX requests when quantity field changes
  Version: 1.2
  Author: Katori
  Author URI: https://cmsmart.net/wordpress-plugins/nbt-woocommerce-price-matrix
 */
define('NBT_FAQS_PATH', plugin_dir_path( __FILE__ ));
define('NBT_FAQS_URL', plugin_dir_url( __FILE__ ));
class NBT_Solutions_Faqs {
    /**
     * Variable to hold the initialization state.
     *
     * @var  boolean
     */
    protected static $initialized = false;

    public static $types = array();
    
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
            add_action( 'admin_notices', 'install_woocommerce_admin_notice' );
        }else{
          require_once NBT_FAQS_PATH .'inc/admin.php';
          require_once NBT_FAQS_PATH .'inc/frontend.php';
          if( ! defined('PREFIX_NBT_SOL')){
              include(NBT_FAQS_PATH . 'inc/settings.php');
          }

          if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX) ) {
              require_once NBT_FAQS_PATH . 'inc/ajax.php';
              NBT_FAQs_Ajax::initialize();
          }
          
        }
        
        // State that initialization completed.
        self::$initialized = true;
    }

}


if( ! function_exists( 'install_woocommerce_admin_notice' ) ) {
    function install_woocommerce_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'WooCommerce plugin is not activated. Please install and activate it to use for plugin <strong>NBT Order Upload</strong>.', 'yith-woocommerce-wishlist' ); ?></p>
        </div>
    <?php
    }
}

if( ! function_exists( 'notice_writable_folder' ) ) {
    function notice_writable_folder() {
        ?>
        <div class="error">
            <p><?php _e( 'Uploads folder not writable. Please create a new folder as name <strong>nbt-order-uploads</strong> in path wp-content/uploads', 'yith-woocommerce-wishlist' ); ?></p>
        </div>
    <?php
    }
}

if(!function_exists('log_it')){
    function log_it( $message ) {
        if( WP_DEBUG === true ){
            if( is_array( $message ) || is_object( $message ) ){
                error_log( print_r( $message, true ) );
            } else {
                error_log( $message );
            }
        }
    }
}

if( ! defined('PREFIX_NBT_SOL')){
    NBT_Solutions_Faqs::initialize();
}