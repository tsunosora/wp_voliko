<?php
/**
 * @version    1.0
 * @package    Package Name
 * @author     Your Team <support@yourdomain.com>
 * @copyright  Copyright (C) 2014 yourdomain.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

/**
 * Plug additional sidebars into WordPress.
 *
 * @package  Package Name
 * @since    1.0
 */
class NBT_Solutions_Modules
{
    /**
     * Variable to hold the initialization state.
     *
     * @var  boolean
     */
    protected static $initialized = false;

    public static $settings = array();

    public static $modules_settings = array();

    protected static $package = '';

    // protected $modules = '';

    /**
     * Initialize functions.
     *
     * @return  void
     */
    public static function initialize()
    {
        // Do nothing if pluggable functions already initialized.
        if (self::$initialized) {
            return;
        }

        // Register actions to do something.
        add_action('init', array(__CLASS__, 'load_modules'));
        add_action( 'admin_notices', array(__CLASS__, 'admin_notice_compile_modules') );

        // State that initialization completed.
        self::$initialized = true;

        self::$package = wp_get_theme(get_template())->get('Tags');
    }

    public static function admin_notice_compile_modules() {
        if( is_multisite() ) {
            $upload_dir   = wp_upload_dir();
            $link_css = $upload_dir['basedir'] . '/frontend.css';
            $link_js = $upload_dir['basedir'] . '/frontend.js';
        }else {
            $link_css = PREFIX_NBT_SOL_PATH . 'assets/frontend/css/frontend.css';
            $link_js = PREFIX_NBT_SOL_PATH . 'assets/frontend/js/frontend.js';
        }

        $isVaild = true;
        if( ! file_exists($link_js) || ! file_exists($link_css) ) {
            $isVaild = false;
        }else {
            $filesize_js = filesize($link_js);
            $filesize_css = filesize($link_css);

            if( empty($filesize_js) || empty($filesize_css) ) {
                $isVaild = false;
            }
        }

        if( empty($isVaild) ) {
            $class = 'notice notice-error';
            $message = '<p><strong>NETBASE SOLUTION NOTICE</strong></p><p>Not find JS, CSS file of plugin Netbase Solutions, please visit <a href="'. admin_url() .'admin.php?page=solution-dashboard">here</a> or click on right admin menu <strong>NBT Solutions</strong> > <strong>Solution Dashboard</strong> and save to compile.</p>';
            printf( '<div class="%1$s">%2$s</div>', esc_attr( $class ), $message ); 
        }
    }

    public static function register_modules()
    {
        $advanced = array(
            'ajax-cart' => array(
                'name' => 'ajax-cart',
                'label' => 'Ajax Cart',
                'class' => 'Ajax_Cart',
                'description' => __('Change the default behavior of Add To Cart buttons of WooCommerce and improve user experience when purchasing products.', 'nbt-solution')
            ),
            'ajax-search' => array(
                'name' => 'ajax-search',
                'label' => 'Ajax Search',
                'class' => 'Ajax_Search',
                'description' => __('Improve user experience by make customers find his/her products more easily.', 'nbt-solution')
            ),
            'wc-currency-switcher' => array(
                'name' => 'wc-currency-switcher',
                'label' => 'Currency Switcher',
                'class' => 'Wc_Currency_Switcher',
                'description' => __('Make your site use multiple currencies at the same time.', 'nbt-solution')
            ),
            'color-swatches' => array(
                'name' => 'color-swatches',
                'label' => 'Color Swatches',
                'class' => 'Color_Swatches',
                'description' => __('Change default select box of attributes to something more intuitive: Radio images, Radio buttons, Color, etc.', 'nbt-solution')
            ),
            'order-delivery-date' => array(
                'name' => 'order-delivery-date',
                'label' => 'Order Delivery Date',
                'class' => 'Order_Delivery_Date',
                'description' => __('Order Delivery Date.', 'nbt-solution')
            ),
            'faqs' => array(
                'name' => 'faqs',
                'label' => 'FAQs',
                'class' => 'Faqs',
                'description' => __('Allow customers to ask question or seek answer about products before purchase', 'nbt-solution')
            ),   
            'frequently-bought-together' => array(
                'name' => 'frequently-bought-together',
                'label' => 'Frequently Bought Together',
                'class' => 'Frequently_Bought_Together',
                'description' => __('Allow customers easy enter price for variation product.', 'nbt-solution')
            ),
            'live-chat' => array(
                'name' => 'live-chat',
                'label' => 'Live Chat',
                'class' => 'Live_Chat',
                'description' => __('Embeds Tawk.to live chat widget to your site.', 'nbt-solution')
            ),
            'one-step-checkout' => array(
                'name' => 'one-step-checkout',
                'label' => 'One Step Checkout',
                'class' => 'One_Step_Checkout',
                'description' => __('Reduce the checkout process by offering the entire purchase process on a single page', 'nbt-solution')
            ),
            'pdf-creator' => array(
                'name' => 'pdf-creator',
                'label' => 'PDF Creator',
                'class' => 'PDF_Creator',
                'description' => __('Allow customers print their orders in beatiful PDF format', 'nbt-solution')
            ),
            'product-notification' => array(
                'name' => 'product-notification',
                'label' => 'Product Notification',
                'class' => 'Product_Notification',
                'description' => __('Customers can register so they will be notified if your products are in stock or not', 'nbt-solution')
            ),
            'social-login' => array(
                'name' => 'social-login',
                'label' => 'Social Login',
                'class' => 'Social_Login',
                'description' => __('Social Login.', 'nbt-solution')
            ),
            'brands' => array(
                'name' => 'brands',
                'label' => 'Shop by Brands',
                'class' => 'Brands',
                'description' => __('Assign products to a brand and let customers filter products by those brands.', 'nbt-solution')
            ),
            'order-delivery-note' => array(
                'name' => 'order-delivery-note',
                'label' => 'Order Delivery Note',
                'class' => 'Order_Delivery_Note',
                'description' => ''
            ),
            'smtp' => array(
                'name' => 'smtp',
                'label' => 'SMTP',
                'class' => 'SMTP',
                'hide' => true,
                'description' => '',
            ),
            'metabox' => array(
                'name' => 'metabox',
                'label' => 'Metabox',
                'class' => 'Metabox',
                'hide' => true,
                'description' => ''
            ),
        );

        $premium = array(
            'price-matrix' => array(
                'name' => 'price-matrix',
                'label' => 'Price Matrix',
                'class' => 'Price_Matrix',
                'description' => __('Allow customers easy enter price for variation product.', 'nbt-solution')
            ),
            'order-upload' => array(
                'name' => 'order-upload',
                'label' => 'Order Upload',
                'class' => 'Order_Upload',
                'description' => __('Function for your customers now can upload file(s) and attach it to the order.', 'nbt-solution')
            ),

        );
        
        $enterprise = array();


        $modules = array();
        if(get_option( 'template' ) == 'printcart') {
            unset($advanced['ajax-search']);
        }
        
        /* For CMSMart */
        if( is_array(self::$package) && in_array('nb-advanced', self::$package) ) {
            $modules = $advanced;
        }
        
        if( is_array(self::$package) && in_array('nb-premium', self::$package) ) {
            $modules = array_merge($advanced, $premium);
        }
        
        if( is_array(self::$package) && in_array('nb-enterprise', self::$package) ) {
            $modules = array_merge($advanced, $premium, $enterprise);
        }
        
        /* For ThemeForest */
        if( ! is_array(self::$package) ||! in_array('nb-advanced', self::$package) && ! in_array('nb-premium', self::$package) && ! in_array('nb-enterprise', self::$package) ) {
            unset($advanced['one-step-checkout']);
            unset($advanced['live-chat']);
            unset($advanced['pdf-creator']);
            unset($advanced['social-login']);
            unset($advanced['faqs']);
            // unset($advanced['ajax-cart']);
            unset($advanced['order-delivery-date']);
            unset($advanced['frequently-bought-together']);
            unset($advanced['order-delivery-note']);
            unset($advanced['product-notification']);
            
            $modules = $advanced;
        }

        if(class_exists('NBWooCommerceDashboard') ) {
            unset($modules['price-matrix']);
            unset($modules['color-swatches']);
            $settings_modules = !empty(get_option('solutions_core_settings')) ? get_option('solutions_core_settings') : array();
            foreach($settings_modules as $index => $setting) {
                if($setting === 'price-matrix' || $setting === 'color-swatches') {
                    unset($settings_modules[$index]);
                }
            }
            update_option('solutions_core_settings', $settings_modules);
        }

        return apply_filters('nb_solution_modules', $modules);
    }

    public static function load_modules()
    {
        $register_modules = self::register_modules();
        $settings_modules = get_option('solutions_core_settings');
        if ($settings_modules) {
            foreach ($settings_modules as $key => $modules) {

                if (isset($register_modules[$modules]['class'])) {
                    $_modules = $register_modules[$modules]['class'];

                    if (class_exists('NBT_Solutions_' . $_modules)) {
                        call_user_func('NBT_Solutions_' . $_modules . '::initialize');
                        /* Check settings modules */
                        if (isset($register_modules[$modules]['class']) && file_exists(PREFIX_NBT_SOL_PATH . 'core/modules/' . $modules . '/inc/settings.php')) {

                          
                            
                            self::$modules_settings[$modules] = array(
                                'class' => $_modules,
                                'label' => $register_modules[$modules]['label']
                            );
                        }

                    }
                }
            }


            if (!empty(self::$modules_settings) && count(self::$modules_settings) > 0) {
                add_action('admin_menu', array(__CLASS__, 'add_menu_settings'));
                foreach (self::$modules_settings as $modules => $_modules) {
                    include(PREFIX_NBT_SOL_PATH . 'core/modules/' . $modules . '/inc/settings.php');
                    self::$settings[$modules]['settings'] = call_user_func('NBT_' . $_modules['class'] . '_Settings::get_settings');
                    if (self::$settings[$modules]) {
                        $value_modules = get_option($modules . '_settings');

                        self::$settings[$modules]['module_name'] = $_modules['label'];
                        self::$settings[$modules]['slug'] = $modules;
                        foreach (self::$settings[$modules]['settings'] as $set => $val) {

                            if (isset($val['id']) && isset($value_modules[$val['id']])) {
                                self::$settings[$modules]['settings'][$set]['value'] = $value_modules[$val['id']];
                            }

                            if (isset($val['id']) && $val['type'] == 'repeater') {
                                self::$settings[$modules]['settings'][$set]['value'] = $value_modules[$val['id']];
                            }

                        }
                       
                    }
                }
            }
        }
    }

    public static function add_menu_settings()
    {
        add_submenu_page('solutions', 'NBT Solutions Settings', 'Settings', 'manage_options', 'solutions-settings', array(__CLASS__, 'page_solutions_settings'));
    }


    public static function page_solutions_settings()
    {       
        if ( ! empty(self::$settings) ) {
            include(PREFIX_NBT_SOL_PATH . 'core/modules/settings.php');
        }
    }
}