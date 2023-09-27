<?php
add_filter( 'woocommerce_my_account_my_orders_actions', 'woopanel_my_account_order_actions', 10, 2 );
function woopanel_my_account_order_actions( $actions, $order ) {
    global $admin_options;

    if( isset($admin_options->options['dashboard_page_id']) ) {
        $actions['name'] = array(
            'url'  => woopanel_get_dashboard_endpoint_url( 'awesome-support/?wc_order=' . $order->get_id() ),
            'name' => esc_html__('Create Ticket', 'woopanel'),
        );
    }

    return $actions;
}