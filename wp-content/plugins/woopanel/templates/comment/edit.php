<?php
/**
 * View Create/Update Post
 */
?>
<form class="m-form m-form--label-align-left- m-form--state-" name="post" method="post" id="post">
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
								<?php echo esc_html( $comment->post_title ); ?>
								<?php if ( $comment->ID > 0 ) { ?>
									<a href="<?php the_permalink($comment->ID);?>" title="<?php echo esc_attr($comment->post_title);?>" target="_blank" class="btn btn-accent m-btn m-btn--icon m-btn--icon-only m-btn--pill m--margin-left-10">
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
									'comment_author', 
									[
										'type'			=> 'text',
										'id'			=> 'comment_author',
										'label'			=> esc_html__('Name', 'woopanel' ),
										'input_class'	=> ['form-control m-input']
									], $comment->comment_author ); ?>
								<?php
									woopanel_form_field(
									'comment_email', 
									[
										'type'			=> 'text',
										'id'			=> 'comment_email',
										'label'			=> esc_html__('Email', 'woopanel' ),
										'input_class'	=> ['form-control m-input']
									], $comment->comment_author_email ); ?>
								<?php
									woopanel_form_field(
									'comment_url', 
									[
										'type'			=> 'text',
										'id'			=> 'comment_url',
										'label'			=> esc_html__('URL', 'woopanel' ),
										'input_class'	=> ['form-control m-input']
									], $comment->comment_author_url ); ?>
								<!-- edit slug -->
								<div class="inside"></div>

								<?php
									woopanel_form_field( 'comment_content',
									[
											'type'			=> 'wpeditor',
											'label'			=> '',
											'description'	=> '',
											'placeholder'	=> '',
											'id'		=> 'comment_content',
											'class'		=> ['comment_content'],
											'settings'	=> [
												'textarea_rows'=>20,
												'media_buttons'	=> false,
												'tinymce'		=> true,
												'quicktags'     => false
											]
									], str_replace('\\', '', $comment->comment_content) );?>

								<?php do_action("woopanel_{$type}_edit_form_after", $comment);?>
								
							</div>
						</div>
					</div>
				</div>

			</div>
			<!--End::Main Portlet-->

			<?php
			if( has_action("woopanel_{$type}_form_fields") ) {
				do_action("woopanel_{$type}_form_fields", $comment);
			}?>

		</div>
		
		<div class="col-xs-12 col-sm-12 col-sidebar">
			<div class="m-portlet">
				<div class="m-portlet__body">
						<?php
						woopanel_form_field(
							'post_status',
							[
								'type'	=> 'select',
								'label'	=> esc_html__( 'Status', 'woopanel' ),
								'id'	=> 'post_status',
								'options'	=> $statuses
							], $status
						);
							?>
				</div>

				<div class="m-portlet__foot">					
					<div id="publishing-actions">
						<div id="publishing-action">
							<button type="submit" class="btn btn-primary m-btn m-loader--light m-loader--right" id="publish" onclick="if(!this.classList.contains('m-loader')) this.className+=' m-loader';">
								<?php esc_html_e('Update', 'woopanel' );?>
							</button>
						</div>
					</div>
					<!--end: Form Body -->
				</div>
			</div>
		</div>
	</div>
</form>