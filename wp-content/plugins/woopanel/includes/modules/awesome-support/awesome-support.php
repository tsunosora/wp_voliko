<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

define('NBT_AWESOME_SUPPORT_PATH', plugin_dir_path( __FILE__ ));
define('NBT_AWESOME_SUPPORT_URL', plugin_dir_url( __FILE__ ));

/**
 * This class will display icon loading effect before page loaded.
 *
 * @package WooPanel_Modules
 */
class NBT_Solutions_Awesome_Support {

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

    add_filter( 'woopanel_query_var_filter', array( __CLASS__, 'woopanel_query_var_filter') );
    add_filter( 'woopanel_menus', array( __CLASS__, 'woopanel_menus') );
    add_filter( 'woopanel_submenus', array( __CLASS__, 'woopanel_submenus') );
    add_action( 'woopanel_enqueue_scripts', array( __CLASS__, 'woopanel_enqueue_scripts') );
    
    include_once NBT_AWESOME_SUPPORT_PATH . 'includes/functions.php';
    if( ! is_admin() ) {
      // include_once NBT_TERA_WALLET_PATH . 'includes/woopanel-transaction-listing.php';

      
      include_once NBT_AWESOME_SUPPORT_PATH . 'includes/permission.php';
      include_once NBT_AWESOME_SUPPORT_PATH . 'includes/list-table.php';
      include_once NBT_AWESOME_SUPPORT_PATH . 'includes/template.php';
      include_once NBT_AWESOME_SUPPORT_PATH . 'includes/email.php';
      include_once NBT_AWESOME_SUPPORT_PATH . 'includes/my-account.php';
    }

    include_once NBT_AWESOME_SUPPORT_PATH . 'includes/admin.php';


  

    // State that initialization completed.
    self::$initialized = true;
  }

  public static function woopanel_query_var_filter( $query_vars ) {
    $query_vars[] = 'awesome-supports';
    $query_vars[] = 'awesome-support';
    $query_vars[] = 'awesome-support-email';

    return $query_vars;
  }

  public static function woopanel_menus( $woopanel_menus ) {
    $woopanel_menus[155] = [
            'id'         => 'awesome-supports',
            'menu_slug'  => 'awesome-supports',
            'menu_title'      => esc_html__( 'Ticket Support', 'woopanel' ),
            'page_title' => '',
            'capability' => '',
            'priority' => 25
    ];

    return $woopanel_menus;
  }

  public static function woopanel_submenus( $submenus ) {
    if( woopanel_is_super_admin() ) {
      $submenus['awesome-supports'] = array(
        5 => array(
          'id'         => 'awesome-supports',
          'menu_slug'  => 'awesome-supports',
          'label'      => esc_html__( 'Ticket Support', 'woopanel' ),
          'page_title' => '',
          'capability' => '',
        ),
        6 => array(
          'id'         => 'awesome-support-email',
          'menu_slug'  => 'awesome-support-email',
          'label'      => esc_html__( 'Email Template', 'woopanel' ),
          'page_title' => '',
          'capability' => '',
        )
      );
    }


    return $submenus;
  }

  static function woopanel_enqueue_scripts() {
    global $wp_query;


    wp_enqueue_style( 'awesome-support', NBT_AWESOME_SUPPORT_URL .'css/style.css', array()  );
    wp_enqueue_script( 'awesome-jscript', NBT_AWESOME_SUPPORT_URL . 'js/admin.js', array('jquery'), '1.2', true );
    // wp_localize_script( 'map-jscript', 'jsMap', array(
    //   'asset' => WOODASHBOARD_URL,
    //   'apiKey' => woopanel_store_config('api_key'),
    //   'default_lat' => woopanel_store_config('default_lat'),
    //   'default_lng' => woopanel_store_config('default_lng'),
    //   'missing_apikey' => esc_html__('You must use an API key to authenticate each request to Google Maps Platform APIs.', 'woopanel')
    // ));
  }
}

/**
 * Returns the main instance of NBT_Solutions_Awesome_Support.
 *
 * @since  1.0.0
 * @return NBT_Solutions_Awesome_Support
 */
NBT_Solutions_Awesome_Support::initialize();