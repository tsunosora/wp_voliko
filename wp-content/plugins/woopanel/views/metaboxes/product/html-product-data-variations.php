<?php
/**
 * Product data variations
 *
 * @package WooCommerce\Admin\Metaboxes\Views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="variable_product_options" class="m-tabs-content__item">
	<?php if ( ! count( $variation_attributes ) ) { ?>
		<div class="m-alert m-alert--outline alert alert-success alert-dismissible fade show" role="alert">
			<p><?php echo wp_kses(
				sprintf(
					esc_html__( 'Before you can add a variation you need to add some variation attributes on the %s tab.', 'woopanel' ),
					'<strong>'.esc_html__('Attributes', 'woopanel' ).'</strong>'
				), array(
					'strong' => array()
				) ); ?></p>
			<p><a class="btn btn-success btn-sm" href="<?php echo esc_url( apply_filters( 'woocommerce_docs_url', 'https://docs.woocommerce.com/document/variable-product/', 'product-variations' ) ); ?>" target="_blank"><?php esc_html_e( 'Learn more', 'woopanel' ); ?></a></p>
		</div>
	<?php } else { ?>
	<div id="variable_product_options_inner" class="m-portlet m-portlet--full-height">
		<div class="m-portlet__head m-toolbar<?php if( count($variation_attributes) > 3 ) { echo ' m-variation-break-select';}?>">
			<div class="m-portlet__head-caption">
				<div class="m-portlet__head-title">
					<div class="row variations-defaults">
						<div class="col-variation col-variation-label">
							<strong><?php esc_html_e( 'Default Form Values', 'woopanel' ); ?>: <?php echo wc_help_tip( esc_html__( 'These are the attributes that will be pre-selected on the frontend.', 'woopanel' ) ); ?></strong>
						</div>
						
						<?php
						foreach ( $variation_attributes as $attribute ) {
							$selected_value = isset( $default_attributes[ sanitize_title( $attribute->get_name() ) ] ) ? $default_attributes[ sanitize_title( $attribute->get_name() ) ] : '';
							?>
							<div class="col-variation">
								<select class="form-control m-input" name="default_attribute_<?php echo esc_attr( sanitize_title( $attribute->get_name() ) ); ?>" data-current="<?php echo esc_attr( $selected_value ); ?>">
									<?php /* translators: WooCommerce attribute label */ ?>
									<option value=""><?php echo esc_html( sprintf( esc_html__( 'No default %s&hellip;', 'woopanel' ), wc_attribute_label( $attribute->get_name() ) ) ); ?></option>
									<?php if ( $attribute->is_taxonomy() ) : ?>
										<?php foreach ( $attribute->get_terms() as $option ) : ?>
											<option <?php selected( $selected_value, $option->slug ); ?> value="<?php echo esc_attr( $option->slug ); ?>"><?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $option->name ) ); ?></option>
										<?php endforeach; ?>
									<?php else : ?>
										<?php foreach ( $attribute->get_options() as $option ) : ?>
											<option <?php selected( $selected_value, $option ); ?> value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ); ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>

		<div class="m-portlet__head m-toolbar toolbar-top">
			<div class="m-portlet__head-caption">
				<div class="m-portlet__head-title">
					<select id="field_to_edit" class="variation_actions form-control m-input">
						<option data-global="true" value="add_variation" selected="selected"><?php esc_html_e( 'Add variation', 'woopanel' ); ?></option>
						<option data-global="true" value="link_all_variations"><?php esc_html_e( 'Create variations from all attributes', 'woopanel' ); ?></option>
						<option value="delete_all"><?php esc_html_e( 'Delete all variations', 'woopanel' ); ?></option>
					</select>
					<a class="btn btn-default m-btn bulk_edit do_variation_action"><?php esc_html_e( 'Go', 'woopanel' ); ?></a>
				</div>
			</div>
		</div>
		<div class="m-portlet__body">
			<?php $attributes_data = htmlspecialchars( wp_json_encode( wc_list_pluck( $variation_attributes, 'get_data' ) ) );?>
			<div class="woocommerce_variations wc-metaboxes m-accordion m-accordion--bordered<?php if( count($variation_attributes) > 3 ) { echo ' m-variation-break-select';}?>" data-attributes="<?php echo esc_attr($attributes_data); // WPCS: XSS ok. ?>" data-total="<?php echo esc_attr( $variations_count ); ?>" data-total_pages="<?php echo esc_attr( $variations_total_pages ); ?>" data-page="1" data-edited="false">
			</div>
		</div>

		<div class="m-portlet__head m-toolbar toolbar-bottom">
			<button type="button" class="btn btn-primary m-btn save_attributes button-primary save-variation-changes" disabled="disabled"><?php esc_html_e( 'Save changes', 'woopanel' ); ?></button>
			<div class="variations-pagenav">
				<?php /* translators: variations count */ ?>
				<span class="displaying-num"><?php echo esc_html( sprintf( _n( '%s item', '%s items', $variations_count, 'woopanel' ), $variations_count ) ); ?></span>
				<span class="pagination-links">
					<a class="prev-page disabled" title="<?php esc_attr_e( 'Go to the previous page', 'woopanel' ); ?>" href="#"><i class="la la-angle-double-left"></i></a>
					<span class="paging-select">
						<label for="current-page-selector-1" class="screen-reader-text"><?php esc_html_e( 'Select Page', 'woopanel' ); ?></label>
						<select class="page-selector form-control m-input" id="current-page-selector-1" title="<?php esc_attr_e( 'Current page', 'woopanel' ); ?>">
							<?php for ( $i = 1; $i <= $variations_total_pages; $i++ ) : ?>
								<option value="<?php echo esc_attr($i); // WPCS: XSS ok. ?>"><?php echo esc_attr($i); // WPCS: XSS ok. ?></option>
							<?php endfor; ?>
						</select>
						<?php echo esc_html_x( 'of', 'number of pages', 'woopanel' ); ?> <span class="total-pages"><?php echo esc_html( $variations_total_pages ); ?></span>
					</span>
					<a class="next-page" title="<?php esc_attr_e( 'Go to the next page', 'woopanel' ); ?>" href="#"><i class="la la-angle-double-right"></i></a>
				</span>
			</div>
		</div>
	</div>

	<?php }?>


</div>
