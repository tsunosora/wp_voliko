<?php
/**
 * This class will show only menu if detect other modules want to add admin_menus
 *
 * @package WooPanel_Modules
 */
class WooPanel_Plugins {

	/**
	 * Return settings
	 *
	 * @return  void
	 */
	public $settings = array();

	/**
	 * WooPanel_Plugins constructor.
	 */
	public function __construct($args = array()) {
		if ( ! empty( $args ) ) {
			$this->settings = $args;

	        if( isset( $this->settings['create_menu_page'] ) && $this->settings[ 'create_menu_page'] ){
	            $this->add_menu_page();
	        }

	        add_action( 'admin_menu', array( $this, 'add_setting_page' ), 20 );
	    }

	}

	/**
	 * Add menu page in WP-Admin
	 */
	public function add_menu_page() {
		global $admin_page_hooks;

		if(!isset($admin_page_hooks['ntb_plugin_panel'])){
			$position = apply_filters( 'WooPanel_Plugins_menu_item_position', '62.32' );
			add_menu_page( 'ntb_plugin_panel', 'NBT Plugins', 'manage_options', 'ntb_plugin_panel', NULL, 'dashicons-awards', $position );
		}
	}
	
	/**
	 * Config menu page in WP-Admin
	 */
	public function add_setting_page(){
	        $this->settings['icon_url'] = isset( $this->settings['icon_url'] ) ? $this->settings['icon_url'] : '';
		    $this->settings['position'] = isset( $this->settings['position'] ) ? $this->settings['position'] : null;
	        $parent = esc_attr( $this->settings['parent_slug'] ) . esc_attr( $this->settings['parent_page'] );

	        if ( ! empty( $parent ) ) {
		        add_submenu_page( $parent, $this->settings['page_title'], $this->settings['menu_title'], $this->settings['capability'], $this->settings['page'], $this->settings['functions'] );
	        }
            /* === Duplicate Items Hack === */
            $this->remove_duplicate_submenu_page();
            do_action( 'nbt_after_add_settings_page' );
	}

	/**
	 * Remove submenu if submenu same main menu
	 */
    public function remove_duplicate_submenu_page() {
        /* === Duplicate Items Hack === */
        remove_submenu_page( 'ntb_plugin_panel', 'ntb_plugin_panel' );
    }



}