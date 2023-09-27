<?php
class Netbase_Dokan{
    public function __construct() {

        add_filter('dokan_no_seller_image', array($this, 'get_dokan_no_seller_image'), 10, 1);

        add_action('dokan_count_store_products', array($this, 'get_dokan_count_store_products'), 10, 2);
        add_action('dokan_count_store_views', array($this, 'get_dokan_count_store_views'), 10, 2);
        add_action('dokan_count_store_reviews', array($this, 'get_dokan_count_store_reviews'), 10, 2);
        add_filter('woocommerce_product_get_rating_html', array($this, 'bh_woocommerce_product_get_rating_html'), 10,2);

        add_action( 'wp_ajax_nopriv_search_store', array($this, 'ajax_search_store') );
        add_action( 'wp_ajax_search_store', array($this, 'ajax_search_store') );

        add_action( 'wp_enqueue_scripts', array($this, 'netbase_dokan_scripts') );


        add_image_size( 'dokan-cover', 600, 300, array( 'left', 'top' ) );



    }


    /**
     * @hooks dokan_no_seller_image
     */
    function get_dokan_no_seller_image(){
        $image = get_template_directory_uri(). '/assets/images/no-seller-image.png';
        return $image;
    }


    /**
     * @hooks wp_enqueue_scripts
     */
    function netbase_dokan_scripts(){
        wp_enqueue_script( 'netbase-dokan', get_template_directory_uri() . '/assets/js/dokan.js', array(), '1.0.0', true );

        wp_localize_script( 'netbase-dokan', 'dokan_ajax', array(
            'url' => admin_url( 'admin-ajax.php' )
        ));
    }

    /**
     * Search Store Listing
     */
    function ajax_search_store() {
        global $wpdb;
        $_json = FALSE;
        $_html = '';


        $input = $_POST['input'];

        $params = array();
        parse_str($input, $params);




        $search_term = '%' . $params['name'] . '%';
        $search_country = '%"' . $params['address'] . '"%';
        $search_tax = $params['product_cat'];
        $search_order = $params['order'];


        $rs = $wpdb->get_results("SELECT * FROM $wpdb->users u LEFT JOIN $wpdb->usermeta name ON u.ID = name.user_id LEFT JOIN $wpdb->usermeta country ON u.ID = country.user_id WHERE name.meta_key = 'dokan_store_name' AND name.meta_value LIKE '$search_term' AND country.meta_key = 'dokan_profile_settings' AND country.meta_value LIKE '$search_country'");

        if($search_tax != '-1'){
            foreach ($rs as $k => $r):
                $rs_posts = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->users u LEFT JOIN $wpdb->posts p ON u.ID = p.post_author LEFT JOIN $wpdb->term_relationships rel ON p.ID = rel.object_id WHERE p.post_author = 12 AND rel.term_taxonomy_id = '$search_tax'");
                if(!$rs_posts){
                    unset($rs[$k]);
                }
            endforeach;
        }


        if($rs){
            $_json['complete'] = true;
            foreach ( $rs as $seller ) {
                $store_info = dokan_get_store_info( $seller->ID );
                $banner_id  = isset( $store_info['banner'] ) ? $store_info['banner'] : 0;
                $store_name = isset( $store_info['store_name'] ) ? esc_html( $store_info['store_name'] ) : __( 'N/A', 'printshop' );
                $store_url  = dokan_get_store_url( $seller->ID );


                $_html .= '
                <div class="col-md-3 dokan-single-seller">
                    <div class="dokan-store-thumbnail">
                        <div class="dokan-store-banner-wrap">
                            <a href="'. $store_url .'">';
                                if ( $banner_id ) {
                                    $banner_url = wp_get_attachment_image_src( $banner_id, $image_size );
                                    $_html .= '<img class="dokan-store-img" src="'. esc_url( $banner_url[0] ) .'" alt="'. esc_attr( $store_name ) .'">';
                                } else {
                                    $_html .= '<img class="dokan-store-img" src="'. dokan_get_no_seller_image() .'" alt="'. __( 'No Image', 'printshop' ) .'">';
                                }
                            $_html .= '
                            </a>
                            <div class="dokan-profile-avatar">
                                <a href="#">
                                    '. get_avatar( $seller->ID, 150 ) .'
                                </a>
                            </div>
                            <div class="dokan-overlay-profile">
                                <a class="dokan-btn dokan-btn-theme" href="'. $store_url. '">'. __( 'Visit Store', 'printshop' ) .'</a>
                            </div>
                        </div>

                        <div class="dokan-store-caption">
                            <p class="dokan-user-store"><a href="#" title="'. $seller->display_name .'">'. $seller->display_name .'</a></p>
                            <h3><a href="'. $store_url .'">'. $store_name .'</a></h3>

                            <ul>
                                <li class="line-clamp"><i class="fa fa-home" aria-hidden="true"></i>';
                                    if ( isset( $store_info['address'] ) && !empty( $store_info['address'] ) ) {
                                        $_html .= dokan_get_seller_address( $seller->ID );
                                    }
                                $_html .= '
                                </li>
                                <li>';
                                    if ( isset( $store_info['phone'] ) && !empty( $store_info['phone'] ) ) {
                                        $_html .= '<i class="fa fa-phone" aria-hidden="true"></i>
                                        <span title="'. __( 'Phone Number', 'printshop' ) .'">'. esc_html( $store_info['phone'] ) .'</span>';
                                    }
                                $_html .= '
                                </li>
                            </ul>
                        </div> <!-- .caption -->
                        <div class="dokan-store-footer">';
                            /**
                             * @hooked dokan_count_store_views
                             * @hooked dokan_count_store_products
                             */
                            $_html .= dokan_count_store_views($seller->ID);
                            $_html .= dokan_count_store_products($seller->ID);
                            $_html .= dokan_count_store_reviews($seller->ID);
                        $_html .= '</div>
                    </div> <!-- .thumbnail -->
                </div> <!-- .single-seller -->';
            }
        }else{
            $_html = __( 'No vendor found!', 'printshop' );;
        }

        $_json['html'] = $_html;

        echo json_encode($_json, TRUE);

        die();
    }


    function bh_woocommerce_product_get_rating_html($rating_html, $rating){
        $rating_html  = '<div class="rating-wrap" title="' . sprintf( __( 'Rated %s out of 5', 'printshop' ), $rating ) . '">';
        $rating_html .= '<div class="rating-content"><div class="star-rating" title="" data-original-title="'. $rating.'"><span style="width:' . ( ( $rating / 5 ) * 100 ) . '%"><strong class="rating">' . $rating . '</strong> ' . __( 'out of 5', 'printshop' ) . '</span></div></div>';
        $rating_html .= '</div>';

        return $rating_html;
    }

}
new Netbase_Dokan();
/**
 * Add container div
 */


function add_wrap_container_before(){
    global $post_id;
    $return = '';
    if ( $post_id ) {
        $return = '<div class="page-title-wrap">
					<div class="container">
						<h1 class="page-entry-title left">';

        $return .= __('Dashboard', 'printshop');

        $return .= '</h1>

<div class="breadcrumbs">			
                <span>'. __('You are here', 'printshop') . ':</span>
				<!-- Breadcrumb NavXT 5.6.0 -->
<span property="itemListElement" typeof="ListItem"><a property="item" typeof="WebPage" title="Go to Tshirt." href="' . esc_url(home_url("/")) .'" class="home"><span property="name">Tshirt</span></a><meta property="position" content="1"></span> &gt; <span property="itemListElement" typeof="ListItem"><span property="name">Dashboard</span><meta property="position" content="2"></span>
			</div>
									
					</div>
				</div>
				<div class="container">';
    }
   print $return;
}
add_action('dokan_dashboard_wrap_before', 'add_wrap_container_before');



function add_wrap_container_after(){
    echo '</div>';
}

add_action('dokan_dashboard_wrap_after', 'add_wrap_container_after');


function add_woocommerce_before_main_content(){
    global $wp;
    $store_user    = get_userdata( get_query_var( 'author' ) );
    $store_info    = dokan_get_store_info( $store_user->ID );

    $label = __('Shop', 'printshop');
    $dokan_general = get_option('dokan_general');

    if(isset($wp->query_vars[$dokan_general['custom_store_url']])){
        $label = $store_info['store_name'];
    }

    $dokan_store_listing = get_post_by_shortcode('dokan-stores');

echo '<div class="page-title-wrap">
					<div class="container">
						<h2 class="page-entry-title left">'.$label.'</h2>
									<div class="breadcrumbs">			
                <span>'. __('You are here', 'printshop') . ':</span>
				<span property="itemListElement" typeof="ListItem">
				<a property="item" typeof="WebPage" title="Go to ." href="' . home_url() . '" class="home"><span property="name">'. get_bloginfo('name') .'</span></a><meta property="position" content="1"></span> &gt; <span property="itemListElement" typeof="ListItem">
				<a property="item" typeof="WebPage" title="Go to '.$dokan_store_listing->post_title.'." href="'.get_permalink($dokan_store_listing->ID).'" class="post post-product-archive"><span property="name">'.$dokan_store_listing->post_title.'</span></a><meta property="position" content="2"></span> &gt; <span property="itemListElement" typeof="ListItem"><span property="name">'.$store_info['store_name'].'</span><meta property="position" content="3"></span>

			</div>
									
					</div>
				</div><div id="content-wrap" class="container left-sidebar">';
}
add_action('woocommerce_before_main_content', 'add_woocommerce_before_main_content');


function add_woocommerce_after_main_content(){

    echo '</div>';
}
add_action('woocommerce_after_main_content', 'add_woocommerce_after_main_content');


remove_action( 'woocommerce_before_main_content','woocommerce_breadcrumb', 20, 0);



register_sidebar( array(
    'name' => __( 'Sidebar Store', 'printshop' ),
    'id' => 'sidebar-store',
    'description' => __( 'show on all posts and pages', 'printshop' ),
    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
    'after_widget'  => '</aside>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
) );


add_filter('body_class', 'multisite_body_classes');

function multisite_body_classes($classes) {
    global $post;
    if( isset($post->post_content) &&  has_shortcode( $post->post_content, 'dokan-best-selling-product' ) ) {
        $classes[] = 'best-sellers';
    }

    $classes[] = 'woocommerce';
    return $classes;
}

remove_shortcode('dokan-best-selling-product');


add_shortcode( 'dokan-best-selling-product', 'best_selling_product_shortcode' );


    function best_selling_product_shortcode( $atts ) {
        /**
         * Filter return the number of best selling product per page.
         *
         * @since 2.2
         *
         * @param array
         */
        $atts_val = shortcode_atts( apply_filters( 'dokan_best_selling_product_per_page', array(
            'no_of_product' => 8,
            'seller_id' => '',
        ), $atts ), $atts );

        ob_start();
        ?>
                <ul class="products dokan-products">
                    <?php
                    $best_selling_query = dokan_get_best_selling_products( $atts_val['no_of_product'], $atts_val['seller_id'] );
                    ?>
                    <?php while ( $best_selling_query->have_posts() ) : $best_selling_query->the_post(); ?>

                        <?php wc_get_template_part( 'content', 'product' ); ?>

                    <?php endwhile; ?>
                </ul>

        <?php

        return ob_get_clean();
    }
remove_shortcode('dokan-top-rated-product');
add_shortcode( 'dokan-top-rated-product','top_rated_product_shortcode' );

function top_rated_product_shortcode( $atts ) {
    /**
     * Filter return the number of top rated product per page.
     *
     * @since 2.2
     *
     * @param array
     */
    $per_page = 4;

    ob_start();
    ?>
    <ul class="products dokan-products">
        <?php
        $best_selling_query = dokan_get_top_rated_products();
        ?>
        <?php while ( $best_selling_query->have_posts() ) : $best_selling_query->the_post(); ?>

            <?php wc_get_template_part( 'content', 'product' ); ?>

        <?php endwhile; ?>
    </ul>
    <?php

    return ob_get_clean();
}



function woo_remove_product_tabs( $tabs )
{
    unset($tabs['seller']);
    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );
//
//
//function dokan_seller_product_tab_rewrite( $tabs) {
//
//    $tabs['sellers'] = array(
//        'title'    => __( 'Vendor Info', 'printshop' ),
//        'priority' => 90,
//        'callback' => 'dokan_product_seller_tab_write'
//    );
//
//    return $tabs;
//}
//
//add_filter( 'woocommerce_product_tabs', 'dokan_seller_product_tab_rewrite' );
//
//function dokan_product_seller_tab_write(){
//    echo rand();
//}




//function show_field_value($store_user, $store_info){
////    echo '<pre>';
////    print_r($store_user);
////    print_r($store_info);
////    echo '</pre>';
//    echo '<h1>Dokan Settings - '.$store_info['store_name'].'</h1>' . __FILE__;
//}
//add_action( 'dokan_store_profile_frame_after', 'show_field_value', 10, 2 );
//
//
//function show_add_before(){
//    echo '---------------> anhlt';
//}
//add_action('dokan_home_after_featured', 'show_add_before', 10, 2);
//
//
///**
// * Insert new URL's to the dashboard navigation bar
// *
// * @param  array  $urls
// *
// * @return array
// */
//function prefix_dokan_add_seller_nav( $urls ) {
//
//    $settings_sub = array(
//        'back' => array(
//            'title' => __( 'Back to Dashboard', 'printshop'),
//            'icon'  => '<i class="fa fa-long-arrow-left"></i>',
//            'url'   => dokan_get_navigation_url(),
//            'pos'   => 10
//        ),
//        'form' => array(
//            'title' => __( 'Form', 'printshop'),
//            'icon'  => '<i class="fa fa-university"></i>',
//            'url'   => dokan_get_navigation_url( 'netbase/form' ),
//            'pos'   => 30
//        )
//    );
//    $urls['google'] = array(
//        'title' => __( 'Google', 'printshop'),
//        'icon'  => '<i class="fa fa-google-plus"></i>',
//        'url'   => dokan_get_navigation_url('netbase/test'),
//        'pos' => 300,
//        'sub' => $settings_sub
//    );
//
//    return $urls;
//}
//
//add_filter( 'dokan_get_dashboard_nav', 'prefix_dokan_add_seller_nav' );
//
///**
// * Unset an item from the menu
// *
// * @param  array  $urls
// *
// * @return array
// */
//function prefix_dokan_add_seller_navs( $urls ) {
//
//    unset( $urls['reviews'] );
//
//    return $urls;
//}
//
//add_filter( 'dokan_get_dashboard_nav', 'prefix_dokan_add_seller_navs' );
//
//
///**
// * Renames an Item title
// *
// * @param  array  $urls
// *
// * @return array
// */
//function prefix_dokan_add_seller_nav_seller( $urls ) {
//
//    $urls['products']['title'] = 'Sản phẩm';
//
//    return $urls;
//}
//
//
//function dokan_before_listing_product_cus(){
//
//    echo 'dokan_before_listing_product_cus';
//
//}
//add_action('dokan_before_listing_product', 'dokan_before_listing_product_cus');


//add_action('dokan_product_edit_before_sidebar', 'dokan_product_edit_before_sidebar_cus');
//function dokan_product_edit_before_sidebar_cus(){
//    echo '<h1>dokan_product_edit_before_sidebar_cus</h1>';
//}

add_action('dokan_edit_product_wrap_before', 'dokan_edit_product_wrap_before_cus', 10, 1);
function dokan_edit_product_wrap_before_cus(){
    echo '<div class="page-title-wrap Dashboard">
					<div class="container">
						<h1 class="page-entry-title left">
						Dashboard</h1>								
				
									<div class="breadcrumbs">			
				<span>You are here:</span>
				<!-- Breadcrumb NavXT 5.6.0 -->
<span property="itemListElement" typeof="ListItem"><a property="item" typeof="WebPage" title="Go to Tshirt." href="http://192.168.9.111/wpdemo/teepro" class="home"><span property="name">Tshirt</span></a><meta property="position" content="1"></span> &gt; <span property="itemListElement" typeof="ListItem"><span property="name">Dashboard</span><meta property="position" content="2"></span>				
			</div>
						
					</div>
				</div>
				<div id="content-wrap" class="container no-sidebar">';

}


add_action('dokan_edit_product_wrap_after', 'dokan_edit_product_wrap_after_cus', 10, 1);

function dokan_edit_product_wrap_after_cus(){
    echo '</div>';
}



/* Marc Dingena Utilities
 * Test image resolution before image crunch
 */
// add_filter('wp_handle_upload_prefilter','mdu_validate_image_size');
// function mdu_validate_image_size( $file ) {
//     global $args;

//     $image = getimagesize($file['tmp_name']);
//     $minimum = array(
//         'width' => '400',
//         'height' => '400'
//     );
//     $maximum = array(
//         'width' => '2000',
//         'height' => '2000'
//     );
//     $image_width = $image[0];
//     $image_height = $image[1];

//     $too_small = "Image dimensions are too small. Minimum size is {$minimum['width']} by {$minimum['height']} pixels. Uploaded image is $image_width by $image_height pixels.";
//     $too_large = "Image dimensions are too large. Maximum size is {$maximum['width']} by {$maximum['height']} pixels. Uploaded image is $image_width by $image_height pixels.";

//     if ( $image_width < $minimum['width'] || $image_height < $minimum['height'] ) {
//         // add in the field 'error' of the $file array the message
//         $file['error'] = $too_small;
//         return $file;
//     }
//     elseif ( $image_width > $maximum['width'] || $image_height > $maximum['height'] ) {
//         //add in the field 'error' of the $file array the message
//         $file['error'] = $too_large;
//         return $file;
//     }
//     else
//         return $file;
// }











/**
 * Renders the Dokan dashboard menu
 *
 * For settings menu, the active menu format is `settings/menu_key_name`.
 * The active menu will be splitted at `/` and the `menu_key_name` will be matched
 * with settings sub menu array. If it's a match, the settings menu will be shown
 * only. Otherwise the main navigation menu will be shown.
 *
 * @param  string  $active_menu
 *
 * @return string rendered menu HTML
 */

function dokan_dashboard_nav_edit( $active_menu = '' ) {

    $nav_menu          = dokan_get_dashboard_nav();

    $active_menu_parts = explode( '/', $active_menu );

    if ( isset( $active_menu_parts[1] ) && $active_menu_parts[0] == 'settings' && array_key_exists( $active_menu_parts[1], $nav_menu['settings']['sub'] ) ) {
        $urls        = $nav_menu['settings']['sub'];
        $active_menu = $active_menu_parts[1];
    } else {
        $urls = $nav_menu;
    }





    $menu = '<ul class="dokan-dashboard-menu">';

    foreach ($urls as $key => $item) {
        $sub_menu = '';
        if(isset($item['sub'])){
            $item['title'] = strip_tags($item['title']);

            $sub_menu .= '<ul class="dokan-submenu">';
            foreach ($item['sub'] as $k => $sub):
                if($sub['url'] != home_url('dashboard/')){
                    $sub_menu .= sprintf( '<li class="%s"><a href="%s">%s %s</a></li>', $class, $sub['url'], $sub['icon'], $sub['title'] );
                }



            endforeach;

            $sub_menu .= '</ul>';

        }
        $class = ( $active_menu == $key ) ? 'active ' . $key : $key;
        $menu .= sprintf( '<li class="%s"><a href="%s">%s %s</a>%s</li>', $class, $item['url'], $item['icon'], $item['title'], $sub_menu );
    }

    $menu .= '<li class="dokan-common-links dokan-clearfix"><a title="' . __( 'Visit Store', 'printshop' ) . '" class="tips" data-placement="top" href="' . dokan_get_store_url( get_current_user_id()) .'" target="_blank"><i class="fa fa-external-link"></i> ' . __( 'Visit Store', 'printshop' ) . '</a></li>
            <li class="dokan-common-links dokan-clearfix"><a title="' . __( 'Edit Account', 'printshop' ) . '" class="tips" data-placement="top" href="' . dokan_get_navigation_url( 'edit-account' ) . '"><i class="fa fa-user"></i> ' . __( 'Edit Account', 'printshop' ) . '</a>
            <li class="dokan-common-links dokan-clearfix"><a title="' . __( 'Log out', 'printshop' ) . '" class="tips" data-placement="top" href="' . wp_logout_url( home_url() ) . '"><i class="fa fa-power-off"></i> ' . __( 'Log out', 'printshop' ) . '</a></li>';

    $menu .= '</ul>';

    return $menu;
}

/**
 * Callback for Bank in store settings
 *
 * @global WP_User $current_user
 * @param array $store_settings
 */
function dokan_withdraw_method_bank_edit( $store_settings ) {
    $account_name   = isset( $store_settings['payment']['bank']['ac_name'] ) ? esc_attr( $store_settings['payment']['bank']['ac_name'] ) : '';
    $account_number = isset( $store_settings['payment']['bank']['ac_number'] ) ? esc_attr( $store_settings['payment']['bank']['ac_number'] ) : '';
    $bank_name      = isset( $store_settings['payment']['bank']['bank_name'] ) ? esc_attr( $store_settings['payment']['bank']['bank_name'] ) : '';
    $bank_addr      = isset( $store_settings['payment']['bank']['bank_addr'] ) ? esc_textarea( $store_settings['payment']['bank']['bank_addr'] ) : '';
    $swift_code     = isset( $store_settings['payment']['bank']['swift'] ) ? esc_attr( $store_settings['payment']['bank']['swift'] ) : '';
    ?>
    <div class="form-group">
        <input name="settings[bank][ac_name]" value="<?php echo $account_name; ?>" class="dokan-form-control" placeholder="<?php esc_attr_e( 'Your bank account name', 'printshop' ); ?>" type="text">

    </div>

    <div class="form-group">
        <input name="settings[bank][ac_number]" value="<?php echo $account_number; ?>" class="dokan-form-control" placeholder="<?php esc_attr_e( 'Your bank account number', 'printshop' ); ?>" type="text">

    </div>

    <div class="form-group">
        <input name="settings[bank][bank_name]" value="<?php echo $bank_name; ?>" class="dokan-form-control" placeholder="<?php _e( 'Name of bank', 'printshop' ) ?>" type="text">

    </div>

    <div class="form-group">
        <textarea name="settings[bank][bank_addr]" class="dokan-form-control" placeholder="<?php esc_attr_e( 'Address of your bank', 'printshop' ) ?>"><?php echo $bank_addr; ?></textarea>
    </div>

    <div class="form-group">
        <input value="<?php echo $swift_code; ?>" name="settings[bank][swift]" class="dokan-form-control" placeholder="<?php esc_attr_e( 'Swift code', 'printshop' ); ?>" type="text">
    </div> <!-- .dokan-form-group -->
    <?php
}

add_filter( 'dokan_withdraw_methods', 'dokan_withdraw_register_methods_edit', 10, 1 );

function dokan_withdraw_register_methods_edit($methods){
    $methods['paypal'] = array(
        'title'    =>  __( 'PayPal', 'printshop' ),
        'callback' => 'dokan_withdraw_method_paypal_edit'
    );

    $methods['bank'] = array(
        'title'    => __( 'Bank Transfer', 'printshop' ),
        'callback' => 'dokan_withdraw_method_bank_edit'
    );
    //mang($methods);
    return $methods;
}

/**
 * Callback for PayPal in store settings
 *
 * @global WP_User $current_user
 * @param array $store_settings
 */
function dokan_withdraw_method_paypal_edit( $store_settings ) {
    global $current_user;

    $email = isset( $store_settings['payment']['paypal']['email'] ) ? esc_attr( $store_settings['payment']['paypal']['email'] ) : $current_user->user_email ;
    ?>
    <div class="dokan-form-group">
            <div class="dokan-input-group">
                <span class="dokan-input-group-addon"><?php _e( 'E-mail', 'printshop' ); ?></span>
                <input value="<?php echo $email; ?>" name="settings[paypal][email]" class="dokan-form-control email dokan-email-group" placeholder="you@domain.com" type="text">
            </div>
    </div>
    <?php
}

add_filter('dokan_get_seller_address', 'get_dokan_get_seller_address', 10, 2);
 function get_dokan_get_seller_address($formatted_address, $profile_info){


     $formatted_address = str_replace( array('<br>', '<br/>'), ', ', $formatted_address);


     return $formatted_address;
 }




function get_post_by_shortcode($shortcode) {
    global $wpdb;
    $sql = 'SELECT ID, post_title
		FROM ' . $wpdb->posts . '
		WHERE
			post_type = "page"
			AND post_status="publish"
			AND post_content LIKE "%' . $shortcode . '%"';

    $rs = $wpdb->get_row($sql);

    return $rs;
}


function dokan_count_store_views($user_id) {
    $_pageviews = get_user_meta( $user_id, '_pageviews', true );
    if(!$_pageviews){
        $_pageviews = 0;
    }
     return '<span><i class="fa fa-eye" aria-hidden="true"></i> '. $_pageviews .'</span>';
}



/**
 * @param $user_id
 * @hooks dokan_count_store_products
 */
function dokan_count_store_products($user_id, $return = false) {
    global $wpdb;
    $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'publish' AND post_author = '$user_id'");

        return '<span><i class="fa fa-pencil-square-o" aria-hidden="true"></i> '.$rowcount.'</span>';


}

/**
 * @param $user_id
 * @hooks dokan_count_store_views
 */


/**
 * @param $user_id
 * @hooks dokan_count_store_reviews
 */
function dokan_count_store_reviews($user_id) {
    global $wpdb;
    $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments LEFT JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID = $wpdb->posts.ID) WHERE $wpdb->comments.comment_approved = '1' AND $wpdb->posts.post_author = '$user_id' AND $wpdb->posts.post_type = 'product' AND $wpdb->posts.post_status = 'publish'");


        return '<span><i class="fa fa-comment" aria-hidden="true"></i> '.$rowcount.'</span>';


}

require_once 'widget-dokan.php';


function custom_title($title_parts) {
    $store_user   = get_userdata( get_query_var( 'author' ) );
    if($store_user && in_array('seller', $store_user->roles)){
        $store_info   = dokan_get_store_info( $store_user->ID );
        $title_parts['title'] = $store_info['store_name'];
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'custom_title' );