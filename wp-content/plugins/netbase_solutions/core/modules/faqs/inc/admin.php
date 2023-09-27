<?php
class NBT_Faqs_Admin {

	public $available_tabs = array();

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'admin_print_scripts', array( $this, 'enqueue_scripts' ) );

		add_action('init', array($this, 'register_post_type'), 30);
		add_action('add_meta_boxes', array($this, 'add_faq_box'), 30);
		add_filter('hidden_meta_boxes', array($this, 'faq_hidden_meta_boxes'), 10, 2);
		add_action('admin_head',  array($this, 'my_custom_admin_styles'));

		add_action( 'save_post', array($this, 'save_faq_meta'), 9999, 3 );

		add_filter( 'manage_nbt_faq_posts_columns', array($this, 'set_custom_edit_book_columns') );
		add_action( 'manage_nbt_faq_posts_custom_column' , array($this, 'custom_book_column'), 10, 2 );

	}

	public function set_custom_edit_book_columns($columns) {
	    unset( $columns['author'] );
	    unset( $columns['date'] );
	    $columns['shortcode'] = __( 'Shortcode', 'nbt-solution' );

	    return $columns;
	}

	public function custom_book_column( $column, $post_id ) {
	    switch ( $column ) {

	        case 'shortcode' :
				printf('<span class="shortcode"><input type="text" onfocus="this.select();" name="shortcode_faq_%d" value="[nbt_faq id=&quot;%d&quot;]"></span>', $post_id, $post_id );
	            break;

	    }
	}

	public function register_post_type(){
	    $labels = array(
	        'name'               => _x( 'FAQs', 'nbt-solution' ),
	        'singular_name'      => _x( 'FAQs', 'nbt-solution' ),
	        'add_new'            => _x( 'Add New', 'nbt-solution' ),
	        'add_new_item'       => __( 'Add New FAQ', 'nbt-solution' ),
	        'edit_item'          => __( 'Edit FAQ', 'nbt-solution' ),
	        'new_item'           => __( 'New FAQ', 'nbt-solution' ),
	        'all_items'          => __( 'All FAQs', 'nbt-solution' ),
	        'view_item'          => __( 'View FAQs', 'nbt-solution' ),
	        'search_items'       => __( 'Search FAQs', 'nbt-solution' ),
	        'not_found'          => __( 'No FAQ found', 'nbt-solution' ),
	        'not_found_in_trash' => __( 'No FAQ found in the Trash', 'nbt-solution' ),
	        'parent_item_colon'  => '',
	        'menu_name'          => 'FAQs'
	    );
	    $args = array(
	        'labels'        => $labels,
	        'description'   => 'Holds our products and product specific data',
	        'public'        => true,
	        'menu_position' => 5,
	        'supports'      => array( 'title', 'editor' ),
	        'has_archive'   => true,
	    );
	    register_post_type( 'nbt_faq', $args );
	}

	public function add_faq_box(){
		add_meta_box('nbt_faqs_repeater', __('Lists FAQs', 'nbt-solution'), array($this, 'faqs_box'), 'nbt_faq', 'advanced', 'high');
		add_meta_box('nbt_faqs_product', __('Product FAQs', 'nbt-solution'), array($this, 'product_faqs_box'), 'product', 'advanced', 'high');
	}

	public function faqs_box($post){
		$data = get_post_meta($post->ID, '_nbt_faq', true);
		include(NBT_FAQS_PATH . 'tpl/admin/repeater.php');
	}

	public function product_faqs_box($post){
		$data = get_post_meta($post->ID, '_nbt_faq', true);
		include(NBT_FAQS_PATH . 'tpl/admin/product_faqs.php');
	}

	/**
	 * Load stylesheet and scripts in edit product attribute screen
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.full.js', array(  ));

		wp_enqueue_style( 'faqs-admin', NBT_FAQS_URL . 'assets/css/admin.css', array( )  );
		wp_enqueue_script( 'faqs-admin', NBT_FAQS_URL . 'assets/js/admin.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-resizable'));

		wp_localize_script( 'faqs-admin', 'nbtfaqs', array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		));
	}

	public function faq_hidden_meta_boxes($hidden, $screen){
		global $wp_meta_boxes;
		$post_type = $screen->id;

		$array_ex = array('nbt_faqs_repeater');

		switch ($post_type) {
			case 'nbt_faq':

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
	function my_custom_admin_styles() {
	?>
	    <style type="text/css">
	      .post-type-nbt_faq #screen-meta-links, .post-type-nbt_faq #message, .post-type-nbt_faq .notice, .post-type-nbt_faq .wp-media-buttons .button:not(.add_media), .post-type-nbt_faq #postdivrich, .post-type-nbt_faq #titlediv > .inside{
	           display: none;
	       }
	       .post-type-nbt_faq #message.updated.notice-success{
	       		display: block;
	       }
	     </style>
	<?php
	}

	function save_faq_meta( $post_id, $post, $update ) {

	    /*
	     * In production code, $slug should be set only once in the plugin,
	     * preferably as a class property, rather than in each function that needs it.
	     */
	    $post_type = get_post_type($post_id);

	    // If this isn't a 'book' post, don't update it.
	    if ( "nbt_faq" == $post_type ) {
			if(isset($_POST['faq_heading']) && !empty($_POST['faq_heading'])){
				$new = array();
				foreach ($_POST['faq_heading'] as $k => $h):
					$e = array();
					if( isset($_POST['faq_title'][$k]) ) {
						foreach ($_POST['faq_title'][$k] as $ke => $ve):
							$e[$ke] = array(
								'faq_title' => $ve,
								'faq_content' => $_POST['faq_content'][$k][$ke]
							);
						endforeach;
					}



					$new[$k] = array(
						'heading' => $h,
						'lists' => $e
					);
				endforeach;
				update_post_meta( $post_id, '_nbt_faq', $new );
			}
	    }

	    if($post_type == 'product') {
	    	if(isset($_POST['global_faqs'])){
	    		$global_faqs = $_POST['global_faqs'];
	    	}
	    	
	    	
	    	if( isset($_POST['select_global_faqs']) ) {
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
	    	}else {
	    		delete_post_meta($post_id, '_nbt_faq');
	    	}

	    }

	    


	}

}
new NBT_Faqs_Admin();
