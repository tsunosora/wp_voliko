<?php
/**
 * View Create/Update Post
 */
global $current_user, $_wp_post_type_features;

$post_type_feature = $_wp_post_type_features[$post_type];
?>

<form class="m-form m-form--label-align-left- m-form--state-" name="post" method="post" id="post">
	<?php wp_nonce_field($nonce_action); ?>
	<input type="hidden" id="user-id" name="user_ID" value="<?php echo (int) $user_ID ?>" />
	<input type="hidden" id="hiddenaction" name="action" value="<?php echo esc_attr( $form_action ) ?>" />
	<input type="hidden" id="post_author" name="post_author" value="<?php echo esc_attr( $post->post_author ); ?>" />
	<input type="hidden" id="post_type" name="nb_post_type" value="<?php echo esc_attr( $post_type ) ?>" />
	<input type="hidden" id="referredby" name="referredby" value="<?php print($sendback ? esc_url( $sendback ) : ''); ?>" />

	<?php if ($post_id > 0) 
		echo '<input type="hidden" id="post_ID" name="post_ID" value="'. absint($post_id) .'" />
		<input type="hidden" id="original_post_title" name="original_post_title" value="' . esc_attr( $post->post_title ) . '" />'; ?>

	<div class="row">
		<div class="col col-main">

			<!--Begin::Main Portlet-->
			<div class="m-portlet">

				<!--begin: Portlet Head-->
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								<?php echo esc_html( $title ); ?>
								<?php if ($post_id > 0 && $preview) { ?>
									<a href="<?php the_permalink($post_id);?>" title="<?php echo esc_attr($post_type_object->labels->view_item);?>" target="_blank" class="btn btn-accent m-btn m-btn--icon m-btn--icon-only m-btn--pill m--margin-left-10">
										<i class="la la-external-link"></i>
									</a>
								<?php } ?>
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

						<?php if ( ($post_id > 0) && current_user_can( $post_type_object->cap->create_posts ) ) { ?>
							<a href="<?php echo esc_html( woopanel_post_new_url($post_type) );?>" class="btn btn-secondary m-btn m-btn--icon m-btn--md m--margin-right-10">
								<span>
									<i class="flaticon-add"></i>
									<span><?php echo esc_html( $post_type_object->labels->add_new); ?></span>
								</span>
							</a>
						<?php } ?>

						<?php if ( current_user_can( $post_type_object->cap->create_posts ) ) { ?>
							<div class="btn-group">
								<button type="submit" class="btn btn-primary m-btn m-btn--icon m-btn--wide m-btn--md m-loader--light m-loader--right" onclick="if(!this.classList.contains('m-loader')) this.className+=' m-loader';">
									<span>
										<i class="la la-check"></i>
										<span><?php echo esc_attr($submit_text);?></span>
									</span>
								</button>
							</div>
						<?php } ?>

					</div>
				</div>
				<!--end: Portlet Head-->

				<div class="m-portlet__body">
					<div class="row">
						<div class="col-lg-12">
							<div class="m-form__section  m--margin-top-20 m--margin-bottom-20">
								<?php
								if( $post_id > 0) {
									$post_action = 'edit';
								}else {
									$post_action = 'add';
								}

								do_action("woopanel_{$post_type}_edit_form_before", $post_action, $post);

								woopanel_form_field(
								'post_title', 
								[
									'type'			=> 'text',
									'placeholder'	=> apply_filters( 'woopanel_{$post_type}_enter_title_here', esc_html__( 'Enter title here', 'woopanel' ) ),
									'id'			=> 'title',
									'input_class'	=> ['form-control-lg m-input--solid']
								], esc_html($post->post_title) ); ?>

								<!-- edit slug -->
								<div class="inside">
									<?php
									if( $permalink ) {
										print( $post->ID ? woopanel_get_permalink_html($post->ID) : '' );
									}
									?>
								</div>
	
								<?php
								if( isset($post_type_feature['excerpt']) ) {
								woopanel_form_field( 'excerpt',
									[
										'type'	=> 'wpeditor',
										'label'	=> esc_html__('Excerpt', 'woopanel' ),
										'description'=>esc_html__('Excerpts are optional hand-crafted summaries of your content that good for SEO.', 'woopanel' ),
										'id'	=> 'excerpt',
										'class'	=> ['post_excerpt'],
										'settings'	=> [
											'textarea_rows'	=> 5,
											'media_buttons'	=> false,
											'tinymce'		=> true,
                                            'quicktags'     => false
										]
									], $post->post_excerpt );
								}
								?>

								<?php
								if( $editor && isset($post_type_feature['editor']) ) {
									woopanel_form_field( 'content',
									[
											'type'			=> 'wpeditor',
											'label'			=> '',
											'description'	=> '',
											'placeholder'	=> '',
											'id'		=> 'post_content',
											'class'		=> ['post_content'],
											'settings'	=> [
												'textarea_rows'=>20
											]
									], $post->post_content );
								}?>

								<?php do_action("woopanel_{$post_type}_edit_form_after", $post_action, $post);?>
								
							</div>
						</div>
					</div>
				</div>

			</div>
			<!--End::Main Portlet-->

			<?php
            /**
             * Fires after all built-in meta boxes have been added.
             *
             * @since 1.1.0
             * @hook woopanel_{$post_type}_meta_boxes
             * @param {array} $metaboxes
             * @param {object} $post Post object.
             * @returns {array} $metaboxes
             */

			$meta_boxes = apply_filters( "woopanel_{$post_type}_meta_boxes", array() );

			usort($meta_boxes, function($a, $b) {
			    return $a['priority'] - $b['priority'];
			});

			if( ! empty($meta_boxes) && is_array($meta_boxes) ) {
				foreach ($meta_boxes as $metabox_id => $metabox) {
					if( ! empty($metabox['content']) ) {
						$metabox['id'] = $metabox_id;
						if( is_array( $metabox['content'] ) && is_callable( $metabox['content'] ) ) {
							woopanel_render_metaboxes( $metabox, $post );
						}else {
							if( function_exists($metabox['content'])) {
								woopanel_render_metaboxes( $metabox, $post );
							}
							
						}
					}
				}
			}

			if( has_action("woopanel_{$post_type}_form_fields") ) {
				do_action("woopanel_{$post_type}_form_fields", $post_id);
			}?>
		</div>
		
		<div class="col-xs-12 col-sm-12 col-sidebar">
			<div class="m-portlet">
				<div class="m-portlet__body">
					<?php do_action("woopanel_{$post_type}_edit_sidebar_before", $post_action, $post);?>
					<?php if( $thumbnail && isset($post_type_feature['thumbnail']) ) {?>
					<div class="form-group m-form__group type-file">
						<label for="thumbnail" class=""><?php esc_html_e('Featured Image', 'woopanel' );?></label>
						<?php woopanel_attachment_image($thumbnail_id);?>
					</div>
					<?php }?>
						
						<?php
						if( $gallery && isset($product_image_gallery) ) { ?>
						<div class="form-group m-form__group type-files">
							<label for="images" class=""><?php esc_html_e('Gallery Images', 'woopanel' );?></label>
							<?php woopanel_gallery_images($product_image_gallery);?>
						</div>
						<?php }?>

						<?php if( $post_type_cat ) { ?>
						<div id="categorydiv" class="postbox">
							<label><?php esc_html_e('Categories', 'woopanel' );?></label>
							<?php woopanel_taxonomies_checkboxtree_metabox($post_id, $post_type_cat, $post_type_cat); ?>
						</div>
						<?php }?>

						<?php if( $post_tags ) { ?>
						<div id="tagsdiv-<?php echo esc_attr($post_tags);?>" class="postbox">
							<label><?php esc_html_e('Tags', 'woopanel' );?></label>
							<?php woopanel_taxonomies_tags_metabox($post_id, $post_tags, $post_tags); ?>
						</div>
						<?php }?>

						<?php woopanel_form_field(
							'post_status',
							[
								'type'	=> 'select',
								'label'	=> esc_html__( 'Status', 'woopanel' ),
								'id'	=> 'post_status',
								'options'	=> $post_statuses
							], $post->post_status );
						do_action("woopanel_{$post_type}_edit_sidebar_after", $post_action, $post);?>
				</div>

				<div class="m-portlet__foot">					
					<div id="publishing-actions">
						<div id="publishing-action">
							<button type="submit" class="btn btn-primary m-btn m-loader--light m-loader--right" id="publish" onclick="if(!this.classList.contains('m-loader')) this.className+=' m-loader';">
								<?php echo esc_attr($submit_text);?>
							</button>
						</div>
						<?php if ( current_user_can( 'delete_post', $post_id ) ) : ?>
							<div id="delete-action">
								<?php
								if ( EMPTY_TRASH_DAYS && MEDIA_TRASH ) {
									echo "<a class='btn btn-link submitdelete deletion' href='" . woopanel_post_delete_url( $post_id ) . "'>" . _x( 'Trash', 'verb' ) . "</a>";
								} else {
									$delete_ays = ! MEDIA_TRASH ? " onclick='return showNotice.warn();'" : '';
									echo  "<a class='btn btn-link submitdelete deletion'$delete_ays href='" . woopanel_post_delete_url( $post_id, null, true ) . "'>" . esc_html__( 'Delete Permanently', 'woopanel' ) . "</a>";
								} ?>
							</div>
						<?php endif;?>
					</div>
					<!--end: Form Body -->
				</div>
			</div>
		</div>
	</div>
</form>