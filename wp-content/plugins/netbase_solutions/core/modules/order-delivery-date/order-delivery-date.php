<?php 
if( !defined( 'NBTODD_JS_DIR' ) ) {
    define( 'NBTODD_JS_DIR', plugin_dir_url( __FILE__ ) . 'js' );
}

if( !defined( 'NBTODD_CSS_DIR' ) ) {
    define( 'NBTODD_CSS_DIR', plugin_dir_url( __FILE__ ) . 'css' );
}
define('NBTODD_PATH', plugin_dir_path(__FILE__));

if( !defined( 'NBTODD_SETTINGS' ) ) {
    define( 'NBTODD_SETTINGS', 'order-delivery-date_settings' );
}

$wpefield_version = '3.6';

/**
 * Include the require files
 * @since 1.0
 */
include_once NBTODD_PATH . 'integration.php';
include_once NBTODD_PATH . 'nbtodd-config.php';

include_once NBTODD_PATH . 'nbtodd-process.php';
include_once NBTODD_PATH . 'filter.php';
include_once NBTODD_PATH . 'nbtodd-privacy.php';

class NBT_Solutions_Order_Delivery_Date {
    /**
     * Variable to hold the initialization state.
     *
     * @var  boolean
     */
    protected static $initialized = false;
    public static function initialize()
    {
        if (self::$initialized) {
            return;
        }        
        $options = get_option( NBTODD_SETTINGS );
        
        add_action( 'admin_init', array(__CLASS__, 'nbtodd_capabilities') );        

            //Admin scripts
            //add_action( 'admin_enqueue_scripts', array(__CLASS__, 'nbtodd_my_enqueue') );
            add_action( 'admin_print_scripts', array(__CLASS__, 'nbtodd_admin_enqueue') );

            //Frontend
            add_action( NBTODD_SHOPPING_CART_HOOK, array( 'nbtodd_process', 'nbtodd_my_custom_checkout_field' ) );
            add_action( NBTODD_SHOPPING_CART_HOOK, array(__CLASS__, 'nbtodd_front_scripts_js'));

            if( '1' == $options['nbt_order-delivery-date_cart_page'] ) {
                add_action( 'woocommerce_cart_collaterals', array( 'nbtodd_process', 'nbtodd_my_custom_checkout_field' ) );
                add_action( 'woocommerce_cart_collaterals', array(__CLASS__, 'nbtodd_front_scripts_js') );
            }

            add_action( 'woocommerce_checkout_update_order_meta', array( 'nbtodd_process', 'nbtodd_my_custom_checkout_field_update_order_meta' ) );
           
            if ( defined( 'WOOCOMMERCE_VERSION' ) && version_compare( WOOCOMMERCE_VERSION, "2.3", '>=' ) < 0 ) {
                add_filter( 'woocommerce_email_order_meta_fields', array( 'nbtodd_process', 'nbtodd_add_delivery_date_to_order_woo_new' ), 11, 3 );
            } else {
                add_filter( 'woocommerce_email_order_meta_keys', array( 'nbtodd_process', 'nbtodd_add_delivery_date_to_order_woo_deprecated' ), 11, 1 );
            }
            
            if ( $options['nbt_order-delivery-date_field_mandatory'] == '1' && $options['nbt_order-delivery-date_enable']=='1' ) {
                add_action( 'woocommerce_checkout_process', array( 'nbtodd_process', 'nbtodd_validate_date_wpefield' ) );
            }

            add_filter( 'woocommerce_order_details_after_order_table', array( 'nbtodd_process', 'nbtodd_add_delivery_date_to_order_page_woo' ) );

            //WooCommerce Edit Order page
            add_filter( 'manage_edit-shop_order_columns', array( 'nbtodd_filter', 'nbtodd_woocommerce_order_delivery_date_column'), 20, 1 );
            add_action( 'manage_shop_order_posts_custom_column', array( 'nbtodd_filter', 'nbtodd_woocommerce_custom_column_value') , 20, 1 );
            add_filter( 'manage_edit-shop_order_sortable_columns', array( 'nbtodd_filter', 'nbtodd_woocommerce_custom_column_value_sort' ) );
            add_filter( 'request', array( 'nbtodd_filter', 'nbtodd_woocommerce_delivery_date_orderby' ) );

            //To recover the delivery date when order is cancelled, refunded, failed or trashed.
            add_action( 'woocommerce_order_status_cancelled' , array(__CLASS__, 'nbtodd_cancel_delivery'), 10, 1 );
            add_action( 'woocommerce_order_status_refunded' , array(__CLASS__, 'nbtodd_cancel_delivery'), 10, 1 );
            add_action( 'woocommerce_order_status_failed' , array(__CLASS__, 'nbtodd_cancel_delivery'), 10, 1 );

            add_action( 'wp_trash_post', array(__CLASS__, 'nbtodd_cancel_delivery_for_trashed'), 10, 1 );

            //Ajax calls
            //add_action( 'init', array(__CLASS__, 'nbtodd_load_ajax') );
            add_action( 'wp_ajax_nopriv_nbtodd_update_delivery_session', array( 'nbtodd_process', 'nbtodd_update_delivery_session' ) );
            add_action( 'wp_ajax_nbtodd_update_delivery_session', array( 'nbtodd_process', 'nbtodd_update_delivery_session' ) );

            add_action( 'woocommerce_admin_order_data_after_shipping_address', array(__CLASS__, 'nbtodd_wc_order_data'), 10, 2 );


        self::$initialized = true;
    } 
    /**
         * Loads ajax callback
         * 
         * @hook init
         * @since 1.5
         */  

        public static function nbtodd_load_ajax() {
            if( '' == session_id() ) {
                session_start();    
            }
            add_action( 'wp_ajax_nopriv_nbtodd_update_delivery_session', array( 'nbtodd_process', 'nbtodd_update_delivery_session' ) );
            add_action( 'wp_ajax_nbtodd_update_delivery_session', array( 'nbtodd_process', 'nbtodd_update_delivery_session' ) );
            /*if ( !is_user_logged_in() ) {
                add_action( 'wp_ajax_nopriv_nbtodd_update_delivery_session', array( 'nbtodd_process', 'nbtodd_update_delivery_session' ) );
            } else {
                add_action( 'wp_ajax_nbtodd_update_delivery_session', array( 'nbtodd_process', 'nbtodd_update_delivery_session' ) );
            }*/
        }            


    public static function nbtodd_wc_order_data () {
            global $post, $nbtodd_date_formats;
            $options = get_option( NBTODD_SETTINGS );        
                $delivery_date_formatted = self::nbtodd_get_order_delivery_date( $post->ID  );
                if( $delivery_date_formatted != '' ) {
                echo '<p><strong>'. $options['nbt_order-delivery-date_field_label'] . ':</strong> ' . $delivery_date_formatted . '</p>';
            }
            
    }

    public static function nbtodd_admin_enqueue(){
        wp_enqueue_style( 'odd-admin', NBTODD_CSS_DIR . '/order-delivery-date.css', array( )  );
        wp_enqueue_script( 'odd-admin-script', NBTODD_JS_DIR . '/admin.js', array(  ));
    }

    public static function nbtodd_my_enqueue( $hook ) {
            global $nbtodd_languages, $wpefield_version;
            if( 'toplevel_page_order_delivery_date_lite' != $hook ) {
                return;
            }
            
            wp_dequeue_script( 'themeswitcher' );
            wp_enqueue_script( 'themeswitcher-orddd', NBTODD_JS_DIR . '/jquery.themeswitcher.min.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker' ), $wpefield_version, false );
                
            /*foreach ( $nbtodd_languages as $key => $value ) {
                wp_enqueue_script( $value, NBTODD_JS_DIR . '/i18n/jquery.ui.datepicker-'.$key.'.js', array( 'jquery', 'jquery-ui-datepicker' ), $wpefield_version, false );
            }*/
            wp_enqueue_script( 'jquery.ui.datepicker-en-GB', NBTODD_JS_DIR . '/i18n/jquery.ui.datepicker-en-GB.js', array( 'jquery', 'jquery-ui-datepicker' ), $wpefield_version, false );

            /*wp_register_style( 'woocommerce_admin_styles', esc_url( plugins_url() . '/woocommerce/assets/css/admin.css' ), array(), WC_VERSION );
            wp_enqueue_style( 'woocommerce_admin_styles' );*/
            /*wp_enqueue_style( 'order-delivery-date12', NBTODD_CSS_DIR . '/order-delivery-date.css', '', $wpefield_version, false);*/
            wp_register_style( 'jquery-ui-style', NBTODD_CSS_DIR . '/themes/smoothness/jquery-ui.css', '', $wpefield_version, false );
            wp_enqueue_style( 'jquery-ui-style' );
            wp_enqueue_style( 'datepicker', NBTODD_CSS_DIR . '/datepicker.css', '', $wpefield_version, false);            
        }

    
    public static function repeater_show_field($field, $value = false, $tr = true)
    {
        if (!empty($value)) {
            if (!isset($field['fid'])) {
                if ($field['type'] == 'repeater') {
                    $value = $value[$field['id']];
                } else {
                    if ($value[$field['id']]) {
                        $value = $value[$field['id']];
                    } else {
                        $value = $field['default'];
                    }
                }
            }

        } else {
            if (isset($field['default'])) {
                $value = $field['default'];
            }
        }

        if (is_array($field)) {
            switch ($field['type']) {               

                case 'text':
                    ?>
                    <input type="text" name="<?php echo esc_attr($field['id']) ?>"
                                   value="<?php echo esc_attr($value) ?>" style="width: 100%;"/>
                            <?php if (isset($field['desc_tip'])) {
                                echo $field['desc_tip'];
                            } ?>
                    
                    <?php
                    break;
                case 'label':
                    ?>
                    <label class="field-type-label" style="text-transform: uppercase;font-weight: bold;width: 100%;border-bottom: 1px solid;" for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_html($field['name']); ?></label>
                    
                    <?php
                    break;
                case 'textarea':
                    ?>
                     <textarea style="width: 100%" rows="<?php if (isset($field['rows'])) {
                                echo $field['rows'];
                            } else {
                                echo '3';
                            } ?>"
                                      name="<?php echo esc_attr($field['id']) ?>"><?php echo str_replace('\\', '', esc_attr($value)) ?></textarea>
                            <?php if (isset($field['desc_tip'])) {
                                echo $field['desc_tip'];
                            } ?>
                   
                    <?php
                    break;
                case 'repeater':
                    $field_id = $field['id'];
                    $fields = $field['fields']; ?>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr($field['id']); ?>"><?php echo esc_html($field['name']); ?></label>
                        </th>
                        <td class="forminp forminp-<?php echo sanitize_title($field['type']) ?>">
                            <?php include($field['temp']); ?>
                        </td>
                    </tr>
                    <?php
                break;                   
                
                case 'checkbox':
                    ?>
                    <div class="checkbox">
                                <label><input type="checkbox" name="<?php echo $field['id']; ?>"
                                              value="1"<?php if ($value == 1 || $value == true) {
                                        echo ' checked';
                                    } ?>> <?php echo $field['label']; ?></label>
                            </div>                   

                    <?php
                break;
                case 'number':
                ?>
                        <input type="number" name="<?php echo esc_attr($field['id']) ?>"
                           value="<?php echo esc_attr($value) ?>"<?php if (isset($field['min'])) {
                        echo ' min="' . $field['min'] . '"';
                    } ?><?php if (isset($field['max'])) {
                        echo ' max="' . $field['max'] . '"';
                    } ?> />
                        
                    <?php
                
                    break;
                
                case 'select':
                ?>
                    <select <?php if (isset($field['class'])) {
                        echo ' class="' . $field['class'] . '"';
                    } ?> name="<?php echo $field['id']; ?>">
                        <?php foreach ($field['options'] as $k_select => $val_select) {
                            ?>
                            <option value="<?php echo $k_select; ?>"<?php selected($value, $k_select); ?>><?php echo $val_select; ?></option>
                            <?php
                        } ?>
                    </select>
                    <?php
                
                    break;
                case $field['type']:
                    echo apply_filters('nbt_admin_field_' . $field['type'], $field['type'], $field, $value);
                    break;

                default:
                    # code...

                    break;
            }

        }
    }    
    
    /** 
         * Capability to allow shop manager to edit settings
         * 
         * @hook admin_init
         * @since 2.2
         */
    public static function nbtodd_capabilities() {
            $role = get_role( 'shop_manager' );
            if( '' != $role ) {
                $role->add_cap( 'manage_options' );
            }
        }

        
        public static function nbtodd_check_woo_installed() {
            if ( class_exists( 'WooCommerce' ) ) {
                return true;
            } else {
                return false;
            }
        }
        
        /**
         * Check if WooCommerce plugin is active or not. If it is not active then it will display a notice.
         * 
         * @hook admin_init
         * @since 2.6
         */
        
        function nbtodd_check_if_woocommerce_active() {
            if ( ! self::nbtodd_check_woo_installed() ) {
                if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
                    deactivate_plugins( plugin_basename( __FILE__ ) );
                    add_action( 'admin_notices', array( 'order_delivery_date_lite', 'nbtodd_disabled_notice' ) );
                    if ( isset( $_GET[ 'activate' ] ) ) {
                        unset( $_GET[ 'activate' ] );
                    }
                }
            }
        }
        
        /**
         * Display a notice in the admin Plugins page if the plugin is activated while WooCommerce is deactivated.
         * 
         * @hook admin_notices
         * @since 2.6
         */
        public static function nbtodd_disabled_notice() {
            $class = 'notice notice-error';
            $message = __( 'Order Delivery Date for WooCommerce plugin requires WooCommerce installed and activate.', 'order-delivery-date' );
        
            printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
        }
   

    /**
     * Free up the delivery date and time if an order is cancelled, refunded or failed
     * 
     * @hook woocommerce_order_status_cancelled
     * @hook woocommerce_order_status_refunded
     * @hook woocommerce_order_status_failed
     *
     * @param int $order_id Order ID
     * @globals string typenow
     * @since 2.5
     */

    public static function nbtodd_cancel_delivery( $order_id ) {
        global $wpdb, $typenow;
        $post_meta = get_post_meta( $order_id, '_nbtodd_timestamp' );
        if( isset( $post_meta[0] ) && $post_meta[0] != '' && $post_meta[0] != null ) {
            $delivery_date_timestamp = $post_meta[0];
        } else {
            $delivery_date_timestamp = '';
        }
         
        if( $delivery_date_timestamp != '' ) {
            $delivery_date = date( NBTODD_LOCKOUT_DATE_FORMAT, $delivery_date_timestamp );
        } else {
            $delivery_date = '';
        }
        $lockout_days = get_option( 'nbtodd_lockout_days' );
        if ( $lockout_days == '' || $lockout_days == '{}' || $lockout_days == '[]' || $lockout_days == "null" ) {
            $lockout_days_arr = array();
        } else {
            $lockout_days_arr = (array) json_decode( $lockout_days );
        }
        foreach ( $lockout_days_arr as $k => $v ) {
            $orders = $v->o;
            if ( $delivery_date == $v->d ) {
                if( $v->o == '1' ) {
                    unset( $lockout_days_arr[ $k ] );
                } else {
                    $orders = $v->o - 1;
                    $lockout_days_arr[ $k ] = array( 'o' => $orders, 'd' => $v->d );
                }
            }
        }
         
        $lockout_days_jarr = json_encode( $lockout_days_arr );
        update_option( 'nbtodd_lockout_days', $lockout_days_jarr );
    }
    

    public static function nbtodd_cancel_delivery_for_trashed( $order_id ) {
        global $typenow;
        $post_obj = get_post( $order_id );
        if ( 'shop_order' != $typenow ) {
            return;
        } else {
            if ( 'wc-cancelled' == $post_obj->post_status || 'wc-refunded' == $post_obj->post_status || 'wc-failed' == $post_obj->post_status ) {
            } else {
                self::nbtodd_cancel_delivery( $order_id );
            }
        }
    }
    /**
     * Returns timestamp for the selected Delivery date
     * 
     * @param string $delivery_date Selected Delivery Date 
     * @param string $date_format Date Format 
     * @return string Timestamp for the selected delivery date
     * @since 1.7
     */
    
    public static function nbtodd_get_timestamp( $delivery_date, $date_format ) {
        $hour = 0;
        $min = 1;
        $date_str = '';
        $m = $d = $y = 0;
        if( $delivery_date != '' ) {
            switch ( $date_format ) {
                case 'mm/dd/y':
                    $date_arr = explode( '/', $delivery_date );
                    $m = $date_arr[ 0 ];
                    $d = $date_arr[ 1 ];
                    $y = $date_arr[ 2 ];
                    break;
                case 'dd/mm/y':
                    $date_arr = explode( '/', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 0 ];
                    $y = $date_arr[ 2 ];
                    break;
                case 'y/mm/dd':
                    $date_arr = explode( '/', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 2 ];
                    $y = $date_arr[ 0 ];
                    break;
                case 'dd.mm.y':
                    $date_arr = explode( '.', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 0 ];
                    $y = $date_arr[ 2 ];
                    break;
                case 'y.mm.dd':
                    $date_arr = explode( '.', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 2 ];
                    $y = $date_arr[ 0 ];
                    break;
                case 'yy-mm-dd':
                    $date_arr = explode( '-', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 2 ];
                    $y = $date_arr[ 0 ];
                    break;
                case 'dd-mm-y':
                    $date_arr = explode( '-', $delivery_date );
                    $m = $date_arr[ 1 ];
                    $d = $date_arr[ 0 ];
                    $y = $date_arr[ 2 ];
                    break;
                case 'd M, y':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'd M, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'd MM, y':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'd MM, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'DD, d MM, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'D, M d, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'DD, M d, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'DD, MM d, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
                case 'D, MM d, yy':
                    $date_str = str_replace( ',', '', $delivery_date );
                    break;
            }
            if ( isset( $date_str ) && $date_str != '' ) {
                $timestamp = strtotime( $date_str );
            } else {
                $timestamp = mktime( 0, 0, 0, $m, $d, $y );
            }
        } else {
            $timestamp = '';
        }

        return $timestamp;
    }
    /**
     * Checks if there is a Virtual product in cart
     *
     * @globals resource $woocommerce WooCommerce Object
     * @return string yes if virtual product is there in the cart else no
     * @since 1.7
     */
    public static function nbtodd_is_delivery_enabled() {
        global $woocommerce;
        $delivery_enabled = 'yes';
        if ( get_option( 'nbtodd_no_fields_for_virtual_product' ) == 'on' && get_option( 'nbtodd_no_fields_for_featured_product' ) == 'on' ) {
            foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
                $product_id = $values[ 'product_id' ];
                $_product = wc_get_product( $product_id );
                if( $_product->is_virtual() == false && $_product->is_featured() == false ) {
                    $delivery_enabled = 'yes';
                    break;
                } else {
                    $delivery_enabled = 'no';
                }
            }
        } else if( get_option( 'nbtodd_no_fields_for_virtual_product' ) == 'on' && get_option( 'nbtodd_no_fields_for_featured_product' ) != 'on' ) {
            foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
                $_product = $values[ 'data' ];
                if( $_product->is_virtual() == false ) {
                    $delivery_enabled = 'yes';
                    break;
                } else {
                    $delivery_enabled = 'no';
                }
            }
        } else if( get_option( 'nbtodd_no_fields_for_virtual_product' ) != 'on' && get_option( 'nbtodd_no_fields_for_featured_product' ) == 'on' ) {
            foreach ( $woocommerce->cart->get_cart() as $cart_item_key => $values ) {
                $product_id = $values[ 'product_id' ];
                $_product = wc_get_product( $product_id );
                if( $_product->is_featured() == false ) {
                    $delivery_enabled = 'yes';
                    break;
                } else {
                    $delivery_enabled = 'no';
                }
            }
        } else {
            $delivery_enabled = 'yes';
        }
        return $delivery_enabled;
    }

    /**
     * Return the date with the selected langauge in Appearance tab
     * 
     * @param string $delivery_date_formatted Default Delivery Date
     * @param string $delivery_date_timestamp Delivery Date Timestamp
     * @return string Translated Delivery Date
     * @globals array $nbtodd_languages Languages array
     * @globals array $nbtodd_languages_locale Locale of all languages array
     * @since 1.9
     */
    public static function delivery_date_lite_language( $delivery_date_formatted, $delivery_date_timestamp ) {
        //global $nbtodd_languages, $nbtodd_languages_locale;
        $options = get_option( NBTODD_SETTINGS );
        /*$date_language = $options['nbt_order-delivery-date_lang_selected'];*/
        if( $delivery_date_timestamp != '' ) {
             
            /*if( $date_language != 'en-GB' ) {*/
                
                /*$locale_format = $nbtodd_languages[ $date_language ];
                $time = setlocale( LC_ALL, $nbtodd_languages_locale[ $locale_format ] );*/
                $date_format = $options['nbt_order-delivery-date_date_format'];
                //var_dump($date_format);
                switch ( $date_format ) {
                    case 'mm/dd/y':
                        $date_str = str_replace( 'dd', '%d', $date_format );
                        $month_str = str_replace( 'mm', '%m', $date_str );
                        $year_str = str_replace( 'y', '%y', $month_str );
                        break;
                    case 'dd/mm/y':
                        $date_str = str_replace( 'dd', '%d', $date_format );
                        $month_str = str_replace( 'mm', '%m', $date_str );
                        $year_str = str_replace( 'y', '%y', $month_str );
                        break;
                    case 'y/mm/dd':
                        $date_str = str_replace( 'dd', '%d', $date_format );
                        $month_str = str_replace( 'mm', '%m', $date_str );
                        $year_str = str_replace( 'y', '%y', $month_str );
                        break;
                    case 'dd.mm.y':
                        $date_str = str_replace( 'dd', '%d', $date_format );
                        $month_str = str_replace( 'mm', '%m', $date_str );
                        $year_str = str_replace( 'y', '%y', $month_str );
                        break;
                    case 'y.mm.dd':
                        $date_str = str_replace( 'dd', '%d', $date_format );
                        $month_str = str_replace( 'mm', '%m', $date_str );
                        $year_str = str_replace( 'y', '%y', $month_str );
                        break;
                    case 'yy-mm-dd':
                        $date_str = str_replace( 'dd', '%d', $date_format );
                        $month_str = str_replace( 'mm', '%m', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'dd-mm-y':
                        $date_str = str_replace( 'dd', '%d', $date_format );
                        $month_str = str_replace( 'mm', '%m', $date_str );
                        $year_str = str_replace( 'y', '%y', $month_str );
                        break;
                        /*--*/
                    case 'd M, y':
                        $date_str = str_replace( 'd', '%d', $date_format );
                        $month_str = str_replace( 'M', '%b', $date_str );
                        $year_str = str_replace( 'y', '%y', $month_str );
                        break;
                    case 'd M, yy':
                        $date_str = str_replace( 'd', '%d', $date_format );
                        $month_str = str_replace( 'M', '%b', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'd MM, y':
                        $date_str = str_replace( 'd', '%d', $date_format );
                        $month_str = str_replace( 'MM', '%B', $date_str );
                        $year_str = str_replace( 'y', '%y', $month_str );
                        break;
                    case 'd MM, yy':
                        $date_str = str_replace( 'd', '%d', $date_format );
                        $month_str = str_replace( 'MM', '%B', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'DD, d MM, yy':
                        $day_str = str_replace( 'DD', '%A', $date_format );
                        $date_str = str_replace( 'd', '%d', $day_str );
                        $month_str = str_replace( 'MM', '%B', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'D, M d, yy':
                        $day_str = str_replace( 'D', '%a', $date_format );
                        $date_str = str_replace( 'd', '%d', $day_str );
                        $month_str = str_replace( 'M', '%b', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'DD, M d, yy':
                        $day_str = str_replace( 'DD', '%A', $date_format );
                        $date_str = str_replace( 'd', '%d', $day_str );
                        $month_str = str_replace( 'M', '%b', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'DD, MM d, yy':
                        $day_str = str_replace( 'DD', '%A', $date_format );
                        $date_str = str_replace( 'd', '%d', $day_str );
                        $month_str = str_replace( 'MM', '%B', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                    case 'D, MM d, yy':
                        $day_str = str_replace( 'D', '%a', $date_format );
                        $date_str = str_replace( 'd', '%d', $day_str );
                        $month_str = str_replace( 'MM', '%B', $date_str );
                        $year_str = str_replace( 'yy', '%Y', $month_str );
                        break;
                }
                     
                if( isset( $year_str ) ) {

                    $delivery_date_formatted = strftime( $year_str, $delivery_date_timestamp );
                }                
                setlocale( LC_ALL, 'en_GB.utf8' );
            /*}*/
        }
        return $delivery_date_formatted;
    }
    
    /**
     * Return the delivery date selected for the order
     *
     * @param int $order_id Order ID
     * @return string Delivery Date for the order
     * @globals array $nbtodd_date_formats Date Format array
     * @since 1.9
     */

    public static function nbtodd_get_order_delivery_date( $order_id ) {
        global $nbtodd_date_formats;

        $data = get_post_meta( $order_id );
        /*echo '<pre>';
        var_dump($data);
        echo '</pre>';*/
        $options = get_option( NBTODD_SETTINGS );
        $field_date_label = $options['nbt_order-delivery-date_field_label'];
        $delivery_date_formatted = $delivery_date_timestamp = '';
        if ( isset( $data[ '_nbtodd_timestamp' ] ) || isset( $data[ $field_date_label ] ) ) {
            if ( isset( $data[ '_nbtodd_timestamp' ] ) ) {
                $delivery_date_timestamp = $data[ '_nbtodd_timestamp' ][ 0 ];
            }
            
            $date_f = $options['nbt_order-delivery-date_date_format'];
            if ( $delivery_date_timestamp != '' ) {
                $delivery_date_formatted = date(  $date_f , $delivery_date_timestamp );
                
            } else {
                if ( array_key_exists( $field_date_label, $data ) ) {
                    //$delivery_date_replace = str_replace(","," ",$data[ $field_date_label ][ 0 ]);
                    $delivery_date_timestamp = strtotime( $data[ $field_date_label ][ 0 ] );
                    if ( $delivery_date_timestamp != '' ) {
                        //$delivery_date_formatted = date( $nbtodd_date_formats[ $date_f ], $delivery_date_timestamp );
                        $delivery_date_formatted = date(  $date_f , $delivery_date_timestamp );
                    }
                } elseif ( array_key_exists( ORDDD_DELIVERY_DATE_FIELD_LABEL, $data ) ) {
                    $delivery_date_timestamp = strtotime( $data[ ORDDD_DELIVERY_DATE_FIELD_LABEL ][ 0 ] );
                    if ( $delivery_date_timestamp != '' ) {
                        //$delivery_date_formatted = date( $nbtodd_date_formats[ $date_f ], $delivery_date_timestamp );
                        $delivery_date_formatted = date(  $date_f , $delivery_date_timestamp );
                    }
                }
            }

            $delivery_date_formatted = self::delivery_date_lite_language( $delivery_date_formatted, $delivery_date_timestamp );
        }
        

        return $delivery_date_formatted;
    }

    public static function nbtodd_front_scripts_js() {
            global $wpefield_version;
            $options = get_option( NBTODD_SETTINGS );
            if ( $options['nbt_order-delivery-date_enable']=='1' ) {
                $calendar_theme = $options['nbt_order-delivery-date_calendar_theme'];
                if ( $calendar_theme == '' ) {
                    $calendar_theme = 'base';
                }
                wp_dequeue_style( 'jquery-ui-style' );
                wp_register_style( 'jquery-ui-style-nbtodd', NBTODD_CSS_DIR . '/themes/' . $calendar_theme . '/jquery-ui.css', '', $wpefield_version, false );
                wp_enqueue_style( 'jquery-ui-style-nbtodd' );
                wp_enqueue_style( 'datepicker', NBTODD_CSS_DIR . '/datepicker.css', '', $wpefield_version, false);
                
                wp_dequeue_script( 'initialize-datepicker' );
                wp_enqueue_script( 'initialize-datepicker-orddd', NBTODD_JS_DIR . '/nbtodd-initialize-datepicker.js', '', $wpefield_version, false );
                
                $jsArgs = array(
                        'clearText'    => __( 'Clear', 'order-delivery-date' ),
                        'holidayText'  => __( 'Holiday', 'order-delivery-date' ),
                        'bookedText'   => __( 'Booked', 'order-delivery-date' )
                    );
                wp_localize_script( 'initialize-datepicker-orddd', 'jsL10n', $jsArgs );

                /*if ( isset( $_GET[ 'lang' ] ) && $_GET[ 'lang' ] != '' && $_GET[ 'lang' ] != null ) {
                    $language_selected = $_GET['lang'];
                } else {
                    $language_selected = $options['nbt_order-delivery-date_lang_selected'];
                    if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
                        if( constant( 'ICL_LANGUAGE_CODE' ) != '' ) {
                            $wpml_current_language = constant( 'ICL_LANGUAGE_CODE' );
                            if ( !empty( $wpml_current_language ) ) {
                                $language_selected = $wpml_current_language;
                            } else {
                                $language_selected = $options['nbt_order-delivery-date_lang_selected'];
                            }
                        }
                    }
                    if ( $language_selected == "" ) {
                        $language_selected = "en-GB";
                    }
                }*/
                $language_selected = "en-GB";
                 
                wp_enqueue_script( $language_selected,  NBTODD_JS_DIR . '/i18n/jquery.ui.datepicker-'.$language_selected.'.js', array( 'jquery', 'jquery-ui-datepicker' ), $wpefield_version, false );
                
            }
        }               
    }

?>