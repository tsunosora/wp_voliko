	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_address"><?php esc_html_e( 'Street address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<textarea rows="4" class="woocommerce-Input woocommerce-Input--text input-text" name="account_address" id="account_address" value="<?php echo esc_attr( $my_account['address'] ); ?>"></textarea>
	</p>


	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_city"><?php esc_html_e( 'Town / City', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_city" id="account_city" value="<?php echo esc_attr( $my_account['city'] ); ?>" />
	</p>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_city"><?php esc_html_e( 'Country / Region', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<select name="<?php echo esc_attr( $value['id'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>" data-placeholder="<?php esc_attr_e( 'Choose a country / region&hellip;', 'woocommerce' ); ?>" aria-label="<?php esc_attr_e( 'Country / Region', 'woocommerce' ); ?>" class="wc-enhanced-select">
			<?php WC()->countries->country_dropdown_options( $country, $state ); ?>
		</select>
	</p>
	

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_phone"><?php esc_html_e( 'Phone', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_phone" id="account_phone" value="<?php echo esc_attr( $my_account['phone'] ); ?>" />
	</p>