<form action="<?php echo admin_url('admin.php?page='.WooPanel_Price_Matrix::$plugin_id) ?>" method="post" >
	<table class="form-table">
		<tbody>
		<?php foreach ($all_settings as $key => $set) {
			echo WooPanel_Modules_Metabox::show_field($set, $options);
		}?>
		</tbody>
	</table>
	<div class="submit">
		<?php wp_nonce_field( 'plugin-settings' ) ?>
		<input type="submit" name="submit-<?php echo esc_attr($key_id);?>" value="<?php esc_html_e('Save Changes', 'woopanel' ) ?>" class="button-primary" />
	</div>
</form>