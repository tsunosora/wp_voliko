				<div id="heading-<?php echo $k_id;?>" class="heading-box" data-id="<?php echo $k_id;?>">
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
												<option value="<?php echo $post->ID;?>"<?php if(is_array($value['heading']) && $value['heading']['faq'] == $post->ID){ echo ' selected';}?>><?php echo $post->post_title;?></option>
												<?php endwhile;
												wp_reset_postdata();
											endif;?>
										</select>
									</p>
								</div>

								<div class="faq-col select-data"<?php if(!is_array($value['heading'])){ echo ' style="display: none"';}?>>
									<p>
										<label for="nbt-repeater-content-title"><?php _e('Global FAQs', 'nbt-solution');?>:</label>
					
										<select class="select-global-data js-select2" name="global_faqs[]">
											<?php
											if(isset($value['heading']['faq']) && is_numeric($value['heading']['faq'])){
												$datas = get_post_meta($value['heading']['faq'], '_nbt_faq', true);
												if($datas){
													foreach ($datas as $key1 => $val) {
														if($value['heading']){?>
														<option value="<?php echo $key1;?>"<?php if($key1 == $value['heading']['id']){ echo ' selected';}?>><?php echo $val['heading'];?></option>
														<?php
														}
													}
												}

												
											}?>

										</select>
									</p>
								</div>
							</div>


							<div class="field-single-faq">
								<label for="nbt-repeater-content-title"><?php _e('Heading', 'nbt-solution');?>: <?php echo $type;?></label>
								<input class="widefat heading_title" id="nbt-heading-content-title" name="faq_heading[]" type="text" value="<?php echo $heading;?>">
							</div>
							<div class="faq-action-row wp-core-ui">
								<button type="button" class="button-link button-link-delete widget-control-remove">Delete</button> |
								<button type="button" class="button-link widget-control-close">Close</button>
							</div>
						</div>
					</div>
					<?php if(isset($lists)) {
						foreach ($lists as $kl => $vl):?>
							<div class="repeater-row" id="repeater-row-<?php echo $k_id; ?>">
								<div class="repeater-heading nbt-repeater-order">
									Title<span class="nbt-repeater-title">: <?php echo $vl['faq_title']; ?></span>
									<button type="button" class="nbt-repeater-arrow" aria-expanded="false">
										<span class="nbt-toggle-indicator" aria-hidden="true"></span>
									</button>
								</div>

								<div class="repeater-content">
									<div class="faq-row">
										<div class="faq-col">
											<p>
												<label for="nbt-repeater-content-title"><?php _e('Type', 'nbt-solution');?>:</label>
												<select class="select-global-faqs js-select2" name="select_repeater_faq_type[<?php echo $k_id; ?>][]">
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
														<option value="<?php echo $post->ID;?>"<?php if(isset($vl['faq']) && $vl['faq'] == $post->ID){ echo ' selected';}?>><?php echo $post->post_title;?></option>
														<?php endwhile;
														wp_reset_postdata();
													endif;?>
												</select>
											</p>
										</div>

										<div class="faq-col select-data"<?php if(!isset($vl['faq'])){ echo ' style="display: none"';}?>>
											<p>
												<label for="nbt-repeater-content-title"><?php _e('Global FAQs', 'nbt-solution');?>:</label>
												<select class="select-global-data js-select2" name="select_repeater_faq_option[<?php echo $k_id; ?>][]">
													<?php if(!isset($vl['faq'])){
														echo '<option value="">(Please select Type)</option>';
													}else{

														if(isset($vl['faq']) && is_numeric($vl['faq'])){
															$datas = get_post_meta($vl['faq'], '_nbt_faq', true);
															if($datas){
																foreach ($datas as $key => $val) {
																	if($val['heading']){
																		echo '<optgroup label="'.$val['heading'].'">';
																	}
																	foreach ($val['lists'] as $key2 => $value2) {
																		$selected_option = '';
																		if($key.'_'.$key2 == $vl['id']){
																			$selected_option = ' selected';
																		}
																		echo '<option value="'.$key.'_'.$key2.'"'.$selected_option.'>&nbsp;&nbsp;&nbsp;'.$value2['faq_title'].'</option>';
																	}
																	if($val['heading']){
																		echo '</optgroup>';
																	}
																}
															}

															
														}
													}?>

												</select>
											</p>
										</div>
									</div>


									<div class="field-single-faq"<?php if(isset($vl['faq'])){ echo ' style="display: none"';}?>>
										<label for="nbt-repeater-content-title"><?php _e('Title', 'nbt-solution'); ?>
											:</label>
										<input class="widefat faq_title" id="nbt-repeater-content-title"
											   name="faq_title[<?php echo $k_id;?>][]" type="text" value="<?php echo $vl['faq_title']; ?>">
									</div>
									<div class="field-single-faq"<?php if(isset($vl['faq'])){ echo ' style="display: none"';}?>>
										<label for="nbt-repeater-content-title"><?php _e('Content', 'nbt-solution'); ?>
											:</label>

										<?php
										$settings = array(
											'quicktags' => array('buttons' => 'em,strong,link',),
											'textarea_name' => 'faq_content['.$k_id.'][]',//name you want for the textarea
											'quicktags' => true,
											'tinymce' => array(
												'toolbar1' => 'formatselect,bold,italic,bullist,numlist,blockquote,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,fullscreen,wp_adv',
												'toolbar2' => 'strikethrough,hr,forecolor,pastetext,removeformat,charmap,outdent,indent,undo,redo,wp_help'
											),
											'editor_height' => 250,
										);
										$id = md5($k_id.$kl);//has to be lower case
										wp_editor($vl['faq_content'], $id, $settings);
										?>
									</div>
									<div class="faq-action-row wp-core-ui">
										<button type="button"
												class="button-link button-link-delete widget-control-remove">Delete
										</button>
										|
										<button type="button" class="button-link widget-control-close">Close</button>
									</div>
								</div>
							</div>
						<?php endforeach;
					}?>
				</div>