<?php

/**
 * This class will load product categories
 *
 * @package WooPanel_Store_Categories
 */
class WooPanel_Store_Categories extends WooPanel_Taxonomy {
	public $taxonomy = 'store_category';
	public $endpoint = 'store-category';
	protected $columns = array();
	public $panels = array();

	public function __construct() {
		$this->columns = array(
			'image' => array(
				'label' => esc_html__('Image', 'woopanel' ),
				'order' => 1
			)
		);

		parent::__construct();
		
		$this->hooks_table();

		add_filter( 'woopanel_taxonomy_store_category_description', '__return_false' );



		add_action( 'store_category_add_form_fields', array( $this, 'add_category_fields' ), 99, 1 );
		add_action( 'store_category_edit_form_fields', array( $this, 'edit_form_fields'), 99, 2 );
	}

	/**
	 * Category thumbnail fields.
	 */
	public function add_category_fields( $taxonomy ) {
		woopanel_form_field(
			'icon', 
			[
				'type'	=> 'image',
				'label'	=> esc_html__( 'Thumbnail', 'woopanel' ),
				'id'	=> 'icon'
			],
			false
		);
	}


	public function edit_form_fields( $taxonomy, $term ) {
		woopanel_form_field(
			'icon', 
			[
				'type'	=> 'image',
				'label'	=> esc_html__( 'Icon', 'woopanel' ),
				'id'	=> 'icon'
			],
			$term->icon
		);
	}

	// public function thumbnail_column() {
	// 	echo '<img src="http://localhost/netbase/woopanel/wp-content/uploads/woocommerce-placeholder.png" alt="Thumbnail" class="wp-post-image" height="48" width="48">';
	// }

	/**
	 * Form create/edit for displaying.
	 *
	 * @since 1.0.0
	 * @abstract
	 */
	public function index() {
		global $wpdb, $current_user;

		$prefix = WOOPANEL_STORE_LOCATOR_PREFIX;
		$this->tax = new stdClass;

		if( isset($_GET['id']) ) {
			

			$term = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}categories WHERE id = %d", absint($_GET['id']) ) );

			if( ! $term ) {
				return 'redirect';
			}

			$term->name = $term->category_name;

			//this->save_taxonomy($_GET['id']);

			if( isset($_GET['action']) ) {
				if ( wp_verify_nonce( $_GET['_wpnonce'], 'woopanel-category-trash' ) ) {
					$wpdb->delete( $prefix . 'categories', array( 'id' => $term->id ) );
					wp_redirect( woopanel_dashboard_url('store-category') );
					die();
				}
			}

			if( isset($_POST[ sprintf( 'tax_%s', $this->taxonomy ) ]) ) {
				$term = new stdClass;
				$term->term_id = (int)$_GET['id'];
				$term->name = sanitize_text_field($_POST['tax_name']);
				$term->slug = sanitize_title($_POST['tax_slug']);
				$term->icon = absint($_POST['icon']);

				$wpdb->update( $prefix . 'categories', array(
					'category_name' => $term->name,
					'slug' => $term->slug
				), array(
					'id' => $term->term_id
				) ); 
			}


			$this->tax->labels['edit_item'] = esc_html__('Edit Category', 'woopanel');
			
			$this->tax->labels = (object)$this->tax->labels;
		}else {




			$term = new stdClass;
			$term->category_name = sanitize_text_field($_POST['tax_name']);
			$term->name = sanitize_title($_POST['tax_name']);
			$term->is_active = true;
	
			$term->icon = absint($_POST['icon']);
			$term->created_on = '';

			if( isset($_POST['tax_store_category']) ) {
				$wpdb->insert(
					$prefix . 'categories',
					array(
						'category_name' => $term->category_name,
						'slug' => $term->name,
						'is_active' => true,
						'user_id' => $current_user->ID,
						'icon' => $term->icon,
						'created_on' => date( 'Y-m-d h:i:s', time() )
					),
					array(
						'%s', '%s', '%d', '%d', '%d', '%s'
					)
				);
			}

			
			$this->tax->labels['add_new_item'] = esc_html__('Add Category', 'woopanel');
			
			$this->tax->labels = (object)$this->tax->labels;
		}

		include_once WOODASHBOARD_VIEWS_DIR . 'taxonomy.php';
		
	}

	public function hooks_table() {
		// Custom column data
		// add_action( 'product_cat_add_form_fields', array( $this, 'add_category_fields' ), 99, 1 );
		// add_action( 'product_cat_edit_form_fields', array( $this, 'edit_category_fields' ), 99, 2 );

		add_action( 'woopanel_store_category_image_column', array($this, 'image_column'), 99, 2);
		add_action( 'woopanel_store_category_count_column', array($this, 'count_column'), 99, 2);
		// add_action( 'woopanel_save_taxonomy_product_cat', array($this, 'woopanel_save_taxonomy_product_cat'), 99, 2 );
		// add_filter( 'woopanel_bulk_action_product_cat', array($this, 'woopanel_bulk_action_product_cat'), 99, 2);
		// add_filter( 'woopanel_taxonomy_delete_product_cat', array($this, 'woopanel_taxonomy_delete_product_cat'), 99, 2);
	}



	public function image_column($echo, $tax) {
		$thumbnail_id = $tax->icon;

		if ( $thumbnail_id ) {
			$image = wp_get_attachment_thumb_url( $thumbnail_id );
		}

		if(empty($image)) {
			$image = wc_placeholder_img_src();
		}

		$image    = str_replace( ' ', '%20', $image );
		echo '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Thumbnail', 'woopanel' ) . '" class="wp-post-image" height="48" width="48" />';
	}

	public function count_column($echo, $tax) {
		global $wpdb;

		$prefix = WOOPANEL_STORE_LOCATOR_PREFIX;

		echo $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) as total FROM {$prefix}stores_categories WHERE category_id = %d", $tax->id) );
	}

	public function woopanel_save_taxonomy_product_cat( $term_id, $data ) {
		if ( isset( $data['display_type'] ) && 'product_cat' === $this->taxonomy ) {
			update_term_meta( $term_id, 'display_type', esc_attr( $data['display_type'] ) );
		}
		if ( isset( $data['product_cat_thumbnail_id'] ) && 'product_cat' === $this->taxonomy ) {
			update_term_meta( $term_id, 'thumbnail_id', absint( $data['product_cat_thumbnail_id'] ) );
		}
	}

	public function woopanel_bulk_action_product_cat($return, $term) {
		$default_category_id = absint( get_option( 'default_product_cat', 0 ) );

		if ( $default_category_id == $term->term_id ) {
			echo '<span class="dashicons dashicons-editor-help" data-toggle="tooltip" data-original-title="' . esc_html__( 'This is the default category and it cannot be deleted. It will be automatically assigned to products with no category.', 'woopanel' ) .'"></span>';
		}else {
			return true;
		}
	}

	public function woopanel_taxonomy_delete_product_cat($return, $term_id) {
		$default_category_id = absint( get_option( 'default_product_cat', 0 ) );

		if ( $default_category_id == $term_id ) {
			return false;
		}else {
			return true;
		}
	}

	public function title_column($return, $tax) {
		echo '<strong><a class="row-title" href="'. esc_url(woopanel_dashboard_url($this->endpoint) . '?id=' . absint($tax->id) ) .'" aria-label="'. esc_attr($tax->category_name) .' (' . esc_html__('Edit', 'woopanel' ) .')">'. esc_attr($tax->category_name) .'</a></strong>';

		echo '<div class="row-actions">';
			echo '<span class="edit"><a href="'. esc_url(woopanel_dashboard_url($this->endpoint) . '?id=' . absint($tax->id) ) .'" aria-label="' . esc_html__('Edit', 'woopanel' ) .' “'. esc_attr($tax->category_name) .'”">' . esc_html__('Edit', 'woopanel' ) .'</a> | </span>';

	        /**
	         * Filters permission delete taxonomy
	         *
	         * @since 1.0.0
	         * @hook woopanel_taxonomy_delete_{$taxonomy}
	         * @param {int} $term_id
	         * @return boolean
	         */
			if( apply_filters("woopanel_taxonomy_delete_{$this->taxonomy}", true, absint($tax->id) ) ) {
				echo  '<span class="delete"><a href="'. esc_url(woopanel_dashboard_url($this->endpoint) . '?id=' . absint( $tax->id ) ) .'&action=delete&_wpnonce='. wp_create_nonce('woopanel-category-trash') .'" class="submitdelete" aria-label="'. esc_html__('Delete', 'woopanel' ) .' “'. esc_attr($tax->category_name) .'”">' . esc_html__('Delete', 'woopanel' ) .'</a></span>';
			}

		echo '</div>';
	}

	public function slug_column($return, $tax) {
		echo esc_attr($tax->slug);
	}



	public function query() {
		global $current_user;

		$fullPermission = woopanel_full_permission();

		$offset = ($this->paged - 1) * $this->per_page;
		$prefix = WOOPANEL_STORE_LOCATOR_PREFIX;

		$query = array();
		$query['select'] = "SELECT * FROM {$prefix}categories as t";


		$query['where']  = "WHERE 1=1";

		if( empty($fullPermission) ) {
			$query['where'] .= " AND t.user_id = {$current_user->ID}";
		}

		if( isset($_GET['search_name']) ) {
			$query['where_like']  = "AND (t.category_name LIKE %s OR t.name LIKE %s)";
		}

		$query['orderby'] = "ORDER BY t.category_name ASC";
		$query['limit']   = "LIMIT {$offset}, {$this->per_page}";


		return $query;
	}

	public function get_count() {
		global $wpdb;

		$prefix = WOOPANEL_STORE_LOCATOR_PREFIX;

		$query = $this->query();
		$query['select'] = "SELECT COUNT( DISTINCT t.id ) as total FROM {$prefix}categories as t";
		unset($query['limit']);


		if( isset($_GET['search_name']) && ! empty($_GET['search_name']) ) {
			$sql = implode(' ', $query);
			$search_name = $wpdb->esc_like( $_GET['search_name'] );
			$search_name = '%' . esc_attr($search_name) . '%';
			$sql = $wpdb->prepare($sql, $search_name, $search_name);
		}else {
			unset($query['where_like']);
			$sql = implode(' ', $query);
		}

		return $wpdb->get_var($sql);
	}

	public function get_results() {
		global $wpdb;

		$query = $this->query();

		if( isset($_GET['search_name']) && ! empty($_GET['search_name']) ) {
			$sql = implode(' ', $query);
			$search_name = $wpdb->esc_like( $_GET['search_name'] );
			$search_name = '%' . esc_attr($search_name) . '%';
			$sql = $wpdb->prepare($sql, $search_name, $search_name);
		}else {
			unset($query['where_like']);
			$sql = implode(' ', $query);
		}

		return $wpdb->get_results($sql);
	}
}