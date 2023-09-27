<?php
// Netbase Product Category
add_shortcode('netbase_list_products_cat', 'netbase_list_products_cat_shortcode');

function netbase_list_products_cat_shortcode($atts, $content = null) {
    ob_start();
    if ($template = netbase_shortcode_woo_template('netbase_list_products_cat'))
        include $template;
    return ob_get_clean();
}