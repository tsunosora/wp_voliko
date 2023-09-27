<?php
/**
 * Plugin Name: NetBase Solutions
 * Plugin URI: https://cmsmart.net
 * Description: Plugin that contain a lot of feature in Printshop Solution Package.
 * Version: 1.9.8
 * Author: Netbase-Team
 * Author URI: https://woocommerce.com
 * Text Domain: nbt-solution
 * Domain Path: /languages
 *
 * @package Solutions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
    exit;

// Define plugin textdomain & theme templates folder for plugin.
define( 'PREFIX_NBT_SOL', 'nbt-solutions' );
// Define path to plugin directory.
define( 'PREFIX_NBT_SOL_PATH', plugin_dir_path( __FILE__ ) );
// Define URL to plugin directory.
define( 'PREFIX_NBT_SOL_URL', plugin_dir_url( __FILE__ ) );
// Define plugin base file.
define( 'PREFIX_NBT_SOL_BASENAME', plugin_basename( __FILE__ ) );
define( 'PREFIX_NBT_SOL_DEV', false );

class NB_Solution {
    /**
     * @var null
     *
     * @since 0.0.1
     */
    private static $instance = null;
	
    /**
     * Get instance.
     *
     * @since 0.0.1
     *
     * @return null|NB_Solutions
     */
    public static function instance() {
        if ( ! self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
	
    /**
     * NB_Solutions constructor.
     *
     * @since 0.0.1
     */
    private function __construct() {		
		// Load the core class.
		require_once PREFIX_NBT_SOL_PATH . 'core/core.php';
		require_once PREFIX_NBT_SOL_PATH . 'api/api.php';
		require_once PREFIX_NBT_SOL_PATH . 'dashboard.php';

		load_plugin_textdomain( 'nbt-solution', false, PREFIX_NBT_SOL_PATH . 'languages' );
	}
	
    /**
     * Check Slug Page.
     *
     * @since 0.0.1
     */
	public static function is_page( $slug ) {
        $uri = ltrim( rtrim( $_SERVER['REQUEST_URI'], '/' ), '/');
        
        if ( strpos($uri, 'nb-checkout') !== false ) {
            return true;
        }

		return false;
    }
    
    public static function get_setting($module, $name = null) {
        $option_name = $module . '_settings';
        $settings = get_transient( $option_name );
        
        if ( false === $settings || empty($settings) ) {
            $settings = get_option($module . '_settings');
            
            if( ! $settings ) {
                $register_modules = NBT_Solutions_Modules::register_modules();

                if( isset($register_modules[$module]['class']) ) {
                    $class = $register_modules[$module]['class'];
    
                    if ( class_exists('NBT_' . $class . '_Settings') ) {
                        $module_setting = call_user_func('NBT_' . $class . '_Settings::get_settings');

                        if( is_array($module_setting) ) {
                            $default_setting = array();
                            foreach( $module_setting as $key => $set) {
                                if( isset($set['id']) ) {
                                    $default_setting[$set['id']] = $set['default'];
                                }
                                
                            }

                            $settings = apply_filters( 'nbt_'.$option_name, $default_setting );
                        }
                    }
                }
            }

            set_transient( $option_name, $settings );
        }

        return $settings;
    }

    public static function log( $data ) {
        if ( defined('WP_DEBUG') && true === WP_DEBUG) {
            if ( is_array( $data ) || is_object( $data ) ) {
                error_log( print_r( $data, true ) );
            } else {
                error_log( $data );
            }
        }
    }
}

/**
* Load plugin for NB_Solution
*
* @since 2.5.3
*
* @return void
**/
add_action( 'plugins_loaded', array( 'NB_Solution', 'instance' ) );

// register_activation_hook( __FILE__, array( 'NB_Solution', 'netbase_solutions__activate' ) );
// register_deactivation_hook( __FILE__, array( 'NB_Solution', 'netbase_solutions__deactivate' ) );