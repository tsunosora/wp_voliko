<?php
/**
 * Plugin Name: WooCommerce Frontend Dashboard Manager
 * Plugin URL: http://demo12.cmsmart.net/demo-woopanel/sellercenter/
 * Description: You are the store/ vendor manager and you want to get everything in the easiest way to know how your business works well or not.  WooPanel is the right plugin that you must to integrate in your site. Your articles, products, orders, coupons, customers will be arraigned clean and neat to bring you a general look with optimized UX/UI compare to our other competitors.
 * Version: 1.2.7
 * Author: NetBase Team
 * Author URI: https://codecanyon.net/item/woocommerce-frontend-dashboard-manager/25474509
 * Text Domain: woopanel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Main WooDashboard Class.
 *
 * @class NBWooCommerceDashboard
 */
final class NBWooCommerceDashboard {

    /**
     * WooDashboard version.
     *
     * @var string
     */
    public $version = '1.2.5';

    /**
     * The single instance of the class.
     *
     * @var WooDashboard
     * @since 1.0
     */
    protected static $_instance = null;

    /**
     * Query instance.
     *
     * @var WooPanel_Rewrites
     */
    public $query = null;

    /**
     * A boolean flag on whether or not the user is logged in
     * @var bool
     */
    public $shortcodes = null;

    /**
     * Session instance.
     *
     * @var WooDashboard_Session
     */
    public $session = null;


    /**
     * Seller instance.
     *
     * @var WooDashboard_Session
     */
    public $seller = null;

    public $store = null;

    /**
     * Set role will support on WooDashboard
     * @var array
     */
    static $permission = array(
        'shop_manager', 'seller', 'wpl_seller'
    );

    /**
    * Set role for super admin
    * @since 18/06/2020
    **/
    static $role_super_admin = array(
        'administrator', 'shop_manager'
    );

    /**
    * Set role for vendor
    * @since 18/06/2020
    **/
    static $role_seller = array(
        'seller', 'wpl_seller'
    );


    static $role = array(
        'wpl_seller'
    );

    /**
     * Main WooDashboard Instance.
     *
     * Ensures only one instance of WooDashboard is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see WooDashboard()
     * @return NBWooCommerceDashboard - Main instance.
     */
    public static function init() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * WooDashboard Constructor.
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();

        $this->query->init();
    }

    /**
     * Hook into actions and filters.
     *
     * @since 1.0
     */
    private function init_hooks() {
        register_activation_hook( __FILE__, array( 'WooPanel_Installer', 'do_install' ) );
        register_deactivation_hook( __FILE__,  array( 'WooPanel_Installer', 'deactive' ) );
        add_action( 'init', array($this, 'load_textdomain') );
        add_action( 'wp_before_admin_bar_render', array( $this, 'woopanel_admin_toolbar' ) );
        add_action( 'admin_init', array( $this, 'woopanel_block_wp_admin' ) );
        add_filter( 'plugin_action_links_'. plugin_basename(__FILE__), array( &$this, 'woopanel_plugin_action_links' ) );
        add_action( 'activate_woocommerce/woocommerce.php', array($this, 'clear_cache') );
    }

    /**
     * Define WooDashboard Constants.
     */
    private function define_constants() {
        $this->define( 'WOODASHBOARD_DIR', plugin_dir_path( __FILE__ ) );
        $this->define( 'WOODASHBOARD_URL', plugin_dir_url( __FILE__ ) );
        $this->define( 'WOODASHBOARD_FILE', __FILE__ );
        $this->define( 'WOODASHBOARD_INC_DIR', plugin_dir_path( __FILE__ ) .'includes/' );
        $this->define( 'WOODASHBOARD_VIEWS_DIR', plugin_dir_path( __FILE__ ) .'views/' );
        $this->define( 'WOODASHBOARD_TEMPLATE_DIR', plugin_dir_path( __FILE__ ) .'templates/' );
        $this->define( 'WOODASHBOARD_TEMPLATE_DEBUG', false );

        include_once WOODASHBOARD_INC_DIR . "global.php";
    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    private function includes() {

        include_once WOODASHBOARD_INC_DIR . "classes/class-session.php";
        include_once WOODASHBOARD_INC_DIR ."functions.php";
        include_once WOODASHBOARD_INC_DIR ."classes/vendor/functions.php";
        include_once WOODASHBOARD_DIR . 'admin/admin-options.php';
        include_once WOODASHBOARD_INC_DIR . 'helper/options.php';

        include_once WOODASHBOARD_INC_DIR . "classes/class-vendor.php";
        include_once WOODASHBOARD_INC_DIR . "classes/class-rewrites.php";
        include_once WOODASHBOARD_INC_DIR . "classes/class-installer.php";
        include_once WOODASHBOARD_INC_DIR . 'modules/store-locator/includes/class-store-locator-activator.php';
        include_once WOODASHBOARD_INC_DIR . "classes/class-shortcodes.php";
        include_once WOODASHBOARD_INC_DIR . "classes/class-template.php";

        
        include_once WOODASHBOARD_INC_DIR . 'helper/actions.php';

        include_once WOODASHBOARD_INC_DIR . 'fields/form-fields.php';
        include_once WOODASHBOARD_INC_DIR . 'helper/notice.php';
        
        include_once WOODASHBOARD_INC_DIR . "classes/dashboard/class-dashboard-report.php";
        include_once WOODASHBOARD_INC_DIR . "classes/dashboard/class-dashboard-report-order.php";
        include_once WOODASHBOARD_INC_DIR . "classes/dashboard/class-dashboard-ajax.php";
        include_once WOODASHBOARD_INC_DIR . 'classes/vendor/manager.php';

        if( !is_admin() ) {
            include_once WOODASHBOARD_INC_DIR . 'walkers/class-walker-category.php';

            include_once WOODASHBOARD_INC_DIR . 'helper/post.php';
            include_once WOODASHBOARD_INC_DIR . 'helper/list-table.php';
            
            include_once WOODASHBOARD_INC_DIR . 'helper/template.php';
            include_once WOODASHBOARD_INC_DIR . 'helper/menus.php';
            include_once WOODASHBOARD_INC_DIR . 'helper/permalinks.php';
            include_once WOODASHBOARD_INC_DIR . 'helper/filter.php';
            include_once WOODASHBOARD_INC_DIR . 'helper/pagination.php';
            
            include_once WOODASHBOARD_INC_DIR . 'helper/dashboard.php';
            include_once WOODASHBOARD_INC_DIR . "helper/metabox.php";

            include_once WOODASHBOARD_INC_DIR . 'woocommerce.php';

            include_once WOODASHBOARD_INC_DIR . "classes/class-list-table.php";
            include_once WOODASHBOARD_INC_DIR . "classes/class-list-post-table.php";
            include_once WOODASHBOARD_INC_DIR . 'classes/class-taxonomy.php';

            // WooDashboard Pages
            include_once WOODASHBOARD_INC_DIR . "pages/dashboard.php";
            include_once WOODASHBOARD_INC_DIR . "pages/article.php";
            include_once WOODASHBOARD_INC_DIR . "pages/product.php";
            include_once WOODASHBOARD_INC_DIR . "pages/product-categories.php";
            include_once WOODASHBOARD_INC_DIR . "pages/product-tags.php";
            include_once WOODASHBOARD_INC_DIR . "pages/product-attributes.php";
            include_once WOODASHBOARD_INC_DIR . "pages/coupon.php";
            include_once WOODASHBOARD_INC_DIR . "pages/customer.php";
            include_once WOODASHBOARD_INC_DIR . "pages/order.php";
            include_once WOODASHBOARD_INC_DIR . "pages/comment.php";
            include_once WOODASHBOARD_INC_DIR . "pages/review.php";

            $this->shortcodes = new WooDashboard_Shortcodes();
        }

        include_once WOODASHBOARD_INC_DIR . 'modules/modules.php';
        include_once WOODASHBOARD_INC_DIR . "pages/faq.php";
        include_once WOODASHBOARD_INC_DIR . "classes/live-chat/class-live-chat.php";
        include_once WOODASHBOARD_INC_DIR . 'helper/post-function.php';
        include_once WOODASHBOARD_INC_DIR . 'ajax.php';
        include_once WOODASHBOARD_INC_DIR . 'login.php';
        include_once WOODASHBOARD_INC_DIR . 'membership.php';

        if( class_exists('Nbdesigner_Plugin') ) {
            include_once WOODASHBOARD_INC_DIR . 'modules/online-desginer/online-desginer.php';
        }
        

        $this->query = new WooDashboard_Rewrites();
        $this->session = new WooDashboard_Session();
        $this->store = new WooPanel_Store();
    }

    /**
     * Support textdomain multi-language
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'woopanel', false, WOODASHBOARD_DIR . 'languages' ); 
    }

    /**
     * Display menu on admin bar
     */
    function woopanel_admin_toolbar() {
        global $wp_admin_bar;

        if ( ! is_shop_staff() ) {
            return;
        }

        $args = array(
            'id'     => 'woopanel',
            'title'  => esc_html__( 'WooPanel', 'woopanel' ),
            'href'   => woopanel_dashboard_url()
        );

        $wp_admin_bar->add_menu( $args );

        if( woopanel_dashboard_url() ) {
            $wp_admin_bar->add_menu(array(
                'id' => 'woopanel-dashboard',
                'parent' => 'woopanel',
                'title' => esc_html__('Seller Center', 'woopanel' ),
                'href' => woopanel_dashboard_url()
            ));
            if( current_user_can('administrator') ) {
                $wp_admin_bar->add_menu(array(
                    'id' => 'woopanel-settings',
                    'parent' => 'woopanel',
                    'title' => esc_html__( 'Settings', 'woopanel' ),
                    'href' => admin_url( 'options-general.php?page=woopanel-settings' )
                ));
            }

            if (is_admin()) {
                $wp_admin_bar->add_menu(array(
                    'id' => 'view-dashboard',
                    'parent' => 'site-name',
                    'title' => esc_html__('Visit Seller Center', 'woopanel' ),
                    'href' => woopanel_dashboard_url()
                ));
            }
        }
    }

    /**
     * Disable access WP-Admin page if enable
     */
    function woopanel_block_wp_admin() {
        if ( is_admin() &&
            ( WooPanel_Admin_Options::get_option('block_wp_admin') == 'yes' ) &&
            !current_user_can( 'administrator' ) &&
            !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            wp_die( esc_html__('You need a higher level of permission.', 'woopanel' ) );
        }
    }

    /**
     * Filters the list of action links displayed for a specific plugin in the Plugins list table.
     *
     * @return array
     */
    function woopanel_plugin_action_links( $links ) {
        $action_links = array(
            'settings' => '<a href="' . admin_url( 'options-general.php?page=woopanel-settings' ) . '" aria-label="' . esc_attr__( 'View settings' ) . '">' . esc_html__( 'Settings', 'woopanel' ) . '</a>',
        );

        $action_links = array_merge( $action_links, $links );

        return $action_links;
    }

    /**
     * Clear message for the session
     */
    function clear_cache() {
        $this->session->set( 'woopanel_notices', null );
    }

    /**
     * Get the plugin url.
     *
     * @return string
     */
    public function plugin_url( $slug = null ){
        return esc_url( WOODASHBOARD_URL . esc_attr( $slug ) );
    }
}

/**
 * Returns the main instance of NBWooCommerceDashboard.
 *
 * @since  1.0.0
 * @return NBWooCommerceDashboard
 */
if ( ! function_exists( 'WooDashboard' ) ) {
    function WooDashboard() {
        return NBWooCommerceDashboard::init();
    }
}

WooDashboard();


if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}