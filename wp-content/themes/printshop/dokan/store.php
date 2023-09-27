<?php
/**
 * The Template for displaying all single posts.
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$store_user   = get_userdata( get_query_var( 'author' ) );
$store_info   = dokan_get_store_info( $store_user->ID );
$map_location = isset( $store_info['location'] ) ? esc_attr( $store_info['location'] ) : '';

$_pageviews = get_user_meta( $store_user->ID, '_pageviews', true );
if(!$_pageviews){
    $_pageviews = 0;
}
update_user_meta($store_user->ID, '_pageviews', ($_pageviews + 1));
printshop_get_header()
?>
    <?php do_action( 'woocommerce_before_main_content' ); ?>
    <?php wc_print_notices();?>
    <div class="row">
        <div class="col-md-3" id="netbase-sidebar-dokan">
            <?php if ( dokan_get_option( 'enable_theme_store_sidebar', 'dokan_general', 'off' ) == 'off' ) { ?>
                <div id="dokan-secondary" class="dokan-clearfix dokan-store-sidebar" role="complementary">
                    <div class="dokan-widget-area widget-collapse">
                        <?php do_action( 'dokan_sidebar_store_before', $store_user, $store_info ); ?>
                        <?php
                        if ( ! dynamic_sidebar( 'sidebar-store' ) ) {

                            $args = array(
                                'before_widget' => '<aside class="widget">',
                                'after_widget'  => '</aside>',
                                'before_title'  => '<h3 class="widget-title">',
                                'after_title'   => '</h3>',
                            );

                            if ( class_exists( 'Dokan_Store_Location' ) ) {
                                the_widget( 'Dokan_Store_Category_Menu', array( 'title' => __( 'Store Category', 'printshop' ) ), $args );

                                if ( dokan_get_option( 'store_map', 'dokan_general', 'on' ) == 'on' ) {
                                    the_widget( 'Dokan_Store_Location', array( 'title' => __( 'Store Location', 'printshop' ) ), $args );
                                }

                                if ( dokan_get_option( 'contact_seller', 'dokan_general', 'on' ) == 'on' ) {
                                    the_widget( 'Dokan_Store_Contact_Form', array( 'title' => __( 'Contact Vendor', 'printshop' ) ), $args );
                                }
                            }

                        }
                        ?>

                        <?php do_action( 'dokan_sidebar_store_after', $store_user, $store_info ); ?>
                    </div>
                </div><!-- #secondary .widget-area -->
                <?php
            } else {
                dynamic_sidebar( 'sidebar-store' );
            }
            ?>
        </div>
        <div class="col-md-9">
            <div id="dokan-primary" class="dokan-single-store">
                <div id="dokan-content" class="store-page-wrap woocommerce" role="main">

                    <?php dokan_get_template_part( 'store-header' ); ?>

                    <?php do_action( 'dokan_store_profile_frame_after', $store_user, $store_info ); ?>

                    <?php
                    $paged = get_query_var( 'dokan_per_page', 1 );
                    $args = array(
                        'post_type' => 'product',
                        'author' => $store_user->ID,
                        'posts_per_page' => 3,
                        'paged' => $paged
                    );
                    $query_author = new WP_Query( $args );
                    if ( $query_author->have_posts() ) { ?>

                        <div class="seller-items">

                            <?php woocommerce_product_loop_start(); ?>

                            <?php while ( $query_author->have_posts() ) : $query_author->the_post(); ?>

                                <?php wc_get_template_part( 'content', 'product' ); ?>

                            <?php endwhile; // end of the loop. ?>

                            <?php woocommerce_product_loop_end(); ?>

                        </div>
       
                        <?php dokan_content_nav( 'nav-below', $query_author ); ?>

                    <?php } else { ?>

                        <p class="dokan-info"><?php _e( 'No products were found of this vendor!', 'printshop' ); ?></p>

                    <?php } ?>
                </div>

            </div><!-- .dokan-single-store -->
        </div>
    </div>



    <?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer( 'shop' ); ?>