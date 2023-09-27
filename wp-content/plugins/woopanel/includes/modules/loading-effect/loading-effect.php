<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

define('NBT_LOADING_EFFECT_PATH', plugin_dir_path( __FILE__ ));
define('NBT_LOADING_EFFECT_URL', plugin_dir_url( __FILE__ ));

/**
 * This class will display icon loading effect before page loaded.
 *
 * @package WooPanel_Modules
 */
class NBT_Solutions_Loading_Effect {

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

      $woopanel_admin_options = get_option( 'woopanel_admin_options' );

      if( isset($woopanel_admin_options['show_loading_effect']) && $woopanel_admin_options['show_loading_effect'] == 'yes' ) {
          self::$is_show = true;
      }

      if( isset($woopanel_admin_options['shop_loading_closed']) && $woopanel_admin_options['shop_loading_closed'] == 'yes' ) {
          self::$is_closed = true;
      }

      if( isset($woopanel_admin_options['shop_loading_icon']) && $woopanel_admin_options['shop_loading_icon'] ) {
          self::$is_checked = true;
      }

      add_action( 'woopanel_footer', array( __CLASS__, 'display_loading_effect') );
      add_filter( 'woopanel_options', array( __CLASS__, 'get_settings'), 99, 1);
      add_action( 'woopanel_init', array( __CLASS__, 'save_settings') );

      // State that initialization completed.
      self::$initialized = true;
  }

  /**
   * Display HTML loading effect
   *
   * @return string
   */
  public static function display_loading_effect() {
      global $current_user;

      $shop_loading_effect = WooPanel_Modules::check_user_meta( $current_user->ID, 'shop_loading_effect', 'meta_value' );
      if( empty($shop_loading_effect) ) {
          $shop_loading_effect = empty(self::$is_show) ? 'no' : 'yes';
      }

      $shop_loading_closed = WooPanel_Modules::check_user_meta( $current_user->ID, 'shop_loading_closed', 'meta_value' );
      if( empty($shop_loading_closed) ) {
          $shop_loading_closed = empty(self::$is_closed) ? 'no' : 'yes';
      }

      $shop_loading_icon = WooPanel_Modules::check_user_meta( $current_user->ID, 'shop_loading_icon', 'meta_value' );
      if( empty($shop_loading_icon) ) {
          $shop_loading_icon = empty(self::$is_checked) ? '' : self::$is_checked;
      }

      if( $shop_loading_effect == 'yes' ) {
        $get_settings = self::get_settings();
        $options = $get_settings['general']['fields']['shop_loading_icon']['options'];?>
        <div class="woopanel-loading-wrapper">
            <div class="woopanel-loading loading-<?php echo $shop_loading_icon;?>"><?php echo $options[$shop_loading_icon];?></div>
            <?php if( $shop_loading_closed == 'yes' ) { ?>
            <span class="woopanel-loading-closed"><?php esc_html_e('Close Loading', 'woopanel' );?></span>
            <?php }?>
        </div>
        <?php
      }
  }

  /**
   * Set field settings
   *
   * @return array
   */
  public static function get_settings( $fields = array() ) {
      global $current_user;

      $shop_loading_effect = WooPanel_Modules::check_user_meta( $current_user->ID, 'shop_loading_effect', 'meta_value' );
      if( empty($shop_loading_effect) ) {
          $shop_loading_effect = empty(self::$is_show) ? 'no' : 'yes';
      }
      
      $shop_loading_closed = WooPanel_Modules::check_user_meta( $current_user->ID, 'shop_loading_closed', 'meta_value' );
      if( empty($shop_loading_closed) ) {
          $shop_loading_effect = empty(self::$is_closed) ? 'no' : 'yes';
      }

      $shop_loading_icon = WooPanel_Modules::check_user_meta( $current_user->ID, 'shop_loading_icon', 'meta_value' );
      if( empty($shop_loading_icon) ) {
          $shop_loading_icon = empty(self::$is_checked) ? '' : self::$is_checked;
      }

      $fields['general']['fields'][] = array(
          'id'       => 'shop_loading_effect',
          'type'     => 'checkbox',
          'title'    => __( 'Show Loading Effect', 'dokan-lite'  ),
          'default'   => 'yes',
          'value' => $shop_loading_effect
      );

      $fields['general']['fields'][] = array(
          'id'       => 'shop_loading_closed',
          'type'     => 'checkbox',
          'title'    => __( 'Show Loading Close', 'dokan-lite'  ),
          'default'   => 'yes',
          'value' => $shop_loading_closed
      );

     $fields['general']['fields']['shop_loading_icon'] = array(
          'id'       => 'shop_loading_icon',
          'type'     => 'icon_list',
          'title'    => __( 'Show Loading Icon', 'dokan-lite'  ),
          'options'   => array(
            'style1' => '<div class="loader__wrap" role="alertdialog" aria-busy="true" aria-live="polite" aria-label="Loading…"><div class="loader" aria-hidden="true"><div class="loader__sq"></div><div class="loader__sq"></div></div></div>',
            'style2' => '<div class="loader loader-1"><div class="loader-outter"></div><div class="loader-inner"></div></div>',
            'style3' => '<div class="loader loader-3"><div class="dot dot1"></div><div class="dot dot2"></div><div class="dot dot3"></div></div>',
            'style4' => '<div class="loader loader-7"><div class="line line1"></div><div class="line line2"></div><div class="line line3"></div></div>',
            'style5' => '<div class="loader loader-17"><div class="css-square square1"></div><div class="css-square square2"></div><div class="css-square square3"></div><div class="css-square square4"></div><div class="css-square square5"></div><div class="css-square square6"></div><div class="css-square square7"></div><div class="css-square square8"></div></div>',
            'style6' => '<div class="loader loader-6"><div class="loader-inner"></div></div>',
            'style7' => '<div class="loader">'.__('Loading', 'woopanel' ).'...</div>',
            'style8' => '<div class="loader loader-2"><svg class="loader-star" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"><polygon points="29.8 0.3 22.8 21.8 0 21.8 18.5 35.2 11.5 56.7 29.8 43.4 48.2 56.7 41.2 35.1 59.6 21.8 36.8 21.8 " fill="#fff" /></svg><div class="loader-circles"></div></div>',
          ),
          'value' => $shop_loading_icon
      );
      return $fields;
  }

  /**
   * Save data field settings
   *
   * @return null
   */
  public static function save_settings() {
      global $current_user;

      if( isset($_POST['save']) ) {
        $shop_loading_effect = isset($_POST['shop_loading_effect']) ? 'yes' : 'no';
        update_user_meta( $current_user->ID, 'shop_loading_effect', $shop_loading_effect );

        $shop_loading_closed = isset($_POST['shop_loading_closed']) ? 'yes' : 'no';
        update_user_meta( $current_user->ID, 'shop_loading_closed', $shop_loading_closed );

        $shop_loading_icon = isset($_POST['shop_loading_icon']) ? $_POST['shop_loading_icon'] : '';
        update_user_meta( $current_user->ID, 'shop_loading_icon', $shop_loading_icon );
      }
  }
}

/**
 * Returns the main instance of NBT_Solutions_Loading_Effect.
 *
 * @since  1.0.0
 * @return NBT_Solutions_Loading_Effect
 */
NBT_Solutions_Loading_Effect::initialize();