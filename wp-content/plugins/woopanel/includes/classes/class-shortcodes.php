<?php

/**
 * Tempalte shortcode class file
 *
 * @load all shortcode for template  rendering
 */
class WooDashboard_Shortcodes {

    private static $user_can = 'edit_posts';
    public static $shortcodes = array(
        'dashboard' => 'woopanel',
    );

    function __construct()
    {
        /**
         * Remove hook wp_head and wp_footer
         *
         * @since 1.0.0
         * @hook wp
         * @param null
         */
        add_action( 'wp', array($this, 'unhook_wp_head_footer') );

        /**
         * Add shortcode [woopanel]
         *
         * @since 1.0.0
         * @hook woopanel
         * @function load_template_files
         * @param null
         */
        add_shortcode( self::$shortcodes['dashboard'], array($this, 'load_template_files') );

        /**
         * Load main scripts and css of WooPanel
         *
         * @since 1.0.0
         * @hook wp_enqueue_scripts
         * @param null
         */
        add_action( 'wp_enqueue_scripts', array($this, 'woopanel_main_scripts'), 99, 1 );

        /**
         * Filters the parts of the document title.
         *
         * @since 1.0.0
         * @hook document_title_parts
         * @param {array} $title Title
         * @return {string} $title
         */
        add_filter( 'document_title_parts', array($this, 'woopanel_document_title'), 99, 1 );

        add_action( 'woopanel_head', array($this, 'load_critical_css') );

        add_action( 'wp_head', array($this, 'load_critical_css') );

    }

    function load_template_files()
    {
        global $admin_options;
        ob_start();
        if ( is_user_logged_in() ) {

            woopanel_get_template('layout.php');

        } else {
            if( is_woo_installed() ) {
                if( woopanel_get_layout() == 'fixed' ) {
                    $this->woopanel_fixed_login();
                }else {
                    $pageDashboard = get_post($admin_options->options['dashboard_page_id']);
                    $a = explode($pageDashboard->post_name, rtrim($_SERVER['REQUEST_URI'], '/') );

                    if( ! empty($a[1]) ) {
                        woopanel_redirect( home_url('?post_type=page&p=' . $pageDashboard->ID) );
                    }else {
                        $this->woopanel_login_form();
                    }
                }
            } else {
                woopanel_redirect( wp_login_url( woopanel_current_url() ) );
            }
        }
        return ob_get_clean();
    }

    public function woopanel_fixed_login() {
        echo '<div class="woopanel-login">';
            echo '<h2>' . esc_html__('Login', 'woopanel' ) .'</h2>';
            $this->woopanel_login_form();
        echo '</div>';
    }

    public function woopanel_login_form() {
        wp_enqueue_style('woopanel-login');

        $register = '';
        if( isset($_POST['register']) ) {
            $register = true;
        }

        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        include_once WOODASHBOARD_TEMPLATE_DIR . 'global/form-login.php';
    }

    function woopanel_main_scripts() {
        global $wp_scripts, $wp_styles, $post, $current_user;
        if( is_woopanel() ) {

            if( woopanel_get_layout() == 'fullwidth' ) {

                $wp_scripts->queue = array();
                $wp_styles->queue = array();

                add_filter('show_admin_bar', '__return_false');
            }

            remove_action('wp_head', '_admin_bar_bump_cb');

            wp_enqueue_style( 'dashicons' );
            wp_enqueue_style('scrollbar', WOODASHBOARD_URL .'vendors/perfect-scrollbar/css/perfect-scrollbar.css', false, '1.4.0', 'all' );
            wp_enqueue_style('bootstrap-select', WOODASHBOARD_URL .'vendors/bootstrap-select/dist/css/bootstrap-select.css', false, '1.13.0-beta', 'all' );
            wp_enqueue_style('metronic', WOODASHBOARD_URL .'vendors/metronic/css/styles.css', false, WooDashboard()->version, 'all' );

            wp_enqueue_style('flaticon', WOODASHBOARD_URL .'vendors/flaticon/css/flaticon.css', false, WooDashboard()->version, 'all' );
            wp_enqueue_style('line-awesome', WOODASHBOARD_URL .'vendors/line-awesome/css/line-awesome.css', false, '1.1.0', 'all' );
            wp_enqueue_style('fontawesome5', WOODASHBOARD_URL .'vendors/fontawesome5/css/all.min.css', false, '5.2.0', 'all' );
            wp_enqueue_style('toastr', WOODASHBOARD_URL .'vendors/toastr/toastr.css', false, WooDashboard()->version, 'all' );

            wp_enqueue_style('woopanel', WOODASHBOARD_URL . 'assets/css/style.css', false, WooDashboard()->version, 'all' );

            wp_enqueue_script('popper', WOODASHBOARD_URL .'vendors/popper.js/dist/umd/popper.js', array('jquery'), '1.14.4' , true );
            wp_enqueue_script('bootstrap', WOODASHBOARD_URL .'vendors/bootstrap/dist/js/bootstrap.min.js', array('jquery'), '4.1.3' , true );
            wp_enqueue_script('bootstrap-select', WOODASHBOARD_URL .'vendors/bootstrap-select/dist/js/bootstrap-select.js', array('jquery'), '1.13.0-beta' , true );
            wp_enqueue_script('cookie', WOODASHBOARD_URL .'vendors/js-cookie/src/js.cookie.js', array('jquery'), '2.2.0' , true );
            wp_enqueue_script('moment', WOODASHBOARD_URL .'vendors/moment/min/moment.min.js', false, WooDashboard()->version , true );
            wp_enqueue_script('tooltip', WOODASHBOARD_URL .'vendors/tooltip.js/dist/umd/tooltip.min.js', array('jquery'), '1.3.0' , true );
            wp_enqueue_script('scrollbar', WOODASHBOARD_URL .'vendors/perfect-scrollbar/dist/perfect-scrollbar.js', array('jquery'), '1.4.0' , true );
            wp_enqueue_script('wnumb', WOODASHBOARD_URL .'vendors/wnumb/wNumb.js', array('jquery'), WooDashboard()->version , true );

            wp_enqueue_script('metronic-scripts', WOODASHBOARD_URL .'vendors/metronic/scripts.bundle.js', array('jquery'), WooDashboard()->version , true );

            wp_enqueue_script('nb-tags-box', WOODASHBOARD_URL .'assets/js/nb-tags-box.js', array('jquery'), WooDashboard()->version , true );
            wp_enqueue_script('nb-media-uploader', WOODASHBOARD_URL .'assets/js/nb-media-uploader.js', array('jquery-ui-sortable'), WooDashboard()->version , true );


            add_filter('show_admin_bar', '__return_false');

            wp_enqueue_script('jquery-blockui', WOODASHBOARD_URL . 'vendors/blockUI/jquery.blockUI.js', array(), '2.70.0', true);
            wp_enqueue_script('toastr', WOODASHBOARD_URL .'vendors/toastr/toastr.min.js', array('jquery'), '2.1.4', true );

            wp_register_style( 'wpl-bootstrap-datepicker', WOODASHBOARD_URL . 'vendors/bootstrap-datepicker/bootstrap-datepicker3.min.css', WooDashboard()->version, 'all' );
            wp_register_script('wpl-bootstrap-datepicker', WOODASHBOARD_URL . 'vendors/bootstrap-datepicker/bootstrap-datepicker.min.js', array(), false, true);

            if ( is_woo_installed() ) {
                $price = str_replace('%2$s', 'number', get_woocommerce_price_format());
                $price = str_replace('%1$s', get_woocommerce_currency_symbol(), $price);
                $decimals = wc_get_price_decimals();
                $decimal_separator = wc_get_price_decimal_separator();
                $thousand_separator = wc_get_price_thousand_separator();
            }else {
                $price = '%1$s';
                $decimals = 2;
                $decimal_separator = '.';
                $thousand_separator = ',';
            }
            
            $extra = array(
                'format_money' => $price,
                'decimals' => $decimals,
                'decimal_separator' => $decimal_separator,
                'thousand_separator' => $thousand_separator
            );

            $myvars = array_merge(
                $extra,
                array( 
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'url' => woopanel_dashboard_url(),
                'json_url' => home_url() . '/wp-json/woopanel/v2/%action%',
                'label' => array(
                    'item' => esc_html__('Order', 'woopanel' ),
                    'items' => esc_html__('Orders', 'woopanel' ),
                    'i18n_deny'            => esc_js( esc_html__( 'You do not have permission for this action!', 'woopanel' ) ),
                    'i18n_image_title'               => esc_js( esc_html__( 'Image', 'woopanel' ) ),
                    'i18n_set_image'               => esc_js( esc_html__( 'Set Image', 'woopanel' ) ),
                    'i18n_featured_image'               => esc_js( esc_html__( 'Featured Image', 'woopanel' ) ),
                    'i18n_set_featured_image'               => esc_js( esc_html__( 'Set featured image', 'woopanel' ) ),
                    'i18n_featured_image'               => esc_js( esc_html__( 'Featured Image', 'woopanel' ) ),
                    'i18n_add_gallery'               => esc_js( esc_html__( 'Add to gallery', 'woopanel' ) ),
                ),
            ));

            //Dashboard
            if( is_woopanel_endpoint_url('dashboard') ) {
                wp_enqueue_style( 'admin-daterangepicker', WOODASHBOARD_URL . 'vendors/daterangepicker/daterangepicker.css', false, '3.0.3', 'all' );
                wp_enqueue_script('moment.daterangepicker', WOODASHBOARD_URL . 'vendors/daterangepicker/moment.min.js', array(), false, true);
                wp_enqueue_script('daterangepicker', WOODASHBOARD_URL . 'vendors/daterangepicker/daterangepicker.js', array(), '3.0.3', true);
                wp_enqueue_script('Chart', WOODASHBOARD_URL . 'assets/js/chart.js', array(), '2.7.2', true);

                if ( is_woo_installed() ) {
                    wp_enqueue_script('dashboard-woocommerce', WOODASHBOARD_URL . 'assets/js/dashboard-woocommerce.js', array(), WooDashboard()->version, true);
                }

                wp_enqueue_script('jquery-ui-sortable', home_url() . 'wp-includes/js/jquery/ui/sortable.min.js', array(), '1.11.4', true);
            }

            // Order
            if( is_woopanel_endpoint_url('orders') || is_woopanel_endpoint_url('order') ) {
                wp_enqueue_style('select2', WOODASHBOARD_URL .'vendors/select2/select2.min.css', false, '4.0.3','all');
                wp_enqueue_style('select2-bootstrap', WOODASHBOARD_URL .'vendors/select2/select2-bootstrap.min.css', false, '0.1.0-beta.4', 'all');

                wp_enqueue_script('select2', WOODASHBOARD_URL . 'vendors/select2/select2.full.min.js', array(), '4.0.3', true );

                wp_enqueue_script( 'wc-backbone-modal', WC()->plugin_url() . '/assets/js/admin/backbone-modal.min.js', array( 'underscore', 'backbone', 'wp-util' ), WC_VERSION );
                wp_enqueue_script('wpel-orders', WOODASHBOARD_URL . 'assets/js/orders.js', array(), false, true);
                wp_localize_script(
                    'wpel-orders',
                    'wpel_orders_params',
                    array(
                        'ajax_url'      => admin_url( 'admin-ajax.php' ),
                        'preview_nonce' => wp_create_nonce( 'woocommerce-preview-order' ),
                        'search_customers_nonce'    => wp_create_nonce( 'search-customers' ),
                        'add_order_note_nonce'          => wp_create_nonce( 'add-order-note' ),
                        'delete_order_note_nonce'       => wp_create_nonce( 'delete-order-note' ),
                        'i18n_delete_note'              => esc_html__( 'Are you sure you wish to delete this note? This action cannot be undone.', 'woopanel' ),
                        'i18n_add_note'              => esc_html__( 'Please enter your note!', 'woopanel' ),
                    )
                );
            }


            if( is_woopanel_endpoint_url('coupon') ) {
                wp_enqueue_style('wpl-bootstrap-datepicker');
                wp_enqueue_script('wpl-bootstrap-datepicker');
            }

            if( is_woopanel_endpoint_url('profile') ) {
                wp_enqueue_style('wpl-bootstrap-datepicker');
                wp_enqueue_script('wpl-bootstrap-datepicker');
            }

            if( is_woopanel_endpoint_url('article') || is_woopanel_endpoint_url('product') ) {
                wp_enqueue_script('posts', WOODASHBOARD_URL . 'assets/js/posts.js', array(), WooDashboard()->version, true );
            }
            if( is_woopanel_endpoint_url('settings') || is_woopanel_endpoint_url('product-categories') || is_woopanel_endpoint_url('store-category') ) {
                wp_enqueue_media();
                wp_enqueue_style( 'jquery.timepicker', WOODASHBOARD_URL . 'vendors/timepicker/jquery.timepicker.min.css', false, '3.0.3', 'all' );
                wp_enqueue_script('jquery.timepicker', WOODASHBOARD_URL . 'vendors/timepicker/jquery.timepicker.min.js', array(), WooDashboard()->version, true );
                
                wp_enqueue_style( 'jquery.magnific-popup', WOODASHBOARD_URL . 'includes/modules/dokan/assets/css/magnific-popup.css', false, '3.0.3', 'all' );
                wp_enqueue_script('jquery.magnific-popup', WOODASHBOARD_URL . 'includes/modules/dokan/assets/js/jquery.magnific-popup.min.js', array(), WooDashboard()->version, true );
                
                wp_enqueue_script('settings', WOODASHBOARD_URL . 'assets/js/settings.js', array(), WooDashboard()->version, true );
            }

            if( is_woopanel_endpoint_url('product-attributes') ) {
                do_action('woopanel_product_attribute_enqueue_scripts');
            }

            if( is_woopanel_endpoint_url('product') ) {
                wp_enqueue_style('wpl-bootstrap-datepicker');
                wp_enqueue_script('wpl-bootstrap-datepicker');
                wp_enqueue_script('jquery-ui-sortable');


                wp_enqueue_style('select2', WOODASHBOARD_URL .'vendors/select2/select2.min.css', false, '4.0.3','all');
                wp_enqueue_style('select2-bootstrap', WOODASHBOARD_URL .'vendors/select2/select2-bootstrap.min.css', false, '0.1.0-beta.4', 'all');

                wp_enqueue_script('select2', WOODASHBOARD_URL . 'vendors/select2/select2.full.min.js', array(), '4.0.3', true );

                wp_enqueue_script('woopanel-serializejson', WOODASHBOARD_URL . 'vendors/serializeJSON/jquery.serializejson.min.js', array(), '2.9.0', true );
                wp_enqueue_script('woopanel-meta-boxes-product', WOODASHBOARD_URL . 'assets/js/meta-boxes-product.js', array(), WooDashboard()->version, true );

                wp_enqueue_script('woopanel-meta-boxes-product', WOODASHBOARD_URL . 'assets/js/meta-boxes-product.js', array(), WooDashboard()->version, true );
                $myvars['product'] = array(
                    'product_types' => array_unique( array_merge( array( 'simple', 'grouped', 'variable', 'external' ), array_keys( wc_get_product_types() ) ) ),
                    'search_products_nonce'     => wp_create_nonce( 'search-products' ),
                    'load_variations_nonce'     => wp_create_nonce( 'load-variations' ),
                    'add_attribute_nonce'           => wp_create_nonce( 'add-attribute' ),
                    'save_attributes_nonce'         => wp_create_nonce( 'save-attributes' ),
                    'save_variations_nonce'               => wp_create_nonce( 'save-variations' ),
                    'add_variation_nonce'                 => wp_create_nonce( 'add-variation' ),
                    'link_variation_nonce'                => wp_create_nonce( 'link-variations' ),
                    'delete_variations_nonce'             => wp_create_nonce( 'delete-variations' ),
                    'search_customers_nonce'    => wp_create_nonce( 'search-customers' ),
                    'input_price_nonce'       => wp_create_nonce( 'input-price' ),
                    'save_price_nonce'        => wp_create_nonce( 'save-price' ),

                    'post_id'                   => isset( $_GET['id'] ) ? $_GET['id'] : '',
                    'label_remove_attribute'    => esc_html__('Remove this attribute?', 'woopanel' ),
                    'variations_per_page'                 => absint( apply_filters( 'woocommerce_admin_meta_boxes_variations_per_page', 15 ) ),
                    'i18n_delete_all_variations'          => esc_js( esc_html__( 'Are you sure you want to delete all variations? This cannot be undone.', 'woopanel' ) ),
                    'i18n_last_warning'                   => esc_js( esc_html__( 'Last warning, are you sure?', 'woopanel' ) ),
                    'i18n_variation_count_single'         => esc_js( esc_html__( '%qty% variation', 'woopanel' ) ),
                    'i18n_variation_count_plural'         => esc_js( esc_html__( '%qty% variations', 'woopanel' ) ),
                    'bulk_edit_variations_nonce'          => wp_create_nonce( 'bulk-edit-variations' ),
                    'i18n_edited_variations'              => esc_js( esc_html__( 'Save changes before changing page?', 'woopanel' ) ),
                    'i18n_link_all_variations'            => esc_js( sprintf( esc_html__( 'Are you sure you want to link all variations? This will create a new variation for each and every possible combination of variation attributes (max %d per run).', 'woopanel' ), defined( 'WC_MAX_LINKED_VARIATIONS' ) ? WC_MAX_LINKED_VARIATIONS : 50 ) ),
                    'i18n_variation_added'                => esc_js( esc_html__( 'variation added', 'woopanel' ) ),
                    'i18n_variations_added'               => esc_js( esc_html__( 'variations added', 'woopanel' ) ),
                    'i18n_no_variations_added'            => esc_js( esc_html__( 'No variations added', 'woopanel' ) ),
                    'i18n_first_page'            => esc_js( esc_html__( 'This is first page', 'woopanel' ) ),
                    'i18n_last_page'            => esc_js( esc_html__( 'This is last page', 'woopanel' ) ),
                    'i18n_save_attribute'            => esc_js( esc_html__( 'Save attribute', 'woopanel' ) ),
                    'i18n_update'            => esc_js( esc_html__( 'Update', 'woopanel' ) ),
                    'i18n_remove_variation'               => esc_js( esc_html__( 'Are you sure you want to remove this variation?', 'woopanel' ) ),
                    'variations_per_page'                 => absint( apply_filters( 'woocommerce_admin_meta_boxes_variations_per_page', 15 ) ),
                );

                do_action('woopanel_product_enqueue_scripts');
            }

            if( is_woopanel_endpoint_url('faq') || is_woopanel_endpoint_url('product') ) {
                wp_enqueue_editor();
                wp_enqueue_media();
                wp_enqueue_script('faqs', WOODASHBOARD_URL . 'admin/assets/js/faqs.js',  array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-resizable'), '4.0.3', true );
            }

            if ( is_woo_installed() ) {
                wp_enqueue_script('jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI.min.js', array('jquery'), '2.70', true);
            }

            wp_register_style( 'woopanel-login', WOODASHBOARD_URL . 'assets/css/login.min.css', false, '3.0.3', 'all' );
            
            /**
             * Enqueue a script.
             *
             * @since 1.0.0
             * @hook woopanel_enqueue_scripts
             * @param null
             * @return void
             */
            do_action('woopanel_enqueue_scripts');

            if( woopanel_get_layout() == 'fullwidth' ) {
                wp_enqueue_script('navigation', WOODASHBOARD_URL .'assets/js/navigation.js', array('jquery'), WooDashboard()->version, true );
            }
            
            wp_enqueue_script('woopanel', WOODASHBOARD_URL .'assets/js/main.js', array('jquery'), WooDashboard()->version, true );


            $myvars['modules'] = array(
                'geoApplicationID' => get_user_meta($current_user->ID, 'geo_application_id', true),
                'geoApplicationCode' => get_user_meta($current_user->ID, 'geo_application_code', true),
            );


            /**
             * Localizes a registered script with data for a JavaScript variable.
             *
             * @since 1.1.0
             * @hook woopanel_localize_script
             * @param {array} $myvars
             * @returns {array} $myvars Variable
             */
            $myvars = apply_filters( 'woopanel_localize_script', $myvars );

            wp_localize_script( 'jquery', 'WooPanel', $myvars );
        }
    }

    function unhook_wp_head_footer(){
        global $wp_filter;

        $args_head_action = array(
            '_wp_render_title_tag',
            'wp_enqueue_scripts',
            'wp_resource_hints',
            'feed_links',
            'feed_links_extra',
            'rsd_link',
            'wlwmanifest_link',
            'adjacent_posts_rel_link_wp_head',
            'locale_stylesheet',
            'noindex',
            'print_emoji_detection_script',
            'wp_print_styles',
            'wp_print_head_scripts',
            'wp_generator',
            'rel_canonical',
            'wp_shortlink_wp_head',
            'wp_custom_css_cb',
            'wp_site_icon'
        );

        $args_fooder_action = array(
            'wp_print_footer_scripts',
            'wp_admin_bar_render'
        );

        if( is_woopanel() && woopanel_get_layout() == 'fullwidth' ) {
            $accept_scripts = array(
                'woopanel_main_scripts',
                'frontend_embed_assets'
            );

            $woopanel_callbacks = array();
            foreach ($wp_filter['wp_enqueue_scripts']->callbacks as $priority => $enqueue_script_hooks) {
                if( ! empty( $enqueue_script_hooks ) ) {
                    foreach( $enqueue_script_hooks  as $hook_k => $enqueue_script_hook ) {
                        if( is_array($enqueue_script_hook['function']) && in_array($enqueue_script_hook['function'][1], $accept_scripts) ) {
                            $woopanel_callbacks[$priority][$hook_k] = $enqueue_script_hook;
                        }
                    }
                }
            }

            $wp_filter['wp_enqueue_scripts']->callbacks = $woopanel_callbacks;
   
            foreach ( $wp_filter['wp_head'] as $priority => $wp_head_hooks ) {
                if( is_array( $wp_head_hooks ) ){
                    foreach ( $wp_head_hooks as $wp_head_hook ) {
                        if( in_array( $wp_head_hook['function'], $args_head_action) ) continue;
                        remove_action( 'wp_head', $wp_head_hook['function'], $priority );
                    }
                }
            }
            foreach ($wp_filter['wp_footer'] as $priority => $wp_footer_hooks ) {
                if( is_array( $wp_footer_hooks ) ){
                    foreach ( $wp_footer_hooks as $wp_footer_hook ) {
                        if( in_array( $wp_footer_hook['function'], $args_fooder_action) ) continue;
                        remove_action( 'wp_footer', $wp_footer_hook['function'], $priority );
                    }
                }
            }
        }
    }

    function woopanel_document_title( $title ){
        if ( is_woopanel() ) {
            if (is_woopanel_endpoint_url('dashboard')) $title['title'] = esc_html__( 'Dashboard', 'woopanel' );

            if (is_woopanel_endpoint_url('articles')) $title['title'] = esc_html__('Articles', 'woopanel' );
            if (is_woopanel_endpoint_url('article')) {
                $title['title'] = isset($_GET['id']) ? sprintf( esc_html__( 'Edit article %s', 'woopanel' ), '#'.esc_attr($_GET['id']) ) : esc_html__('Add new article', 'woopanel' );
            }

            if (is_woopanel_endpoint_url('products')) $title['title'] = esc_html__('Products', 'woopanel' );
            if (is_woopanel_endpoint_url('product')) {
                $title['title'] = isset($_GET['id']) ? sprintf( esc_html__( 'Edit product', 'woopanel' ), '#'.esc_attr($_GET['id']) ) : esc_html__('Add new product', 'woopanel' );
            }

            if (is_woopanel_endpoint_url('orders')) $title['title'] = esc_html__('Orders', 'woopanel' );
            if (is_woopanel_endpoint_url('order')) {
                $title['title'] = sprintf( esc_html__( 'View order: %s', 'woopanel' ), '#'.esc_attr($_GET['id']) );
            }

            if (is_woopanel_endpoint_url('coupons')) $title['title'] = esc_html__('Coupons', 'woopanel' );
            if (is_woopanel_endpoint_url('coupon')) {
                $title['title'] = isset($_GET['id']) ? sprintf( esc_html__( 'Edit coupon %s', 'woopanel' ), '#'.esc_attr($_GET['id']) ) : esc_html__('Add new coupon', 'woopanel' );
            }

            if (is_woopanel_endpoint_url('customers')) $title['title'] = esc_html__('Customers', 'woopanel' );
            if (is_woopanel_endpoint_url('customer')) {
                $title['title'] = esc_html__('View Customer', 'woopanel' );
            }

            if (is_woopanel_endpoint_url('comments')) $title['title'] = esc_html__( 'Comments', 'woopanel' );
            if (is_woopanel_endpoint_url('comment')) $title['title'] = esc_html__( 'Edit Comment', 'woopanel' );

            if (is_woopanel_endpoint_url('reviews')) $title['title'] = esc_html__( 'Reviews', 'woopanel' );
            if (is_woopanel_endpoint_url('review')) $title['title'] = esc_html__( 'Edit Review', 'woopanel' );

            if (is_woopanel_endpoint_url('profile')) $title['title'] = esc_html__( 'My Profile', 'woopanel' );

            if (is_woopanel_endpoint_url('settings')) $title['title'] = esc_html__( 'Settings', 'woopanel' );

            $title['site'] = esc_html__('WooCommerce Dashboard', 'woopanel' );

            if( WooPanel_Admin_Options::get_option( 'customize_dashboard' ) == 'yes' &&
                woopanel_get_option( 'shop_name' ) ){
                $title['site'] = woopanel_get_option( 'shop_name' );
            }

        }
        
        return $title;
    }

    public function load_critical_css() {
        global $wp_query;

        if( isset($wp_query->query_vars['pagename']) && $wp_query->query_vars['pagename'] == woopanel_dashboard_pagename() ) {
            $query_vars = WooDashboard()->query->query_vars;
            $query_vars = array_merge( $query_vars, array_keys($wp_query->query) );

            $new_dashboard = '';
            foreach ($query_vars as $key => $var) {
                if( isset($wp_query->query[$var]) || isset ( $wp_query->query['page'] ) && $var == 'dashboard' ) {

                    if( ! is_user_logged_in() && $var == 'dashboard' ) {
                        $var = 'login';
                    }

                    if( $var != 'page' ) {
                        $new_dashboard = $var;
                    }
                }
            }

            if( ! empty($new_dashboard) ) {
                $link_critical = sprintf(
                    '%s.min.css',
                    $new_dashboard
                );

                $path_critical = apply_filters('woopanel_critical_path', WOODASHBOARD_DIR . 'themes/default/critical', 'path', $new_dashboard);
                $url_critical = apply_filters('woopanel_critical_url', WOODASHBOARD_URL . 'themes/default/critical', 'url', $new_dashboard);
                
                if( file_exists( $path_critical . '/' . $link_critical ) ) {
                    $myfile = fopen($path_critical . '/' . $link_critical, "r") or die("Unable to open file!");
                    $contentCSS = fread($myfile,filesize($path_critical . '/' . $link_critical));
                    if( ! empty($contentCSS) ) {
                        printf('<style type="text/css">%s</style>', $contentCSS);
                    }

                    fclose($myfile);
                }


                if( is_user_logged_in() ) {
                    printf( "\n" . '<link rel="preload stylesheet" href="%1$s" as="style">' ."\n" .'<noscript><link rel="stylesheet" href="%1$s"></noscript>' ."\n", WOODASHBOARD_URL . 'themes/default/main.min.css' );
                }

            }
        }
    }
}