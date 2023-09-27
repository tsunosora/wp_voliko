<?php
class NBT_Solutions_Dashboard
{
    public function __construct()
    {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            add_action( 'admin_notices', array( __CLASS__, 'install_woocommerce_admin_notice') );
        }else{
            add_action('admin_menu', array($this, 'register_dashboard_submenu'));
        }

        add_action('admin_head', array($this, 'hide_notices'));
    }

    public function install_woocommerce_admin_notice()
    {
        ?>
        <div class="error">
            <p><?php _e('WooCommerce plugin is not activated. Please install and activate it to use for plugin <strong>NBT WooCommerce Price Matrix</strong>.', 'nbt-solution'); ?></p>
        </div>
        <?php
    }

    public function register_dashboard_submenu()
    {
        $submenu = add_submenu_page('solutions', __('Dashboard', 'nbt-solution'), __('Solution Dashboard', 'nbt-solution'), 'manage_options', 'solution-dashboard', array($this, 'dashboard_page'));
        add_action('admin_print_scripts-' . $submenu, array($this, 'dashboard_scripts'));
    }

    //Most important lines of code in this modules :D
    public function dashboard_page()
    {   
        echo '<div id="dashboard-app"></div>';
    }

    public function get_geo_country() {
        $ip = WC_Geolocation::get_ip_address();
        $country = WC_Geolocation::geolocate_ip($ip);

        return $country;
    }

    public function get_user_display_name() {
        $current_user = wp_get_current_user();

        return $current_user->display_name;
    }

    public function dashboard_scripts()
    {
        wp_enqueue_media();
        wp_register_style('solution-admin-icon', PREFIX_NBT_SOL_URL . 'assets/admin/css/flaticon.css', array(), '1.0.0', false);
        wp_register_style('solution-admin', PREFIX_NBT_SOL_URL . 'assets/admin/dashboard/main.css', array(), '1.0.0', false);
        wp_register_script('solution-admin', PREFIX_NBT_SOL_URL . 'assets/admin/dashboard/build.js', array(), '1.0.0', true);        
        wp_localize_script('solution-admin', 'nb', array(
            'api_route' => get_site_url() . '/wp-json/solutions/v1/',
            'site_currency_symbol' => get_woocommerce_currency_symbol(),
            'site_currency' => get_woocommerce_currency(),
            'user_display_name' => $this->get_user_display_name(),
            'geo_country' => $this->get_geo_country(),
            'ds_api_key' => 'd9c0dcd06b65cf017f0c9463292e0355',
            'site_currency_country' => get_woocommerce_currencies(),
			'nonce' => wp_create_nonce( 'wp_rest' ),
        ));
        wp_enqueue_script('solution-admin');
        wp_enqueue_style('solution-admin');
        wp_enqueue_style('solution-admin-icon');
        wp_enqueue_style('wpb-google-fonts', 'https://fonts.googleapis.com/icon?family=Material+Icons', false);
    }

    public function hide_notices() {
        if(isset($_GET['page']) && $_GET['page'] === 'solution-dashboard') {
            echo '<style>#setting-error-tgmpa>.updated, .settings-error, .notice, .is-dismissible, .update-nag, .updated { display: none; }</style>';        
        }
    }
}
new NBT_Solutions_Dashboard();