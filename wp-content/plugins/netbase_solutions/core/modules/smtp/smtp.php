<?php
/*
  Plugin Name: NBT SMTP
  Plugin URI: http://abc.com/woocommerce-price-matrix
  Description: Change the default behavior of WooCommerce Cart page, making AJAX requests when quantity field changes
  Version: 1.2
  Author: Katori
  Author URI: https://cmsmart.net/wordpress-plugins/nbt-woocommerce-price-matrix
 */
define('NBT_SMTP_PATH', plugin_dir_path( __FILE__ ));
define('NBT_SMTP_URL', plugin_dir_url( __FILE__ ));

class NBT_Solutions_SMTP {
    protected static $initialized = false;

    public static $wpms_options = array (
        'mail_from' => '',
        'mail_from_name' => '',
        'mailer' => 'smtp',
        'mail_set_return_path' => 'false',
        'smtp_host' => 'localhost',
        'smtp_port' => '25',
        'smtp_ssl' => 'none',
        'smtp_auth' => false,
        'smtp_user' => '',
        'smtp_pass' => '',
        'pepipost_user' => '',
        'pepipost_pass' => '',
        'pepipost_port' => '2525',
        'pepipost_ssl' => 'none'
    );
    
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
            $a = self::$wpms_options;

            add_filter( 'wp_mail_content_type', array(__CLASS__, 'set_content_type') );

        }
        // State that initialization completed.
        self::$initialized = true;
    }

    public static function set_content_type( $content_type ) {
        return 'text/html';
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

if( ! defined('PREFIX_NBT_SOL')){
    NBT_Solutions_SMTP::initialize();
}