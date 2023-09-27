<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (defined('DOING_AJAX')) {
    if (isset($_REQUEST['action'])) {
//do not recalculate refund amounts when we are in order backend
        if ($_REQUEST['action'] == 'woocommerce_refund_line_items') {
            return;
        }

        if (isset($_REQUEST['order_id']) AND $_REQUEST['action'] == 'woocommerce_load_order_items') {
            return;
        }
    }
}

define('NBTWCCS_VERSION', '2.2.3');
define('NBTWCCS_MIN_WOOCOMMERCE', '2.6');
define('NBTWCCS_PATH', plugin_dir_path(__FILE__));
define('NBTWCCS_LINK', plugin_dir_url(__FILE__));


//classes
include_once NBTWCCS_PATH . 'inc/storage.php';
include_once NBTWCCS_PATH . 'inc/cron.php';
include_once NBTWCCS_PATH . 'inc/fixed/fixed_amount.php';
include_once NBTWCCS_PATH . 'inc/fixed/fixed_price.php';


class NBT_Solutions_Wc_Currency_Switcher {

    /**
     * Variable to hold the initialization state.
     *
     * @var  boolean
     */
    protected static $initialized = false;
    private static $support_time = 1519919054; // Date of the old < 3.3 version support
    private static $default_woo_version = 2.9;
    private static $actualized = 0.0;
    private static $version_key = "nbtwccs_woo_version";
    private static $_nbtwccs = null;
    
    public static function initialize()
    {
        if (self::$initialized) {
            return;
        }
        if (time() > self::$support_time) {
            self::$default_woo_version = 3.3;
        }
        $actualized = floatval(get_option(self::$version_key, self::$default_woo_version));

        if (version_compare($actualized , '3.3', '<')) {
            include_once NBTWCCS_PATH . 'inc/nbtwccs_before_33.php';
           
        } else {
            include_once NBTWCCS_PATH . 'inc/nbtwccs_after_33.php';
            include_once NBTWCCS_PATH . 'inc/fixed/fixed_coupon.php';
            include_once NBTWCCS_PATH . 'inc/fixed/fixed_shipping.php';
            include_once NBTWCCS_PATH . 'inc/fixed/fixed_shipping_free.php';
        } 

        self::$initialized = true;
    } 

}

if (isset($_GET['P3_NOCACHE'])) {
    //stupid trick for that who believes in P3
    return;
}
