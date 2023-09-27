<?php global $seller_ajax;?>
<div class="wpl-store-locator wpl-store-style-2">
	<div class="wpl-store-list-left">
		<div class="wpl-store-list-header">
			<label><?php echo esc_html__('Search Location', 'woopanel');?></label>
			<input type="text" id="wpl-store-auto-complete" class="form-control" placeholder="<?php echo esc_html__('Type your address', 'woopanel');?>">
		</div>

		<div id="wpl-store-list-container" data-paged="<?php echo absint($paged);?>" data-per_page="<?php echo absint($limit);?>">
			<?php
			$template_args = array(
			    'results'         => $seller_ajax->load_stores($limit),
			    'limit'           => $limit,
			    'paged'           => $paged,
			    'count'			  => $count,
			    'pagination_base' => false,
			    'per_row'         => $per_row,
			);

			woopanel_get_template_part( 'vendor/store-lists-loop', false, $template_args ); ?>
		</div>
	</div>

	<div class="wpl-store-list-right">
		<div id="asl-map-canv"></div>

		<?php echo woopanel_modal_directions();?>
	</div>


</div>