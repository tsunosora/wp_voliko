<?php

/**
 * This class will load product
 *
 * @package WooPanel_Template_Product
 */
class WooPanel_Template_Product {
	private $post_statuses = array();
	private $classes;
	public $taxonomy = 'product_cat';
	public $tags = 'product_tag';
	public $panels = array();

	public function __construct() {
		$this->post_statuses = get_post_statuses();

		$this->classes = new WooPanel_Post_List_Table(array(
			'post_type'     	=> 'product',
			'taxonomy'			=> $this->taxonomy,
			'editor'			=> true,
			'thumbnail'			=> true,
			'preview'			=> true,
			'tags'				=> $this->tags,
			'gallery'		=> true,
			'screen'        	=> 'posts',
			'columns'       	=> array(
				'thumbnail'     => '<span class="fa fa-image" data-toggle="tooltip" data-placement="top" data-original-title="'. esc_html__('Thumbnail', 'woopanel' ) .'"><span class="screen-reader-text">'. esc_html__('Thumbnail', 'woopanel' ) .'</span></span>',
				'title'     	=> esc_html__( 'Title', 'woopanel' ),
				'sku'      		=> esc_html__( 'SKU', 'woopanel' ),
				'stock'      	=> esc_html__( 'Stock', 'woopanel' ),
				'price'      	=> esc_html__( 'Price', 'woopanel' ),
				'type'      	=> '<span class="flaticon-box" data-toggle="tooltip" data-placement="top" data-original-title="'. esc_html__('Type', 'woopanel' ) .'"><span class="screen-reader-text">'. esc_html__('Type', 'woopanel' ) .'</span></span>',
				'categories'    => esc_html__( 'Categories', 'woopanel' ),
				'tags'   		=> esc_html__( 'Tags', 'woopanel' ),
				'comments'     	=> '<span class="vers comment-grey-bubble" data-toggle="tooltip" data-placement="top" data-original-title="'. esc_html__('Comments', 'woopanel' ) .'"><span class="screen-reader-text">'. esc_html__('Comments', 'woopanel' ) .'</span></span>',
				'date'   		=> esc_html__( 'Date', 'woopanel' )
			),
			'primary_columns' 	=> 'title',
			'post_statuses' 	=> $this->post_statuses,
		));

		$this->hooks_table();
		$this->hooks_form();
	}

	public function lists() {
		$this->classes->prepare_items();
		$this->classes->display();
	}

	public function form() {

		$GLOBALS['product_object']    = isset($_GET['id']) ? wc_get_product( $_GET['id'] ) : new WC_Product();


			$product_image_gallery = empty($GLOBALS['product_object']) ? array() : $GLOBALS['product_object']->get_gallery_image_ids( 'edit' );

			$this->classes->form(
				array(
					'product_image_gallery' => $product_image_gallery
				)
			);
	}

	public function hooks_table() {
		// Custom column data
		add_filter( 'woopanel_product_thumbnail_column', array($this, 'thumbnail_custom'), 99, 3);
		add_filter( 'woopanel_product_sku_column', array($this, 'sku_custom'), 99, 3);
		add_filter( 'woopanel_product_stock_column', array($this, 'stock_custom'), 99, 3);
		add_filter( 'woopanel_product_price_column', array($this, 'price_custom'), 99, 3);
		add_filter( 'woopanel_product_type_column', array($this, 'type_custom'), 99, 3);
		add_filter( 'woopanel_product_categories_column', array($this, 'categories_custom'), 99, 3);
		add_filter( 'woopanel_product_tags_column', array($this, 'tags_custom'), 99, 3);
		add_filter( 'woopanel_product_comments_column', array($this, 'comments_custom'), 99, 3);

        add_action( 'woopanel_product_no_item_icon', array($this, 'no_item_icon'));

		add_action( 'woopanel_product_filter_display', array($this, 'filter_display'), 99, 2 );
		add_filter( 'posts_distinct', array($this, 'search_distinct'), 99, 1 );
		add_filter( 'woopanel_product_state', array($this, 'product_state'), 99, 2);
		add_filter( 'woopanel_product_action_links', array($this, 'action_links'), 10, 2);
		add_action( 'woopanel_product_table_row', array($this, 'table_row_quick_edit'), 20, 2);
	}

	public function hooks_form() {
		add_filter('woopanel_product_enter_title_here', array($this, 'enter_title_here' ), 999, 1 );
		add_filter( 'woopanel_product_meta_boxes', array( $this, 'product_data_meta_boxes'), 10, 1 );
		add_action( 'woopanel_product_save_post', array( $this, 'save_post'), 99, 2 );
		add_action( "woopanel_product_edit_form_after", array($this, 'edit_form_after'), 20, 2 );
	}

	public function edit_form_after($action, $post) {
		woopanel_form_field(
			'comment_status',
			array(
				'type'		  => 'checkbox',
				'id'          => 'comment_status',
				'label'       => '&nbsp;',
				'description' => esc_html__( 'Allow Reviews', 'woopanel' ),
				'default'	  => 'open'
			),
			$post->comment_status
		);
	}

	public function no_item_icon() {
		echo '<i class="flaticon-box"></i>';
	}

	public function product_state($return, $post) {
		if( $post->post_status != 'publish' && isset($this->post_statuses[$post->post_status]) ) {
			return '  — <span class="post-state">'. esc_attr($this->post_statuses[$post->post_status]) .'</span>';
		}
	}

	public function thumbnail_custom($html, $post, $product) {
		printf( '<a href="%s">%s</a>', esc_url(woopanel_post_edit_url($post->ID)), woodashboard_post_thumbnail( $post->ID, 'shop_thumbnail' ) );
	}

	public function sku_custom($html, $post, $product) {
		echo ( get_post_meta($post->ID, '_sku', true) ) ? get_post_meta($post->ID, '_sku', true) : '-';
	}

	public function stock_custom($html, $post, $product) {
		$stock_html = '';
		$stock_status = $product->get_stock_status();
		$stock_options = array('instock' => esc_html__('In stock', 'woopanel' ), 'outofstock' => esc_html__('Out of stock', 'woopanel' ), 'onbackorder' => esc_html__( 'On backorder', 'woopanel' ) );
		if ( array_key_exists( $stock_status, $stock_options ) ) {
			$stock_html .= '<mark class="m-badge m-badge--brand m-badge--wide '.esc_attr($stock_status).'" data-toggle="tooltip" data-original-title="'. esc_attr($stock_options[$stock_status]) .'"><span>' . esc_attr($stock_options[$stock_status]) . '</span></mark>';
		} else {
			$stock_html .= '<mark class="m-badge m-badge--brand m-badge--wide instock" data-toggle="tooltip" data-original-title="' . esc_html__( 'In stock', 'woopanel' ) . '"><span>' . esc_html__( 'In stock', 'woopanel' ) . '</span>';
		}

		// If the product has children, a single stock level would be misleading as some could be -ve and some +ve, some managed/some unmanaged etc so hide stock level in this case.
		if ( $product->managing_stock() && ! sizeof( $product->get_children() ) ) {
			$stock_html .= ' (' . esc_attr($product->get_stock_quantity()) . ')';
		}

		print($stock_html);
	}

	public function price_custom($html, $post, $product) {
		print($product->get_price_html() ? $product->get_price_html() : '<span class="na">&ndash;</span>');
	}

	public function type_custom($html, $post, $product) {
		$pro_type = '';
		if ( 'grouped' == $product->get_type() ) {
			$pro_type = '<span class="product-type tips grouped wcicon-grouped" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Grouped', 'woopanel' ) . '"></span>';
		} if ( 'groupby' == $product->get_type() ) {
			$pro_type = '<span class="product-type tips grouped wcicon-grouped" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Group By', 'woopanel' ) . '"></span>';
		} elseif ( 'external' == $product->get_type() ) {
			$pro_type = '<span class="product-type tips external wcicon-external" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'External/Affiliate', 'woopanel' ) . '"></span>';
		} elseif ( 'simple' == $product->get_type() ) {

			if ( $product->is_virtual() ) {
				$pro_type = '<span class="product-type tips virtual wcicon-virtual" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Virtual', 'woopanel' ) . '"></span>';
			} elseif ( $product->is_downloadable() ) {
				$pro_type = '<span class="product-type tips downloadable wcicon-downloadable" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Downloadable', 'woopanel' ) . '"></span>';
			} else {
				$pro_type = '<span class="product-type tips simple wcicon-simple" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Simple', 'woopanel' ) . '"></span>';
			}

		} elseif ( 'variable' == $product->get_type() ) {
			$pro_type = '<span class="product-type tips variable wcicon-variable" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Variable', 'woopanel' ) . '"></span>';
		} elseif ( 'subscription' == $product->get_type() ) {
			$pro_type = '<span class="product-type tips wcicon-variable" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Subscription', 'woopanel' ) . '"></span>';
		} elseif ( 'variable-subscription' == $product->get_type() ) {
			$pro_type = '<span class="product-type tips wcicon-variable" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Variable Subscription', 'woopanel' ) . '"></span>';
		} elseif ( 'job_package' == $product->get_type() ) {
			$pro_type = '<span class="product-type tips fa fa-briefcase" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Listings Package', 'woopanel' ) . '"></span>';
		} elseif ( 'resume_package' == $product->get_type() ) {
			$pro_type = '<span class="product-type tips fa fa-suitcase" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Resume Package', 'woopanel' ) . '"></span>';
		} elseif ( 'auction' == $product->get_type() ) {
			$pro_type = '<span class="product-type tips fa fa-gavel" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Auction', 'woopanel' ) . '"></span>';
		} elseif ( 'redq_rental' == $product->get_type() ) {
			$pro_type = '<span class="product-type tips fa fa-cab" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Rental', 'woopanel' ) . '"></span>';
		} elseif ( 'accommodation-booking' == $product->get_type() ) {
			$pro_type = '<span class="product-type tips fa fa-calendar" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Accommodation', 'woopanel' ) . '"></span>';
		} elseif ( 'appointment' == $product->get_type() ) {
			$pro_type = '<span class="product-type tips fa fa-clock-o" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Appointment', 'woopanel' ) . '"></span>';
		} elseif ( 'bundle' == $product->get_type() ) {
			$pro_type = '<span class="product-type tips fa fa-cubes" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Bundle', 'woopanel' ) . '"></span>';
		} elseif ( 'composite' == $product->get_type() ) {
			$pro_type = '<span class="product-type tips fa fa-cubes" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Composite', 'woopanel' ) . '"></span>';
		} elseif ( 'lottery' == $product->get_type() ) {
			$pro_type = '<span class="product-type tips fa fa-dribbble" data-toggle="tooltip" data-placement="top" data-original-title="' . esc_attr__( 'Lottery', 'woopanel' ) . '"></span>';
		} else {
			// Assuming that we have other types in future
			$pro_type = '<span class="product-type tips wcicon-' . esc_attr($product->get_type()) . ' text_tip ' . esc_attr($product->get_type()) . '" data-tip="' . ucfirst( $product->get_type() ) . '"></span>';
		}

		print($pro_type);
	}
	

	public function categories_custom($html, $post, $product) {
		echo woopanel_column_taxonomy($post, $this->taxonomy, 'cat');
	}

	public function tags_custom($html, $post, $product) {
		echo woopanel_column_taxonomy($post, 'product_tag', 'product_tag');
	}

	public function comments_custom($html, $post, $product) {
		echo woopanel_column_comments($post, 'reviews');
	}

	public function table_row_quick_edit($post, $data) {
		$product = wc_get_product($post);
		?>
		<tr id="quick-edit-<?php echo absint($post->ID);?>" class="quick-edit">
			<td colspan="<?php echo esc_attr($this->classes->total_columns);?>">
				<h4 class="quick-edit-title"><?php esc_html_e('Quick Edit', 'woopanel' );?></h4>
				<input type="hidden" name="post_id" value="<?php echo absint($post->ID);?>" />
				<div class="row">
					<div class="col-4">
						<?php
						$tag_checked = wp_get_post_terms($post->ID, 'product_tag', array('fields' => 'names'));

							woopanel_form_field(
								'post_title',
								array(
									'id'          => 'post_title',
									'type'		  => 'text',
									'label'       => esc_html__( 'Title', 'woopanel' ),
								),
								$post->post_title
							);

							woopanel_form_field(
								'post_name',
								array(
									'id'          => 'post_name',
									'type'		  => 'text',
									'label'       => esc_html__( 'Slug', 'woopanel' ),
								),
								$post->post_name
							);
						?>

						<h4 class="section-title"><?php esc_html_e( 'Product data', 'woopanel' ); ?></h4>

						<?php if ( wc_product_sku_enabled() ) :
							woopanel_form_field(
								'_sku',
								array(
									'id'          => '_sku',
									'type'		  => 'text',
									'label'       => esc_html__( 'SKU', 'woopanel' ),
								),
								get_post_meta($post->ID, '_sku', true)
							);
						endif;?>

						<?php
						woopanel_form_field(
							'stock_status',
							array(
								'id'          => 'stock_status',
								'type'		  => 'select',
								'label'       => esc_html__( 'In stock?', 'woopanel' ),
								'options'     => wc_get_product_stock_status_options(),
								'desc_tip'    => 'true',
							),
							get_post_meta($post->ID, '_stock_status', true)
						);
						?>
						<div class="form-group m-form__group type-dropdown " id="_sku_field" data-priority="">
							<label for="post_author" class=""><?php esc_html_e( 'Author', 'woopanel' );?></label>
							<?php
							wp_dropdown_users(array(
								'class' => 'form-control m-input',
								'id' => 'post_author',
								'name' => 'post_author',
								'selected' => $post->post_author,
								'show' => 'display_name'
							));?>
						</div>
					</div>

					<div class="col-4">
						<?php
						woopanel_form_field(
							'post_tag',
							array(
								'id'          => 'post_tag',
								'type'		  => 'textarea',
								'label'       => esc_html__( 'Product tags', 'woopanel' ),
							),
							implode(", ", $tag_checked)
						);

						woopanel_form_field(
							'comment_status',
							array(
								'id'            => 'comment_status',
								'type'			=> 'checkbox',
								'label'         => esc_html__( 'Enable reviews', 'woopanel' ),
								'default'	  	=> 'open'
							),
							$product->get_reviews_allowed( 'edit' ) ? 'open' : 'closed'
						);

						woopanel_form_field(
							'post_status',
							array(
								'id'          => 'post_status',
								'type'		  => 'select',
								'label'       => esc_html__( 'Status', 'woopanel' ),
								'options'     => $this->post_statuses,
								'desc_tip'    => 'true',
							),
							$post->post_status
						);

						?>
					
					</div>

					<div class="col-4">
						<?php
						$taxonomy = 'product_cat';
						$checked = wp_get_post_terms($post->ID, $taxonomy, array('fields' => 'ids'));
						?>
						<div class="form-group m-form__group type-text " id="_sku_field" data-priority="">
							<label for="_sku" class=""><?php esc_html_e( 'Product categories', 'woopanel' );?></label>
							<?php
							$args = array(
								'walker'     => new WooPanel_QuickEdit_Checkbox_List_Tree(),
								'taxonomy'   => $taxonomy,
								'form_name'  => "post_{$taxonomy}",
								'title_li'   => '',
								'hide_empty' => false,
								'checked'    => $checked,
							);?>
							<ul class="product_cat-checklist">
								<?php wp_list_categories($args);?>
							</ul>
						</div>
					</div>
				</div>

				<div class="row quick-edit-actions">
					<div class="col-6">
						<button type="button" class="btn btn-primary m-btn m-loader--light m-loader--right btn-quickedit-submit" onclick="if(!this.classList.contains('m-loader')) this.className+=' m-loader';"><?php esc_html_e('Update', 'woopanel' );?></button>
					</div>

					<div class="col-6">
						<button type="button" class="btn btn-default"><?php esc_html_e('Cancel', 'woopanel' );?></button>
					</div>
				</div>
			</td>
		</tr>

		<?php
	}

	public function action_links($action, $post) {

		return array_merge(array(
			'quick_edit' => '<a href="#" class="product-quick-edit" data-product_id="'. absint($post->ID) .'">'. esc_html__('Quick Edit', 'woopanel' ) .'</a>'
		), $action);
	}

	/**
	 * Change title boxes in admin.
	 *
	 * @param string  $text Text to shown.
	 * @param WP_Post $post Current post object.
	 * @return string
	 */
	public function enter_title_here( $text ) {
		return esc_html__( 'Product name', 'woopanel' );
	}

	public function filter_display($post_type, $post_type_object) {

		$status = isset($_GET['status']) ? strip_tags($_GET['status']) : '';
		?>
		<div class="col-md-4">
			<div class="m-form__group m-form__group--inline">
				<?php woopanel_filter_taxonomies_dropdown($post_type, $this->taxonomy, 'cat');?>
			</div>
			<div class="d-md-none m--margin-bottom-10"></div>
		</div>
		<?php
	}

	public function search_distinct( $where ) {
		return "DISTINCT";
	}

	public function product_data_meta_boxes( $meta_boxes ) {
        $meta_boxes['product_data'] = array(
            'title' => esc_htmL__( 'Product Data', 'woopanel' ),
            'content' => array( $this, 'product_data_metaboxes_content' ),
            'panel' => true,
            'priority' => 0
        );

        return $meta_boxes;

	}
	public function product_data_metaboxes_content($post) {
		global $product_object;

		$post_id = $post;

		include_once WOODASHBOARD_VIEWS_DIR . 'metaboxes/product/panel.php'; 
	}


	public function woopanel_save_shop_coupon_post_meta($post_id, $data) {
		update_post_meta($post_id, 'discount_type', $data['discount_type']);
		update_post_meta($post_id, 'coupon_amount', $data['coupon_amount']);
		update_post_meta($post_id, 'free_shipping', $data['free_shipping']);
		update_post_meta($post_id, 'expiry_date', $data['expiry_date']);
	}

	/**
	 * Return array of tabs to show.
	 *
	 * @return array
	 */
	private static function get_product_data_tabs() {
		$tabs = apply_filters(
			'woopanel_product_data_tabs', array(
				'general'        => array(
					'label'    => esc_html__( 'General', 'woopanel' ),
					'target'   => 'general_product_data',
					'class'    => array( 'hide_if_grouped' ),
					'priority' => 10,
				),
				'inventory'      => array(
					'label'    => esc_html__( 'Inventory', 'woopanel' ),
					'target'   => 'inventory_product_data',
					'class'    => array( 'show_if_simple', 'show_if_variable', 'show_if_grouped', 'show_if_external' ),
					'priority' => 20,
				),
				'shipping'       => array(
					'label'    => esc_html__( 'Shipping', 'woopanel' ),
					'target'   => 'shipping_product_data',
					'class'    => array( 'hide_if_virtual', 'hide_if_grouped', 'hide_if_external' ),
					'priority' => 30,
				),
				'linked_product' => array(
					'label'    => esc_html__( 'Linked Products', 'woopanel' ),
					'target'   => 'linked_product_data',
					'class'    => array(),
					'priority' => 40,
				),
				'attribute'      => array(
					'label'    => esc_html__( 'Attributes', 'woopanel' ),
					'target'   => 'product_attributes',
					'class'    => array(),
					'priority' => 50,
				),
				'variations'     => array(
					'label'    => esc_html__( 'Variations', 'woopanel' ),
					'target'   => 'variable_product_options',
					'class'    => array( 'variations_tab', 'show_if_variable' ),
					'priority' => 60,
				)
			)
		);

		// Sort tabs based on priority.
		uasort( $tabs, array( __CLASS__, 'product_data_tabs_sort' ) );

		return $tabs;
	}

	/**
	 * Return array of product type options.
	 *
	 * @return array
	 */
	private static function get_product_type_options() {
		return apply_filters(
			'product_type_options',
			array(
				'virtual'      => array(
					'id'            => '_virtual',
					'wrapper_class' => 'show_if_simple',
					'label'         => esc_html__( 'Virtual', 'woopanel' ),
					'description'   => esc_html__( 'Virtual products are intangible and are not shipped.', 'woopanel' ),
					'default'       => 'no',
				)
			)
		);
	}

	/**
	 * Callback to sort product data tabs on priority.
	 *
	 * @since 3.1.0
	 * @param int $a First item.
	 * @param int $b Second item.
	 *
	 * @return bool
	 */
	private static function product_data_tabs_sort( $a, $b ) {
		if ( ! isset( $a['priority'], $b['priority'] ) ) {
			return -1;
		}

		if ( $a['priority'] == $b['priority'] ) {
			return 0;
		}

		return $a['priority'] < $b['priority'] ? -1 : 1;
	}

	/**
	 * Filter callback for finding variation attributes.
	 *
	 * @param  WC_Product_Attribute $attribute
	 * @return bool
	 */
	private static function filter_variation_attributes( $attribute ) {
		return true === $attribute->get_variation();
	}

	/**
	 * Show options for the variable product type.
	 */
	public static function output_variations() {
		global $post, $wpdb, $product_object;

		$variation_attributes   = array_filter( $product_object->get_attributes(), array( __CLASS__, 'filter_variation_attributes' ) );
		$default_attributes     = $product_object->get_default_attributes();
		$variations_count       = absint( apply_filters( 'woocommerce_admin_meta_boxes_variations_count', $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'product_variation' AND post_status IN ('publish', 'private')", $product_object->get_id() ) ), $product_object->get_id() ) );
		$variations_per_page    = absint( apply_filters( 'woocommerce_admin_meta_boxes_variations_per_page', 15 ) );
		$variations_total_pages = ceil( $variations_count / $variations_per_page );

		include WOODASHBOARD_VIEWS_DIR . 'metaboxes/product/html-product-data-variations.php';
	}


	/**
	 * Show tab content/settings.
	 */
	private static function output_tabs() {
		global $post, $thepostid, $product_object;

		include_once WOODASHBOARD_VIEWS_DIR . 'metaboxes/product/html-product-data-general.php';
		include_once WOODASHBOARD_VIEWS_DIR . 'metaboxes/product/html-product-data-inventory.php';
  		include_once WOODASHBOARD_VIEWS_DIR . 'metaboxes/product/html-product-data-shipping.php';
		include_once WOODASHBOARD_VIEWS_DIR . 'metaboxes/product/html-product-data-linked-products.php';
		include_once WOODASHBOARD_VIEWS_DIR . 'metaboxes/product/html-product-data-attributes.php';
	}

	public function save_post($post_id, $data) {

		if( empty($post_id) ) {
			return;
		}

		$this->save_metabox_faq($post_id, $data);

		

		update_post_meta($post_id, '_product_image_gallery', $data['_image_gallery'] );
		
		$product = wc_get_product($post_id);
		$attributes   = WC_Meta_Box_Product_Data::prepare_attributes( $data );
		$stock        = null;

		// Handle stock changes.
		if ( isset( $_POST['_stock'] ) && ! empty($_POST['_stock']) ) {
			if ( isset( $_POST['_original_stock'] ) && wc_stock_amount( $product->get_stock_quantity( 'edit' ) ) !== wc_stock_amount( $_POST['_original_stock'] ) ) {
				/* translators: 1: product ID 2: quantity in stock */
				WC_Admin_Meta_Boxes::add_error( sprintf( esc_html__( 'The stock has not been updated because the value has changed since editing. Product %1$d has %2$d units in stock.', 'woopanel' ), $product->get_id(), $product->get_stock_quantity( 'edit' ) ) );
			} else {
				$stock = wc_stock_amount( wp_unslash( $_POST['_stock'] ) );
			}
		}

		
		


		$errors = $product->set_props(
			array(
				'sku'                => isset( $_POST['_sku'] ) ? wc_clean( wp_unslash( $_POST['_sku'] ) ) : null,
				'purchase_note'      => isset( $_POST['_purchase_note'] ) ? wp_kses_post( wp_unslash( $_POST['_purchase_note'] ) ) : null,
				'downloadable'       => isset( $_POST['_downloadable'] ),
				'virtual'            => isset( $_POST['_virtual'] ),
				'featured'           => isset( $_POST['_featured'] ),
				'catalog_visibility' => isset( $_POST['_visibility'] ) ? wc_clean( wp_unslash( $_POST['_visibility'] ) ) : null,
				'tax_status'         => isset( $_POST['_tax_status'] ) ? wc_clean( wp_unslash( $_POST['_tax_status'] ) ) : null,
				'tax_class'          => isset( $_POST['_tax_class'] ) ? wc_clean( wp_unslash( $_POST['_tax_class'] ) ) : null,
				'weight'             => wc_clean( wp_unslash( $_POST['_weight'] ) ),
				'length'             => wc_clean( wp_unslash( $_POST['_length'] ) ),
				'width'              => wc_clean( wp_unslash( $_POST['_width'] ) ),
				'height'             => wc_clean( wp_unslash( $_POST['_height'] ) ),
				'shipping_class_id'  => absint( wp_unslash( $_POST['product_shipping_class'] ) ),
				'sold_individually'  => ! empty( $_POST['_sold_individually'] ),
				'upsell_ids'         => isset( $_POST['upsell_ids'] ) ? array_map( 'intval', (array) wp_unslash( $_POST['upsell_ids'] ) ) : array(),
				'cross_sell_ids'     => isset( $_POST['crosssell_ids'] ) ? array_map( 'intval', (array) wp_unslash( $_POST['crosssell_ids'] ) ) : array(),
				'regular_price'      => wc_clean( wp_unslash( $_POST['_regular_price'] ) ),
				'sale_price'         => wc_clean( wp_unslash( $_POST['_sale_price'] ) ),
				'date_on_sale_from'  => wc_clean( wp_unslash( $_POST['_sale_price_dates_from'] ) ),
				'date_on_sale_to'    => wc_clean( wp_unslash( $_POST['_sale_price_dates_to'] ) ),
				'manage_stock'       => ! empty( $_POST['_manage_stock'] ),
				'backorders'         => isset( $_POST['_backorders'] ) ? wc_clean( wp_unslash( $_POST['_backorders'] ) ) : null,
				'stock_quantity'     => $stock,
				'low_stock_amount'   => wc_stock_amount( wp_unslash( $_POST['_low_stock_amount'] ) ),
				'download_limit'     => '' === $_POST['_download_limit'] ? '' : absint( wp_unslash( $_POST['_download_limit'] ) ),
				'download_expiry'    => '' === $_POST['_download_expiry'] ? '' : absint( wp_unslash( $_POST['_download_expiry'] ) ),
				'product_url'         => esc_url_raw( wp_unslash( $_POST['_product_url'] ) ),
				'button_text'         => wc_clean( wp_unslash( $_POST['_button_text'] ) ),
				'children'            => 'grouped' === $product->get_type() ? $this->grouped_products() : null,
				'reviews_allowed'     => ! empty( $_POST['comment_status'] ) && 'open' === $_POST['comment_status']
			)
		);

		update_post_meta( $post_id, '_stock_status', isset($_POST['_stock_status']) ? wc_clean( wp_unslash( $_POST['_stock_status'] ) ) : 'instock' );

		if ( is_wp_error( $errors ) ) {
			WC_Admin_Meta_Boxes::add_error( $errors->get_error_message() );
		}

		

		/**
		 * @since 3.0.0 to set props before save.
		 */
		do_action( 'woopanel_admin_process_product_object', $product );

		$product->save();

		if ( $product->is_type( 'variable' ) ) {
			$original_post_title = $_POST['post_title'];
			if( isset($_POST['original_post_title']) ) {
				$original_post_title = $_POST['original_post_title'];
			}
			$product->get_data_store()->sync_variation_names( $product, wc_clean( $original_post_title ), wc_clean( $_POST['post_title'] ) );
		}

		do_action( 'woopanel_process_product_meta_' . esc_attr($data['product_type']), $post_id );

		wp_set_object_terms( $post_id, $data['product_type'], 'product_type' );
		
	}

	public function save_metabox_faq($post_id, $data) {
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
	public function grouped_products() {
		return isset( $_POST['grouped_products'] ) ? array_filter( array_map( 'intval', (array) $_POST['grouped_products'] ) ) : array();
	}
}