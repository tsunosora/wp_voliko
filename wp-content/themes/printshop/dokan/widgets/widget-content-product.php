<?php
/**
 * Dokan Widget Content Product Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>

<?php if ( $r->have_posts() ) : ?>
    <ul class="netbase-widget-bestselling">
    <?php while ( $r->have_posts() ): $r->the_post() ?>
        <?php global $product; ?>
        <li class="clearfix">
            <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>">
                <?php echo $product->get_image(); ?>
            </a>
            <div class="right-product-sidebar">
                <a href="<?php echo esc_url(get_permalink());?>" class="product-title"><?php echo $product->get_title(); ?></a>
                <?php echo $product->get_rating_html(); ?>
                <?php echo $product->get_price_html(); ?>
            </div>
            <!--<div class="bh-add-to-cart">
                <?php //do_action('woocommerce_after_shop_loop_item');?>
            </div>-->
        </li>
        <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p><?php _e( 'No products found', 'printshop' ); ?></p>
<?php endif; ?>
