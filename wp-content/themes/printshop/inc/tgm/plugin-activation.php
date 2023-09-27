<?php

require_once get_template_directory() . '/inc/tgm/class-tgm-plugin-activation.php';

$package = wp_get_theme(get_template())->get('Tags');
if(in_array('nb-advanced', $package) || in_array('nb-premium', $package) || in_array('nb-enterprise', $package)) {
    add_action( 'tgmpa_register', 'register_required_plugins' );
}
else {
    add_action( 'tgmpa_register', 'register_required_plugins_tf' );
}

function register_required_plugins()
{

    $required = array(
        array(
            'name'              => 'Woocommerce',
            'slug'              => 'woocommerce',
            'required'          => true,
            'version'           => '4.8.0',
        ),
        array(
            'name'              => 'Max Mega Menu',
            'slug'              => 'megamenu',
            'required'          => true,
            'version'           => '2.9.2',
        ),
        array(
            'name'              => 'YITH WooCommerce Compare',
            'slug'              => 'yith-woocommerce-compare',
            'required'          => true,
            'version'           => '2.4.3',
        ),
        array(
            'name'              => 'YITH WooCommerce Wishlist',
            'slug'              => 'yith-woocommerce-wishlist',
            'required'          => true,
            'version'           => '3.0.17',
        ),
        array(
            'name'              => 'YITH WooCommerce Quick View',
            'slug'              => 'yith-woocommerce-quick-view',
            'required'          => true,
            'version'           => '1.4.7',
        ),
        array(
            'name'              => 'Contact Form 7',
            'slug'              => 'contact-form-7',
            'required'          => false,
            'version'           => '5.3.2',
        ),
        array(
            'name'              => 'MailChimp for WordPress',
            'slug'              => 'mailchimp-for-wp',
            'required'          => false,
            'version'           => '4.8.1',
        ),
        array(
            'name'               => 'Siteorigin Panel',
            'slug'               => 'siteorigin-panels',
            'required'           => true,
            'version'            => '2.4.18',
        ),    
        array(
            'name'               => 'Redux Framework',
            'slug'               => 'redux-framework',
            'required'           => true,
            'version'            => '3.6.15',
        ),          
        array(
            'name'               => 'So widgets bundle',
            'slug'               => 'so-widgets-bundle', 
            'required'           => true,
            'version'            => '1.15.4',
        ),    
        array(
            'name'              => 'Breadcrumb NavXT',
            'slug'              => 'breadcrumb-navxt', 
            'required'          => false,
            'version'           => '6.2.1',
        ),
        array(
            'name'              => 'Slider Revolution',
            'slug'              => 'revslider',
            'required'          => false,
            'version'           => '6.3.5',
            'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/revslider.zip'),
        ),
        array(
            'name'              => 'Netbase Solutions',
            'slug'              => 'netbase_solutions',
            'required'          => true,
            'version'           => '1.9.7',
            'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/netbase_solution_for_ps/netbase_solutions.zip'),
        ),
        array(
            'name'              => 'WooPanel',
            'slug'              => 'woopanel',
            'required'          => false,
            'version'           => '1.2.7',
            'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/woopanel.zip'),
        ),
        array(
            'name'               => 'Netbase Printshop Widgets for SiteOrigin',
            'slug'               => 'netbase-widgets-for-siteorigin-print', 
            'version'           => '1.0.6',
            'required'           => false,
            'source'             => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/netbase-widgets-for-siteorigin-print.zip'),
        ),  
        array(
            'name'               => 'Netbase Shortcodes',
            'slug'               => 'netbase-shortcodes', 
            'version'           => '1.4.5',
            'required'           => false,
            'source'             => esc_url('http://netbaseteam.com/wordpress/theme/plugins/printshop-solution/netbase-shortcodes.zip'),
        ),
    );
    
    $advance = array(
        array(
            'name'              => 'Order Delivery Date for WooCommerce',
            'slug'              => 'order-delivery-date-for-woocommerce',
            'required'          => false,
            'version'           => '3.11.4',
        ),
        array(
            'name'              => 'WooCommerce Coupon Generator',
            'slug'              => 'coupon-generator-for-woocommerce',
            'required'          => false,
            'version'           => '1.1.0',
        ),
        array(
            'name'              => 'Yoast SEO',
            'slug'              => 'wordpress-seo',
            'required'          => false,
            'version'           => '15.4',
        ),
        array(
            'name'              => 'Popup Maker – Popup Forms, Optins & More',
            'slug'              => 'popup-maker',
            'required'          => true,
            'version'           => '1.7.30',
        ),
        array(
            'name'           => 'Netbase Dashboard',
            'slug'           => 'netbase_dashboard',
            'required'       => false,
            'version'        => '1.2.3',
            'source'         => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/netbase_dashboard.zip'),
        ),
        array(
            'name'              => 'ThirstyAffiliates',
            'slug'              => 'thirstyaffiliates',
            'required'          => false,
            'version'           => '3.4',
        ),
        array(
            'name'              => 'WooCommerce PDF Invoices Pro',
            'slug'              => 'netbase-smart-invoice', 
            'required'          => false,
            'version'           => '1.0.5',
            'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/netbase-smart-invoice.zip'),
        ),
    );
    
    $premium = array(
        array(
            'name'              => 'Nbdesigner',
            'slug'              => 'web-to-print-online-designer',
            'required'          => true,
            'version'           => '2.8.3',
            'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/web-to-print-online-designer.zip'),
        ),
    );
    
    $enterprise = array(
        array(
            'name'              => 'Dokan',
            'slug'              => 'dokan-lite',
            'required'          => true,
            'version'           => '2.9.20',
        ),
        array(
            'name'              => 'Dokan Pro',
            'slug'              => 'dokan-pro',
            'required'          => true,
            'version'           => '3.1.3',
            'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/dokan-pro.zip'),
        ),
    );
    
    $plugins = $required;

    $package = wp_get_theme(get_template())->get('Tags');
    
    if( in_array('nb-advanced', $package) ) {
        $plugins = array_merge($required, $advance);
    }
    
    if( in_array('nb-premium', $package) ) {
        $plugins = array_merge($required, $advance, $premium);
    }
    
    if( in_array('nb-enterprise', $package) ) {
        $plugins = array_merge($required, $advance, $premium, $enterprise);
    }


    $config = array(
        'id'           => 'core-wp',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
    );

    tgmpa( $plugins, $config );
}

function register_required_plugins_tf()
{

    $required = array(
        array(
            'name'              => 'Woocommerce',
            'slug'              => 'woocommerce',
            'required'          => true,
            'version'           => '4.8.0',
        ),
        array(
            'name'              => 'Max Mega Menu',
            'slug'              => 'megamenu',
            'required'          => true,
            'version'           => '2.9.2',
        ),
        array(
            'name'              => 'YITH WooCommerce Compare',
            'slug'              => 'yith-woocommerce-compare',
            'required'          => true,
            'version'           => '2.4.3',
        ),
        array(
            'name'              => 'YITH WooCommerce Wishlist',
            'slug'              => 'yith-woocommerce-wishlist',
            'required'          => true,
            'version'           => '3.0.17',
        ),
        array(
            'name'              => 'YITH WooCommerce Quick View',
            'slug'              => 'yith-woocommerce-quick-view',
            'required'          => true,
            'version'           => '1.4.7',
        ),
        array(
            'name'              => 'Contact Form 7',
            'slug'              => 'contact-form-7',
            'required'          => false,
            'version'           => '5.3.2',
        ),
        array(
            'name'               => 'Siteorigin Panel',
            'slug'               => 'siteorigin-panels',
            'required'           => true,
            'version'            => '2.4.18',
        ),    
        array(
            'name'               => 'Redux Framework',
            'slug'               => 'redux-framework',
            'required'           => true,
            'version'            => '3.6.15',
        ),          
        array(
            'name'               => 'So widgets bundle',
            'slug'               => 'so-widgets-bundle', 
            'required'           => true,
            'version'            => '1.15.4',
        ),    
        array(
            'name'              => 'Breadcrumb NavXT',
            'slug'              => 'breadcrumb-navxt', 
            'required'          => false,
            'version'           => '6.2.1',
        ),
        array(
            'name'              => 'Slider Revolution',
            'slug'              => 'revslider',
            'required'          => false,
            'version'           => '6.3.5',
            'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/revslider.zip'),
        ),
        array(
            'name'              => 'Netbase Solutions',
            'slug'              => 'netbase_solutions',
            'required'          => true,
            'version'           => '1.9.8',
            'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-tf/netbase_solutions.zip'),
        ),
        array(
            'name'              => 'WooPanel',
            'slug'              => 'woopanel',
            'required'          => false,
            'version'           => '1.2.7',
            'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/woopanel.zip'),
        ),
        array(
            'name'               => 'Netbase Printshop Widgets for SiteOrigin',
            'slug'               => 'netbase-widgets-for-siteorigin-print', 
            'version'           => '1.0.6',
            'required'           => false,
            'source'             => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/netbase-widgets-for-siteorigin-print.zip'),
        ),  
        array(
            'name'               => 'Netbase Shortcodes',
            'slug'               => 'netbase-shortcodes', 
            'version'           => '1.4.5',
            'required'           => false,
            'source'             => esc_url('http://netbaseteam.com/wordpress/theme/plugins/printshop-solution/netbase-shortcodes.zip'),
        ),
        array(
            'name'              => 'Yoast SEO',
            'slug'              => 'wordpress-seo',
            'required'          => false,
            'version'           => '15.4',
        ),
        array(
            'name'              => 'Nbdesigner',
            'slug'              => 'web-to-print-online-designer',
            'required'          => true,
            'version'           => '2.8.3',
            'source'            => esc_url('http://demo9.cmsmart.net/plugins/printshop-solution/odfree/web-to-print-online-designer.zip'),
        ),
        array(
            'name'              => 'Dokan',
            'slug'              => 'dokan-lite',
            'required'          => true,
            'version'           => '2.9.20',
        ),
        array(
            'name'              => 'Workchat',
            'slug'              => 'workchat',
            'required'          => false,
            'version'           => '1.0.1',
        ),
        array(
            'name'              => 'WP Worksuite',
            'slug'              => 'cloodo-worksuite',
            'required'          => false,
            'version'           => '2.1.1',
        ),
        array(
            'name'              => 'Contact Form 7 CRM',
            'slug'              => 'crm-for-contact-form-7',
            'required'          => false,
            'version'           => '1.0.1',
        )
    );
    
    $plugins = $required;

    $config = array(
        'id'           => 'core-wp',                 // Unique ID for hashing notices for multiple instances of TGMPA.
        'default_path' => '',                      // Default absolute path to bundled plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => false,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
    );

    tgmpa( $plugins, $config );
}