<?php 

/**
 * WPB WooCommerce Related Products Slider
 * By WPbean
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Configure The Settings
 */

if ( !class_exists('netbaseteam_wrps_settings' ) ):
class netbaseteam_wrps_settings {

    private $settings_api;

    function __construct() {
        $this->settings_api = new NetBaseTeam_Widget_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }
	
    function admin_menu() {
        add_options_page( __( 'NetBaseTeam Widget','netbaseteam-so-widgets' ), __( 'NetBaseTeam Widget','netbaseteam-so-widgets' ), 'manage_options', 'netbaseteam_widget_for_siteorigin', array($this, 'wpb_plugin_page') );

    }
	// setings tabs
    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'netbaseteam_nws_general',
                'title' => __( 'General Settings', 'netbaseteam-so-widgets' )
            )
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array( 
			
            'netbaseteam_nws_general' => array(
            	array(
                    'name'      => 'netbaseteam_nws_zoom',
                    'label'     => __( 'Enable Zoom WooCommerce', 'netbaseteam-so-widgets' ),
                    'desc'      => __( 'Choose a Enable Zoom WooCommerce', 'netbaseteam-so-widgets' ),
                    'type'      => 'select',
                    'default'   => 'no',
                    'options'   => array(
                        '1'  => __( 'Enable', 'netbaseteam-so-widgets' ),
                        '0'    => __( 'Disable', 'netbaseteam-so-widgets' ),
                    )
                ),
                /*
                array(
                    'name'              => 'wpb_wrps_number_of_products',
                    'label'             => __( 'Number of Related Products', 'netbaseteam-so-widgets' ),
                    'desc'              => __( 'Number of Related Products to show in this slider.', 'netbaseteam-so-widgets' ),
                    'type'              => 'number',
                    'default'           => 100,
                    'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'wpb_wrps_number_of_columns',
                    'label'             => __( 'Number of columns in Slider', 'netbaseteam-so-widgets' ),
                    'desc'              => __( 'Default: 3 columns.', 'netbaseteam-so-widgets' ),
                    'type'              => 'number',
                    'default'           => 3,
                    'sanitize_callback' => 'intval'
                ),*/
            )
			
        );
		return $settings_fields;
    }
	
	// warping the settings
    function wpb_plugin_page() {
        ?>
            <?php do_action ( 'wpb_wrps_before_settings' ); ?>
            <div class="netbaseteam_wrps_settings_area">
                <div class="wrap netbaseteam_wrps_settings">
                    <?php
            			$this->settings_api->show_navigation();
            			$this->settings_api->show_forms();
                    ?>
        		</div>
                <div class="netbaseteam_wrps_settings_content">
                    <?php do_action ( 'netbaseteam_wrps_settings_content' ); ?>
                </div>
            </div>
            <?php do_action ( 'wpb_wrps_after_settings' ); ?>
        <?php
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }
        return $pages_options;
    }
}
endif;

$settings = new netbaseteam_wrps_settings();