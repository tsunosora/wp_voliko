<style>
._pm_show_on_field{
	margin-top: 0 !important
}
</style>

	<div id="price_matrix_options_inner" data-count="<?php echo esc_attr($count_attr);?>">

		<?php
		$_product_attributes = get_post_meta($post->ID, '_product_attributes', TRUE);
		if($count_attr >= 2){?>
		<div id="price_matrix_table">
			<form action="" method="POST" id="frm-price-matrix">
				<table class="pm_repeater">
					<thead>
						<tr>
							<th class="pm-row-zero"></th>
							<th class="pm-th"><?php esc_html_e('Attributes', 'woopanel' );?></th>
							<th class="pm-th"><?php esc_html_e('Direction', 'woopanel' );?></th>
							<th class="pm-row-zero"></th>
						</tr>
					</thead>

					<tbody>
						<?php
						if(isset($_pm_table_attr)){
							foreach ($_pm_table_attr as $key => $v_attr) {
								if(isset($_product_attributes[$v_attr])){?>
								<tr class="pm-row">
									<td class="pm-row-zero order">
										<span><?php echo ($key+1);?></span>
									</td>

									<td class="pm-field">
										<div class="pm-input">
											<div class="pm-input-wrap">
												<select class="form-control m-input pm-attributes-field" name="pm_attr[]" data-value="<?php echo esc_attr($v_attr);?>">
													<option value="0">(<?php esc_html_e('Select an Attributes', 'woopanel' );?>)</option>
												</select>
												<input type="text" name="attributes_hidden[]" class="attributes_hidden" style="display: none" />
											</div>
										</div>
									</td>
									<td class="pm-field">
										<div class="pm-input">
											<div class="pm-input-wrap">
												<select class="form-control m-input pm-direction-field" name="pm_direction[]" data-value="<?php echo esc_attr($_pm_table_direction[$key]);?>">
													<option value="vertical"><?php esc_html_e('Vertical', 'woopanel' );?></option>
													<option value="horizontal"><?php esc_html_e('Horizontal', 'woopanel' );?></option>
												</select>
											</div>
										</div>
									</td>
									<td class="pm-row-zero">
										<a class="pm-icon -plus small" href="#" data-event="add-row" title="<?php esc_html_e('Add row', 'woopanel' );?>"></a>
										<a class="pm-icon -minus small" href="#" data-event="remove-row" title="<?php esc_html_e('Remove row', 'woopanel' );?>"></a>
									</td>
								</tr>
							<?php }
							}
						}?>
					</tbody>
				</table>

				<input type="hidden" name="security" value="<?php echo wp_create_nonce( "_price_matrix_save" );?>" />
				<button type="button" class="button save_price_matrix button-primary"><?php esc_html_e('Save', 'woopanel' );?></button>
				<button type="button" class="button btn-enter-price"><?php esc_html_e('Input Price', 'woopanel' );?></button>
				<button type="button" class="button btn-order-attributes"><?php esc_html_e('Order Attributes', 'woopanel' );?></button>
			</form>
		</div>

		<?php }else {
			$class_btn = 'button-primary';
			$class_alert = 'inline notice woocommerce-message';
			if( class_exists('NBWooCommerceDashboard') && ! is_admin() ) {
				$class_alert = 'm-alert m-alert--outline alert alert-success alert-dismissible fade show';
				$class_btn = 'btn btn-success btn-sm';
			}?>
			<div class="<?php echo esc_attr($class_alert);?>" role="alert">
				<p><?php echo wp_kses(
				sprintf(
					esc_html__( 'Before you can add a variation you need to add some variation attributes on the %s tab.', 'woopanel' ),
					'<strong>'.esc_html__('Attributes', 'woopanel' ).'</strong>'
				), array(
					'strong' => array()
				) ); ?></p>
				<p><a class="<?php echo esc_attr($class_btn);?>" href="<?php echo esc_url( apply_filters( 'woocommerce_docs_url', 'https://docs.woocommerce.com/document/variable-product/', 'product-variations' ) ); ?>" target="_blank"><?php esc_html_e( 'Learn more', 'woopanel' ); ?></a></p>
			</div>
		<?php }?>
	</div>