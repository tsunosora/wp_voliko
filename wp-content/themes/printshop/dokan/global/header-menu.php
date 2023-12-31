<?php
/**
 * Dokan Header Menu Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>

<ul class="nav navbar-nav navbar-right">
    <li>
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php printf( __( 'Cart %s', 'printshop' ), '<span class="dokan-cart-amount-top">(' . WC()->cart->get_cart_total() . ')</span>' ); ?> <b class="caret"></b></a>

        <ul class="dropdown-menu">
            <li>
                <div class="widget_shopping_cart_content"></div>
            </li>
        </ul>
    </li>

    <?php if ( is_user_logged_in() ) { ?>

        <?php

        if ( dokan_is_user_seller( $user_id ) ) {
            ?>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php _e( 'Vendor Dashboard', 'printshop' ); ?> <b class="caret"></b></a>

                <ul class="dropdown-menu">
                    <li><a href="<?php echo dokan_get_store_url( $user_id ); ?>" target="_blank"><?php _e( 'Visit your store', 'printshop' ); ?> <i class="fa fa-external-link"></i></a></li>
                    <li class="divider"></li>
                    <?php
                    foreach ( $nav_urls as $key => $item ) {
                        printf( '<li><a href="%s">%s &nbsp;%s</a></li>', $item['url'], $item['icon'], $item['title'] );
                    }
                    ?>
                </ul>
            </li>
        <?php } ?>

        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo esc_html( $current_user->display_name ); ?> <b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li><a href="<?php echo dokan_get_page_url( 'my_orders' ); ?>"><?php _e( 'My Orders', 'printshop' ); ?></a></li>
                <li><a href="<?php echo dokan_get_page_url( 'myaccount', 'woocommerce' ); ?>"><?php _e( 'My Account', 'printshop' ); ?></a></li>
                <li><a href="<?php echo wc_customer_edit_account_url(); ?>"><?php _e( 'Edit Account', 'printshop' ); ?></a></li>
                <li class="divider"></li>
                <li><a href="<?php echo wc_get_endpoint_url( 'edit-address', 'billing', get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>"><?php _e( 'Billing Address', 'printshop' ); ?></a></li>
                <li><a href="<?php echo wc_get_endpoint_url( 'edit-address', 'shipping', get_permalink( wc_get_page_id( 'myaccount' ) ) ); ?>"><?php _e( 'Shipping Address', 'printshop' ); ?></a></li>
            </ul>
        </li>

        <li><?php wp_loginout( home_url() ); ?></li>

    <?php } else { ?>
        <li><a href="<?php echo dokan_get_page_url( 'myaccount', 'woocommerce' ); ?>"><?php _e( 'Log in', 'printshop' ); ?></a></li>
        <li><a href="<?php echo dokan_get_page_url( 'myaccount', 'woocommerce' ); ?>"><?php _e( 'Sign Up', 'printshop' ); ?></a></li>
    <?php } ?>
</ul>
