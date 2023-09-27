<?php
class NBT_Color_Swatches_Admin {

	public $color_swatches = 'color_swatches';

	/**
	 * Class constructor.
	 */
	public function __construct() {
		//include_once NBT_CS_PATH . 'inc/attributes.php';
		add_action( 'admin_init', array( $this, 'init_attribute_hooks' ) );
		
		add_action( 'admin_print_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'nbt_cs_product_attribute_field', array( $this, 'attribute_fields' ), 10, 3 );
		add_action( 'woocommerce_product_option_terms', array( $this, 'product_option_terms' ), 10, 2 );


		add_filter( 'product_type_options', array( $this, 'admin_toggle_option' ), 10, 1  );


		add_filter('woocommerce_product_data_tabs', array($this, 'woocommerce_product_tabs'), 50, 1);
		add_action( 'woocommerce_product_data_panels', array($this, 'woocommerce_product_panels') );
		add_action('woocommerce_process_product_meta_variable', array($this, 'woocommerce_process_product_meta_variable'), 10, 1);

		
		add_action( 'woocommerce_after_add_attribute_fields', array($this, 'woocommerce_after_add_attribute_fields'), 10, 0 );
		add_action( 'woocommerce_after_edit_attribute_fields', array($this, 'woocommerce_after_edit_attribute_fields'), 10, 0 );
		add_action( 'woocommerce_attribute_added', array($this, 'woocommerce_attribute_added'), 10, 2 );
		add_action( 'woocommerce_attribute_updated', array($this, 'woocommerce_attribute_updated'), 10, 3 );

		
		if(get_option('attribute_break')){
			add_action('woocommerce_before_variations_form', array($this, 'woocommerce_before_variations_form'), 10, 0 );
			add_action('woocommerce_after_variations_form', array($this, 'woocommerce_after_variations_form'), 10, 0 );
		}
	}
    public function init_attribute_hooks() {
        $attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( empty( $attribute_taxonomies ) ) {
			return;
		}

		foreach ( $attribute_taxonomies as $tax ) {
			add_action( 'pa_' . $tax->attribute_name . '_add_form_fields', array( $this, 'add_attribute_fields' ) );
			add_action( 'pa_' . $tax->attribute_name . '_edit_form_fields', array( $this, 'edit_attribute_fields' ), 10, 2 );

			add_filter( 'manage_edit-pa_' . $tax->attribute_name . '_columns', array( $this, 'add_attribute_columns' ) );
			add_filter( 'manage_pa_' . $tax->attribute_name . '_custom_column', array( $this, 'add_attribute_column_content' ), 10, 3 );
		}

		add_action( 'created_term', array( $this, 'save_term_meta' ), 10, 2 );
		add_action( 'edit_term', array( $this, 'save_term_meta' ), 10, 2 );
    }
	public function woocommerce_after_add_attribute_fields(){?>
		<div class="form-field">
			<label for="attribute_style"><?php _e( 'Style', 'nbt-solution' ); ?></label>
			<select name="attribute_style" id="attribute_orderby">
				<?php
				foreach (NBT_Solutions_Color_Swatches::get_style() as $key => $value) {
					?>
					<option value="<?php echo $key;?>"><?php _e( $value, 'nbt-solution' ); ?></option>
					<?php
				}?>
			</select>
		</div>
		<?php
	}

	public function woocommerce_after_edit_attribute_fields(){
		global $wpdb;

		$edit = absint( $_GET['edit'] );

		$attribute_to_edit = $wpdb->get_row( "SELECT attribute_type, attribute_label, attribute_name, attribute_orderby, attribute_public FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_id = '$edit'" );

		if($attribute_to_edit){
			$style = get_option('attribute_style_'.$edit);?>
		<tr class="form-field form-required">
			<th scope="row" valign="top">
				<label for="attribute_style"><?php _e( 'Style', 'nbt-solution' ); ?></label>
			</th>
			<td>
				<select name="attribute_style" id="attribute_style">
					<?php
					foreach (NBT_Solutions_Color_Swatches::get_style() as $key => $value) {
						?>
						<option value="<?php echo $key;?>"<?php if($key == $style){ echo ' selected';}?>><?php _e( $value, 'woocommerce' ); ?></option>
						<?php
					}?>
				</select>
			</td>
		</tr>

		<?php
		}
	}

	public function woocommerce_attribute_added($id, $attr){
		update_option( 'attribute_style_'.$id, $_REQUEST['attribute_style'] );
		update_option( 'attribute_break_'.$id, $_REQUEST['attribute_break'] );

	}

	public function woocommerce_attribute_updated($attribute_id, $attribute, $old_attribute_name){
		update_option( 'attribute_style_'.$attribute_id, $_REQUEST['attribute_style'] );
		update_option( 'attribute_break_'.$attribute_id, $_REQUEST['attribute_break'] );

	}

	public function woocommerce_product_tabs($product_data_tabs){
		$product_data_tabs[$this->color_swatches] = array(
			'label' => __( 'Color Swatches', 'nbt-solution' ),
			'target' => $this->color_swatches,
			'class'  => array( 'hide show_if_variable' ),
		);

		return $product_data_tabs;
	}

	public function woocommerce_product_panels(){
		global $post;

		$product = wc_get_product( $post->ID );
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		include_once( NBT_CS_PATH . 'tpl/admin/tabs.php');
	}

	public function woocommerce_process_product_meta_variable($post_id){
		if(isset($_POST['_color_swatches'])){
			update_post_meta( $post_id, '_color_swatches', 'on');
			
			if( isset($_POST['color_swatches']) && isset($_POST['color_swatches_attribute']) ) {
				$color_swatches = $_POST['color_swatches'];
				$save_color_swatches = array();
				foreach( $_POST['color_swatches_attribute'] as $attribute_name) {
					if( isset($color_swatches[$attribute_name]) ) {
						$cs_attr_name = $color_swatches[$attribute_name];

						if( isset($cs_attr_name['type']) && ! empty($cs_attr_name['type']) ) {
							$save_color_swatches[$attribute_name] = $cs_attr_name;
						}
					}
				}

				update_post_meta($post_id, '_nb_color_swatches', $save_color_swatches);
			}
		}else {
			update_post_meta( $post_id, '_color_swatches', false);
		}

	}

	public function admin_toggle_option( $options ) {
		global $post;
		$default = 'no ';
		if(get_post_meta($post->ID, '_color_swatches', true) == 'on'){
			$default = 'yes ';
		}
		$options['color_swatches'] = array(
			'id'            => '_color_swatches',
			'wrapper_class' => $default.'show_if_variable',
			'label'         => __( 'Color Swatches', 'nbt-solution' ),
			'description'   => __( 'Replace front-end dropdowns with a price matrix. This option limits "Used for varations" to 2.', 'nbt-solution' ),
			'default'       => trim($default),
		);

		return $options;
	}

	public function woocommerce_before_variations_form(){
		echo '<div id="nbtcs-unlinebreak">';
	}
	public function woocommerce_after_variations_form(){
		echo '</div>';
	}
	/**
	 * Load stylesheet and scripts in edit product attribute screen
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen();
		if ( ! empty($screen) && strpos( $screen->id, 'edit-pa_' ) === false && ! empty($screen) && strpos( $screen->id, 'product' ) === false ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_style( 'color-swatches-admin', NBT_CS_URL . 'assets/css/admin.css', array( 'wp-color-picker' )  );
		wp_enqueue_script( 'color-swatches-admin', NBT_CS_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker', 'wp-util' ));

		wp_localize_script(
			'color-swatches-admin',
			'nbtcs',
			array(
				'i18n'        => array(
					'mediaTitle'  => esc_html__( 'Choose an image', 'nbt-solution' ),
					'mediaButton' => esc_html__( 'Use image', 'nbt-solution' ),
				),
				'placeholder' => WC()->plugin_url() . '/assets/images/placeholder.png'
			)
		);
	}


	/**
	 * Create hook to add fields to add attribute term screen
	 *
	 * @param string $taxonomy
	 */
	public function add_attribute_fields( $taxonomy ) {
		$attr = NBT_Solutions_Color_Swatches::get_tax_attribute( $taxonomy );
		do_action( 'nbt_cs_product_attribute_field', $attr->attribute_type, '', 'add' );
	}

	/**
	 * Create hook to fields to edit attribute term screen
	 *
	 * @param object $term
	 * @param string $taxonomy
	 */
	public function edit_attribute_fields( $term, $taxonomy ) {
		$attr  = NBT_Solutions_Color_Swatches::get_tax_attribute( $taxonomy );
		$value = get_term_meta( $term->term_id, $attr->attribute_type, true );
		do_action( 'nbt_cs_product_attribute_field', $attr->attribute_type, $value, 'edit' );
	}

	/**
	 * Print HTML of custom fields on attribute term screens
	 *
	 * @param $type
	 * @param $value
	 * @param $form
	 */
	public function attribute_fields( $type, $value, $form ) {
		// Return if this is a default attribute type
		if ( in_array( $type, array( 'select', 'text' ) ) ) {
			return;
		}

		// Print the open tag of field container
		if($type != 'radio'){
			printf(
				'<%s class="form-field">%s<label for="term-%s">%s</label>%s',
				'edit' == $form ? 'tr' : 'div',
				'edit' == $form ? '<th>' : '',
				esc_attr( $type ),
				NBT_Solutions_Color_Swatches::$types[$type],
				'edit' == $form ? '</th><td>' : ''
			);
		}


		switch ( $type ) {
			case 'image':
				$image = $value ? wp_get_attachment_image_src( $value ) : '';
				$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
				?>
				<div class="nbtcs-wrap-image">
					<div class="nbtcs-term-image-thumbnail" style="float:left;margin-right:10px;">
						<img src="<?php echo esc_url( $image ) ?>" width="60px" height="60px" />
					</div>
					<div class="right-button-term" style="line-height:60px;">
						<input type="hidden" class="nbtcs-term-image" name="image" value="<?php echo esc_attr( $value ) ?>" />
						<button type="button" class="nbtcs-upload-image-button button"><?php esc_html_e( 'Upload/Add image', 'nbt-solution' ); ?></button>
						<button type="button" class="nbtcs-remove-image-button button <?php echo $value ? '' : 'hidden' ?>"><?php esc_html_e( 'Remove image', 'nbt-solution' ); ?></button>
					</div>
				</div>
				<?php
				break;

			case 'color':
				?>
				<input type="text" id="term-<?php echo esc_attr( $type ) ?>" name="<?php echo esc_attr( $type ) ?>" value="<?php echo esc_attr( $value ) ?>" />
				<?php
				break;
			default:
				break;
		}

		if($type != 'radio'){
			echo 'edit' == $form ? '</td></tr>' : '</div>';
		}
	}

	public static function show_field($type, $key, $value){
		switch ( $type ) {
			case 'image':
				$image = $value ? wp_get_attachment_image_src( $value ) : '';
				$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
				?>
				<div class="nbtcs-wrap-image">
					<div class="nbtcs-term-image-thumbnail" style="float:left;margin-right:10px;">
						<img src="<?php echo esc_url( $image ) ?>" width="60px" height="60px" />
					</div>
					<div class="right-button-term" style="line-height:60px;">
						<input type="hidden" class="nbtcs-term-image" name="<?php echo esc_attr( $key ) ?>" value="<?php echo esc_attr( $value ) ?>" />
						<button type="button" class="nbtcs-upload-image-button button"><?php esc_html_e( 'Upload/Add image', 'nbt-solution' ); ?></button>
						<button type="button" class="nbtcs-remove-image-button button <?php echo $value ? '' : 'hidden' ?>"><?php esc_html_e( 'Remove image', 'nbt-solution' ); ?></button>
					</div>
				</div>
				<?php
				break;

			case 'color':
				?>
				<input type="text" class="term-alt-color term-<?php echo esc_attr( $type ) ?>" name="<?php echo esc_attr( $key ) ?>" value="<?php echo esc_attr( $value ) ?>" />
				<?php
				break;
			default:
				break;
		}
	}

	/**
	 * Save term meta
	 *
	 * @param int $term_id
	 * @param int $tt_id
	 */
	public function save_term_meta( $term_id, $tt_id ) {
		foreach ( NBT_Solutions_Color_Swatches::$types as $type => $label ) {
			if ( isset( $_POST[$type] ) ) {
				update_term_meta( $term_id, $type, $_POST[$type] );
			}
		}
	}

	/**
	 * Add selector for extra attribute types
	 *
	 * @param $taxonomy
	 * @param $index
	 */
	public function product_option_terms( $taxonomy, $index ) {
		if ( ! array_key_exists( $taxonomy->attribute_type, NBT_Solutions_Color_Swatches::$types ) ) {
			return;
		}

		$taxonomy_name = wc_attribute_taxonomy_name( $taxonomy->attribute_name );
		global $thepostid;
		?>

		<select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'nbt-solution' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo $index; ?>][]">
			<?php

			$all_terms = get_terms( $taxonomy_name, apply_filters( 'woocommerce_product_attribute_terms', array( 'orderby' => 'name', 'hide_empty' => false ) ) );
			if ( $all_terms ) {
				foreach ( $all_terms as $term ) {
					echo '<option value="' . esc_attr( $term->term_id ) . '" ' . selected( has_term( absint( $term->term_id ), $taxonomy_name, $thepostid ), true, false ) . '>' . esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) . '</option>';
				}
			}
			?>
		</select>
		<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'nbt-solution' ); ?></button>
		<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'nbt-solution' ); ?></button>
		<button class="button fr plus tawcvs_add_new_attribute" data-type="<?php echo $taxonomy->attribute_type ?>"><?php esc_html_e( 'Add new', 'nbt-solution' ); ?></button>

		<?php
	}

	/**
	 * Add thumbnail column to column list
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_attribute_columns( $columns ) {
		global $wpdb;
		$show = array(
			'color', 'image'
		);
		$name = str_replace('pa_', '', $_GET['taxonomy']);
		if( isset($_POST['action']) && $_POST['action'] == 'add-tag' ) {
			$name = str_replace('pa_', '', $_POST['taxonomy']);
		}
		$attribute = $wpdb->get_row( 'SELECT attribute_type, attribute_label, attribute_name, attribute_orderby, attribute_public FROM ' . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$name'" );
		

		
		if( $attribute && in_array( $attribute->attribute_type, $show) ) {
			
			$new_columns          = array();
			
			$new_columns['cb']    = $columns['cb'];
			$new_columns['thumb'] = '';
			
			unset( $columns['cb'] );
	
			return array_merge( $new_columns, $columns );
		}

		return $columns;

	}

	/**
	 * Render thumbnail HTML depend on attribute type
	 *
	 * @param $columns
	 * @param $column
	 * @param $term_id
	 */
	public function add_attribute_column_content( $columns, $column, $term_id ) {
		$attr  = NBT_Solutions_Color_Swatches::get_tax_attribute( $_REQUEST['taxonomy'] );
		$value = get_term_meta( $term_id, $attr->attribute_type, true );
		switch ( $attr->attribute_type ) {
			case 'color':
				printf( '<div class="swatch-preview swatch-color" style="background-color:%s;"></div>', esc_attr( $value ) );
				break;

			case 'image':
				$image = $value ? wp_get_attachment_image_src( $value ) : '';
				$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
				printf( '<img class="swatch-preview swatch-image" src="%s" width="44px" height="44px">', esc_url( $image ) );
				break;

			case 'label':
				printf( '<div class="swatch-preview swatch-label">%s</div>', esc_html( $value ) );
				break;
		}
	}
}
new NBT_Color_Swatches_Admin();