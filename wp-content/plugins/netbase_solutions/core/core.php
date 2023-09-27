<?php
/**
 * 1.3.3    1.0
 * @package    Package Name
 * @author     Your Team Name <support@yourdomain.com>
 * @copyright  Copyright (C) 2014 yourdomain.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.yourdomain.com
 */

// Prevent direct access to this file
defined('ABSPATH') || die('Direct access to this file is not allowed.');

/**
 * Core class.
 *
 * @package  Package Name
 * @since    1.0
 */
define('NBT_CORE_PATH', plugin_dir_path(__FILE__));


    require_once PREFIX_NBT_SOL_PATH .'core/vendor/scssphp/scss.inc.php';
    use Leafo\ScssPhp\Compiler;


class PREFIX_CORE
{
    /**
     * Define theme version.
     *
     * @var  string
     */
    const VERSION = '1.0.0';

    /**
     * Define valid class prefix for autoloading.
     *
     * @var  string
     */
    protected static $prefix = 'NBT_Solutions_';

    /**
     * Initialize Package Name.
     *
     * @return  void
     */
    public static function initialize()
    {
        // Register class autoloader.
        spl_autoload_register(array(__CLASS__, 'autoload'));

//		print_r('Hello This Core');

        // Include function plugins if not include.
        self::include_function_plugins();

        // Register necessary actions.
        add_action('admin_menu', array(__CLASS__, 'admin_menu'));

        // Add custom css to admin
        add_action('admin_head', array(__CLASS__, 'admin_custom_css'));
        add_action('init', array(__CLASS__, 'stop_heartbeat'), 1);
        //add_action('init', array(__CLASS__, 'register_my_session'), 1);


        add_action( 'rest_api_init', array(__CLASS__, 'register_rest_route') );

        add_action( 'wp_ajax_nb_export_settings', array(__CLASS__, 'nb_export_settings') );
        add_action( 'wp_ajax_nb_generator_php', array(__CLASS__, 'nb_generator_php') );


        // add_action( 'admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue' ));

        // Plug into WordPress and supported 3rd-party plugins.
        NBT_Solutions_Pluggable::initialize();
        NBT_Solutions_Update::initialize();


    }

    /**
     * Method to autoload class declaration file.
     *
     * @param   string $class_name Name of class to load declaration file for.
     *
     * @return  mixed
     */
    public static function autoload($class_name)
    {
        // Verify class prefix.
        if (0 !== strpos($class_name, self::$prefix)) {
            return false;
        }

        // Generate file path from class name.
        $base = PREFIX_NBT_SOL_PATH . '/core/modules/';
        $path = strtolower(str_replace('_', '/', substr($class_name, strlen(self::$prefix))));

        // Check if class file exists.
        $standard = $path . '.php';
        $alternative = $path . '/' . current(array_slice(explode('/', str_replace('\\', '/', $path)), -1)) . '.php';

        while (true) {
            // Check if file exists in standard path.
            if (@is_file($base . $standard)) {
                $exists = $standard;

                break;
            }

            // Check if file exists in alternative path.
            if (@is_file($base . $alternative)) {
                $exists = $alternative;

                break;
            }

            // If there is no more alternative file, quit the loop.
            if (false === strrpos($standard, '/') || 0 === strrpos($standard, '/')) {
                break;
            }

            // Generate more alternative files.
            $standard = preg_replace('#/([^/]+)$#', '-\\1', $standard);
            $alternative = implode('/', array_slice(explode('/', str_replace('\\', '/', $standard)), 0, -1)) . '/' . substr(current(array_slice(explode('/', str_replace('\\', '/', $standard)), -1)), 0, -4) . '/' . current(array_slice(explode('/', str_replace('\\', '/', $standard)), -1));
        }

        // Include class declaration file if exists.
        if (isset($exists)) {
            return include_once $base . $exists;
        }

        return false;
    }

    public static function nb_generator_php() {
        if( isset($_POST['modules']) ) {
			$content = '';
			$modules = explode(',', $_POST['modules']);
            $template = get_option('template');
            $stylesheet = get_option('stylesheet');

			$link_file = str_replace($template, $stylesheet, get_template_directory()) . '/inc/netbase_solutions.php';
			$link_dl = str_replace($template, $stylesheet, get_template_directory_uri()) . '/inc/netbase_solutions.php';

            
			if( !empty($modules) && is_array($modules) ) {
				foreach( $modules as $module ) {
					$settings = get_option($module . '_settings');
					
					if( ! empty($settings) && is_array($settings) ) {
                        $function_name = str_replace('-', '_', 'nbs_' . $template . '_' . $module .'_settings');

                        $content .= 'if ( ! function_exists( \'' . $function_name . '\' ) ) {' ."\n";
                            $content .= "\t" . 'add_filter( \'nbt_' . $module . '_settings\', \'' . $function_name . '\', 999, 1);'."\n\n";
                            $content .= "\t" . 'function ' . $function_name .'() {'."\n";
                                $content .= "\t\t" . 'return array('."\n";
                                foreach($settings as $k_setting => $val_setting) {
                                    if( ! is_numeric($val_setting) ) {
                                        $val_setting = "'" . $val_setting ."'";
                                    }
                                    $content .= "\t\t\t'" . $k_setting ."' => " . $val_setting. ",\n";
                                }
                                $content .= "\t\t" . ');'."\n";
                            $content .= "\t" . '}'."\n";
                        $content .= '}' . "\n\n";
					}
				}
            }

            $json['content'] = trim($content);
            $json['complete'] = true;
			
			wp_send_json($json);
        }
    }

	
	public static function nb_export_settings() {
		$json = array();
		
		if( isset($_POST['modules']) ) {
			$content = array();
			$modules = explode(',', $_POST['modules']);

			$template = get_option('template');
            $stylesheet = get_option('stylesheet');
			
			$link_file = get_template_directory() . '/settings.txt';
            $link_dl = get_template_directory_uri() . '/settings.txt';

			$content['enable'] = $modules;
			if( !empty($modules) && is_array($modules) ) {
				foreach( $modules as $module ) {
					$settings = get_option($module . '_settings');
					
					if( ! empty($settings) && is_array($settings) ) {
						$content['settings'][$module] = $settings;
					}
				}
            }
			
			if ( file_exists($link_file) && ! is_writable($link_file) ) {
				$json['message'] = 'The file is not writable, please set permission writable for this!';
			}else {
				$file_content = @fopen($link_file,"w");
				fwrite($file_content, json_encode($content) ); 
				fclose($file_content);
				
				$json['complete'] = true;
				$json['url'] = 'File Storage Settings at: <a target="_blank" href="'. esc_url($link_dl). '" style="color: #0066cc; text-decoration: none;">'. $link_dl.'</a>';
		
			}
			
			wp_send_json($json);
		}
	}
	
    public static function register_rest_route() {
        register_rest_route(
            'solutions/v1/',
            '/save_settings',
            array(
                'methods'  => 'POST',
                'callback' => array(__CLASS__, 'rest_save_settings_callback'),
            )
        );
    }


    public static function rest_save_settings_callback( WP_REST_Request $request ) {
        header("Access-Control-Allow-Origin: *");
        global $blog_id;
        $json = array();
        // $param = $request->get_param( '_wpnonce' );
        $settings_modules = $request->get_param( 'nbt_solutions_func' );

        $json = self::compile_modules($settings_modules);


        wp_send_json($json);
    }

    public static function compile_modules( $settings_modules ) {
        $json = array();
        $upload_dir   = wp_upload_dir();
        $register_modules = NBT_Solutions_Modules::register_modules();

        $compile_js = $compile_css = '';

        if( is_multisite() ) {
            $link_css = $upload_dir['basedir'] . '/frontend.css';
            $link_js = $upload_dir['basedir'] . '/frontend.js';
        }else {
            $link_css = PREFIX_NBT_SOL_PATH . 'assets/frontend/css/frontend.css';
            $link_js = PREFIX_NBT_SOL_PATH . 'assets/frontend/js/frontend.js';
        }

        if( ! file_exists($link_js) ) {
            $file_js = @fopen($link_js,"w");
            if($file_js)
            {
                fwrite($file_js, ''); 
                fclose($file_js); 
            }
        }

        if( ! file_exists($link_css) ) {
            $file_css = @fopen($link_css,"w");
            if($file_css)
            {
                fwrite($file_css, ''); 
                fclose($file_css); 
            }
        }

        if ( ! is_writable($link_css) && ! is_writable($link_css) ) {
            $json['message'] = 'The file is not writable, please set permission writable for this!';
        }else {

            if( $settings_modules ) {
                $scss = new Compiler();
                $scss->setImportPaths(PREFIX_NBT_SOL_PATH .'core/modules/');

                require_once PREFIX_NBT_SOL_PATH .'core/vendor/jsmin/jsmin.php';
                

                foreach ($settings_modules as $modules) {

                    if( isset($register_modules[$modules]) && !isset($register_modules[$modules]['hide']) && file_exists(PREFIX_NBT_SOL_PATH . 'core/modules/'.$modules.'/style.scss')){
                        $compile_css .= $scss->compile('@import "'.$modules.'/style.scss";');

                        if(file_exists(PREFIX_NBT_SOL_PATH . 'core/modules/'.$modules.'/assets/js/frontend.js')){
                            $compile_js .= JSMin::minify(file_get_contents(PREFIX_NBT_SOL_PATH . 'core/modules/'.$modules.'/assets/js/frontend.js'));
                        }
                        
                        /* Create Page */
                        if($modules == 'one-step-checkout') {
                            $page_checkout = (int)NBT_Solutions_One_Step_Checkout::nb_get_page_id( 'checkout' );
                            if( $page_checkout <= 0 ) {
                                NBT_Solutions_One_Step_Checkout::install_page_checkout();
                            }
                        }
                    }
                }

                $file_css = @fopen($link_css,"w");
                if($file_css)
                {
                    fwrite($file_css, $compile_css); 
                    fclose($file_css); 
                }

                $file_js = @fopen($link_js,"w");
                if($file_js)
                {
                    fwrite($file_js, trim($compile_js)); 
                    fclose($file_js); 
                }

                $json['complete'] = true;
                $json['message'] = __('Your data has been successfully saved!', 'nbt-solution');
                update_option('solutions_core_settings', $settings_modules);
            }
        }

        return $json;

    }

    /**
     * Include function plugins if not include.
     *
     * @return  void
     *
     * @since  1.1.8
     *
     */
    public static function include_function_plugins()
    {
        if (!function_exists('is_plugin_active')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        if (!function_exists('wp_get_current_user')) {
            require_once(ABSPATH . 'wp-includes/pluggable.php');
        }
    }

    /**
     * Creates a new top level menu section.
     *
     * @return  void
     */
    public static function admin_menu()
    {

    }

    /**
     * Add some custom css to control notice of 3rd-plugin
     *
     * @since  1.1.8
     */
    public static function admin_custom_css()
    {
        echo '
			<style>
				.vc_license-activation-notice.updated,
				.rs-update-notice-wrap.updated,
				.installer-q-icon {
					display: none;
				}
			</style>
		';
    }


    public static function stop_heartbeat()
    {
        wp_deregister_script('heartbeat');
    }

    public static function register_my_session(){
        if( ! session_id() ){
        session_start();
        }
    }
}

// Initialize PREFIX.
PREFIX_CORE::initialize();