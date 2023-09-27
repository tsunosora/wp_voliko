<?php
class NBT_Solutions_Vendor_Profile_Agile {
	function __construct() {
		add_action( 'woopanel_stores_fields', array( $this, 'woopanel_stores_fields'), 10, 1);
		add_filter( 'woopanel_stores_field_update', array( $this, 'woopanel_stores_field_update'), 10, 2 );

		add_filter( 'woopanel_vendor_query_var', array( $this, 'woopanel_query_var_filter') );
		add_filter( 'woopanel_menus', array( $this, 'woopanel_menus') );
		add_filter( 'woopanel_submenus', array( $this, 'woopanel_submenus') );

		add_action( 'woopanel_dashboard_store-listing_endpoint', array( $this, 'woopanel_store_listing_endpoint_content' ) );
		add_action( 'woopanel_dashboard_store_endpoint', array( $this, 'woopanel_store_endpoint_content' ) );
		add_action( 'woopanel_dashboard_store-category_endpoint', array( $this, 'woopanel_store_category_endpoint_content' ) );

		add_action( 'admin_enqueue_scripts', array($this, 'woopanel_admin_scripts'));

		add_filter( 'body_class', array( $this, 'woopanel_stores_listing' ) );
	}

	public function woopanel_stores_fields( $store ) {
		include_once WOODASHBOARD_TEMPLATE_DIR . 'vendor/agile-store.php';
	}

	public function woopanel_stores_field_update( $fields, $form_data ) {
		$fields['banner_id'] = $form_data['banner_id'];
		$fields['user_id'] = $form_data['user_id'];
		$fields['name'] = sanitize_title($form_data['title']);

		return $fields;
	}

	public function woopanel_query_var_filter( $query_vars ) {
		$query_vars[] = 'store-listing';
		$query_vars[] = 'store';
		$query_vars[] = 'store-category';

		return $query_vars;
	}

	public function woopanel_menus( $woopanel_menus ) {
		$woopanel_menus[100] = [
            'id'         => 'store-listing',
            'menu_slug'  => 'store-listing',
            'menu_title'      => esc_html__( 'Store Listing', 'woopanel' ),
            'page_title' => '',
            'capability' => '',
            'priority' => 10,
            'permission' => 'vendor'
		];

		return $woopanel_menus;
	}

	public function woopanel_submenus( $submenus ) {
        $submenus['store-listing'] = array(
            5 => array(
                'id'         => 'store-listing',
                'menu_slug'  => 'store-listing',
                'label'      => esc_html__( 'Store Listing', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ),
            6 => array(
                'id'         => 'store',
                'menu_slug'  => 'store',
                'label'      => _x( 'Add Store', 'post status' ),
                'page_title' => '',
                'capability' => '',
			),
            10 => array(
                'id'         => 'store-category',
                'menu_slug'  => 'store-category',
                'label'      => _x( 'Category', 'post status' ),
                'page_title' => '',
                'capability' => '',
			)
		);

		return $submenus;
	}

	public function woopanel_store_listing_endpoint_content() {
		$profile = new WooPanel_Template_Profile_Listing();
		$profile->lists();
	}

	public function woopanel_store_endpoint_content() {
		$profile = new WooPanel_Template_Profile_Listing();
		$profile->form();
	}

	public function woopanel_store_category_endpoint_content() {
		$profile = new WooPanel_Store_Categories();
		$profile->index();
	}

	public function woopanel_stores_listing( $classes ) {
		global $wp_query, $admin_options;

		if( ! empty($wp_query->query['pagename']) && ! empty($admin_options->options['woopanel_page_stores']) && $wp_query->query['pagename'] == $admin_options->options['woopanel_page_stores'] ) {
			$layout = empty($admin_options->options['store_listing_layout']) ? 'style1' : $admin_options->options['store_listing_layout'];

			$classes[] = sprintf('woopanel-%s-layout', $layout);
		}

	    
	     
	    return $classes;
	}

    /**
     * Enqueue styles.
     */
    public function woopanel_admin_scripts( $hook ) {

    	if( $hook == 'admin_page_woopanel-edit-store' || $hook == 'store-locator_page_woopanel-create-store' ) {
    		wp_enqueue_media();
    		wp_enqueue_style('woopanel_settings_styles', WOODASHBOARD_URL . 'assets/css/admin-settings.css', array());
    		wp_enqueue_style('select2', WOODASHBOARD_URL . 'vendors/select2/select2.min.css', array());
    		wp_enqueue_script('select2', WOODASHBOARD_URL . 'vendors/select2/select2.full.min.js', array('jquery', 'selectWoo'), WC_VERSION);
    		wp_enqueue_script('woopanel_admin_scripts', WOODASHBOARD_URL . 'admin/assets/js/scripts.js', array('jquery'), WooDashboard()->version );
    	}

    	
    }
}

new NBT_Solutions_Vendor_Profile_Agile();