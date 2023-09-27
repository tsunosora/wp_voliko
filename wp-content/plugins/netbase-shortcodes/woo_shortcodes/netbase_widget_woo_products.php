<?php
// Netbase Widget Woo Products
add_shortcode('netbase_widget_woo_products', 'netbase_shortcode_widget_woo_products');

function netbase_shortcode_widget_woo_products($atts, $content = null) {
    ob_start();
    if ($template = netbase_shortcode_woo_template('netbase_widget_woo_products'))
        include $template;
    return ob_get_clean();
}