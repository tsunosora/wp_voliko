<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

define('NBT_TERA_WALLET_PATH', plugin_dir_path( __FILE__ ));
define('NBT_TERA_WALLET_URL', plugin_dir_url( __FILE__ ));

/**
 * This class will display icon loading effect before page loaded.
 *
 * @package WooPanel_Modules
 */
class NBT_Solutions_Tera_Wallet {

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
    
    if( ! is_admin() ) {
      include_once NBT_TERA_WALLET_PATH . 'includes/woopanel-transaction-listing.php';

      include_once NBT_TERA_WALLET_PATH . 'includes/template.php';
      include_once NBT_TERA_WALLET_PATH . 'includes/functions.php';
      include_once NBT_TERA_WALLET_PATH . 'includes/settings.php';
    }

  

    // State that initialization completed.
    self::$initialized = true;
  }

  public static function woopanel_query_var_filter( $query_vars ) {
    $query_vars[] = 'tera-wallet';
    $query_vars[] = 'wallet-transaction';

    return $query_vars;
  }

  public static function woopanel_menus( $woopanel_menus ) {
    $woopanel_menus[150] = [
            'id'         => 'tera-wallet',
            'menu_slug'  => 'tera-wallet',
            'menu_title'      => esc_html__( 'Tera Wallet', 'woopanel' ),
            'page_title' => '',
            'capability' => '',
            'priority' => 25
    ];

    return $woopanel_menus;
  }

  public static function woopanel_submenus( $submenus ) {
    $submenus['tera-wallet'] = array(
      5 => array(
        'id'         => 'tera-wallet',
        'menu_slug'  => 'tera-wallet',
        'label'      => esc_html__( 'Tera Wallet', 'woopanel' ),
        'page_title' => '',
        'capability' => '',
      ),
      6 => array(
        'id'         => 'wallet-transaction',
        'menu_slug'  => 'wallet-transaction',
        'label'      => esc_html__( 'Transactions', 'woopanel' ),
        'page_title' => '',
        'capability' => '',
      )
    );

    return $submenus;
  }
}

/**
 * Returns the main instance of NBT_Solutions_Tera_Wallet.
 *
 * @since  1.0.0
 * @return NBT_Solutions_Tera_Wallet
 */
NBT_Solutions_Tera_Wallet::initialize();