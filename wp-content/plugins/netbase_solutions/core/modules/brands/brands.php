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
define('NBT_BRANDS_PATH', plugin_dir_path( __FILE__ ));
define('NBT_BRANDS_URL', plugin_dir_url( __FILE__ ));

class NBT_Solutions_Brands {
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
		add_action('wp_enqueue_scripts', array(__CLASS__, 'embed_style'));
		
		// State that initialization completed.
		self::$initialized = true;
	}

	public static function embed_style() {
        wp_enqueue_style('owl.carousel', NBT_BRANDS_URL . 'assets/css/owl.carousel.min.css', false, '1.1', 'all');
        wp_enqueue_script('owl.carousel', NBT_BRANDS_URL . 'assets/js/owl.carousel.min.js', null, null, true);
	}
}

