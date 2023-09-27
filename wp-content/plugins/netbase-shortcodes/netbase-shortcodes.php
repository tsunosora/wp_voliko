<?php
/*
Plugin Name: Netbase Shortcodes
Plugin URI: http://netbaseteam.com
Description: Shortcodes for WpNetbase Wordpress Theme.
Version: 1.4.5
Author: Netbaseteam
Author URI: http://netbaseteam.com
*/

// don't load directly
if (!defined('ABSPATH'))
    die('-1');

define('NETBASE_SHORTCODES_URL', plugin_dir_url(__FILE__));
define('NETBASE_SHORTCODES_WOO_PATH', dirname(__FILE__) . '/woo_shortcodes/');
define('NETBASE_SHORTCODES_LIB', dirname(__FILE__) . '/lib/');
define('NETBASE_SHORTCODES_WOO_TEMPLATES', dirname(__FILE__) . '/woo_templates/');

class NetbaseShortcodesClass {

    private $woo_shortcodes = array( 
        "netbase_product_category",        
        "netbase_list_products_cat",        
    );

    function __construct() {

        // Init plugins
        add_action( 'init', array( $this, 'initPlugin' ) );

        $this->addShortcodes();

        add_action( 'admin_enqueue_scripts', array( $this, 'loadAdminCssAndJs' ) );
        add_filter( 'the_content', array( $this, 'formatShortcodes' ) );
        add_filter( 'widget_text', array( $this, 'formatShortcodes' ) );
    }

    // Init plugins
    function initPlugin() {
        $this->addTinyMCEButtons();
    }

    // load css and js
    function loadAdminCssAndJs() {
        wp_register_style( 'netbase_shortcodes_admin', NETBASE_SHORTCODES_URL . 'assets/css/admin.css' );
        wp_enqueue_style( 'netbase_shortcodes_admin' );
        wp_register_style( 'netbase_shortcodes_simpleline', NETBASE_SHORTCODES_URL . 'assets/css/Simple-Line-Icons/Simple-Line-Icons.css' );
        wp_enqueue_style( 'netbase_shortcodes_simpleline' );
    }

    // Add buttons to tinyMCE
    function addTinyMCEButtons() {
        if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
            return;

        if ( get_user_option('rich_editing') == 'true' ) {
            add_filter( 'mce_external_plugins', array(&$this, 'addTinyMCEJS') );
            add_filter( 'mce_buttons', array(&$this, 'registerTinyMCEButtons') );
        }
    }

    function addTinyMCEJS($plugin_array) {
        if (get_bloginfo('version') >= 3.9)
            $plugin_array['shortcodes'] = NETBASE_SHORTCODES_URL . 'assets/tinymce/shortcodes_4.js';
        else
            $plugin_array['shortcodes'] = NETBASE_SHORTCODES_URL . 'assets/tinymce/shortcodes.js';

        $plugin_array['netbase_shortcodes'] = NETBASE_SHORTCODES_URL . 'assets/tinymce/netbase_shortcodes.js';
        return $plugin_array;
    }

    function registerTinyMCEButtons($buttons) {
        array_push($buttons, "netbase_shortcodes_button");
        return $buttons;
    }

    // Add shortcodes
    function addShortcodes() {

        if (function_exists('get_plugin_data')) {
            $plugin = get_plugin_data(dirname(__FILE__) . '/netbase-shortcodes.php');
            define('NETBASE_SHORTCODES_VERSION', $plugin['Version']);
        } else {
            define('NETBASE_SHORTCODES_VERSION', '');
        }

        require_once(NETBASE_SHORTCODES_LIB . 'functions.php');

        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            foreach ($this->woo_shortcodes as $woo_shortcode) {
                require_once(NETBASE_SHORTCODES_WOO_PATH . $woo_shortcode . '.php');
            }
        }
    }

    // Format shortcodes content
    function formatShortcodes($content) {
        $woo_block = join("|", $this->woo_shortcodes);
        // opening tag
        $content = preg_replace("/(<p>)?\[($woo_block)(\s[^\]]+)?\](<\/p>|<br \/>)?/","[$2$3]", $content);
        // closing tag
        $content = preg_replace("/(<p>)?\[\/($woo_block)](<\/p>|<br \/>)/","[/$2]", $content);

        return $content;
    }

}

// Finally initialize code
new NetbaseShortcodesClass();