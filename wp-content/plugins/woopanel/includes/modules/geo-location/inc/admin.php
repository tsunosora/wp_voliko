<?php
/**
 * Geo Location Admin class
 *
 * @package WooPanel_Modules
 */
class NBT_Geo_Location_Admin {
    /**
     * NBT_PriceMatrix_Admin Constructor.
     */
	function __construct() {
		add_filter( 'woopanel_product_meta_boxes', array( $this, 'geo_location_meta_boxes'), 10, 1 );
		add_action( 'woopanel_product_save_post', array( $this, 'save_post'), 99, 2 );
		add_action( 'woopanel_save_settings', array($this, 'update_user_setting'), 10, 1);
        add_action( 'woopanel_enqueue_scripts', array($this, 'woopanel_enqueue_scripts') );
	}
	
    /**
     * Display form field in woopanel product
     */
    public function geo_location_meta_boxes( $meta_boxes ) {
        $meta_boxes['geo_location'] = array(
            'title' => esc_htmL__( 'GEO Location', 'woopanel' ),
            'content' => array( $this, 'geo_location_metaboxes_content' ),
            'priority' => 2
        );

        return $meta_boxes;
    }

    public function geo_location_metaboxes_content( $post ) {
        global $current_user;

        $post_id = $post->ID;

        $geo_application_id = get_user_meta( $current_user->ID, 'geo_application_id', true );
        $geo_application_code = get_user_meta( $current_user->ID, 'geo_application_code', true );


        $desc = esc_html__( 'Please fill your product adress to show map.', 'woopanel' );
        if( empty($geo_application_id) && empty($geo_application_code) ) {
            $desc = sprintf( esc_html__( 'You did not set up api key. Please %s to set up it.', 'woopanel' ), sprintf( '<a href="'. woopanel_dashboard_url().'/settings/#geo_location">%s</a>', esc_html__('click here', 'woopanel' ) ) );
        }
 
        woopanel_form_field(
            'user_geo_location',
            array(
                'type'		  => 'map',
                'id'          => 'user_geo_location',
                'label'       => esc_html__( 'Product Address', 'woopanel' ),
                'placeholder' => esc_html__('Enter your store address here.', 'woopanel' ),
                'form_inline' => true,
                'description' => $desc
            ),
            get_post_meta($post_id, 'user_geo_location', true)
        );

        woopanel_form_field(
            '_product_map_lat',
            array(
                'type'		  => 'hidden',
                'id'          => 'woopanel_map_lat',
                'label'       => esc_html__( 'GEO Location', 'woopanel' ),
                'placeholder' => esc_html__('Enter your store address here.', 'woopanel' ),
                'form_inline' => true
            ),
            get_post_meta($post_id, '_product_map_lat', true)
        );

        woopanel_form_field(
            '_product_map_lng',
            array(
                'type'		  => 'hidden',
                'id'          => 'woopanel_map_lng',
                'label'       => esc_html__( 'GEO Location', 'woopanel' ),
                'placeholder' => esc_html__('Enter your store address here.', 'woopanel' ),
                'form_inline' => true
            ),
            get_post_meta($post_id, '_product_map_lng', true)
        );
    }

    /**
     * Save form field in woopanel product
     */
	public function save_post($post_id, $data) {
		if( isset($_POST['user_geo_location']) && isset($_POST['_product_map_lat']) && isset($_POST['_product_map_lng']) ) {
			update_post_meta( $post_id, 'user_geo_location', $_POST['user_geo_location']);
			update_post_meta( $post_id, '_product_map_lat', $_POST['_product_map_lat']);
			update_post_meta( $post_id, '_product_map_lng', $_POST['_product_map_lng']);
		}
	}

    /**
     * Save form field in woopanel setting
     */
	public function update_user_setting($user_id) {
		if( isset($_POST['woopanel_map_lat']) ) {
			update_user_meta($user_id, 'user_geo_lat', $_POST['woopanel_map_lat']);
		}
		
		if( isset($_POST['woopanel_map_lng']) ) {
			update_user_meta($user_id, 'user_geo_lng', $_POST['woopanel_map_lng']);
		}
	}

    public function woopanel_enqueue_scripts() {
        global $wp_query;

        if( ! empty($wp_query->query) && ( isset($wp_query->query['settings']) || isset($wp_query->query['product']) ) && NBT_Solutions_Geo_Location::get_woopanel_geo() ) {
            wp_enqueue_style('here-mapsjs', '//js.api.here.com/v3/3.0/mapsjs-ui.css?dp-version=1549984893', array(), NBT_Solutions_Geo_Location::$here_ver, 'all' );
            wp_enqueue_script( 'here-mapsjs-core', '//js.api.here.com/v3/3.0/mapsjs-core.js', array(), NBT_Solutions_Geo_Location::$here_ver, false );
            wp_enqueue_script( 'here-mapsjs-service', '//js.api.here.com/v3/3.0/mapsjs-service.js', array(), NBT_Solutions_Geo_Location::$here_ver, false );
            wp_enqueue_script( 'here-mapsjs-ui', '//js.api.here.com/v3/3.0/mapsjs-ui.js', array(), NBT_Solutions_Geo_Location::$here_ver, false );
            wp_enqueue_script( 'here-mapsjs-mapevents', '//js.api.here.com/v3/3.0/mapsjs-mapevents.js', array(), NBT_Solutions_Geo_Location::$here_ver, false );
            wp_enqueue_script( 'heremap-admin', NBT_GEOLOCAL_URL . 'assets/js/admin.js', array(), '1.0', true );
        }
    }
}

/**
 * Returns the main instance of NBT_Geo_Location_Admin.
 *
 * @since  1.0.0
 * @return NBT_Geo_Location_Admin
 */
new NBT_Geo_Location_Admin();