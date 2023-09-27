<?php

class WooPanel_Seller_Template {

    /**
     * Total vendors found
     *
     * @var integer
     */
    private $total_users;

    function __construct()
    {
        add_action( 'page_template', array( $this, 'store_template' ) );

    }

    public function store_template( $page_template ) {
        global $wp_query, $admin_options;

        if( ! empty($admin_options) && isset($admin_options->options['woopanel_page_stores']) && $admin_options->options['woopanel_page_stores'] == get_query_var('pagename') && $admin_options->options['store_listing_layout'] == 'style1' ) {
            $page_template = woopanel_locate_template( 'vendor.php' );
        }
        
        return $page_template;
    }

}

new WooPanel_Seller_Template();