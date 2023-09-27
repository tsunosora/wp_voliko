<?php

define('NBT_DOKAN_REVIEW_PATH', plugin_dir_path( __FILE__ ));
define('NBT_DOKAN_REVIEW_URL', plugin_dir_url( __FILE__ ));

/**
 * This class will load modules Dokan Review of WooPanel.
 *
 * @package WooPanel_Modules
 */

class NBT_Solutions_Dokan_Review {

  /**
   * Set module id
   *
   * @var string
   */
    static $plugin_id = 'dokan-review';

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


        if ( ! class_exists( 'WeDevs_Dokan' ) ) {
            add_action( 'admin_notices', array( __CLASS__, 'install_dokan_admin_notice') );
        }else{
          add_action( 'wp', array( __CLASS__, 'dokan_init') );
        }
        // State that initialization completed.
        self::$initialized = true;
    }

    public static function dokan_init() {
      global $current_user;
        
      if ( in_array( 'seller', (array) $current_user->roles ) ) {
        include_once NBT_DOKAN_REVIEW_PATH . 'inc/reviews.php';
        include_once NBT_DOKAN_REVIEW_PATH . 'inc/frontend.php';
        include_once NBT_DOKAN_REVIEW_PATH . 'inc/functions.php';
      }
    }

  /**
   * Display admin_notice if Dokan plugin is not activated
   *
   * @return string
   */
    public static function install_dokan_admin_notice() {?>
        <div class="error">
            <p><?php printf( esc_html__( 'Dokan plugin is not activated. Please install and activate it to use for module %s.', 'woopanel' ), '<strong>Dokan Review</strong>' ); ?></p>
        </div>
        <?php    
    }
}

/**
 * Returns the main instance of NBT_Solutions_Dokan_Review.
 *
 * @since  1.0.0
 * @return NBT_Solutions_Dokan_Review
 */
NBT_Solutions_Dokan_Review::initialize();