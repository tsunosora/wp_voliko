<?php
/**
 * WooPanel Compatibility Online Desginer
 *
 * @package WooPanel_Rewrites
 */
class WooPanel_Online_Desginer {

	private $online_desginer_id = 'online-desginer';
    /**
     * Query var
     * @var array
     */
    public $query_vars = array();

    /**
     * WooPanel_Rewrites Constructor.
     */
    public function __construct() {
        add_filter( 'woopanel_product_meta_boxes', array( $this, 'nbdesigner_options_meta_boxes'), 10, 1 );
        add_action( 'woopanel_product_save_post', array( $this, 'save_data'), 99, 2 );
        add_action( 'woopanel_enqueue_scripts', array($this, 'enqueue_scripts') );
    }

    public function nbdesigner_options_meta_boxes( $meta_boxes ) {
        $meta_boxes['nbdesigner_options'] = array(
            'title' => esc_htmL__( 'NBDesigner Options', 'web-to-print-online-designer' ),
            'content' => array( $this, 'nbdesigner_options_content' ),
            'priority' => 1
        );

        return $meta_boxes;
    }

    public function nbdesigner_options_content( $post ) {
        $post_id = $post->ID;
        $pro_class = class_exists('Dokan_Pro') ? 'dokan_pro' : '';
        $enable = get_post_meta($post_id, '_nbdesigner_enable', true);  
        $_designer_setting = unserialize(get_post_meta($post_id, '_designer_setting', true));
        $enable_upload = get_post_meta($post_id, '_nbdesigner_enable_upload', true);  
        $upload_without_design = get_post_meta($post_id, '_nbdesigner_enable_upload_without_design', true);  
        $unit = nbdesigner_get_option('nbdesigner_dimensions_unit');
        if (isset($_designer_setting[0])){
            foreach ($_designer_setting as $key => $set ){
                $_designer_setting[$key] = array_merge(nbd_default_product_setting(), $set);
            }
            $designer_setting = $_designer_setting;
        }else {   
            $designer_setting = array();
            $designer_setting[0] = nbd_default_product_setting();           
        }
        $option = unserialize(get_post_meta($post_id, '_nbdesigner_option', true));
        $_option = nbd_get_default_product_option();
        if( !is_array($option) ){
            $option = array();
        }
        $option = array_merge($_option, $option);       
        $upload_setting = unserialize(get_post_meta($post_id, '_nbdesigner_upload', true));
        $_upload_setting = nbd_get_default_upload_setting();
        if( !is_array($upload_setting) ){
            $upload_setting = array();
        }   
        $upload_setting = array_merge($_upload_setting, $upload_setting);
        $designer_setting = nbd_update_config_default($designer_setting);
        $atts = array(
            'post_id' => $post_id,
            'enable' => $enable,
            'enable_upload' => $enable_upload,
            'upload_without_design' => $upload_without_design,
            'designer_setting' => $designer_setting,
            'upload_setting' => $upload_setting,
            'option' => $option,
            'unit' => $unit,
            'pro_class' => $pro_class
        );
        
        include_once(NBDESIGNER_PLUGIN_DIR . 'views/metabox-design-setting.php');
    }

    public function save_data( $post_id, $data ) {
        $enable = $data['_nbdesigner_enable']; 
        $enable_upload = $data['_nbdesigner_enable_upload']; 
        $upload_without_design = $data['_nbdesigner_enable_upload_without_design']; 
        $option = serialize($data['_nbdesigner_option']); 
        $setting_design = serialize($data['_designer_setting']);  
        $setting_upload = serialize($data['_designer_upload']);  
        //todo check license here
        update_post_meta($post_id, '_designer_setting', $setting_design);
        update_post_meta($post_id, '_nbdesigner_option', $option);
        update_post_meta($post_id, '_nbdesigner_upload', $setting_upload);
        update_post_meta($post_id, '_nbdesigner_enable_upload_without_design', $upload_without_design);
        update_post_meta($post_id, '_nbdesigner_enable', $enable);
        update_post_meta($post_id, '_nbdesigner_enable_upload', $enable_upload);     
    }

    public function enqueue_scripts() {
        if( is_woopanel_endpoint_url('product') ) {
            wp_register_script('nbd-dokan-product', NBDESIGNER_JS_URL . 'dokan.js', array( 'jquery-ui-resizable', 'jquery-ui-draggable' ));
         wp_enqueue_script( array( 'jquery-ui-core', 'nbd-dokan-product' ) );
            wp_enqueue_script(
                'iris',
                admin_url( 'js/iris.min.js' ),
                array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
                false,
                1
            );
            wp_enqueue_script(
                'wp-color-picker',
                admin_url( 'js/color-picker.min.js' ),
                array( 'iris' ),
                false,
                1
            );
            $colorpicker_l10n = array(
                'clear' => __( 'Clear', 'web-to-print-online-designer' ),
                'defaultString' => __( 'Default', 'web-to-print-online-designer' ),
                'pick' => __( 'Select Color', 'web-to-print-online-designer' ),
                'current' => __( 'Current Color', 'web-to-print-online-designer' ),
            );
            wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n ); 
            $wp_scripts = wp_scripts();
            wp_enqueue_style(
                'jquery-ui-nbd-dokan', sprintf('http://ajax.googleapis.com/ajax/libs/jqueryui/%s/themes/smoothness/jquery-ui.css', $wp_scripts->registered['jquery-ui-core']->ver)
            );         
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style('nbd-dokan-product', NBDESIGNER_CSS_URL . 'dokan.css');
        }
    }
}

new WooPanel_Online_Desginer();