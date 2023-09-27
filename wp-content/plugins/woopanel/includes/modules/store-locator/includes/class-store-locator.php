<?php

class WooPanel_Store_Locator {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WooPanel_Store_Locator_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $_store_locator    The string used to uniquely identify this plugin.
	 */
	protected $_store_locator;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->_store_locator = 'woopanel-store-locator';
		$this->version = WOOPANEL_STORE_LOCATOR_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		
		$this->plugin_admin = new WooPanel_Store_Locator_Admin( $this->get_woopanel_store_locator(), $this->get_version() );
		
		//FRONTEND HOOOKS
		$this->plugin_public = new WooPanel_Store_Locator_Public( $this->get_woopanel_store_locator(), $this->get_version() );
		// add_action('wp_ajax_wplsl_load_stores', array($this->plugin_public, 'load_stores'));	
		// add_action('wp_ajax_nopriv_wplsl_load_stores', array($this->plugin_public, 'load_stores'));	

		

		if (is_admin()) 
			$this->define_admin_hooks();
		else
			$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WooPanel_Store_Locator_Loader. Orchestrates the hooks of the plugin.
	 * - wpl_store_locator_i18n. Defines internationalization functionality.
	 * - WooPanel_Store_Locator_Admin. Defines all hooks for the admin area.
	 * - WooPanel_Store_Locator_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once WOOPANEL_STORE_LOCATOR_PATH . 'includes/class-store-locator-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once WOOPANEL_STORE_LOCATOR_PATH . 'includes/class-store-locator-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once WOOPANEL_STORE_LOCATOR_PATH . 'admin/class-store-locator-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once WOOPANEL_STORE_LOCATOR_PATH . 'public/class-agile-store-locator-public.php';

		$this->loader = new WooPanel_Store_Locator_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WooPanel_Store_Locator_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WooPanel_Store_Locator_i18n();
		$plugin_i18n->set_domain( $this->get_woopanel_store_locator() );

		//$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

		add_action( 'plugins_loaded', array($this, 'load_plugin_textdomain') );
		//$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	public function load_plugin_textdomain() {


		$domain 		= 'asl_locator';
		$admin_domain 	= 'asl_admin';


		$mo_file  		= WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . get_locale() . '.mo';
		$mo_admin_file 	= WP_LANG_DIR . '/' . $admin_domain . '/' . $admin_domain . '-' . get_locale() . '.mo';

		//Plugin Frontend
		load_textdomain( $domain, $mo_file ); 
		load_plugin_textdomain( $domain, false, WOOPANEL_STORE_LOCATOR_BASE . '/languages/');


		//Load the Admin Language File
		if (is_admin()) {

			load_textdomain( $admin_domain, $mo_admin_file ); 
			load_plugin_textdomain( $admin_domain, false, WOOPANEL_STORE_LOCATOR_BASE . '/languages/');
		}
	}


	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

	
		//ad menu if u r an admin
		add_action('admin_menu', array($this,'add_admin_menu'));
		add_action('wp_ajax_wplsl_upload_logo', array($this->plugin_admin, 'upload_logo'));	
		add_action('wp_ajax_wplsl_upload_marker', array($this->plugin_admin, 'upload_marker'));	
		/*For Stores*/
		add_action('wp_ajax_wplsl_add_store', array($this->plugin_admin, 'add_new_store'));	
		add_action('wp_ajax_wplsl_delete_all_stores', array($this->plugin_admin, 'admin_delete_all_stores'));	
		add_action('wp_ajax_wplsl_edit_store', array($this->plugin_admin, 'update_store'));	
		add_action('wp_ajax_wplsl_get_store_list', array($this->plugin_admin, 'get_store_list'));	
		add_action('wp_ajax_wplsl_delete_store', array($this->plugin_admin, 'delete_store'));	
		add_action('wp_ajax_wplsl_duplicate_store', array($this->plugin_admin, 'duplicate_store'));	
		add_action('wp_ajax_wplsl_store_status', array($this->plugin_admin, 'store_status'));	

		/*Categories*/
		add_action('wp_ajax_wplsl_add_categories', array($this->plugin_admin, 'add_category'));
		add_action('wp_ajax_wplsl_delete_category', array($this->plugin_admin, 'delete_category'));
		add_action('wp_ajax_wplsl_update_category', array($this->plugin_admin, 'update_category'));
		add_action('wp_ajax_wplsl_get_category_byid', array($this->plugin_admin, 'get_category_by_id'));
		add_action('wp_ajax_wplsl_get_categories', array($this->plugin_admin, 'get_categories'));	

		/*Markers*/
		add_action('wp_ajax_wplsl_add_markers', array($this->plugin_admin, 'add_marker'));
		add_action('wp_ajax_wplsl_delete_marker', array($this->plugin_admin, 'delete_marker'));
		add_action('wp_ajax_wplsl_update_marker', array($this->plugin_admin, 'update_marker'));
		add_action('wp_ajax_wplsl_get_marker_byid', array($this->plugin_admin, 'get_marker_by_id'));
		add_action('wp_ajax_wplsl_get_markers', array($this->plugin_admin, 'get_markers'));	

		/*Import and settings*/
		add_action('wp_ajax_wplsl_import_store', array($this->plugin_admin, 'import_store'));	
		add_action('wp_ajax_wplsl_delete_import_file', array($this->plugin_admin, 'delete_import_file'));	
		add_action('wp_ajax_wplsl_upload_store_import_file', array($this->plugin_admin, 'upload_store_import_file'));
		add_action('wp_ajax_wplsl_export_file', array($this->plugin_admin, 'export_store'));
		add_action('wp_ajax_wplsl_save_setting', array($this->plugin_admin, 'save_setting'));
		

		/*Infobox & Map*/
		add_action('wp_ajax_wplsl_save_custom_map', array($this->plugin_admin, 'save_custom_map'));



		$this->loader->add_action( 'admin_enqueue_scripts', $this->plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->plugin_admin, 'enqueue_scripts' );

	}

	/*All Admin Callbacks*/
	public function add_admin_menu() {

		if (current_user_can('delete_posts')){
			$svg = 'dashicons-location';
			add_Menu_page('Agile Store Locator', __('Store Locator','asl_admin'), 'delete_posts', 'asl-plugin', array($this->plugin_admin,'admin_plugin_settings'),$svg);
			add_submenu_page( 'asl-plugin', __('Dashboard','asl_admin'), __('Dashboard','asl_admin'), 'delete_posts', 'agile-dashboard', array($this->plugin_admin,'admin_dashboard'));
			add_submenu_page( 'asl-plugin', __('Create New Store','asl_admin'), __('Add New Store','asl_admin'), 'delete_posts', 'woopanel-create-store', array($this->plugin_admin,'admin_add_new_store'));
			add_submenu_page( 'asl-plugin', __('Manage Stores','asl_admin'), __('Manage Stores','asl_admin'), 'delete_posts', 'woopanel-manage-store', array($this->plugin_admin,'admin_manage_store'));
			add_submenu_page( 'asl-plugin', __('Manage Categories','asl_admin'), __('Manage Categories','asl_admin'), 'delete_posts', 'manage-asl-categories', array($this->plugin_admin,'admin_manage_categories'));
		
			add_submenu_page( 'asl-plugin', __('Settings','asl_admin'), __('Settings','asl_admin'), 'delete_posts', 'user-settings', array($this->plugin_admin,'admin_user_settings'));
			
			add_submenu_page('asl-plugin-edit', __('Edit Store','asl_admin'), __('Edit Store','asl_admin'), 'delete_posts', 'woopanel-edit-store', array($this->plugin_admin,'edit_store'));
			remove_submenu_page( "asl-plugin", "asl-plugin" );
			remove_submenu_page( "asl-plugin", "asl-plugin-edit" );
			//edit-agile-store
        }
	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$this->loader->add_action( 'wp_enqueue_scripts', $this->plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->plugin_public, 'enqueue_scripts' );
		

        add_shortcode( 'woopanel_store_locator',array($this->plugin_public,'woopanel_store_locator_frontend'));	
	}

	
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_woopanel_store_locator() {
		return $this->_store_locator;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WooPanel_Store_Locator_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
