jQuery(function() {
    if( ! jQuery('body').hasClass('woocommerce_page_wc-settings') ) {
        jQuery('.form-table .forminp #woocommerce_currency').parents('tr').remove();
        jQuery('.form-table .forminp #woocommerce_currency_pos').parents('tr').remove();
    }
});
