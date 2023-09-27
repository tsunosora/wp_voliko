<?php
/**
 * @version    1.0
 * @package    Package Name
 * @author     Your Team <support@yourdomain.com>
 * @copyright  Copyright (C) 2014 yourdomain.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

/**
 * Plug additional sidebars into WordPress.
 *
 * @package  Package Name
 * @since    1.0
 */


class NBT_Solutions_Pluggable {
	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;

	private static $settings_saved;

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

		self::$settings_saved = false;

		/**
		* Load modules
		*/
		NBT_Solutions_Modules::initialize();
		NBT_Solutions_Register::initialize();

        
		// Register actions to do something.
		add_action( 'action_name', array( __CLASS__, 'method_name' ) );
		add_action( 'admin_menu', array( __CLASS__, 'nbt_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts_method') );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'frontend_enqueue_scripts' ) );


		// State that initialization completed.
		self::$initialized = true;
	}

	/**
	 * Register a custom menu page.
	 *
	 * @return  none
	 */
	public static function nbt_admin_menu() {


	    if(defined('PREFIX_NBT_SOL_DEV') && PREFIX_NBT_SOL_DEV){
		    $page = add_menu_page( 
		        __( 'NBT Solutions', 'nbt-solution' ),
		        'NBT Solutions',
		        'manage_options',
		        'solutions',
		        array( __CLASS__, 'display_settings_page' ),
		        '',
		        3
		    );
	    	
	    }else{
			add_menu_page( 'solutions', 'NBT Solutions', 'manage_options', 'solution-dashboard', null, 'dashicons-awards', 3 );
	    }
	}

	/**
	 * Display homepage Panel.
	 *
	 * @return  none
	 */
	public static function display_settings_page(){
		$register_modules = NBT_Solutions_Modules::register_modules();
		$settings_modules = get_option('solutions_core_settings' );
		include PREFIX_NBT_SOL_PATH . 'templates/tpl/settings.php';
	}

	/**
	 * Register script & css
	 *
	 * @return  none
	 */
	public static function admin_scripts_method(){
		wp_enqueue_media();
		wp_enqueue_style( 'font-solutions', PREFIX_NBT_SOL_URL . 'assets/admin/css/font.css', array( 'wp-color-picker' )  );
		wp_enqueue_script( 'admin-solutions', PREFIX_NBT_SOL_URL . 'assets/admin/js/admin.js?t='.time(), array( 'jquery', 'wp-color-picker', 'wp-util' ) );

		$localize_arr = apply_filters('nbs_admin_localize_script', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'save_settings' => home_url() . '/wp-json/solutions/v1/save_settings/',
            'i18n'        => array(
                'mediaTitle'  => esc_html__( 'Choose an image', 'wcvs' ),
                'mediaButton' => esc_html__( 'Use image', 'wcvs' ),
			),
        ));

        if ( in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ))) ) {
            $localize_arr['placeholder'] = WC()->plugin_url() . '/assets/images/placeholder.png';
        }

		wp_localize_script( 'admin-solutions', 'nbt_solutions', $localize_arr);
	}

	/**
	 * Register script & css
	 *
	 * @return  none
	 */
	public static function frontend_enqueue_scripts(){
		
		wp_enqueue_style( 'font-solutions', PREFIX_NBT_SOL_URL . 'assets/frontend/css/nbt-fonts.css'  );

		if( is_multisite() ) {
			
			$upload_dir   = wp_upload_dir();
			wp_enqueue_style( 'frontend-solutions', $upload_dir['baseurl'] . '/frontend.css'  );
			wp_enqueue_script( 'frontend-solutions', $upload_dir['baseurl'] . '/frontend.js?t='.time(), array(), false, true );
		}else {
			wp_enqueue_style( 'frontend-solutions', PREFIX_NBT_SOL_URL . 'assets/frontend/css/frontend.css'  );
			wp_enqueue_script( 'frontend-solutions', PREFIX_NBT_SOL_URL . 'assets/frontend/js/frontend.js?t='.time(), array(), false, true );
		}


        $localize_arr = apply_filters('nbt_solutions_localize', array(
			'debug' => WP_DEBUG,
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'i18n'        => array(
                'mediaTitle'  => esc_html__( 'Choose an image', 'wcvs' ),
                'mediaButton' => esc_html__( 'Use image', 'wcvs' ),
            )
        ));

        if (in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ))) {
            $localize_arr['placeholder'] = WC()->plugin_url() . '/assets/images/placeholder.png';
            $localize_arr['customer_id'] = WC()->session->get_customer_id();
        }

		wp_localize_script( 'frontend-solutions', 'nbt_solutions', $localize_arr);
	}
}