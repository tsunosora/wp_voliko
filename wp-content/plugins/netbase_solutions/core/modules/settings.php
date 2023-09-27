<div class="wrap" id="panels-settings-page">
	<h1><?php _e('Settings', 'nbt-solution-core') ?></h1>
	

	<?php
	foreach (self::$settings as $key => $setting) {
		echo '<h3>'.$setting['module_name'].'</h3>';
		if(isset($_POST['submit-'.$key])){
			$new_settings = array();

			foreach ($setting['settings'] as $value) {

				if(isset($value['id'])){
					$id = $value['id'];

					if($value['type'] == 'repeater'){
						reset($value['fields']);
						$first_key = key($value['fields']);

						$first_id = $value['fields'][$first_key]['id'];




						$post_new = array();
						foreach ($value['fields'] as $k => $f):
							$repeat_id = $f['id'];
							if($first_id != $repeat_id){
								foreach ($_POST[$repeat_id] as $k2 => $f2):
									$post_new[$repeat_id][] = $f2;

								endforeach;


							}
						endforeach;



						$n = array();
						foreach ($_POST[$first_id] as $k1 => $v):


							$x = array();
							foreach ($post_new as $k2 => $v2):
								$x[$k2] = $v2[$k1];
							endforeach;
							$n[$v] = array_merge(array(
								$first_id => $v
							), $x, apply_filters('metabox_extra_'.$id, array(), $_POST, $v, $k1));
						endforeach;

						$n = array_values($n);
						$new_settings[$id] = $n;

					}else{
						if(isset($_POST[$id])){
							$new_settings[$id] = $_POST[$id];
						}else{
							$new_settings[$id] = '';
						}
					}


				}
				
			}

			$module_name_set = $key.'_settings';

			$get_transient = get_transient( $module_name_set );

			delete_transient($module_name_set);

			?>
			<div class="notice notice-success is-dismissible ppec-dismiss-prompt-to-connect-message">
				<p>Successfully saved module <strong><?php echo $key;?></strong></p>
				<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
			</div>
			<?php

			update_option($module_name_set, $new_settings);

			if( $key == 'pdf-creator' && isset($_POST['nbt_' . NBT_Pdf_Creator_Settings::$id.'_pdf_preview_page']) ) {
				$pdf_page_id = (int)$_POST['nbt_' . NBT_Pdf_Creator_Settings::$id.'_pdf_preview_page'];
				update_option( '_create_page_pdf', $pdf_page_id);
				update_post_meta($pdf_page_id, '_wp_page_template', 'preview.php');
			}
		}
		$options = NB_Solution::get_setting($key);
		?>
    <form id="frm-<?php echo $key;?>" action="<?php echo admin_url('admin.php?page=solutions-settings&modules='.$key) ?>" method="post" >
    	<table class="form-table">
    		<tbody>
			<?php
			foreach ($setting['settings'] as $key_set => $set) {
				$_value = '';
				if( isset($set['id']) && isset($options[$set['id']]) ) {
					$_value = $options[$set['id']];
				}
				echo NBT_Solutions_Metabox::show_field($set, $_value);
			}?>
			</tbody>
		</table>
		<div class="submit">
			<?php wp_nonce_field( 'solution-settings' ) ?>
			<input type="submit" name="submit-<?php echo $key;?>" value="<?php _e('Save Changes', 'nbt-solution-core') ?>" class="button-primary" />
		</div>
	</form>
	<?php }?>

</div>