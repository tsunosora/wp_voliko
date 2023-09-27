<form action="<?php echo WooPanel_Price_Matrix::$redirectURL;?>" method="POST">
	<p style="font-style: italic;"><?php esc_html_e('Please buy PRO version, if you purchased this product on CodeCanyhon, press button!', 'woopanel' );?></p>
	<input type="submit" id="wppm_connect_envato" value="<?php esc_html_e('Connect Envato', 'woopanel' );?>" class="button button-primary">
	<input type="hidden" name="redirect_uri" value="<?php echo admin_url( 'admin.php?page=' . WooPanel_Price_Matrix::$plugin_id );?>" />
</form>