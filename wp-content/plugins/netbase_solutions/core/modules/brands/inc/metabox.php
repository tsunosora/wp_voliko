<?php
/**
 * @version    1.0
 * @package    Package Name
 * @author     Your Team <support@yourdomain.com>
 * @copyright  Copyright (C) 2014 yourdomain.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

/**
 * Plug additional sidebars into WordPress.
 *
 * @package  Package Name
 * @since    1.0
 */
if( ! class_exists('NBT_Solutions_Brands_Metabox') ) {
	
	class NBT_Solutions_Brands_Metabox {
		/**
		 * Variable to hold the initialization state.
		 *
		 * @var  boolean
		 */
		protected static $initialized = false;

		private static $settings_saved;

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

			self::$settings_saved = false;

			/**
			* Load modules
			*/
			add_action( 'init', array(__CLASS__, 'create_brands_tax') );
			add_action( 'product_brand_add_form_fields', array(__CLASS__, '___add_form_field_term_meta_text') );
			add_action( 'product_brand_edit_form_fields', array(__CLASS__, '___edit_form_field_term_meta_text') );
			// State that initialization completed.

			add_action( 'edit_product_brand',   array(__CLASS__, '___save_term_meta_text') );
			add_action( 'create_product_brand', array(__CLASS__, '___save_term_meta_text') );

			add_filter( 'manage_edit-product_brand_columns', array(__CLASS__, '___edit_term_columns'), 10, 3 );
			add_filter( 'manage_edit-product_brand_columns', array(__CLASS__, 'add_columns_thumbnail'), 10, 3 );
		

			self::$initialized = true;
		}

		public static function _get(){
			return array(
				'default' => __('Default', 'nbt-solution'),
				'url' => __('URL', 'nbt-solution')
			);
		}

		public static function _open(){
			return array(
				'_self' => __('Default', 'nbt-solution'),
				'_blank' => __('Open a new window or new tab', 'nbt-solution')
			);
		}

		public static function create_brands_tax() {
			register_taxonomy(
				'product_brand',
				'product',
				array(
					'label' => __( 'Brands', 'nbt-solution' ),
					'rewrite' => array( 'slug' => 'brands' ),
					'hierarchical' => true,
				)
			);

			register_meta( 'product_brand', 'brands_url', array(__CLASS__, 'sanitize_term_meta_text') );
			add_filter( 'manage_product_brand_custom_column', array(__CLASS__, '___manage_term_custom_column'), 10, 3 );
			add_filter( 'manage_product_brand_custom_column', array(__CLASS__, 'add_columns_thumbnail_content'), 10, 3 );
		}

		public static function sanitize_term_meta_text ( $value ) {
			return sanitize_text_field ($value);
		}

		public static function ___add_form_field_term_meta_text() { ?>
			<?php wp_nonce_field( basename( __FILE__ ), 'term_meta_text_nonce' ); ?>
			<div class="form-field term-meta-text-wrap">
				<label for="term-meta-text"><?php _e( 'Type', 'nbt-solution' ); ?></label>
				<select name="brands_type" id="brands_type" class="postform">
					<?php if(self::_get()){
						foreach (self::_get() as $key => $value) {
							?>
							<option value="<?php echo $key;?>"><?php echo $value;?></option>
							<?php
						}
					}?>
				</select>
			</div>
			<div class="form-field term-meta-text-wrap">
				<label for="term-meta-text"><?php _e( 'Target', 'nbt-solution' ); ?></label>
				<select name="brands_target" id="brands_target" class="postform">
					<?php if(self::_open()){
						foreach (self::_open() as $key => $value) {
							?>
							<option value="<?php echo $key;?>"><?php echo $value;?></option>
							<?php
						}
					}?>
				</select>
			</div>
			<div class="form-field term-meta-text-wrap">
				<label for="term-meta-text"><?php _e( 'URL', 'nbt-solution' ); ?></label>
				<input type="text" name="brands_url" id="term-meta-text" value="" class="term-meta-text-field" />
			</div>
		<?php }

		public static function ___edit_form_field_term_meta_text(){
			$term_id = absint( $_REQUEST['tag_ID'] );

			$brands_type = get_term_meta( $term_id, 'brands_type', true );
			$brands_type = self::sanitize_term_meta_text( $brands_type );
			if ( ! $brands_type ){
				$brands_type = "";
			}

			$brands_thumbnail = get_term_meta( $term_id, 'brands_thumbnail', true );
			$brands_thumbnail = self::sanitize_term_meta_text( $brands_thumbnail );
			if ( ! $brands_thumbnail ){
				$brands_thumbnail = "";
			}

			$brands_url = get_term_meta( $term_id, 'brands_url', true );
			$brands_url = self::sanitize_term_meta_text( $brands_url );
			if ( ! $brands_url ){
				$brands_url = "";
			}

			?>

			<tr class="form-field term-meta-text-wrap">
				<th scope="row"><label for="term-meta-text"><?php _e( 'Logo', 'nbt-solution' ); ?></label></th>
				<td>
					<?php wp_nonce_field( basename( __FILE__ ), 'term_meta_text_nonce' ); ?>
					<?php
					$image = $brands_thumbnail ? wp_get_attachment_image_src( $brands_thumbnail ) : '';
					$image = $image ? $image[0] : NBT_BRANDS_URL . '/images/placeholder.png';
					?>
					<div class="nbtcs-wrap-image">
						<div class="nbtcs-term-image-thumbnail" style="float:left;margin-right:10px;">
							<img src="<?php echo esc_url( $image ) ?>" width="60px" height="60px" />
						</div>
						<div style="line-height:60px;">
							<input type="hidden" class="nbtcs-term-image" name="brands_thumbnail" value="<?php echo esc_attr( $brands_thumbnail ) ?>" />
							<button type="button" class="nbtcs-upload-image-button button"><?php esc_html_e( 'Upload/Add image', 'nbt-solution' ); ?></button>
							<button type="button" class="nbtcs-remove-image-button button <?php echo $brands_thumbnail ? '' : 'hidden' ?>"><?php esc_html_e( 'Remove image', 'nbt-solution' ); ?></button>
						</div>
					</div>
				</td>
			</tr>


			<tr class="form-field term-meta-text-wrap">
				<th scope="row"><label for="term-meta-text"><?php _e( 'Type', 'nbt-solution' ); ?></label></th>
				<td>
					<?php wp_nonce_field( basename( __FILE__ ), 'term_meta_text_nonce' ); ?>
					<select name="brands_type" id="brands_type" class="postform">
						<?php if(self::_get()){
							foreach (self::_get() as $key => $value) {
								?>
								<option value="<?php echo $key;?>"<?php selected( $key, $brands_type); ?>><?php echo $value;?></option>
								<?php
							}
						}?>
					</select>
				</td>
			</tr>
			<tr class="form-field term-meta-text-wrap">
				<th scope="row"><label for="term-meta-text"><?php _e( 'Target', 'nbt-solution' ); ?></label></th>
				<td>
					<?php wp_nonce_field( basename( __FILE__ ), 'term_meta_text_nonce' ); ?>
					<select name="brands_target" id="brands_target" class="postform">
						<?php if(self::_open()){
							foreach (self::_open() as $key => $value) {
								?>
								<option value="<?php echo $key;?>"<?php selected( $key, $brands_type); ?>><?php echo $value;?></option>
								<?php
							}
						}?>
					</select>
				</td>
			</tr>
			<tr class="form-field term-meta-text-wrap">
				<th scope="row"><label for="term-meta-text"><?php _e( 'URL', 'nbt-solution' ); ?></label></th>
				<td>
					<?php wp_nonce_field( basename( __FILE__ ), 'term_meta_text_nonce' ); ?>
					<input type="text" name="brands_url" id="term-meta-text" value="<?php echo $brands_url;?>" class="term-meta-text-field" />
				</td>
			</tr>
			<?php
		}

		public static function ___save_term_meta_text( $term_id ) {
			// verify the nonce --- remove if you don't care
			if ( ! isset( $_POST['term_meta_text_nonce'] ) || ! wp_verify_nonce( $_POST['term_meta_text_nonce'], basename( __FILE__ ) ) )
				return;

			if(isset($_POST['brands_thumbnail'])){
				update_term_meta( $term_id, 'brands_thumbnail', $_POST['brands_thumbnail'] );
			}
			


			$brands_type = get_term_meta( $term_id, 'brands_type', true );
			$brands_type = self::sanitize_term_meta_text( $brands_type );

			$brands_type_new = isset( $_POST['brands_type'] ) ? self::sanitize_term_meta_text ( $_POST['brands_type'] ) : '';
			if ( $brands_type && '' === $brands_type_new ){
				delete_term_meta( $term_id, 'brands_type' );
			}
			else if ( $brands_type !== $brands_type_new ){
				update_term_meta( $term_id, 'brands_type', $brands_type_new );
			}

			$brands_target = get_term_meta( $term_id, 'brands_target', true );
			$brands_target = self::sanitize_term_meta_text( $brands_target );

			$brands_target_new = isset( $_POST['brands_target'] ) ? self::sanitize_term_meta_text ( $_POST['brands_target'] ) : '';
			if ( $brands_target && '' === $brands_target_new ){
				delete_term_meta( $term_id, 'brands_target' );
			}
			else if ( $brands_target !== $brands_target_new ){
				update_term_meta( $term_id, 'brands_target', $brands_target_new );
			}

			$brands_url = get_term_meta( $term_id, 'brands_url', true );
			$brands_url = self::sanitize_term_meta_text( $brands_url );

			$brands_url_new = isset( $_POST['brands_url'] ) ? self::sanitize_term_meta_text ( $_POST['brands_url'] ) : '';
			if ( $brands_url && '' === $brands_url_new ){
				delete_term_meta( $term_id, 'brands_url' );
			}
			else if ( $brands_url !== $brands_url_new ){
				update_term_meta( $term_id, 'brands_url', $brands_url_new );
			}
		}

		public static function ___edit_term_columns( $columns ) {
			$columns['brands_type'] = __( 'Type', 'nbt-solution' );
			return $columns;
		}

		public static function ___manage_term_custom_column( $out, $column, $term_id ) {

			if ( 'brands_type' === $column ) {
				$brands_type = get_term_meta( $term_id, 'brands_type', true );
				$brands_type = self::sanitize_term_meta_text( $brands_type );

				$type = self::_get();

				if(!$brands_type){
					$brands_type = 'default';
				}
				echo $type[$brands_type];
		   
			   
			}
		}

		public static function add_columns_thumbnail( $columns ) {
			$new_columns          = array();
			$new_columns['cb']    = $columns['cb'];
			$new_columns['brands_thumbnail'] = '';
			unset( $columns['cb'] );
			unset( $columns['description'] );

			return array_merge( $new_columns, $columns );
		}

		public static function add_columns_thumbnail_content( $columns, $column, $term_id ) {

			if ( $column == 'brands_thumbnail' ) {
				$brands_thumbnail = get_term_meta( $term_id, 'brands_thumbnail', true );
				$brands_thumbnail = self::sanitize_term_meta_text( $brands_thumbnail );
				?>
	<style>
	.column-brands_thumbnail .swatch-preview {
		width: 80px;
		height: auto;
		line-height: 80px;
		text-align: center;
		font-weight: 700;
		border: 1px solid #e6e6e6;
	}
	#brands_thumbnail{
		width: 60px;
	}
	</style>
				<?php

				$image = $brands_thumbnail ? wp_get_attachment_image_src( $brands_thumbnail ) : '';
				$image = $image ? $image[0] : NBT_BRANDS_URL . '/images/placeholder.png';
				printf( '<img class="swatch-preview swatch-image" src="%s" width="44px" height="44px">', esc_url( $image ) );
		
			}
		
		}
	}

}