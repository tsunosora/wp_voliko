<?php
/*
  Plugin Name: Ajax Drop Down Cart for WooCommerce Wordpress
  Plugin URI: http://netbaseteam.com
  Description: Change the default behavior of WooCommerce Cart page, making AJAX requests when quantity field changes
  Version: 1.5.1
  Author: Netbaseteam
  Author URI: ttps://netbaseteam.com/
*/
 
define('NB_AJAXCART_PATH', plugin_dir_path( __FILE__ ));
define('NB_AJAXCART_URL', plugin_dir_url( __FILE__ ));
class NBT_Solutions_Ajax_Cart {

    static $plugin_id = 'ajax-cart';
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
          $plugin = plugin_basename( __FILE__ );
          add_filter( "plugin_action_links_$plugin", array(__CLASS__, 'settings_link') );

            if( !class_exists('NBT_WooCommerce_AjaxCart_Admin') ){
               require_once( NB_AJAXCART_PATH . '/inc/admin.php' ); 
            }
	

			     require_once( NB_AJAXCART_PATH . '/inc/frontend.php' );
	

            require_once( NB_AJAXCART_PATH . '/inc/functions.php' );

            if( ! defined('PREFIX_NBT_SOL')){
                include(NB_AJAXCART_PATH . '/inc/widgets.php');
                include(NB_AJAXCART_PATH . '/inc/settings.php');

                add_action( 'widgets_init', array(__CLASS__, 'register_widgets') );
            }
            if ( !class_exists('NBT_Solutions_Metabox') ) {
                require_once NB_AJAXCART_PATH . '/inc/metabox.php';
                NBT_Solutions_Metabox::initialize();
            }
        }
        // State that initialization completed.
        self::$initialized = true;
    }
    
    public static function settings_link( $links ) {
        unset($links['edit']);
        $settings_link['configure'] = '<a href="'.admin_url('admin.php?page=nbt-ajax-cart').'">' . __( 'Configure' ) . '</a>';
        $settings_link['docs'] = '<a href="http://demo5.cmsmart.net/wordpress/plg_ajaxcart/userguide.pdf">' . __( 'Docs' ) . '</a>';
        $settings_link['support'] = '<a href="https://cmsmart.net/support_ticket/" target="_blank">' . __( 'Support' ) . '</a>';


        $links = array_merge( $settings_link, $links );
        return $links;
    }
    /**
     * Method Featured.
     *
     * @return  array
     */
    public static function install_woocommerce_admin_notice() {?>
        <div class="error">
            <p><?php _e( 'WooCommerce plugin is not activated. Please install and activate it to use for plugin <strong>Ajax Drop Down Cart for WooCommerce Wordpress</strong>.', 'nbt-solution' ); ?></p>
        </div>
        <?php    
    }

    public static function set_ajaxcart_icon(){
        $set_icon = array(
            'ajaxcart-icon-basket',
            'ajaxcart-icon-basket-1',
            'ajaxcart-icon-basket-2',
            'ajaxcart-icon-basket-3',
            'ajaxcart-icon-basket-4',
            'ajaxcart-icon-basket-alt',
            'ajaxcart-icon-shopping-basket',
        );

        return $set_icon;
    }

    public static function register_widgets(){
        register_widget( 'NBT_Ajax_Cart_Widget' );
    }
}

if( ! defined('PREFIX_NBT_SOL')){
    NBT_Solutions_Ajax_Cart::initialize();
}
