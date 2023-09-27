<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

define('NBT_VENDOR_PROFILE_PATH', plugin_dir_path( __FILE__ ));
define('NBT_VENDOR_PROFILE_URL', plugin_dir_url( __FILE__ ));

/**
 * This class will display icon loading effect before page loaded.
 *
 * @package WooPanel_Modules
 */
class NBT_Solutions_Vendor_Profile {

  /**
   * Show loading effect.
   *
   * @var boolean
   */
  static $is_show = false;

  /**
   * Show close loading effect.
   *
   * @var boolean
   */
	static $is_closed = false;

  /**
   * Set default loading effect.
   *
   * @var boolean
   */
  static $is_checked = false;

  /**
   * The single instance of the class.
   *
   * @var NBT_Solutions_Loading_Effect
   * @since 1.0
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

    add_action( 'wp', array( __CLASS__, 'store_init') );

    if( ! is_admin() ) {
      
      include_once NBT_VENDOR_PROFILE_PATH . 'includes/woopanel-profile-listing.php';
      include_once NBT_VENDOR_PROFILE_PATH . 'includes/profile-categories.php';
    }
    
    include_once NBT_VENDOR_PROFILE_PATH . 'includes/agile-stores.php';

    add_action( 'woopanel_enqueue_scripts', array( __CLASS__, 'woopanel_enqueue_scripts') );


    // State that initialization completed.
    self::$initialized = true;
  }

  static function store_init() {
    global $current_user;


    if ( in_array( 'wpl_seller', (array) $current_user->roles ) ) {
      include_once NBT_VENDOR_PROFILE_PATH . 'includes/profile-store.php';
      include_once NBT_VENDOR_PROFILE_PATH . 'includes/profile-social.php';
    }
  }

  static function woopanel_enqueue_scripts() {
    global $wp_query;

    if( ! isset($wp_query->query['store']) ) {
      return;
    }
    
    wp_enqueue_media();
    wp_enqueue_script( 'map-jscript', NBT_VENDOR_PROFILE_URL . 'js/admin.js', array('jquery'), '1.2', true );
    wp_localize_script( 'map-jscript', 'jsMap', array(
      'asset' => WOODASHBOARD_URL,
      'apiKey' => woopanel_store_config('api_key'),
      'default_lat' => woopanel_store_config('default_lat'),
      'default_lng' => woopanel_store_config('default_lng'),
      'missing_apikey' => esc_html__('You must use an API key to authenticate each request to Google Maps Platform APIs.', 'woopanel')
    ));
  }
}

/**
 * Returns the main instance of NBT_Solutions_Vendor_Profile.
 *
 * @since  1.0.0
 * @return NBT_Solutions_Vendor_Profile
 */
NBT_Solutions_Vendor_Profile::initialize();