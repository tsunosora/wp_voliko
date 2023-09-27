<?php
defined( 'ABSPATH' ) || exit;

/**
 * This class will install data
 *
 * @package WooPanel_Installer
 */

class WooPanel_Installer {

    /**
     * Return Seller Center page
     */
    public static $pages = array(
        'dashboard' => array(
            'post_name'    => 'sellercenter',      // Slug of page.
            'post_title'   => 'Seller Center', // Title of page
            'post_content' => '[woopanel]',    // Content of page
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ),
        'stores' => array(
            'post_name'    => 'stores',      // Slug of page.
            'post_title'   => 'Store Listing', // Title of page
            'post_content' => '[woopanel_stores]',    // Content of page
            'post_status'  => 'publish',
            'post_type'    => 'page',
        )
    );

    /**
     * WooPanel_Installer init.
     */
    public static function do_install() {
        WooPanel_Store_Locator_Activator::activate();
        self::woopanel_insert_page();
        self::set_default_options();
        self::reset_permalinks();
    }

    public static function deactive() {
         include_once WOODASHBOARD_INC_DIR . 'modules/store-locator/includes/class-store-locator-deactivator.php';
        WooPanel_Store_Locator_Deactivator::deactivate();
    }

    /**
     * Create Seller Center page
     */
    public static function woopanel_insert_page(){
        foreach (self::$pages as $k => $page) {
            // Create post object
            $my_post = array(
                'post_name'     => $page['post_name'],
                'post_title'    => $page['post_title'],
                'post_content'  => $page['post_content'],
                'post_status'   => $page['post_status'],
                'post_author'   => get_current_user_id(),
                'post_type'     => $page['post_type'],
            );

            $page_check = get_page_by_path($page['post_name']);
            if(!isset($page_check->ID)){
                wp_insert_post( $my_post, '' );
            }
            /* else if( get_post_status( $page_check->ID ) != 'publish' ) {
                wp_update_post( array( 'ID' => $page_check->ID, 'post_status' => 'publish' ) );
            } */
        }
    }

    /**
     * Get URL Seller Center page
     */
    static function woopanel_page_url($id){
        return get_page_by_path( self::$pages[$id]['post_name'] ) ? get_permalink( get_page_by_path( self::$pages[$id]['post_name'] ) ) : null;
    }

    /**
     * Set default option
     */
    static function set_default_options(){
        $dashboard_page = get_page_by_path( self::$pages['dashboard']['post_name'] );
        $stores_page = get_page_by_path( self::$pages['stores']['post_name'] );

        WooPanel_Admin_Options::set_option( 'dashboard_page_id', $dashboard_page ? $dashboard_page->ID : null );
        WooPanel_Admin_Options::set_option( 'woopanel_page_stores', $stores_page ? $stores_page->ID : null );
        WooPanel_Admin_Options::set_option( 'woocommerce_enable', is_woo_installed() ? 'yes' : 'no' );
    }

    /**
     * Reset permalink
     */
    static function reset_permalinks() {
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');
        $wp_rewrite->flush_rules();
    }
}