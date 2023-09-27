<?php

/**
 * This class will load FAQs Admin
 *
 * @package WooPanel_Template_FAQ_Admin
 */
class WooPanel_Template_FAQ_Admin {
	private $post_statuses = array();
	private $classes;
	public $panels = array();
	public $post_types = 'wpl-faq';

	public function __construct() {
		$this->hooks();
	}

	public function hooks() {
		add_action( 'init', array($this, 'register_post_type') );
		add_action('add_meta_boxes', array($this, 'add_faq_box'), 30);
		add_filter('hidden_meta_boxes', array($this, 'faq_hidden_meta_boxes'), 10, 2);
		add_action( 'admin_print_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'save_post_' . esc_attr($this->post_types), array($this, 'save_faq_meta'), 9999, 3 );
	}

	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'FAQs', 'post type general name', 'woopanel' ),
			'singular_name'      => _x( 'FAQs', 'post type singular name', 'woopanel' ),
			'menu_name'          => _x( 'FAQs', 'admin menu', 'woopanel' ),
			'name_admin_bar'     => _x( 'FAQ', 'add new on admin bar', 'woopanel' ),
			'add_new'            => _x( 'Add New', 'book', 'woopanel' ),
			'add_new_item'       => esc_html__( 'Add New FAQ', 'woopanel' ),
			'new_item'           => esc_html__( 'New FAQ', 'woopanel' ),
			'edit_item'          => esc_html__( 'Edit FAQ', 'woopanel' ),
			'view_item'          => esc_html__( 'View FAQ', 'woopanel' ),
			'all_items'          => esc_html__( 'All FAQs', 'woopanel' ),
			'search_items'       => esc_html__( 'Search FAQs', 'woopanel' ),
			'parent_item_colon'  => esc_html__( 'Parent FAQs:', 'woopanel' ),
			'not_found'          => esc_html__( 'No FAQs found.', 'woopanel' ),
			'not_found_in_trash' => esc_html__( 'No FAQs found in Trash.', 'woopanel' )
		);
	
		$args = array(
	        'labels'        => $labels,
	        'description'   => 'Holds our products and product specific data',
	        'public'        => true,
	        'menu_position' => 5,
	        'supports'      => array( 'title'),
	        'has_archive'   => true,
		);
	
		register_post_type( $this->post_types, $args );
	}

	public function add_faq_box() {
		add_meta_box('wpl-faq_repeater', esc_html__('Lists FAQs', 'woopanel' ), array($this, 'faqs_box'), $this->post_types, 'advanced', 'high');
		add_meta_box('nbt_faqs_product', esc_html__('Product FAQs', 'woopanel' ), array($this, 'product_faqs_box'), 'product', 'advanced', 'high');
	}

	public function faqs_box($post){
		$data = get_post_meta($post->ID, '_wpl-faq', true);
		include_once WOODASHBOARD_VIEWS_DIR . 'faqs/admin-repeater.php';
	}

	public function product_faqs_box($post){
		$data = get_post_meta($post->ID, '_nbt_faq', true);
	}

	public function faq_hidden_meta_boxes($hidden, $screen) {
		global $wp_meta_boxes;

		$post_type = $screen->id;
		$array_ex = array('wpl-faq_repeater');
		switch ($post_type) {
			case $this->post_types:

				foreach ($wp_meta_boxes[$post_type]['normal'] as $key => $array) {
					if(!empty($array)){
						foreach ($array as $key_hidden => $value) {
							if(!in_array($key_hidden, $array_ex)){
								$hidden[] = $key_hidden;
							}
						}
					}
				}

				break;
		}
		
		return $hidden;
	}

	public function save_faq_meta( $post_id, $post, $update ) {
		
	    /*
	     * In production code, $slug should be set only once in the plugin,
	     * preferably as a class property, rather than in each function that needs it.
	     */
	    $post_type = get_post_type($post_id);

	    // If this isn't a 'book' post, don't update it.
	    if ( $this->post_types == $post_type ) {
			$faq_heading = isset($_POST['faq_heading']) ? woopanel_sanitize($_POST['faq_heading']) : array();
			$faq_title = isset($_POST['faq_title']) ? woopanel_sanitize($_POST['faq_title']) : array();
			$faq_content = isset($_POST['faq_content']) ? woopanel_sanitize($_POST['faq_content']) : array();

			if( !empty($faq_heading) ) {
				$new = array();

				foreach ( $faq_heading as $k => $h ):
					$e = array();
					if( isset($faq_title[$k]) ) {
						foreach ($faq_title[$k] as $ke => $ve):
							$e[$ke] = array(
								'faq_title' => $ve,
								'faq_content' => isset($faq_content[$k][$ke]) ? sanitize_text_field($faq_content[$k][$ke]) : ''
							);
						endforeach;
					}

					$new[$k] = array(
						'heading' => $h,
						'lists' => $e
					);
				endforeach;

				update_post_meta( $post_id, '_wpl-faq', $new );
			}
	    }

	    if($post_type == 'product') {
	    	if(isset($_POST['global_faqs'])) {
	    		$global_faqs = $_POST['global_faqs'];
	    	}
	    	
	    	$new = array();
	    	foreach ($_POST['select_global_faqs'] as $key => $value) {
	    		if(is_numeric($value)){
	    			$select_global_faqs = get_post_meta($value, '_nbt_faq', true);
	    			if(isset($global_faqs[$key])){
	    				$global_faq = $select_global_faqs[$global_faqs[$key]];

	    				$new[$key] = array(
	    					'heading' => array(
	    						'title' => $global_faq['heading'],
	    						'faq' => $value,
	    						'id' => $global_faqs[$key]
	    					)
	    				);
	    			}
	    		}else{
					$e = array();
					foreach ($_POST['select_repeater_faq_type'][$key] as $ke => $faq_type):
						if(!empty($faq_type) && is_numeric($faq_type)){
							$faq = get_post_meta($faq_type, '_nbt_faq', true);
							$vid = explode('_', $_POST['select_repeater_faq_option'][$key][$ke]);
							$first_key = $vid[0];
							$last_key = $vid[1];

							$e[$ke] = array_merge(array(
								'faq' => $_POST['select_repeater_faq_type'][$key][$ke],
								'id' => $_POST['select_repeater_faq_option'][$key][$ke]
							), $faq[$first_key]['lists'][$last_key]);
						}else{
							$e[$ke] = array(
								'faq_title' => $_POST['faq_title'][$key][$ke],
								'faq_content' => $_POST['faq_content'][$key][$ke]
							);
						}
					endforeach;
    				$new[$key] = array(
    					'heading' => $_POST['faq_heading'][$key],
    					'lists' => $e
    				);
	    		}
	    		
	    	}
	    	update_post_meta( $post_id, '_nbt_faq', $new );
	    }
	}

	/**
	 * Load stylesheet and scripts in edit product attribute screen
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.full.js', array(  ));

		wp_enqueue_style( 'faqs-admin', WOODASHBOARD_URL . 'admin/assets/css/faq.css', array( )  );
		wp_enqueue_script( 'faqs-admin', WOODASHBOARD_URL . 'admin/assets/js/faqs.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-resizable'));

		wp_localize_script( 'faqs-admin', 'nbtfaqs', array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		));
	}
}


if ( ! is_admin() ) {
	new WooPanel_Template_FAQ_Admin();
}
