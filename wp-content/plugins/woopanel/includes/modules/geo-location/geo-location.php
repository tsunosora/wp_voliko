<?php

define('NBT_GEOLOCAL_PATH', plugin_dir_path( __FILE__ ));
define('NBT_GEOLOCAL_URL', plugin_dir_url( __FILE__ ));

/**
 * This class will load modules Geo Location of WooPanel.
 *
 * @package WooPanel_Modules
 */
class NBT_Solutions_Geo_Location {

  /**
   * Set module id
   *
   * @var string
   */
    static $plugin_id = 'geo-location';

    /**
     * Variable to hold the initialization state.
     *
     * @var  boolean
     */
    protected static $initialized = false;

    /**
     * Set heremap API version
     *
     * @var  number
     */
    static $here_ver = 3.0;

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
            require_once NBT_GEOLOCAL_PATH .'inc/ajax.php';
            require_once NBT_GEOLOCAL_PATH .'inc/widgets.php';
            require_once( NBT_GEOLOCAL_PATH . 'inc/admin.php' );
            require_once( NBT_GEOLOCAL_PATH . 'inc/frontend.php' );

        }
        // State that initialization completed.
        self::$initialized = true;
    }

    public static function get_woopanel_geo() {
      global $current_user;

      if( is_woopanel() && $current_user->exists() ) {
          $geoApplicationID = get_user_meta($current_user->ID, 'geo_application_id', true);
          $geoApplicationCode =  get_user_meta($current_user->ID, 'geo_application_code', true);

          if( ! empty($geoApplicationID) && ! empty($geoApplicationCode) ) {
              return true;
          }
      }
    }

    public static function get_admin_geo() {
      global $post, $current_user;

      if( empty($post) ) {
        return;
      }

      $location_tab = get_option('user_location_tab');
      $user_location_tab = self::user_location_tab($post->post_author, 'user_location_tab');
      if( ! empty($user_location_tab) ) {
        $location_tab = get_user_meta($post->post_author, 'user_location_tab', true);
      }
      
      if( ! empty($location_tab) && $location_tab == 'on' ) {
        return true;
      }

    }

    public static function user_location_tab( $user_id, $meta_key ) {
        global $wpdb;

        $sql = $wpdb->prepare( "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND user_id = %d", $meta_key, $user_id);
        $rs = $wpdb->get_var($sql);

        return $rs;
    }

  /**
   * Display admin_notice if Dokan plugin is not activated
   *
   * @return string
   */
    public static function install_woocommerce_admin_notice() {?>
      <div class="error">
          <p><?php printf( esc_html__( 'Dokan plugin is not activated. Please install and activate it to use for module %s.', 'woopanel' ), '<strong>Geo Location</strong>' ); ?></p>
      </div>
      <?php    
    }
}

/**
 * Returns the main instance of NBT_Solutions_Geo_Location.
 *
 * @since  1.0.0
 * @return NBT_Solutions_Geo_Location
 */
NBT_Solutions_Geo_Location::initialize();