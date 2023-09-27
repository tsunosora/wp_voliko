<?php
/**
 * View Create/Update Post
 */
global $wp_query;

if ( $store_id > 0 ) {
	$store_user = WooDashboard()->store->get( $store->name );
}

wpl_print_notices();
?>
<form class="m-form m-form--label-align-left- m-form--state-" name="post" method="post" id="post">
	<input type="hidden" name="storeID" id="storeID" value="<?php echo $store_id;?>" />
	<div class="row">
		<div class="col col-main">
			<input type="hidden" name="_type" value="<?php echo esc_attr($type);?>">



			<!--Begin::Main Portlet-->
			<div class="m-portlet">
				<!--begin: Portlet Head-->
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								<?php echo esc_html__( 'Store Banner', 'woopanel' ); ?>
							</h3>
						</div>
					</div>
				</div>
				<!--end: Portlet Head-->

				<div class="m-portlet__body">
					<div class="form-group m-form__group type-file">
						<label for="banner_id" class=""><?php esc_html_e('Banner', 'woopanel' );?></label>
						<div><?php woopanel_attachment_image($store->banner_id, true, false, 'banner_id', 'full', array(
							'fullwidth' => true,
							'height' => 250
						));?></div>
					</div>
				</div>
			</div>






			<!--Begin::Main Portlet-->
			<div class="m-portlet">
				<!--begin: Portlet Head-->
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								<?php echo esc_attr( $label ); ?>
								<?php if ( $store_id > 0 && isset($wp_query->query_vars['store_user']) ) { ?>
									<a href="<?php echo esc_url( $wp_query->query_vars['store_user']->get_url() );?>" title="<?php echo esc_attr($store->title);?>" target="_blank" class="btn btn-accent m-btn m-btn--icon m-btn--icon-only m-btn--pill m--margin-left-10">
										<i class="la la-external-link"></i>
									</a>
								<?php } ?>
							</h3>
						</div>
					</div>
				</div>
				<!--end: Portlet Head-->

				<div class="m-portlet__body">
					<div class="row">
						<div class="col-lg-12">
							<div class="m-form__section  m--margin-top-20 m--margin-bottom-20">
								<?php
									woopanel_form_field(
									'title', 
									[
										'type'			=> 'text',
										'id'			=> 'title',
										'label'			=> esc_html__('Name', 'woopanel' ),
										'input_class'	=> ['form-control m-input']
									], $store->title ); ?>

								<?php
									woopanel_form_field(
									'phone', 
									[
										'type'			=> 'text',
										'id'			=> 'phone',
										'label'			=> esc_html__('Phone', 'woopanel' ),
										'input_class'	=> ['form-control m-input']
									], $store->phone ); ?>

								<?php
									woopanel_form_field(
									'street', 
									[
										'type'			=> 'text',
										'id'			=> 'street',
										'label'			=> esc_html__('Street', 'woopanel' ),
										'input_class'	=> ['form-control m-input']
									], $store->street ); ?>

								<div class="row">
									<div class="col-3">
										<?php
											woopanel_form_field(
											'city', 
											[
												'type'			=> 'text',
												'id'			=> 'city',
												'label'			=> esc_html__('City', 'woopanel' ),
												'input_class'	=> ['form-control m-input']
											], $store->city ); ?>
									</div>

									<div class="col-3">
										<?php
										woopanel_form_field(
										'state', 
										[
											'type'			=> 'text',
											'id'			=> 'state',
											'label'			=> esc_html__('State', 'woopanel' ),
											'input_class'	=> ['form-control m-input']
										], $store->state ); ?>
									</div>

									<div class="col-3">
										<?php
										woopanel_form_field(
										'postal_code', 
										[
											'type'			=> 'number',
											'id'			=> 'postal_code',
											'label'			=> esc_html__('Postal Code', 'woopanel' ),
											'input_class'	=> ['form-control m-input']
										], $store->postal_code ); ?>
									</div>

									<div class="col-3">
										<?php
											woopanel_form_field(
												'country',
												array(
													'type'	  => 'select',
													'id'      => 'country',
													'options' => $countries,
													'label'   => esc_html__( 'Country', 'woopanel' ),
												),
												$store->country
											);
										?>
									</div>
								</div>


								<!-- edit slug -->
								<div class="inside"></div>

								<Div id="map_canvas" class="map_canvas"></Div>

								<div class="row">
									<div class="col-6">
										<?php
										woopanel_form_field(
										'lat', 
										[
											'type'			=> 'text',
											'id'			=> 'lat',
											'label'			=> esc_html__('Lat', 'woopanel' ),
											'input_class'	=> ['form-control m-input']
										], $store->lat ); ?>
									</div>

									<div class="col-6">
										<?php
										woopanel_form_field(
										'lng', 
										[
											'type'			=> 'text',
											'id'			=> 'lng',
											'label'			=> esc_html__('Lng', 'woopanel' ),
											'input_class'	=> ['form-control m-input']
										], $store->lng ); ?>
									</div>
								</div>




								<?php
									global $current_user;

									woopanel_form_field(
										'user_id',
										array(
											'type'	  => 'hidden',
											'id'      => 'user_id',
											'label'   => esc_html__( 'Users', 'woopanel' ),
										),
										$current_user->ID
									);
								?>

								<?php do_action("woopanel_{$type}_edit_form_after", $comment);?>
								
							</div>
						</div>
					</div>
				</div>

			</div>
			<!--End::Main Portlet-->

			<!--Begin::Main Portlet-->
			<div class="m-portlet">
				<!--begin: Portlet Head-->
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								<?php echo esc_html__( 'Extra Information', 'woopanel' ); ?>
							</h3>
						</div>
					</div>
				</div>
				<!--end: Portlet Head-->

				<div class="m-portlet__body">
					<?php
					woopanel_form_field( 'intro', [
							'type'	=> 'wpeditor',
							'label'	=> esc_html__('Introduction', 'woopanel' ),
							'id'	=> 'intro',
							'class'	=> ['post_excerpt'],
							'settings'	=> [
								'textarea_rows'	=> 5,
								'media_buttons'	=> false,
								'tinymce'		=> true,
                                'quicktags'     => false
							]
						], $store->intro
					);?>

					<?php
					woopanel_form_field( 'tos', [
							'type'	=> 'wpeditor',
							'label'	=> esc_html__('TOS', 'woopanel' ),
							'id'	=> 'tos',
							'class'	=> ['post_excerpt'],
							'settings'	=> [
								'textarea_rows'	=> 5,
								'media_buttons'	=> false,
								'tinymce'		=> true,
                                'quicktags'     => false
							]
						], $store->tos
					);?>
				</div>
			</div>


			<?php
			if( has_action("woopanel_{$type}_form_fields") ) {
				do_action("woopanel_{$type}_form_fields", $comment);
			}?>

		</div>
		
		<div class="col-xs-12 col-sm-12 col-sidebar">
			<div class="m-portlet">
				<div class="m-portlet__body">
					<div class="form-group m-form__group type-file">
						<label for="logo_id" class=""><?php esc_html_e('Logo', 'woopanel' );?></label>
						<div><?php woopanel_attachment_image($store->logo_id, true, false, 'logo_id');?></div>
					</div>

					<div class="form-group m-form__group type-file">
						<label for="logo_id" class=""><?php esc_html_e('Category', 'woopanel' );?></label>
						<?php woopanel_store_category_checkboxtree_metabox($store_id);?>
					</div>
				</div>

				<div class="m-portlet__foot">					
					<div id="publishing-actions">
						<div id="publishing-action">
							<button type="submit" class="btn btn-primary m-btn m-loader--light m-loader--right" id="publish" onclick="if(!this.classList.contains('m-loader')) this.className+=' m-loader';">
								<?php
								if ( $store_id > 0 ) {
									esc_html_e('Update', 'woopanel' );
								}else {
									esc_html_e('Publish', 'woopanel' );
								}?>
							</button>
						</div>
					</div>
					<!--end: Form Body -->
				</div>
			</div>
		</div>
	</div>
</form>