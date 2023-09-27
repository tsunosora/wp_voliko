<div class="wrap" id="panels-settings-page">
	<h1><?php _e('NBT Solutions Core', 'nbt-solution-core') ?></h1>
	<?php if( self::$settings_saved ) : ?>
		<div id="setting-error-settings_updated" class="updated settings-error">
			<p><strong><?php _e('Settings Saved', 'siteorigin-panels') ?></strong></p>
		</div>
	<?php endif; ?>

    <form id="frm-solution-settings" action="<?php echo admin_url('admin.php?page=solutions') ?>" method="post" >
		<ul>
			<?php foreach ($register_modules as $key_modules => $modules) {
				$values = '';
				if(is_array($settings_modules) && in_array($key_modules, $settings_modules)){
					$values = $key_modules;
				}
				?>
				<li<?php if(isset($modules['hide'])) { echo ' style="display: none"';}?>><label for="<?php echo $key_modules;?>"><input type="checkbox" name="nbt_solutions_func[]" id="<?php echo $key_modules;?>" value="<?php echo $key_modules;?>" <?php if( isset($modules['hide']) && $modules['hide'] == 1 ) { echo ' checked'; }else { checked( ! empty( $values ) ); }?> /> <?php echo $modules['label'];?></label></li>
			<?php }?>
		</ul>
		<div class="submit">
			<input type="submit" value="<?php _e('Save Changes', 'nbt-solution-core') ?>" class="button-primary" />
			<input type="button" name="submit-export" value="<?php _e('Export Settings', 'nbt-solution-core') ?>" class="button submit-export" />
			<input type="button" name="submit-generator" value="<?php _e('Generator PHP', 'nbt-solution-core') ?>" class="button generator-php" />
		</div>
	</form>

	<div id="show_export_url" style="display: none; background: #fff; color: red; border: 1px solid #ccc; padding: 5px 10px;"></div>
	<textarea name="" style="display: none; width: 100%; height: 500px;" onfocus="this.select();" id="show_generator_php"></textarea>
</div>
<script>
jQuery(document).ready(function($) {
	$(document).on('click', '.submit-export', function(e) {
		e.preventDefault();


		
		
		var modules = [];
		$("#frm-solution-settings li input:checkbox:checked").each(function(){
			if( $(this).is(':checked') ) {
				modules.push( $(this).val() );
			}
		});
				
		$.ajax({
			url: '<?php echo admin_url('/admin-ajax.php');?>',
			data: 'action=nb_export_settings&modules=' + modules,
			type: 'POST',
			datatype: 'json',
			success: function( response ) {
				if( response.complete != undefined) {
					$('#show_export_url').show().html(response.url);
				}else {
					alert(response.message);
				}
			},
			error:function(){
				alert('There was an error when processing data, please try again !');
			}
		});
	});

	$(document).on('click', '.generator-php', function(e) {
		e.preventDefault();

		$('#show_generator_php').hide().empty();

		var modules = [];
		$("#frm-solution-settings li input:checkbox:checked").each(function(){
			if( $(this).is(':checked') ) {
				modules.push( $(this).val() );
			}
		});

		$.ajax({
			url: '<?php echo admin_url('/admin-ajax.php');?>',
			data: 'action=nb_generator_php&modules=' + modules,
			type: 'POST',
			datatype: 'json',
			success: function( response ) {
				if( response.complete != undefined) {
					$('#show_generator_php').show().html(response.content);
				}else {
					alert(response.message);
				}
			},
			error:function(){
				alert('There was an error when processing data, please try again !');
			}
		});

	});
});
</script>