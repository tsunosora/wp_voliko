<?php
define('NBT_ODN_PATH', plugin_dir_path( __FILE__ ));
define('NBT_ODN_URL', plugin_dir_url( __FILE__ ));

class NBT_Solutions_Order_Delivery_Note {

    static $plugin_id = 'order-delivery-note';
    /**
     * Variable to hold the initialization state.
     *
     * @var  boolean
     */
    protected static $initialized = false;

    public  $types = array();

        public static $writepanel;
        public static $print;
        public static $theme;

        public static $plugin_url;
		public static $plugin_path;
		public static $plugin_basefile;
		public static $plugin_basefile_path;
		public static $plugin_text_domain;
    
    /**
     * Initialize functions.
     *
     * @return  void
     */

    public  static function initialize() {
        // Do nothing if pluggable functions already initialized.

        if ( self::$initialized ) {
            return;
        }

        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            add_action( 'admin_notices', array( __CLASS__, 'install_woocommerce_admin_notice') );
        }else{
            add_action( 'woocommerce_settings_start', array( __CLASS__, 'add_assets' ) );
            require_once NBT_ODN_PATH . 'nbt_delivery_note_extra.php';
            require_once NBT_ODN_PATH . 'inc/settings.php';
            
        }
        // State that initialization completed.
        self::$initialized = true;
    }
        	public static function add_assets() {
			// Styles
			wp_enqueue_style( 'woocommerce-delivery-notes-admin', Order_Delivery_Note::$plugin_url . '/css/admin.css' );

			// Scripts
			wp_enqueue_media();
			wp_enqueue_script( 'woocommerce-delivery-notes-print-link',Order_Delivery_Note::$plugin_url  . '/js/jquery.print-link.js', array( 'jquery' ) );
			wp_enqueue_script( 'woocommerce-delivery-notes-admin',Order_Delivery_Note::$plugin_url . 'js/admin.js', array( 'jquery', 'custom-header', 'woocommerce-delivery-notes-print-link' ) );

			// Localize the script strings
			$translation = array( 'resetCounter' => __( 'Do you really want to reset the counter to zero? This process can\'t be undone.', 'woocommerce-delivery-notes' ) );
			wp_localize_script( 'woocommerce-delivery-notes-admin', 'WCDNText', $translation );
		}
        public  function install_woocommerce_admin_notice() {
            ?>
            <div class="error">
                <p><?php _e( 'WooCommerce plugin is not activated. Please install and activate it to use for plugin <strong>NBT Order Upload</strong>.', 'yith-woocommerce-wishlist' ); ?></p>
            </div>
        <?php
        }
}
NBT_Solutions_Order_Delivery_Note::initialize();
