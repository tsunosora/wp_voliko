<?php
/**
 * The Template for displaying all reviews.
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */

$store_user = get_userdata( get_query_var( 'author' ) );
$store_info = dokan_get_store_info( $store_user->ID );
$map_location = isset( $store_info['location'] ) ? esc_attr( $store_info['location'] ) : '';

get_header( 'shop' );
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
            <div id="dokan-content" class="store-review-wrap woocommerce" role="main">

        <?php dokan_get_template_part( 'store-header' ); ?>


        <?php
        $dokan_template_reviews = dokan_pro()->review;
        $id                     = $store_user->ID;
        $post_type              = 'product';
        $limit                  = 20;
        $status                 = '1';
        $comments               = $dokan_template_reviews->comment_query( $id, $post_type, $limit, $status );
        ?>

        <div id="reviews">
            <div id="comments">

              <?php do_action( 'dokan_review_tab_before_comments' ); ?>

                <h2 class="headline"><?php _e( 'Vendor Review', 'printshop' ); ?></h2>

                <ol class="commentlist">
                    <?php echo $dokan_template_reviews->render_store_tab_comment_list( $comments , $store_user->ID); ?>
                </ol>

            </div>
        </div>

        <?php
        echo $dokan_template_reviews->review_pagination( $id, $post_type, $limit, $status );
        ?>

            </div>

        </div><!-- .dokan-single-store -->
    </div>
</div>
<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer(); ?>