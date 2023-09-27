<?php
global $woopanel_options;

$user_ID = get_current_user_id();
$nonce_action = 'update_options';
?>
<form name="post" method="post" id="post" class="m-form" data-page="tera-wallet">
	<input type="hidden" name="user_ID" value="<?php echo (int) $user_ID ?>" />
	<?php wp_nonce_field($nonce_action); ?>
	<div class="m-portlet" id="main_portlet">
		<div class="m-portlet__head m-portlet__head-ticket">
			<div class="m-portlet__head-caption">
				<div class="m-portlet__head-title">
					<span class="m-portlet__head-icon">
						<i class="flaticon-cogwheel-2 mr10"></i>
					</span>
					<h3 class="m-portlet__head-text">
    					<?php esc_html_e('Email Settings', 'woopanel' );?>
					</h3>
				</div>
			</div>
			<div class="m-portlet__head-tools">
				<button type="submit" name="save" class="btn btn-accent m-btn m-btn--icon m-btn--wide m-btn--md m-loader--light m-loader--right m--margin-right-10" onclick="if(!this.classList.contains('m-loader')) this.className+=' m-loader';">
					<?php esc_html_e( 'Update', 'woopanel' );?>
				</button>
			</div>
		</div>

		<div class="m-portlet__body">
			<div class="row">


				<div class="col-xl-12">
					<div class="m-tabs-content" id="m_sections">
						<?php
						foreach ($fields as $field) {?>
						<div class="m-tabs-content__item m-tabs-content__item--active">
							<?php
							woopanel_form_field(
								$field['id'], 
								$field,
                                woopanel_get_option($field['id'])
                            );?>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		
		<div class="m-portlet__foot">
			<div class="row">
				<div class="col-xl-9 offset-xl-3">
					<button type="submit" name="save" class="btn btn-accent m-btn m-btn--wide m-btn--md m-loader--light m-loader--right" onclick="if(!this.classList.contains('m-loader')) this.className+=' m-loader';">
						<?php esc_html_e( 'Update', 'woopanel' );?>
					</button>
				</div>
			</div>
		</div>
	</div>
</form>

<?php do_action('woopanel_setting_footer');?>