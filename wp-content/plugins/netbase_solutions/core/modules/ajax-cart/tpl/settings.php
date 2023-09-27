<?php
$settings = get_option('nbt_ajax_cart_settings' );
$all_settings = NBT_Ajax_Cart_Settings::get_settings();
$options = get_option(NBT_Solutions_Ajax_Cart::$plugin_id.'_settings');

if(isset($_POST['submit-ajaxcart'])){
	$options = array();
	foreach ($all_settings as $key => $value) {
		$id = $value['id'];
		if(isset($_POST[$id])){
			$options[$id] = $_POST[$id];
		}
	}
	?>
	<div id="setting-error-settings_updated" class="updated settings-error">
		<p><strong><?php _e('Settings Saved', 'nbt-solution') ?></strong></p>
	</div>
	<?php
	update_option(NBT_Solutions_Ajax_Cart::$plugin_id.'_settings', $options);
}

?>
    <form action="<?php echo admin_url('admin.php?page=nbt-ajax-cart') ?>" method="post" >
    	<table class="form-table">
    		<tbody>
			<?php foreach ($all_settings as $key => $set) {
				echo NBT_Solutions_Metabox::show_field($set, $options);
			}?>
			</tbody>
		</table>
		<div class="submit">
			<?php wp_nonce_field( 'plugin-settings' ) ?>
			<input type="submit" name="submit-ajaxcart" value="<?php _e('Save Changes', 'nbt-solution') ?>" class="button-primary" />
		</div>
	</form>