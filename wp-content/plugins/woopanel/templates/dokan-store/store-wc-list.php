<?php $product = wc_get_product($post_id);?>
<div class="woopanel-list-wc-wrap">
	<?php woocommerce_template_loop_product_link_open();?>
	<?php do_action( 'woocommerce_before_shop_loop_item_title' );?>
	<?php woocommerce_template_loop_product_link_close();?>
	<div class="woopanel-list-right">
		<h4 class="product-title"><a href="<?php echo get_permalink($post_id);?>"><?php echo esc_attr($product->get_title());?></a></h4>
		<?php woocommerce_template_loop_rating();?>
		<?php woocommerce_template_loop_price();?>

		<div class="product-excerpt">
			<?php echo wpautop( get_the_excerpt( $product->get_id()) );?>
		</div>

		<?php woocommerce_template_loop_add_to_cart();?>
	</div>
</div>
