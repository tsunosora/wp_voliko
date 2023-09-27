<?php
// Add menus when woocommerce available
function woopanel_add_woo_menus(){

    if( !is_woo_available() ) return;

    global $woopanel_menus, $woopanel_submenus;

    $woopanel_menus_woo = [
        15 => [
            'id'         => 'products',
            'menu_slug'  => get_option( 'woopanel_products_endpoint', 'products' ),
            'menu_title' => esc_html__( 'Products', 'woopanel' ),
            'capability' => '',
            'page_title' => '',
            'icon'       => 'flaticon-box',
            'classes'    => '',
            'permission' => 'vendor'
        ],
        20 => [
            'id'         => 'product-orders',
            'menu_slug'  => get_option( 'woopanel_orders_endpoint', 'product-orders' ),
            'menu_title' => esc_html__( 'Orders', 'woopanel' ),
            'capability' => '',
            'page_title' => '',
            'icon'       => 'flaticon-notepad',
            'classes'    => '',
            'permission' => 'vendor'
        ],
        23 => [
            'id'         => 'coupons',
            'menu_slug'  => get_option( 'woopanel_coupons_endpoint', 'coupons' ),
            'menu_title' => esc_html__( 'Coupons', 'woopanel' ),
            'capability' => '',
            'page_title' => '',
            'icon'       => 'flaticon-price-tag',
            'classes'    => '',
            'permission' => 'vendor'
        ],
        25 => [
            'id'         => 'customers',
            'menu_slug'  => get_option( 'woopanel_customers_endpoint', 'customers' ),
            'menu_title' => esc_html__( 'Customers', 'woopanel' ),
            'capability' => '',
            'page_title' => '',
            'icon'       => 'flaticon-users',
            'classes'    => '',
            'permission' => 'vendor'
        ],
    ];
    $woopanel_submenus_woo = [
        'products' => [
            4 => [
                'id'         => 'products',
                'menu_slug'  => 'products',
                'label'      => esc_html__( 'All Products', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ],
            5 => [
                'id'         => 'products_publish',
                'menu_slug'  => 'products?post_status=publish',
                'label'      => _x( 'Published', 'post status' ),
                'page_title' => '',
                'capability' => '',
            ],
            6 => [
                'id'         => 'products_pending',
                'menu_slug'  => 'products?post_status=pending',
                'label'      => _x( 'Pending Preview', 'post status' ),
                'page_title' => '',
                'capability' => '',
            ],
            7 => [
                'id'         => 'products_draft',
                'menu_slug'  => 'products?post_status=draft',
                'label'      => _x( 'Draft', 'post status' ),
                'page_title' => '',
                'capability' => '',
            ],
            8 => [
                'id'         => 'products_trash',
                'menu_slug'  => 'products?post_status=trash',
                'label'      => _x( 'Trash', 'post status' ),
                'page_title' => '',
                'capability' => '',
            ],
            10 => [
                'id'         => 'separator'
            ],
            15 => [
                'id'         => 'product_new',
                'menu_slug'  => 'product',
                'label'      => esc_html__( 'Add new', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ],
            20 => [
                'id'         => 'review',
                'menu_slug'  => 'reviews',
                'label'      => esc_html__( 'Reviews', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ],
            30 => [
                'id'         => 'separator'
            ],
            35 => [
                'id'         => 'product-categories',
                'menu_slug'  => 'product-categories',
                'label'      => esc_html__( 'Categories', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ],
            40 => [
                'id'         => 'product-tags',
                'menu_slug'  => 'product-tags',
                'label'      => esc_html__( 'Tags', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ],
            45 => [
                'id'         => 'product-attributes',
                'menu_slug'  => 'product-attributes',
                'label'      => esc_html__( 'Attributes', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ],
        ],
        'product-orders' => [
            5 => [
                'id'         => 'product-orders',
                'menu_slug'  => 'product-orders',
                'label'      => esc_html__( 'All Orders', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ],
            6 => [
                'id'         => 'orders_processing',
                'menu_slug'  => 'product-orders?status=wc-processing',
                'label'      => _x( 'Processing', 'Order status', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ],
            7 => [
                'id'         => 'orders_on-hold',
                'menu_slug'  => 'product-orders?status=wc-on-hold',
                'label'      => _x( 'On hold', 'Order status', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ],
            8 => [
                'id'         => 'orders_completed',
                'menu_slug'  => 'product-orders?status=wc-completed',
                'label'      => _x( 'Completed', 'Order status', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ],
            9 => [
                'id'         => 'orders_cancelled',
                'menu_slug'  => 'product-orders?status=wc-cancelled',
                'label'      => _x( 'Cancelled', 'Order status', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ]
        ],
        'coupons' => [
            5 => [
                'id'         => 'coupons',
                'menu_slug'  => 'coupons',
                'label'      => esc_html__( 'All coupons', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ],
            6 => [
                'id'         => 'coupon_new',
                'menu_slug'  => 'coupon',
                'label'      => esc_html__( 'Add new', 'woopanel' ),
                'page_title' => '',
                'capability' => '',
            ],
        ]
    ];



    $woopanel_menus    = $woopanel_menus + $woopanel_menus_woo;
    $woopanel_submenus = apply_filters( 'woopanel_submenus_woocommerce', $woopanel_submenus + $woopanel_submenus_woo );
    ksort($woopanel_menus);

    if( ! is_super_admin() ) {
        unset($woopanel_submenus['articles'][15]);

        unset($woopanel_submenus['products'][15]);
        unset($woopanel_submenus['products'][30]);
        unset($woopanel_submenus['products'][35]);
        unset($woopanel_submenus['products'][40]);
        unset($woopanel_submenus['products'][45]);


    }
    unset($woopanel_menus[23]);
    unset($woopanel_submenus['coupons'][5]);
    unset($woopanel_submenus['coupons'][6]);
}

add_action( 'woopanel_add_menu', 'woopanel_add_woo_menus' );