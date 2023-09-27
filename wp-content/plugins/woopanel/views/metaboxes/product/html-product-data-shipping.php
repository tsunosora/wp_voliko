<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="shipping_product_data" class="m-tabs-content__item">
	<div class="options_group">
		<?php
		if ( wc_product_weight_enabled() ) {
			woopanel_form_field(
				'_weight',
				array(
					'id'          => '_weight',
					'type'        => 'text',
					'label'       => esc_html__( 'Weight', 'woopanel' ) . ' (' . get_option( 'woocommerce_weight_unit' ) . ')',
					'placeholder' => wc_format_localized_decimal( 0 ),
					'desc_tip'    => true,
					'description' => esc_html__( 'Weight in decimal form', 'woopanel' ),
					'type'        => 'text',
					'data_type'   => 'decimal',
					'form_inline' => true
				),
				$product_object->get_weight( 'edit' )
			);
		}

		if ( wc_product_dimensions_enabled() ) {
			?>
			<div class="form-group m-form__group type-text row" id="_dimensions_field" data-priority="">
				<?php /* translators: WooCommerce dimension unit*/ ?>
				<label class="col-3 col-form-label" for="product_length"><?php printf( esc_html__( 'Dimensions (%s)', 'woopanel' ), get_option( 'woocommerce_dimension_unit' ) ); ?></label>
				
				<div class="col-9">
					<div class="row">
						<div class="col-3">
							<input id="product_length" placeholder="<?php esc_attr_e( 'Length', 'woopanel' ); ?>" class="form-control m-input wc_input_decimal" size="6" type="text" name="_length" value="<?php echo esc_attr( wc_format_localized_decimal( $product_object->get_length( 'edit' ) ) ); ?>" />
						</div>

						<div class="col-3">
							<input placeholder="<?php esc_attr_e( 'Width', 'woopanel' ); ?>" class="form-control m-input wc_input_decimal" size="6" type="text" name="_width" value="<?php echo esc_attr( wc_format_localized_decimal( $product_object->get_width( 'edit' ) ) ); ?>" />
						</div>

						<div class="col-3">
							<input placeholder="<?php esc_attr_e( 'Height', 'woopanel' ); ?>" class="form-control m-input wc_input_decimal last" size="6" type="text" name="_height" value="<?php echo esc_attr( wc_format_localized_decimal( $product_object->get_height( 'edit' ) ) ); ?>" />
						</div>
					</div>
					<span class="m-form__help" id="_dimensions-description" aria-hidden="true"><?php esc_html_e( 'LxWxH in decimal form', 'woopanel' );?></span>
				</div>
			</div>
			<?php
		}

		do_action( 'woocommerce_product_options_dimensions' );
		?>
	</div>

	<div class="options_group">
		<div class="form-group m-form__group type-select  row" id="product_shipping_class_field" data-priority="">
			<label for="product_shipping_class" class="col-3 col-form-label"><?php esc_html_e( 'Shipping class', 'woopanel' );?></label>
			<div class="col-9">
				<?php
				wp_dropdown_categories( array(
					'taxonomy'         => 'product_shipping_class',
					'hide_empty'       => 0,
					'show_option_none' => esc_html__( 'No shipping class', 'woopanel' ),
					'name'             => 'product_shipping_class',
					'id'               => 'product_shipping_class',
					'selected'         => $product_object->get_shipping_class_id( 'edit' ),
					'class'            => 'form-control m-input',
				) );?>
				<span class="m-form__help" id="product_shipping_class-description" aria-hidden="true"><?php esc_html_e( 'Shipping classes are used by certain shipping methods to group similar products.', 'woopanel' );?></span>
			</div>
		</div>
		<?php

		do_action( 'woocommerce_product_options_shipping' );
		?>
	</div>
</div>
