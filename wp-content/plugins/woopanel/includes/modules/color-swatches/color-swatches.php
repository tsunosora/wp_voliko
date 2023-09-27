<?php
define('WOOPANEL_COLOR_SWATCHES_PATH', plugin_dir_path( __FILE__ ));
define('WOOPANEL_COLOR_SWATCHES_URL', plugin_dir_url( __FILE__ ));

/**
 * Color Swatches modules class
 *
 * @since 1.0
 */
class WooPanel_Color_Swatches {

    static $plugin_id = 'color-swatches';
    /**
     * Variable to hold the initialization state.
     *
     * @var  boolean
     */
    protected static $initialized = false;

    public static $types = array();
    
    /**
     * Initialize functions.
     *
     * @return  void
     */
    public static function initialize() {
        // Do nothing if pluggable functions already initialized.
        if ( self::$initialized ) {
            return;
        }

        if ( ! function_exists( 'WC' ) ) {
            add_action( 'admin_notices', array( __CLASS__, 'install_woocommerce_admin_notice') );
        }else{
            self::$types = array(
                'color' => esc_html__( 'Color', 'woopanel' ),
                'image' => esc_html__( 'Image', 'woopanel' ),
                'radio' => esc_html__( 'Radio', 'woopanel' ),
                'label' => esc_html__( 'Label', 'woopanel' ),
            );

            add_action( 'init', array( __CLASS__, 'load_textdomain' ) );
            add_filter( 'product_attributes_type_selector', array( __CLASS__, 'add_attribute_types' ) );


            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                require_once 'inc/ajax.php';
                WooPanel_Color_Swatches_Ajax::initialize();
            }
            
            require_once WOOPANEL_COLOR_SWATCHES_PATH . 'inc/admin.php';
            require_once WOOPANEL_COLOR_SWATCHES_PATH . 'inc/frontend.php';
        }

        // State that initialization completed.
        self::$initialized = true;
    }
    
    /**
     * Method Featured.
     *
     * @return  array
     */
    public static function install_woocommerce_admin_notice() {?>
        <div class="error">
            <p><?php printf( esc_html__( 'WooCommerce plugin is not activated. Please install and activate it to use for module %s.', 'woopanel' ), '<strong>Color Swatches</strong>' ); ?></p>
        </div>
        <?php    
    }


    /**
     * Load plugin text domain
     */
    public static function load_textdomain() {
        load_plugin_textdomain( 'wcvs', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Add extra attribute types
     * Add color, image and label type
     *
     * @param array $types
     * @return array
     */
    public static function add_attribute_types( $types ) {

        if( isset($_GET['page']) && $_GET['page'] == 'product_attributes' ) {
            return array_merge( $types, self::$types );
        }

        return $types;
    }


    /**
     * Get attribute's properties
     *
     * @param string $taxonomy
     * @return object
     */
    public static function get_tax_attribute( $taxonomy ) {
        global $wpdb;

        $attr = substr( $taxonomy, 3 );
        $attr = $wpdb->get_row( "SELECT * FROM " . esc_attr($wpdb->prefix) . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attr'" );

        return $attr;
    }

    public static function get_style(){
        return array(
            'square' => esc_html__('Square', 'woopanel' ),
            'circle' => esc_html__('Circle', 'woopanel' )

        );
    }

    public static function get_attribute_taxonomies($product_id, $attribute_name) {
        $transient_name = 'wc_attr_tax_' . md5( absint($product_id) . esc_attr( $attribute_name) );
        $attribute_taxonomies = get_transient( $transient_name );
        if ( false === $attribute_taxonomies ) {
            global $wpdb;

            $attribute_taxonomies = $wpdb->get_row( 'SELECT * FROM ' . esc_attr($wpdb->prefix) . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attribute_name'" );

            set_transient( $transient_name, $attribute_taxonomies );
        }

        return $attribute_taxonomies;
    }
}

/**
 * Returns the main instance of WooPanel_Color_Swatches.
 *
 * @since  1.0.0
 * @return WooPanel_Color_Swatches
 */
WooPanel_Color_Swatches::initialize();