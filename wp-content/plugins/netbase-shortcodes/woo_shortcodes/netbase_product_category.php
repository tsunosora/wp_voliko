<?php
// Netbase Product Category
add_shortcode('netbase_product_category', 'netbase_shortcode_product_category');

function netbase_shortcode_product_category($atts, $content = null) {
    ob_start();
    if ($template = netbase_shortcode_woo_template('netbase_product_category'))
        include $template;
    return ob_get_clean();
}