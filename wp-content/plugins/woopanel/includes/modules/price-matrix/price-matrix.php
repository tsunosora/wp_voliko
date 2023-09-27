<?php
define('WOOPANEL_PRICEMATRIX_PATH', plugin_dir_path( __FILE__ ));
define('WOOPANEL_PRICEMATRIX_URL', plugin_dir_url( __FILE__ ));

/**
 * This class will load modules Price Matrix of WooPanel.
 *
 * @package WooPanel_Modules
 */
class WooPanel_Price_Matrix {

  /**
   * Set module id
   *
   * @var string
   */
    static $plugin_id = 'price-matrix';

  /**
   * Set URL redirect when verify Envato
   *
   * @var string
   */
	static $redirectURL = 'http://demo5.cmsmart.net/wordpress/envato/verify.php';

  /**
   * Return template path auth verify Envato
   *
   * @var string
   */
    static $temp_auth = false;
		
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

            self::$temp_auth = WOOPANEL_PRICEMATRIX_PATH . 'tpl/admin/settings/auth.php';
            
            if( file_exists(self::$temp_auth) ) {
    			   add_action( 'admin_notices', array( __CLASS__, 'install_license_notice') );
            }

            add_filter( 'body_class', array( __CLASS__, 'pm_body_classes') );

            $GLOBALS['price_matrix_settings'] = self::get_settings();

            require_once( WOOPANEL_PRICEMATRIX_PATH . 'inc/ajax.php' );
            require_once( WOOPANEL_PRICEMATRIX_PATH . 'inc/functions.init.php' );
            require_once( WOOPANEL_PRICEMATRIX_PATH . 'inc/admin.php' );
            require_once( WOOPANEL_PRICEMATRIX_PATH . 'inc/frontend.php' );

            if( ! defined('PREFIX_NBT_SOL')){
                include(WOOPANEL_PRICEMATRIX_PATH . 'inc/settings.php');
            }

            if ( ! class_exists('WooPanel_Modules_Metabox')  ){
                require_once WOOPANEL_PRICEMATRIX_PATH . 'inc/metabox.php';
                WooPanel_Modules_Metabox::initialize();
            }
        }
        // State that initialization completed.
        self::$initialized = true;
    }

    public static function get_settings() {
      $settings = WooPanel_Price_Matrix_Settings::get_settings();

      if( $settings ) {
        $new_settings = array();
        $data_setting = get_option(WooPanel_Price_Matrix::$plugin_id.'_settings');
        foreach ($settings as $setting_id => $value) {
          $setting_key = sprintf('wc_%s_%s', WooPanel_Price_Matrix::$plugin_id, $setting_id);

          if( isset($data_setting[$setting_key]) ) {
            $new_settings[$setting_key] = $data_setting[$setting_key];
          }else {
            $new_settings[$setting_key] = isset($value['default']) ? $value['default'] : false;
          }
        }
      }

      return $new_settings;
    }

  /**
   * Set direction (vertical|horizontal) of table
   *
   * @return array
   */
    public static function pm_direction() {
        return array(
            'vertical' => 'Vertical',
            'horizontal' => 'Horizontal'
        );
    }

  /**
   * Add price matrix class in the body_class()
   *
   * @return array
   */
    public static function pm_body_classes( $classes ) {
        global $woocommerce, $post, $product;
        if(isset($post) && get_post_meta($post->ID, '_enable_price_matrix', true) == 'on' && get_post_meta($post->ID, '_pm_num', true)){
                $classes[] = 'wc-price-matrix';
        }
        return $classes;
    }

  /**
   * Display admin_notice if WooCommerce plugin is not activated
   *
   * @return string
   */
    public static function install_woocommerce_admin_notice() {?>
        <div class="error">
            <p><?php printf( esc_html__( 'WooCommerce plugin is not activated. Please install and activate it to use for module %s.', 'woopanel' ), '<strong>Price Matrix</strong>' ); ?></p>
        </div>
        <?php    
    }

  /**
   * Get attribute by product id
   *
   * @return array
   */
    public static function get_attribute_taxonomies($product_id, $attribute_name) {
        $transient_name = 'wc_attr_tax_' . md5( absint($product_id) . esc_attr( $attribute_name ) );
        $attribute_taxonomies = get_transient( $transient_name );
        if ( false === $attribute_taxonomies ) {
            global $wpdb;

            $attribute_taxonomies = $wpdb->get_row( 'SELECT * FROM ' . esc_attr($wpdb->prefix) . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attribute_name'" );

            set_transient( $transient_name, $attribute_taxonomies );
        }

        return $attribute_taxonomies;
    }
	
  /**
   * Display admin_notice if use version FREE
   *
   * @return string
   */
    public static function install_license_notice() {
      if( woopanel_price_matrix_check_license() ) {?>
        <div class="error">
            <p><?php esc_html_e( 'You are use version FREE will limit 03 products can enable Price Matrix, please upgrade PRO version', 'woopanel' ); ?></p>
        </div>
        <?php
      }
    }
}

/**
 * Returns the main instance of WooPanel_Price_Matrix.
 *
 * @since  1.0.0
 * @return WooPanel_Price_Matrix
 */
WooPanel_Price_Matrix::initialize();