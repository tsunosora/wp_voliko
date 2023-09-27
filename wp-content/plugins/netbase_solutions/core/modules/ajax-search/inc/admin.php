<?php
class NBT_WooCommerce_AjaxSearch_Admin{

	static $id = 'ajax-search';
	static $ajaxcart_settings;
	
	public function __construct() {



        if( defined('PREFIX_NBT_SOL') && !class_exists('NBT_Plugins') ){
        }else{

            if( !class_exists('NBT_Plugins') ){
                require_once AJAX_SEARCH_PATH . 'inc/plugins.php';
            }
            //add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
        }

	}

    public function register_panel(){
        $args = array(
            'create_menu_page' => true,
            'parent_slug'   => '',
            'page_title'    => __( 'Ajax Search', 'nbt-plugins' ),
            'menu_title'    => __( 'Ajax Search', 'nbt-plugins' ),
            'capability'    => apply_filters( 'nbt_cs_settings_panel_capability', 'manage_options' ),
            'parent'        => '',
            'parent_page'   => 'ntb_plugin_panel',
            'page'          => 'nbt-ajax-search',
            'admin-tabs'    => $this->available_tabs,
            'functions'     => array(__CLASS__ , 'ntb_cs_page'),
            'font-path'  => AJAX_SEARCH_URL . 'assets/css/nbt-plugins.css'
        );

        $this->_panel = new NBT_Plugins($args);
    }

    public function ntb_cs_page(){
        echo rand();
        include(AJAX_SEARCH_PATH .'tpl/admin.php');
    }

}
new NBT_WooCommerce_AjaxSearch_Admin();