<?php
/**
 *  Dokan Dahsboard Template
 *
 *  Dokan Dashboard order status filter template
 *
 *  @since 2.4
 *
 *  @package dokan
 */

if ( isset( $_GET['order_id'] ) ) {
    ?>
        <a href="<?php echo dokan_get_navigation_url( 'orders' ) ; ?>" class="dokan-btn"><?php _e( '&larr; Orders', 'printshop' ); ?></a>
    <?php
} else {

    dokan_order_listing_status_filter();

}