<?php
if( ! function_exists('nbt_ajax_template') ) {
	function nbt_ajax_template() {
		$id = 'ajax-cart' . rand();
		?>
		<div id="<?php echo $id;?>" class="nbt-ajax-cart">
			<div class="nbt-ajax-cart-icon">
				<?php echo nbt_ajax_cart_icon();?>
			</div>
			
			<div class="nbt-ajax-cart-popup">
				<?php nbt_ajax_cart_popup();?>
			</div>
		</div>
		<?php
	}
}

if( ! function_exists('nbt_ajax_cart_icon') ) {
	function nbt_ajax_cart_icon() {
		if(!isset(WC()->cart)) {
			return;
		}
		$ajaxcart_settings = get_option('ajax-cart_settings');
		$ajaxcart_icon = $ajaxcart_settings['wc_ajax_cart_icon'];
		$get_settings = NBT_Ajax_Cart_Settings::get_settings();
		if(!$ajaxcart_icon){
			if(isset($get_settings['icon']['default'])){
				$ajaxcart_icon = $get_settings['icon']['default'];
			}
		}

		$price = WC()->cart->get_cart_total();
		$total_count = WC()->cart->get_cart_contents_count();
		if($total_count == 0){
			$count = wp_kses_data( sprintf( _n( '%d item', '%d item', $total_count, 'nbt-solution' ), $total_count ) );
		}else{
			$count = wp_kses_data( sprintf( _n( '%d item', '%d items', $total_count, 'nbt-solution' ), $total_count ) );
		}

		return apply_filters( 'nbt_ajax_cart_icon', '', $ajaxcart_icon, $count, $price );
	}	
}

if( ! function_exists('nbt_ajax_cart_popup') ) {
	function nbt_ajax_cart_popup() {
		if(!isset(WC()->cart)) {
			return;
		}
		?>
		<div class="nbt-mini-cart-wrap">
			<?php
			if( WC()->cart->get_cart_contents_count() ){?>
				<div class="nbt-cart-list-wrap">
					<ul class="cart_list">
						<?php nbt_ajaxcart_template_loop();?>
					</ul>
				</div>
				<?php if ( ! WC()->cart->is_empty() ) : ?>
					<p class="total"><strong><?php _e( 'Subtotal', 'nbt-solution' ); ?>:</strong> <?php echo WC()->cart->get_cart_subtotal(); ?></p>

					<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

					<p class="buttons">
						<?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?>
					</p>
				<?php endif; ?>
			<?php }else{?>
				<p class="woocommerce-mini-cart__empty-message"><?php _e( 'No products in the cart.', 'nbt-solution' ); ?></p>
			<?php }?>
		</div>		
		<?php
	}
}

if( ! function_exists('nbt_ajaxcart_template_loop') ) {
	function nbt_ajaxcart_template_loop($is_ajax = false){
		if( WC()->cart->get_cart_contents_count() ) {
		$items = WC()->cart->get_cart();
		foreach ($items as $cart_item_key => $cart_item) {
			$product_id = $cart_item['product_id'];
			$variation_id = $cart_item['variation_id'];

			$attr_variation = $variation_id ? ' data-variation_id="'.$variation_id.'"' : '';
			$_product = wc_get_product( $variation_id ? $variation_id : $product_id );
			$thumb = get_the_post_thumbnail( $cart_item['product_id'], array('50', '50') );
			?>
		<li class="mini_cart_item">
			<div class="nbt-ajax-cart-left">
				<a href="<?php echo get_permalink($cart_item['product_id']);?>" title="<?php echo $_product->get_name();?>"><?php echo $thumb;?></a>
			</div>

			<div class="nbt-ajax-cart-right">
				<h4><a href="<?php echo get_permalink($cart_item['product_id']);?>" title="<?php echo $_product->get_name();?>"><?php echo $_product->get_name();?></a></h4> 
			<?php

			$rating_count = $_product->get_rating_count();
			$review_count = $_product->get_review_count();
			$average      = $_product->get_average_rating();

			if ( $rating_count > 0 ) : ?>
				<div class="woocommerce-product-rating">
					<?php echo wc_get_rating_html( $average, $rating_count ); ?>
				</div>
			<?php else: ?>
				<div class="woocommerce-product-rating">
					<div class="star-rating" title="<?php _e('Rated 0.00 out of 5', 'nbt-solution');?>"><span style="width:0"><strong class="rating">0.00</strong> <?php _e('out of', 'nbt-solution');?> 5</span></div>
				</div>
			<?php endif;
			
		    if($_product->get_price_html() !=''):
		        echo '<span class="product-price sr-price">';
					echo nbt_ajax_cart_get_price($cart_item['data']->get_price(), $is_ajax);
		        echo '</span> × '.$cart_item['quantity'];
		    endif;
		    ?>
			<?php
			echo sprintf('<a href="%s" class="remove" aria-label="%s" data-product_id="%s"%s data-product_sku="%s"><i class="%s"></i></a>',
				esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
				__( 'Remove this item', 'nbt-solution' ),
				esc_attr( $product_id ),
				$attr_variation,
				esc_attr( $_product->get_sku() ),
				apply_filters('pc_cart_item_remove_link', 'ajaxcart-icon-delete')
			);
			?>
			</div>
		</li>
		<?php }
		}
	}
}

if( ! function_exists('nbt_ajax_cart_get_price') ) {
	function nbt_ajax_cart_get_price($price, $is_ajax = false) {
		global $currency;

		$ajaxcart_rate = 1;
		$ajaxcart_pos = '%1$s%2$s';
		$ajaxcart_symbol = get_woocommerce_currency_symbol(get_option('woocommerce_currency'));
		$ajaxcart_decimals = get_option('woocommerce_price_num_decimals' );
		$ajaxcart_currency = get_option('woocommerce_currency');
		if(isset($currency) && is_array($currency)){
			
			$ajaxcart_rate = $currency['nbt_currency-switcher_rates'];
			$ajaxcart_position = $currency['nbt_currency-switcher_position'];
			$ajaxcart_decimals = $currency['nbt_currency-switcher_decimals'];
			$ajaxcart_symbol = $currency['nbt_currency-switcher_repeater_symbol'];
			$ajaxcart_currency = $currency['nbt_currency-switcher_repeater_currency'];

			switch ($ajaxcart_position) {
			    case 'left' :
				$ajaxcart_pos = '%1$s%2$s';
				break;
			    case 'right' :
				$ajaxcart_pos = '%2$s%1$s';
				break;
			    case 'left_space' :
				$ajaxcart_pos = '%1$s&nbsp;%2$s';
				break;
			    case 'right_space' :
				$ajaxcart_pos = '%2$s&nbsp;%1$s';
				break;
			}
		}


		$array_position = str_replace('&nbsp;', '', $ajaxcart_pos);

        if ( $price ) {

        	if($is_ajax){
        		$price = round($price * $ajaxcart_rate);
        		$price = '<span class="woocommerce-Price-amount amount">'.str_replace(array('%2$s', '%1$s'), array($price, '<span class="woocommerce-Price-currencySymbol">'.$ajaxcart_symbol.'</span>'), $array_position).'</span>';
        	}else{
	  			$price = wc_price($price, array(
	        		'decimals' => $ajaxcart_decimals,
	        		'price_format' => $ajaxcart_pos,
	        		'currency' => $ajaxcart_currency,
	        	));
        	}



        	return $price;
        }
	}

}