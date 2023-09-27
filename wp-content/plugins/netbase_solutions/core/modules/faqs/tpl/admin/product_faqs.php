<div id="nbt-repeater" class="faq-product-type" style="<?php if(!$data){ echo 'display: none';}?>">
	<div id="nbt-repeater-wrap">
		<?php if($data){
			foreach ($data as $k_id => $value) {
				if(isset($value['heading'])){
					$type = '';
					if(is_array($value['heading'])){
						$faq = get_post($value['heading']['faq']);
						$type = ' <span class="type-heading">'.$faq->post_title.'</span>';
						$heading = $faq_id = $value['heading']['title'];
					}else{
						$lists = array();
						$heading = $value['heading'];
						if(isset($value['lists'])) {
							$lists = $value['lists'];
						}
					}

					include(NBT_FAQS_PATH . 'tpl/admin/data.php');
	
				}
			}
		}?>
	</div>
</div>


	<div class="nbt-repeater-action">
		<button type="button" class="nbt-heading-product-add button button-primary button-large"><?php _e('Add heading', 'nbt-solution');?></button>
		<button type="button" class="nbt-repeater-product-add button button-default button-large"><?php _e('Add row', 'nbt-solution');?></button>
	</div>


<script id="nbt-heading-template" type="text/template">
	<div class="repeater-row row-heading">
		<div class="nbt-repeater-order">
			<div class="faq-row">
				<div class="faq-col">
					<p>
						<label for="nbt-repeater-content-title"><?php _e('Type', 'nbt-solution');?>:</label>
						<select class="select-global-faqs js-select2" name="select_global_faqs[]">
							<option value="">No select</option>
							<?php
							global $post;
							$args = array(
								'post_type' => 'nbt_faq',
								'post_status' => 'publish'
							);
							$the_query = new WP_Query( $args );
							if ( $the_query->have_posts() ) :
								while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
								<option value="<?php echo $post->ID;?>"><?php echo $post->post_title;?></option>
								<?php endwhile;
								wp_reset_postdata();
							endif;?>
						</select>
					</p>
				</div>

				<div class="faq-col select-data" style="display: none">
					<p>
						<label for="nbt-repeater-content-title"><?php _e('Global FAQs', 'nbt-solution');?>:</label>
						<select class="select-global-data js-select2" name="global_faqs[]"></select>
					</p>
				</div>
			</div>
			<div class="field-single-faq">
				<label for="nbt-repeater-content-title"><?php _e('Heading', 'nbt-solution');?>:</label>
				<input class="widefat heading_title" name="faq_heading[]" type="text" value="">
			</div>
			<div class="faq-action-row wp-core-ui">
				<button type="button" class="button-link button-link-delete widget-control-remove">Delete</button> | 
				<button type="button" class="button-link widget-control-close">Close</button>
			</div>
		</div>
	</div>
</script>
<script id="nbt-repeater-template" type="text/template">
	<div class="repeater-row" id="id_repeater">
		<div class="repeater-heading nbt-repeater-order">
			Title<span class="nbt-repeater-title">: </span>
			<button type="button" class="nbt-repeater-arrow" aria-expanded="false">
				<span class="nbt-toggle-indicator" aria-hidden="true"></span>
			</button>
		</div>

		<div class="repeater-content">
			<div class="faq-row">
				<div class="faq-col">
					<p>
						<label for="nbt-repeater-content-title"><?php _e('Type', 'nbt-solution');?>:</label>
						<select class="select-global-faqs js-select2" name="select_repeater_faq_type[]">
							<option value="">No select</option>
							<?php
							global $post;
							$args = array(
								'post_type' => 'nbt_faq',
								'post_status' => 'publish'
							);
							$the_query = new WP_Query( $args );
							if ( $the_query->have_posts() ) :
								while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
								<option value="<?php echo $post->ID;?>"><?php echo $post->post_title;?></option>
								<?php endwhile;
								wp_reset_postdata();
							endif;?>
						</select>
					</p>
				</div>

				<div class="faq-col select-data" style="display: none">
					<p>
						<label for="nbt-repeater-content-title"><?php _e('Global FAQs', 'nbt-solution');?>:</label>
						<select class="select-global-data js-select2" name="select_repeater_faq_option[]"></select>
					</p>
				</div>
			</div>


			<div class="field-single-faq">
				<label for="nbt-repeater-content-title"><?php _e('Title', 'nbt-solution');?>:</label>
				<input class="widefat faq_title" id="nbt-repeater-content-title" name="faq_title[]" type="text" value="">
			</div>
			<div class="field-single-faq">
				<label for="nbt-repeater-content-title"><?php _e('Content', 'nbt-solution');?>:</label>

				<div class="acf-field nbt-field-wysiwyg acf-field-59ae0d6039d7e" data-name="editor" data-type="wysiwyg" data-id="59ae0d6039d7e">
					<div class="nbt-editor-input">
						<div id="wp-nbt-editor-59ae0d6039d7e-wrap" class="nbt-editor-wrap wp-core-ui wp-editor-wrap tmce-active" data-toolbar="full">
							<div id="wp-nbt-editor-59ae0d6039d7e-editor-tools" class="wp-editor-tools hide-if-no-js">
								<div id="wp-nbt-editor-59ae0d6039d7e-media-buttons" class="wp-media-buttons">
									<button type="button" class="button insert-media add_media" data-editor="nbt-editor-59ae0d6039d7e"><span class="wp-media-buttons-icon"></span> Add Media</button>
								</div>
								<div class="wp-editor-tabs">
									<button id="nbt-editor-59ae0d6039d7e-tmce" class="wp-switch-editor switch-tmce" data-wp-editor-id="nbt-editor-59ae0d6039d7e" type="button">Visual</button>
									<button id="nbt-editor-59ae0d6039d7e-html" class="wp-switch-editor switch-html" data-wp-editor-id="nbt-editor-59ae0d6039d7e" type="button">Text</button>
								</div>
							</div>
							<div id="wp-nbt-editor-59ae0d6039d7e-editor-container" class="wp-editor-container">
								<textarea id="nbt-editor-59ae0d6039d7e" class="wp-editor-area" name="faq_content[]" style="height:250px;" disabled="" aria-hidden="false"></textarea>
							</div>
							<div class="uploader-editor">
								<div class="uploader-editor-content">
									<div class="uploader-editor-title">Drop files to upload</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="faq-action-row wp-core-ui">
				<button type="button" class="button-link button-link-delete widget-control-remove">Delete</button> | 
				<button type="button" class="button-link widget-control-close">Close</button>
			</div>
		</div>
	</div>
</script>