<?php

if ( ! defined( 'WPINC' ) ) {
	die;
}

global $wp_version, $wpdb;

define( 'WOOPANEL_STORE_LOCATOR_URL', plugin_dir_url( __FILE__ ) );
define( 'WOOPANEL_STORE_LOCATOR_PATH', plugin_dir_path(__FILE__) );
define( 'WOOPANEL_STORE_LOCATOR_BASE', dirname( plugin_basename( __FILE__ ) ) );
define( 'WOOPANEL_STORE_LOCATOR_VERSION', "1.1.10" );

class NBT_Solutions_Store_Locator {

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


		/**
		 * The core plugin class that is used to define internationalization,
		 * admin-specific hooks, and public-facing site hooks.
		 */
		require WOOPANEL_STORE_LOCATOR_PATH . 'includes/class-store-locator.php';

		$plugin = new WooPanel_Store_Locator();
		$plugin->run();


      // State that initialization completed.
      self::$initialized = true;
	}
}


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-store-locator-deactivator.php
 */
function woopanel_store_locator_deactivate() {
	require_once WOOPANEL_STORE_LOCATOR_PATH . 'includes/class-store-locator-deactivator.php';
	WooPanel_Store_Locator_Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'woopanel_store_locator_deactivate' );



add_action( 'upgrader_process_complete', 'woopanel_store_locator_upgrate_process',10, 2);
function woopanel_store_locator_upgrate_process( $upgrader_object, $options ) {

	require_once WOOPANEL_STORE_LOCATOR_PATH . 'includes/class-store-locator-activator.php';

	$our_plugin = plugin_basename( __FILE__ );
 	
 	// If an update has taken place and the updated type is plugins and the plugins element exists
 	if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
	  
	  // Iterate through the plugins being updated and check if ours is there
	  foreach( $options['plugins'] as $plugin ) {
	  	
	  	if( $plugin == $our_plugin ) {


	  		//Store Timing
				require_once WOOPANEL_STORE_LOCATOR_PATH . 'includes/class-store-locator-helper.php';
				WooPanel_Store_Locator_Helper::fix_backward_compatible();
				WooPanel_Store_Locator_Activator::upgrade_method();
	    	
	  	}
	  }
 	}
  
}


/**
 * Returns the main instance of NBT_Solutions_Loading_Effect.
 *
 * @since  1.0.0
 * @return NBT_Solutions_Loading_Effect
 */
NBT_Solutions_Store_Locator::initialize();