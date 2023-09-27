<?php
/*
  Plugin Name: NBT Gallery Slider
  Plugin URI: http://abc.com/woocommerce-price-matrix
  Description: Change the default behavior of WooCommerce Cart page, making AJAX requests when quantity field changes
  Version: 1.2
  Author: Katori
  Author URI: https://cmsmart.net/wordpress-plugins/nbt-woocommerce-price-matrix
 */
define('NBT_GSLIDER_PATH', plugin_dir_path(__FILE__));
define('NBT_GSLIDER_URL', plugin_dir_url(__FILE__));

class NBT_Solutions_Gallery_Slider
{
    static $plugin_id = 'gallery_slider';
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
    public static function initialize()
    {
        // Do nothing if pluggable functions already initialized.
        if (self::$initialized) {
            return;
        }
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            add_action('admin_notices', 'install_woocommerce_admin_notice');
        } else {
            require_once NBT_GSLIDER_PATH . 'inc/frontend.php';
        }

        // State that initialization completed.
        self::$initialized = true;
    }

}

if (!defined('PREFIX_NBT_SOL')) {
    NBT_Solutions_Gallery_Slider::initialize();
}