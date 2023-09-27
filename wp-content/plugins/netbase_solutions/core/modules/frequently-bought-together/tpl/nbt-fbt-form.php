<?php
/**
 * Display Form Cross Sell Product
 */
global $product;

// set query
$url        = ! is_null( $product ) ? $product->get_permalink() : '';
$url        = add_query_arg( 'action', 'nb_cross_sell', $url );
$url        = wp_nonce_url( $url, 'nb_cross_sell' );

?>
<div class="nbcs-section woocommerce">
	<h3><?php _e('Frequently Bought Together', 'nbt-solution');?></h3>
	<form class="nbcs-form" method="post" action="<?php echo esc_url($url);?>">
		<div>
			<ul class="nbcs-list-images">
				<?php 
					$total_price 	= 0;
					$total_product 	= count($products);
				?>
				<?php 
				foreach($products as $index => $current_product):
					if(!$current_product->is_type( array( 'grouped', 'external' ))):

						$current_product_price = function_exists('wc_get_price_to_display') ? wc_get_price_to_display( $current_product ) : $current_product->get_display_price();
						$total_price += floatval( $current_product_price );
				?>

					<?php if($index != 0):?>
						<li class="nbcs-plus-icon" data-rel="offeringID_<?php echo $index;?>"><span>+</span></li>
					<?php endif;?>

					<li class="nbcs-thumb" data-rel="offeringID_<?php echo $index;?>"><a href="<?php echo $current_product->get_permalink();?>"><?php echo $current_product->get_image();?></a>
					</li>
				<?php endif; endforeach;?>
			</ul>
			<div class="nbcs-price-box <?php if($total_product > 3) echo 'clear-price-box'; ?>">
				<div class="nbcs-total-price">
					<span><?php _e('Total price', 'nbt-solution');?>:</span>
					<span class="nbcs-display-total-price" data-total-price="<?php echo $total_price;?>"><?php echo wc_price( $total_price ); ?></span>
				</div>

				<div class="nbcs-add-to-cart-button">
					<?php if($product->is_type('variable')):?>
						<input type="hidden" name="p-variation" value="">
					<?php endif;?>
					<input type="submit" name="nbsc-add-to-cart-submit" value="<?php _e('Add all to cart', 'nbt-solution');?>">
				</div>
			</div>
			<div class="nbcs-list-items">
				<ul>
					<?php 
					foreach($products as $index => $current_product):
						if(!$current_product->is_type( array( 'grouped', 'external' ))):

							$current_product_price = function_exists('wc_get_price_to_display') ? wc_get_price_to_display( $current_product ) : $current_product->get_display_price();
							if($current_product_price == '') {
								$current_product_price = 0;
							}
							$product_id = $current_product->get_id();
					?>
						<li class="nbcs-item">
							<label>
								<input type="hidden" name="offeringID[]" class="offeringID-<?php echo $product_id;?>" value="<?php echo $product_id;?>">

								<span><input type="checkbox" checked="checked" name="cb-item[]" value="<?php echo $product_id;?>" data-price="<?php echo $current_product_price;?>" id="offeringID_<?php echo $index;?>"></span>

								<span class="nbcs-item-name"><?php if($index == 0) echo "<span class='nbcs_this_text'>" . esc_html("This item: ") . "</span>";?><a href="<?php echo $current_product->get_permalink();?>"><?php echo $current_product->get_title();?></a></span>
								 – 
								<span class="nbcs-item-price"><?php echo wc_price( $current_product_price ); ?></span>
							<label>
						</li>
					<?php endif; endforeach;?>
				</ul>
			</div>
		</div>
	</form>
</div>