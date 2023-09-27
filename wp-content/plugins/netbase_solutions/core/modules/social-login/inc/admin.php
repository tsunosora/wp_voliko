<?php

class NBT_WooCommerce_Social_Login_Admin{

	public function __construct() {
        if( defined('PREFIX_NBT_SOL') && !class_exists('NBT_Plugins') ){

        }else{
            if( !class_exists('NBT_Plugins') ){
                require_once SOCIAL_LOGIN_PATH . 'inc/plugins.php';
            }

            //add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

            // register_activation_hook
            register_activation_hook( __FILE__, array($this, 'nbt_plugin_activation') ); //load the default setting for the plugin while activating


        }

     
	}

    public function register_panel(){
        $args = array(
            'create_menu_page' => true,
            'parent_slug'   => '',
            'page_title'    => __( 'Social Login', 'nbt-plugins' ),
            'menu_title'    => __( 'Social Login', 'nbt-plugins' ),
            'capability'    => apply_filters( 'nbt_cs_settings_panel_capability', 'manage_options' ),
            'parent'        => '',
            'parent_page'   => 'ntb_plugin_panel',
            'page'          => 'nbt-social-login.css',
            'admin-tabs'    => $this->available_tabs,
            'functions'     => array(__CLASS__ , 'ntb_cs_page'),
            'font-path'  => AJAX_CART_URL . 'assets/css/nbt-plugins.css'
        );

        $this->_panel = new NBT_Plugins($args);
    }

    public function ntb_cs_page(){
        include(SOCIAL_LOGIN_PATH .'tpl/admin.php');
    }

	public function nbt_ajaxcart_scripts_method($hooks){
		wp_enqueue_style( 'ntb-fonts', SOCIAL_LOGIN_URL . 'assets/css/ntb-fonts.css'  );
		wp_enqueue_style( 'admin', SOCIAL_LOGIN_URL . 'assets/css/admin.css'  );
        wp_enqueue_script( 'admin', SOCIAL_LOGIN_URL . 'assets/js/admin.js', null, null, true );
	}

    function nbt_plugin_activation()
    {
        global $wpdb;
        // create user details table
        $nbt_userdetails = "{$wpdb->prefix}nbt_users_social_profile_details";

        $sql = "CREATE TABLE IF NOT EXISTS `$nbt_userdetails` (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    user_id int(11) NOT NULL,
                    provider_name varchar(50) NOT NULL,
                    identifier varchar(255) NOT NULL,
                    unique_verifier varchar(255) NOT NULL,
                    email varchar(255) NOT NULL,
                    email_verified varchar(255) NOT NULL,
                    first_name varchar(150) NOT NULL,
                    last_name varchar(150) NOT NULL,
                    profile_url varchar(255) NOT NULL,
                    website_url varchar(255) NOT NULL,
                    photo_url varchar(255) NOT NULL,
                    display_name varchar(150) NOT NULL,
                    description varchar(255) NOT NULL,
                    gender varchar(10) NOT NULL,
                    language varchar(20) NOT NULL,
                    age varchar(10) NOT NULL,
                    birthday int(11) NOT NULL,
                    birthmonth int(11) NOT NULL,
                    birthyear int(11) NOT NULL,
                    phone varchar(75) NOT NULL,
                    address varchar(255) NOT NULL,
                    country varchar(75) NOT NULL,
                    region varchar(50) NOT NULL,
                    city varchar(50) NOT NULL,
                    zip varchar(25) NOT NULL,
                    UNIQUE KEY id (id),
                    KEY user_id (user_id),
                    KEY provider_name (provider_name)
                )";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

    }



}
new NBT_WooCommerce_Social_Login_Admin();