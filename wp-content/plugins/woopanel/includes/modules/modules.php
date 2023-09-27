<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * This class will load modules (extension) of WooPanel
 *
 * @package WooPanel_Modules
 */
class WooPanel_Modules {

    /**
     * Return all modules registered.
     *
     * @var array
     */
    public $modules = array();

    /**
     * Return active modules enable by Super Admin.
     *
     * @var array
     */
    public $active_modules = array();

    private $admin_page;

    protected $package = '';

    /**
     * WooPanel_Modules Constructor.
     */
    public function __construct()
    {
        $this->package = wp_get_theme(get_template())->get('Tags');

        /**
         * Filters register modules
         *
         * @since 1.0.0
         * @hook woopanel_register_modules
         * @param {array} $modules
         * @return {array} $modules
         */
        $this->modules = apply_filters( 'woopanel_register_modules', array(
            'price-matrix' => array(
                'label' => 'Price Matrix',
                'class' => 'Price_Matrix',
                'file'  => 'price-matrix.php',
                'description' => esc_html__('Allow customers easy enter price for variation product.', 'woopanel' )
            ),
            'color-swatches' => array(
                'label' => 'Color Swatches',
                'class' => 'Color_Swatches',
                'file'  => 'color-swatches.php',
                'description' => esc_html__('Change default select box of attributes to something more intuitive: Radio images, Radio buttons, Color, etc.', 'woopanel' )
            ),
            'online-desginer' => array(
                'label' => 'Online Desginer',
                'class' => 'Online_Desginer',
                'file'  => 'online-desginer.php',
                'description' => esc_html__('Module for Online Desginer', 'woopanel'),
                'plugin_class' => 'Nbdesigner_Plugin'
            ),
            'dokan' => array(
                'label' => 'Dokan',
                'class' => 'Dokan',
                'file'  => 'dokan.php',
                'description' => esc_html__('Module for Dokan', 'woopanel' )
            ),
            'dokan-review' => array(
                'label' => 'Dokan: Review',
                'class' => 'Dokan_Review',
                'file'  => 'dokan-review.php',
                'description' => esc_html__('Change default select box of attributes to something more intuitive: Radio images, Radio buttons, Color, etc.', 'woopanel' )
            ),
            'geo-location' => array(
                'label' => 'Geo Location',
                'class' => 'Geo_Location',
                'file'  => 'geo-location.php',
                'description' => esc_html__('Change default select box of attributes to something more intuitive: Radio images, Radio buttons, Color, etc.', 'woopanel' )
            ),
            'customize' => array(
                'label' => 'Customize',
                'class' => 'Customize',
                'file'  => 'customize.php',
                'description' => esc_html__('Module for Customize', 'woopanel' )
            ),
            'printcart-app' => array(
                'label' => 'Printcart Apps',
                'class' => 'Printcart_App',
                'file'  => 'printcart-app.php',
                'description' => esc_html__('Module for Printcart Apps', 'woopanel')
            ),
            'loading-effect' => array(
                'label' => 'Loading Effect',
                'class' => 'Loading_Effect',
                'file'  => 'loading-effect.php',
                'description' => esc_html__('Module for loading-effect', 'woopanel' )
            ),
            'store-locator' => array(
                'label' => 'Store Locator',
                'class' => 'Store_Locator',
                'file'  => 'store-locator.php',
                'description' => esc_html__('Module for store-locator', 'woopanel' )
            ),
            'vendor-profile' => array(
                'label' => 'Vendor Profile',
                'class' => 'Vendor_Profile',
                'file'  => 'vendor-profile.php',
                'description' => esc_html__('Module for store-locator', 'woopanel' )
            ),
            'smart-invoice' => array(
                'label' => 'Smart Invoice',
                'class' => 'Smart_Invoice',
                'file'  => 'smart-invoice.php',
                'description' => esc_html__('Module for smart-invoice', 'woopanel' ),
                'plugin_class' => 'NBCreatePDF'
            ),
            'tera-wallet' => array(
                'label' => 'Tera Wallet',
                'class' => 'Tera_Wallet',
                'file'  => 'tera-wallet.php',
                'description' => esc_html__('Module for Tera Wallet', 'woopanel' ),
                'plugin_class' => 'WooWallet'
            ),
            'awesome-support' => array(
                'label' => 'Awesome Support',
                'class' => 'Awesome_Support',
                'file'  => 'awesome-support.php',
                'description' => esc_html__('Module for Tera Wallet', 'woopanel' ),
                'plugin_class' => 'Awesome_Support'
            ),
        ));

        if( is_array($this->package) && in_array('price-matrix', $this->package) ) {
            unset( $this->modules['price-matrix'] );
        }

        if( is_array($this->package) && in_array('color-swatches', $this->package) ) {
            unset( $this->modules['color-swatches'] );
        }

        $this->admin_page = isset($_GET['page']) ? esc_attr($_GET['page']) : '';

        if( is_admin() && $this->admin_page = 'woopanel-settings' ) {
            /**
             * Detect plugin. For use on Front End only.
             */
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
             
            // check for plugin using plugin name
            if ( ! is_plugin_active( 'dokan-lite/dokan.php' ) ) {
                unset( $this->modules['dokan'] );
                unset( $this->modules['dokan-review'] );
            }

            // check for plugin using plugin name
            if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
                unset( $this->modules['price-matrix'] );
                unset( $this->modules['color-swatches'] );
            }

            // check for plugin using plugin name
            if ( ! is_plugin_active( 'web-to-print-online-designer/nbdesigner.php' ) ) {
                unset( $this->modules['online-desginer'] );
            }
            
            if ( ! is_plugin_active( 'netbase-smart-invoice/netbase-smart-invoice.php' ) ) {
                unset( $this->modules['smart-invoice'] );
            }

            if ( ! is_plugin_active( 'woo-wallet/woo-wallet.php' ) ) {
                unset( $this->modules['tera-wallet'] );
            }
       
            if ( ! is_plugin_active( 'awesome-support/awesome-support.php' ) ) {
                unset( $this->modules['awesome-support'] );
            }  
        }



        $this->load_modules();


        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'frontend_embed_assets') );

    }

    /**
     * Includes file modules
     */
    public function load_modules() {
        global $admin_options;

        if( empty($admin_options->options['enable_modules']) ) {
            return;
        }

        $woopanel_modules = $active_modules = array();
        foreach ($admin_options->options['enable_modules'] as $module_key) {
            if( isset($this->modules[$module_key]) ) {
                $woopanel_modules[$module_key] = $this->modules[$module_key];
                    $active_modules[$module_key] = $this->modules[$module_key]['label'];
            }

        }


        if( ! empty($woopanel_modules) ) {
            $this->active_modules = $active_modules;

            if( isset($_GET['id']) ) {
                $GLOBALS['_post'] = get_post($_GET['id']);
            }


            foreach( $woopanel_modules as $module_name => $module) {

                if ( ! isset($module['plugin_class']) || isset($module['plugin_class']) && class_exists($module['plugin_class']) ) {


                    $_module_name = str_replace('-', '_', $module_name);
                    $load_module = WOODASHBOARD_INC_DIR . 'modules/'. esc_attr($module_name) .'/'. esc_attr($module['file']);
            
                    $load_setting = WOODASHBOARD_INC_DIR . 'modules/'. esc_attr($module_name) .'/inc/settings.php';
            
            
                    if( file_exists($load_setting) ) {
            
                        include_once $load_setting;
            
                        $settings = get_option($module_name . '_settings');


            
                        if( ! $settings && class_exists('WooPanel_' . esc_attr($module['class']) . '_Settings') && method_exists('WooPanel_' . esc_attr($module['class']) . '_Settings', 'settings') ) {
                            $module_setting = call_user_func('WooPanel_' . esc_attr($module['class']) . '_Settings::settings');

                            if( is_array($module_setting) ) {
                                $settings = array();
                                foreach( $module_setting as $key => $set) {
                                    if( isset($set['id']) ) {
                                        $settings[$set['id']] = esc_attr($set['default']);
                                    }
                                }
                            }
                        }
            
                        $GLOBALS[$_module_name . '_settings'] = $settings;
                    }

                    if( file_exists($load_module) ) {
                        include_once $load_module;
                    }
                }
            }
        }
    }

    /**
     * Check meta_key field exists
     *
     * @param  string $meta_key
     * @return user_id
     */
    public static function check_user_meta( $user_id, $meta_key, $select = 'user_id' ) {
        global $wpdb;

        $sql = $wpdb->prepare( "SELECT ". esc_sql($select) ." FROM $wpdb->usermeta WHERE meta_key = %s AND user_id = %d", $meta_key, $user_id);

        $rs = $wpdb->get_var($sql);

        return $rs;
    }


    /**
     * Enqueue styles.
     */
    public static function frontend_embed_assets() {
        global $current_user, $post, $wp_query;

        if( isset($wp_query->query['product']) && ! empty($post) ) {
            $location_tab = get_user_meta($post->post_author, 'user_location_tab', true);
        }

        if( empty($location_tab) ) {
            $location_tab = get_option('user_location_tab');
        }

        $localize_array = array(
            'site_url' => WOODASHBOARD_URL,
            'ajax_url' => admin_url('admin-ajax.php'),
            'geoLocation' => array(
                'ApplicationID' => get_user_meta($current_user->ID, 'geo_application_id', true),
                'ApplicationCode' => get_user_meta($current_user->ID, 'geo_application_code', true),
                'product_tab' => $location_tab,
                'show_location_shop' => get_option('show_location_shop'),
                'show_location_storelist' => get_option('show_location_storelist'),
            )
        );

        wp_enqueue_style( 'woopanel-modules', WOODASHBOARD_URL . 'assets/css/modules.css',false,'1.1','all');
        wp_enqueue_script( 'woopanel-modules', WOODASHBOARD_URL . 'assets/js/frontend-modules.js', array( 'jquery' ) );

        $localize_array = apply_filters('woopanel_modules_localize', $localize_array);
        wp_localize_script( 'woopanel-modules', 'wplModules', $localize_array);
    }
}

/**
 * Returns global variable of $modules.
 *
 * @since  1.0.0
 * @return WooPanel_Modules
 */
$woo_modules = new WooPanel_Modules();

$GLOBALS['woopanel_modules'] = $woo_modules->modules;
$GLOBALS['active_modules'] = $woo_modules->active_modules;

