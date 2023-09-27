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
class NBT_Solutions_Update {
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

		//print_r('This is Vendor Pluggable !');


		//add_filter('pre_set_site_transient_update_plugins', array( __CLASS__, 'display_transient_update_plugins'), 10, 1 );

		//add_action( 'load-plugins.php', array( __CLASS__, 'wp_plugin_update_rows'), 20, 1 );
		
		// Register actions to do something.
		//sadd_action( 'init'   , array( __CLASS__, 'load_modules'    ) );

		// State that initialization completed.
		self::$initialized = true;
	}

	/**
	 * Method Featured.
	 *
	 * @return  array
	 */
	public static function display_transient_update_plugins($transient)
	{
	    var_dump($transient);
	}
	public static function wp_plugin_update_rows($plugins) {
		$plugin_file = 'core-solutions/nbt-solutions.php';
		add_action( "after_plugin_row_$plugin_file", array( __CLASS__, 'wp_plugin_update_row'), 10, 2 );
	}

	public static function wp_plugin_update_row($file, $plugin_data){
		$current = get_site_transient( 'update_plugins' );
		mang($current);
	}



}
