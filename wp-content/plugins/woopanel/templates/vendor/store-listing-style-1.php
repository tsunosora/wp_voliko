<?php
global $seller_ajax;?>

	<div class="wpl-store-locator">
		<div id="asl-map-canv"></div>
    <?php echo woopanel_modal_directions();?>
	</div>

	<div class="container">
		<div class="wpl-store-list-filter">
			<p class="wpl-store-result-count"><?php echo sprintf(esc_html__('Showing all %d results', 'woopanel'), $count);?></p>
			<form class="wpl-store-ordering" method="get">
				<select name="store_category" class="orderby">
					<option value="0">(<?php echo esc_html__('Select a category', 'woopanel');?>)</option>
					<?php echo woopanel_dropdown_store_categories();?>
				</select>
			</form>
		</div>

		<div id="wpl-store-list-container" data-paged="<?php echo absint($paged);?>" data-per_page="<?php echo absint($limit);?>">
			<?php
			$template_args = array(
			    'results'         => $seller_ajax->load_stores($limit),
			    'limit'           => $limit,
			    'paged'           => $paged,
			    'count'			  => $count,
			    'pagination_base' => $pagination_base,
			    'per_row'         => $per_row,
			);

			woopanel_get_template_part( 'vendor/store-lists-loop', false, $template_args ); ?>
		</div>
	</div>