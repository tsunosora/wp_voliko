<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="general_product_data" class="m-tabs-content__item">

	<div class="options_group show_if_external">
		<?php
		
		woopanel_form_field(
			'_product_url',
			array(
				'id'          => '_product_url',
				'type'		  => 'text',
				'label'       => esc_html__( 'Product URL', 'woopanel' ),
				'placeholder' => 'http://',
				'description' => esc_html__( 'Enter the external URL to the product.', 'woopanel' ),
			),
			is_callable( array( $product_object, 'get_product_url' ) ) ? $product_object->get_product_url( 'edit' ) : ''
		);

		woopanel_form_field(
			'_button_text',
			array(
				'id'          => '_button_text',
				'type'		  => 'text',
				'label'       => esc_html__( 'Button text', 'woopanel' ),
				'placeholder' => _x( 'Buy product', 'placeholder', 'woopanel' ),
				'description' => esc_html__( 'This text will be shown on the button linking to the external product.', 'woopanel' ),
			),
			is_callable( array( $product_object, 'get_button_text' ) ) ? $product_object->get_button_text( 'edit' ) : ''
		);
		?>
	</div>

	<div class="options_group pricing show_if_simple show_if_external hidden">
		<?php

		woopanel_form_field(
			'_regular_price',
			array(
				'id'        => '_regular_price',
				'type'		=> 'text',
				'label'     => esc_html__( 'Regular price', 'woopanel' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'data_type' => 'price',
				'form_inline' => true
			),
			$product_object->get_regular_price( 'edit' )
		);

		woopanel_form_field(
			'_sale_price',
			array(
				'id'          => '_sale_price',
				'type'		  => 'text',
				'data_type'   => 'price',
				'label'       => esc_html__( 'Sale price', 'woopanel' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'wrapper_after' => '<a href="#" data-label-cancel="'. esc_html__('Cancel', 'woopanel' ) .'" data-label-text="' . esc_html__( 'Schedule', 'woopanel' ) . '" class="sale_schedule">' . esc_html__( 'Schedule', 'woopanel' ) . '</a>',
				'form_inline' => true
			),
			$product_object->get_sale_price( 'edit' )
		);

		$sale_price_dates_from = $product_object->get_date_on_sale_from( 'edit' ) && ( $date = $product_object->get_date_on_sale_from( 'edit' )->getOffsetTimestamp() ) ? date_i18n( 'Y-m-d', $date ) : '';
		$sale_price_dates_to   = $product_object->get_date_on_sale_to( 'edit' ) && ( $date = $product_object->get_date_on_sale_to( 'edit' )->getOffsetTimestamp() ) ? date_i18n( 'Y-m-d', $date ) : '';

		echo '<div class="sale_price_dates_fields">';
		woopanel_form_field(
			'_sale_price_dates_from',
			array(
				'id'                => '_sale_price_dates_from',
				'type'				=> 'datepicker',
				'label'             => esc_html__( 'Sale price dates', 'woopanel' ),
				'placeholder'       => esc_html__( _x( 'From&hellip;', 'placeholder', 'woopanel' ) ) . ' YYYY-MM-DD',
				'custom_attributes' => array(
					'maxlength' => '10',
					'pattern'  => esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) ),
				),
				'form_inline' => true
			),
			esc_attr( $sale_price_dates_from ) 
		);

		woopanel_form_field(
			'_sale_price_dates_to',
			array(
				'id'                => '_sale_price_dates_to',
				'type'				=> 'datepicker',
				'label'             => '&nbsp;',
				'placeholder'       => esc_html__( _x( 'To&hellip;', 'placeholder', 'woopanel' ) ) . ' YYYY-MM-DD',
				'custom_attributes' => array(
					'maxlength' => '10',
					'pattern'  => esc_attr( apply_filters( 'woocommerce_date_input_html_pattern', '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ) )
				),
				'form_inline' => true,
				//'wrapper_after' => '<a href="#" class="description cancel_sale_schedule">' . esc_html__( 'Cancel', 'woocommerce' ) . '</a>',
			),
			esc_attr( $sale_price_dates_to ) 
		);

		echo '</div>';
		?>
	</div>

	<div class="options_group show_if_downloadable hidden">
		<div class="form-field downloadable_files">
			<label><?php esc_html_e( 'Downloadable files', 'woopanel' ); ?></label>
			<table class="widefat">
				<thead>
					<tr>
						<th class="sort">&nbsp;</th>
						<th><?php esc_html_e( 'Name', 'woopanel' ); ?> <?php echo wc_help_tip( esc_html__( 'This is the name of the download shown to the customer.', 'woopanel' ) ); ?></th>
						<th colspan="2"><?php esc_html_e( 'File URL', 'woopanel' ); ?> <?php echo wc_help_tip( esc_html__( 'This is the URL or absolute path to the file which customers will get access to. URLs entered here should already be encoded.', 'woopanel' ) ); ?></th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$downloadable_files = $product_object->get_downloads( 'edit' );
					if ( $downloadable_files ) {
						foreach ( $downloadable_files as $key => $file ) {
							include WOODASHBOARD_VIEWS_DIR . 'metaboxes/product/html-product-download.php';
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="5">
							<a href="#" class="button insert" data-row="
							<?php
								$key  = '';
								$file = array(
									'file' => '',
									'name' => '',
								);
								ob_start();
								require 'html-product-download.php';
								echo esc_attr( ob_get_clean() );
							?>
							"><?php esc_html_e( 'Add File', 'woopanel' ); ?></a>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?php
		woopanel_form_field(
			'_download_limit',
			array(
				'id'                => '_download_limit',
				'type'				=> 'text',
				'value'             => -1 === $product_object->get_download_limit( 'edit' ) ? '' : $product_object->get_download_limit( 'edit' ),
				'label'             => esc_html__( 'Download limit', 'woopanel' ),
				'placeholder'       => esc_html__( 'Unlimited', 'woopanel' ),
				'description'       => esc_html__( 'Leave blank for unlimited re-downloads.', 'woopanel' ),
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => '1',
					'min'  => '0',
				),
			)
		);

		woopanel_form_field(
			'_download_expiry',
			array(
				'id'                => '_download_expiry',
				'type'				=> 'text',
				'value'             => -1 === $product_object->get_download_expiry( 'edit' ) ? '' : $product_object->get_download_expiry( 'edit' ),
				'label'             => esc_html__( 'Download expiry', 'woopanel' ),
				'placeholder'       => esc_html__( 'Never', 'woopanel' ),
				'description'       => esc_html__( 'Enter the number of days before a download link expires, or leave blank.', 'woopanel' ),
				'type'              => 'number',
				'custom_attributes' => array(
					'step' => '1',
					'min'  => '0',
				),
			)
		);

		do_action( 'woopanel_product_options_downloads' );
		?>
	</div>

	<?php if ( wc_tax_enabled() ) : ?>
		<div class="options_group show_if_simple show_if_external show_if_variable">
			<?php
			woopanel_form_field(
				'_tax_status',
				array(
					'id'          => '_tax_status',
					'type'		  => 'select',
					'value'       => $product_object->get_tax_status( 'edit' ),
					'label'       => esc_html__( 'Tax status', 'woopanel' ),
					'options'     => array(
						'taxable'  => esc_html__( 'Taxable', 'woopanel' ),
						'shipping' => esc_html__( 'Shipping only', 'woopanel' ),
						'none'     => _x( 'None', 'Tax status', 'woopanel' ),
					),
					'desc_tip'    => 'true',
					'description' => esc_html__( 'Define whether or not the entire product is taxable, or just the cost of shipping it.', 'woopanel' ),
				)
			);

			woopanel_form_field(
				'_tax_class',
				array(
					'id'          => '_tax_class',
					'type'		  => 'select',
					'value'       => $product_object->get_tax_class( 'edit' ),
					'label'       => esc_html__( 'Tax class', 'woopanel' ),
					'options'     => wc_get_product_tax_class_options(),
					'desc_tip'    => 'true',
					'description' => esc_html__( 'Choose a tax class for this product. Tax classes are used to apply different tax rates specific to certain types of product.', 'woopanel' ),
				)
			);

			do_action( 'woocommerce_product_options_tax' );
			?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woopanel_product_options_general_product_data' ); ?>
</div>
