<?php
/**
 * WooCommerce plugin config
 *
 * @package Netbase
 */

// Update product image sized.
function printshop_woo_thumb_sized() {
	$catalog = array('width'  => '262',	'height' => '262',  'crop'   => 1 );
	//$single  = array('width'  => '350',	'height' => '350',	'crop'   => 1 );
	$thumb   = array('width'  => '150',	'height' => '150',	'crop'   => 1 );
	update_option( 'shop_catalog_image_size', $catalog );
	update_option( 'shop_single_image_size', $catalog ); 
	update_option( 'shop_thumbnail_image_size', $thumb );
}
global $pagenow;
if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) {
	add_action( 'init', 'printshop_woo_thumb_sized', 1 );
}

// Return product number on a page.
/*add_filter( 'loop_shop_per_page', create_function( '$number', 'return 9;' ), 20 );*/
add_filter( 'loop_shop_per_page', 'printshop_custom_shop_per_page', 20 );


function printshop_custom_shop_per_page($number){
    $number_per_page = 9;
    if($number){
        $number_per_page = $number;
    }
    return $number_per_page;

}

// Hide the default shop title in content area.
add_filter('woocommerce_show_page_title', '__return_false');

// Add to cart single
add_filter( 'add_to_cart_text', 'printshop_custom_cart_button_text' );
add_filter( 'woocommerce_product_single_add_to_cart_text', 'printshop_custom_cart_button_text' );
function printshop_custom_cart_button_text() {
	return esc_html__( 'Add to cart', 'printshop' );
}

// Add to cart index vs archive 
add_filter( 'add_to_cart_text', 'printshop_archive_custom_cart_button_text' );
add_filter( 'woocommerce_product_add_to_cart_text', 'printshop_archive_custom_cart_button_text' );
function printshop_archive_custom_cart_button_text() {
	return '';
}

// Add related product
function printshop_related_products_args($args ) {
    $args['posts_per_page'] = 4; // 4 related products
    $args['columns'] = 4; // arranged in 2 columns
    return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'printshop_related_products_args' );

// Add to wishlist
add_action('woocommerce_after_shop_loop_item', 'printshop_show_add_to_wishlist', 10 );
function printshop_show_add_to_wishlist()
{
  echo do_shortcode('[yith_wcwl_add_to_wishlist]');
}

/**
 * Add Cart icon and count to header if WC is active
 */
function printshop_wc_cart_count() {
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        $count = WC()->cart->cart_contents_count; ?>
        <a class="cart-contents" href="<?php echo esc_url(wc_get_cart_url()); ?>" title="<?php esc_html_e( 'View your shopping cart', 'printshop' ); ?>">
            <span><?php if ( $count > 0 ) echo intval($count); ?></span>
        </a>
        <?php
    }
}
add_action( 'your_theme_header_top', 'printshop_wc_cart_count' );

/**
 * Ensure cart contents update when products are added to the cart via AJAX
 */
function printshop_header_add_to_cart_fragment($fragments ) {

    ob_start();
    $count = WC()->cart->cart_contents_count; ?>
    <a class="cart-contents" href="<?php echo esc_url(wc_get_cart_url()); ?>" title="<?php esc_html_e( 'View your shopping cart', 'printshop' ); ?>">
        <span><?php if ( $count > 0 ) echo intval($count); ?></span>
    </a>
    <?php
    $fragments['a.cart-contents'] = ob_get_clean();
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'printshop_header_add_to_cart_fragment' );
