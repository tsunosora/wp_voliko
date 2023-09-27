<?php

/**
 * Geo Location Frontend class
 *
 * @package WooPanel_Modules
 */
class NBT_Geo_Location_Frontend {

    /**
     * Return geo address
     *
     * @var string
     */
    protected $geo_address;

    /**
     * Return multistore theme
     *
     * @var boolean
     */
    protected $multistore = null;

    /**
     * Return is shop
     *
     * @var boolean
     */
    protected $is_shop = false;

    /**
     * Return is shop list
     *
     * @var boolean
     */
    protected $is_storelist = false;

    /**
     * NBT_Geo_Location_Frontend Constructor.
     */
    function __construct() {

        add_shortcode( 'nb_geolocation', array($this, 'geo_location') );
        add_shortcode( 'nb_geolocation_near', array($this, 'geo_location_near') );
        add_action( 'wp_enqueue_scripts', array($this,'enqueue_scripts'), 100 );
        add_filter( 'woocommerce_product_tabs', array($this, 'location_product_tab') );

        add_action('woocommerce_before_shop_loop', array($this, 'add_wrapper_begin'), 3);
        add_action('dokan_before_seller_listing_loop', array($this, 'add_wrapper_begin'), 3);

        add_action('dokan_after_seller_listing_loop', array($this, 'add_wrapper_end'), 9999);
        add_action('woocommerce_after_shop_loop', array($this, 'add_wrapper_end'), 99999);
        

        add_filter('dokan_show_seller_search', '__return_false');
        add_action('dokan_before_seller_listing_loop', array($this, 'add_geo_location_store_lists'), 2, 1);
        

        add_action( 'wp_ajax_woopanel_geolocation_search_products', array( new NBT_Geo_Location_Ajax(), 'search_products' ) );
        add_action( 'wp_ajax_nopriv_woopanel_geolocation_search_products', array( new NBT_Geo_Location_Ajax(), 'search_products' ) );

        add_action( 'wp_ajax_woopanel_geolocation_nearstore', array( new NBT_Geo_Location_Ajax(), 'near_store' ) );
        add_action( 'wp_ajax_nopriv_woopanel_geolocation_nearstore', array( new NBT_Geo_Location_Ajax(), 'near_store' ) );

        //add_filter( 'dokan_seller_listing', array( $this, 'dokan_seller_listing') );

        $this->is_shop = get_option('show_location_shop');
        $this->is_storelist = get_option('show_location_storelist');
        $base = get_template_directory() . '/netbase-core/core.php';
        if( file_exists($base) ) {
            if( function_exists('multistore_get_options') ) {
                $this->multistore = true;
            }
        }

        if( ! $this->multistore && ! wp_doing_ajax() && $this->is_shop ) {
            add_action('woocommerce_before_shop_loop', array($this, 'add_geo_location_shop'), 2);
        }

        if( $this->multistore && $this->is_shop ) {
            add_action( 'woocommerce_archive_description', array( $this, 'woocommerce_archive_description') );
            add_action( 'woocommerce_after_main_content', array( $this, 'woocommerce_after_main_content') );
        }


    }

    public function woocommerce_archive_description() {
        $this->add_geo_location_shop();
        echo '<div class="woopanel-wrapper">';
    }

    public function woocommerce_after_main_content() {
        echo '</div>';
    }

    public function add_wrapper_begin() {
        if( ! $this->multistore ) {
            echo '<div class="woopanel-wrapper">';
        }
    }

    public function add_wrapper_end() {
       if( ! $this->multistore ) {
            echo '</div>';
        }
    }


    public function dokan_seller_listing( $content ) {
        if( $this->multistore ) {
            ob_start();
            $this->add_wrapper_begin();
            $start = ob_get_clean();

            ob_start();
            $this->add_wrapper_end();
            $end = ob_get_clean();
            

            ob_start();
            $this->add_geo_location_store_lists(false);
            $geolocation = ob_get_clean();

            $content = esc_attr( $geolocation ) . esc_attr( $start ) . esc_attr( $content ) . esc_attr( $end );
        }

        return $content;
    }

    public function geo_location( $atts ) {

        extract(shortcode_atts(array(
            'type' => 'single',
            'settings' => array(
                'search_product' => ( isset($atts['search_product']) && $atts['search_product'] == true) ? true : false,
                'search_location' => ( isset($atts['search_location']) && $atts['search_location'] == true) ? true : false,
                'search_vendor' => ( isset($atts['search_vendor']) && $atts['search_vendor'] == true) ? true : false,
                'product_cat' => ( isset($atts['product_cat']) && $atts['product_cat'] == true) ? true : false,
            ),
            'address' => false,
            'position' => false,
            'lat' => false,
            'lng' => false
        ), $atts));

        $new_atts = array();
        if( ! empty($atts) ) {
            foreach( $atts as $k => $v) {
                if( isset($settings[$k]) ){
                    $new_atts[$k] = $settings[$k];
                }
            }
        }


        $settings = array_filter($settings);

        if( ! empty($lat) && ! empty($lng) ) {
            $position = htmlspecialchars(json_encode(array(
                'lat' => $lat,
                'lng' => $lng
            )));
        }

        ob_start();
        $attributes = '';
        if( $type == 'single') {
            $attributes .= ' data-address="' . esc_attr($address) .'" data-position="' . esc_attr($position) .'"';
        }else {
            $attributes .= ' data-position="' . esc_attr($position) .'"';
        }

        printf('<div class="woopanel-geolocation-wrapper woopanel-geolocation-%1$s" data-type="%1$s"%2$s>', $type, $attributes);

        if( $type == 'advanced' ) {
            $total_column = count($settings);
            if( ! empty($total_column) ) {
                
                echo '<div class="woopanel-geolocation-row woopanel-geolocation-search">';
                foreach( $new_atts as $setting => $true) {
                    printf('<div class="woopanel-geolocation-col" style="width: %s">', (100/$total_column) .'%' );

                    switch( $setting ) {
                        case 'search_product':
                            echo '<input type="text" name="wpl_geolocation_product" class="form-control wpl-form-control wpl-search-products" placeholder="'. esc_html__('Search Products', 'woopanel' ) .'" />';
                            break;
                        case 'search_location':
                            echo '<div class="woopanel-geolocation-searchlocal">';
                            echo '<input type="text" name="wpl_geolocation_location" class="form-control wpl-form-control wpl-search-location" placeholder="'. esc_html__('Location', 'woopanel' ) .'" />';
                            echo '<i class="wpl-icon-searchlocal"></i>';
                            echo '</div>';
                            break;
                        case 'search_vendor':
                            echo '<input type="text" name="wpl_geolocation_product" class="form-control wpl-form-control wpl-search-vendors" placeholder="'. esc_html__('Search Vendors', 'woopanel' ) .'" />';
                            break;
                        case 'product_cat':
                            wp_dropdown_categories( array(
                                'taxonomy' => 'product_cat',
                                'name' => 'wpl_geolocation_product_cat',
                                'class' => 'input-text form-control wpl-form-control wpl-product-cat'
                            ) );
                            break;
                    }
                    echo '</div>';
                }
                echo '</div>';
            }
        }

        if( $address ) {
            printf('<p class="woopanel-geolocation-address">%s</p>', $address);
        }

        $micro = rand(0000,9999);
        $id = uniqid('woopanel-geoid-'.esc_attr($micro) );
        
        printf('<div id="%s" class="woopanel-geolocation-map"></div></div>', $id);
        return ob_get_clean();
    }

    public function geo_location_near($atts, $content = null) {
        $html = $attr_parent = '';

        $register_atts = array(
            'title'             => '',
            'store_type'        => 'recent',
            'store_ids'          => '',
            'per_page'          => '',
            'per_row'           => 4,
            'slider'            => true,
            'margin'            => 30,
            'avatar_position'   => 'top',
            'bgcolor_custom'    => '',
            'bordercolor_box'   => '',
            'columnstablet'     =>'2',
            'pagination'        => '',
            'autoplay'          => '',
            'rtl'               => ''
        );
        // Extract shortcode parameters.
        extract(
            shortcode_atts(
                $register_atts,
                $atts
            )
        );

        $attribute = str_replace("=", '="', http_build_query($register_atts, null, '" ', PHP_QUERY_RFC3986)).'"';
        return sprintf('<div class="woopanel-near-store"%s></div>', $attribute);
   
    }



    /**
     * Add GEO Location tab in Product Tab
     */ 
    public function location_product_tab($tabs) {
        global $post, $current_user;

        if( NBT_Solutions_Geo_Location::get_admin_geo() ) {
            $this->geo_address = get_post_meta($post->ID, 'user_geo_location', true);
            $this->map_lat = get_post_meta($post->ID, '_product_map_lat', true);
            $this->map_lng = get_post_meta($post->ID, '_product_map_lng', true);

            if( empty($this->geo_address) ) {

                $this->geo_address = get_user_meta($post->post_author, 'user_geo_location', true);
                $this->map_lat = get_user_meta($post->post_author, 'woopanel_map_lat', true);
                $this->map_lng = get_user_meta($post->post_author, 'woopanel_map_lng', true);
            }



            if( $this->geo_address && $this->map_lat ) {
                $tabs['location_tab'] = array(
                    'title'     => esc_html__( 'Product Location', 'woopanel' ),
                    'priority'  => 20,
                    'callback'  => array($this, 'location_product_tab_content')
                );
            }
        }

        return $tabs;
    }


    /**
     * Display content GEO Location in Product Tab
     */ 
    public function location_product_tab_content() {
        if( $this->geo_address ) {
            wp_enqueue_style( 'mapjs-ui' );
            wp_enqueue_script( 'mapsjs-core' );
            wp_enqueue_script( 'mapjs-service' );
            wp_enqueue_script( 'mapjs-ui' );
            wp_enqueue_script( 'mapjs-mapevents' );
        }

        if( $this->geo_address ) {
            echo do_shortcode('[nb_geolocation address="'. esc_attr($this->geo_address) .'" lat="' . esc_attr($this->map_lat) .'" lng="' . esc_attr($this->map_lng) .'"]');
        }else {
            echo '<p>' . esc_html__('Map isn\'t available. If you are vendor of this product, please set address your store at Seller Center.', 'woopanel' ) .'</p>';
        }

        
    }

    /**
     * Shortcode display GEO Location in Store page
     */ 
    public function add_geo_location_shop() {
        global $wp_query;
        
        if( get_query_var( 'store' ) ) {
            return;
        }
        
        if( isset($wp_query->query['post_type']) && $wp_query->query['post_type'] == 'product' ) {
            echo '<div class="woopanel-shop-geolocation">';
            echo do_shortcode('[nb_geolocation type="advanced" search_product="true" search_location="true" product_cat="true" lat="21.0401" lng="105.6863664"]');
            echo '</div>';
        }
    }
   
    /**
     * Shortcode display GEO Location in Store Lists
     */ 
    public function add_geo_location_store_lists($vendor) {
        if( $this->is_storelist ) {
            echo '<div class="woopanel-shop-geolocation">';
            echo do_shortcode('[nb_geolocation type="advanced" search_vendor="true" search_location="true" lat="21.0401" lng="105.6863664"]');
            echo '</div>';
        }
    }



    /**
     * Enqueue styles.
     */
    public function enqueue_scripts() {
        global $wp_query;

        if( ! empty($wp_query->query) && (
            ( isset($wp_query->query['post_type']) && $wp_query->query['post_type'] == 'product') || 
            ( isset($wp_query->query['pagename']) && $wp_query->query['pagename'] == 'store-listing')
        ) ) {
            if( NBT_Solutions_Geo_Location::get_woopanel_geo() || NBT_Solutions_Geo_Location::get_admin_geo() ) {


                wp_enqueue_style('here-mapsjs', '//js.api.here.com/v3/3.0/mapsjs-ui.css?dp-version=1549984893', array(), NBT_Solutions_Geo_Location::$here_ver, 'all' );
                wp_enqueue_script( 'here-mapsjs-core', '//js.api.here.com/v3/3.0/mapsjs-core.js', array(), NBT_Solutions_Geo_Location::$here_ver, false );
                wp_enqueue_script( 'here-mapsjs-service', '//js.api.here.com/v3/3.0/mapsjs-service.js', array(), NBT_Solutions_Geo_Location::$here_ver, false );
                wp_enqueue_script( 'here-mapsjs-ui', '//js.api.here.com/v3/3.0/mapsjs-ui.js', array(), NBT_Solutions_Geo_Location::$here_ver, false );
                wp_enqueue_script( 'here-mapsjs-mapevents', '//js.api.here.com/v3/3.0/mapsjs-mapevents.js', array(), NBT_Solutions_Geo_Location::$here_ver, false );

                wp_enqueue_script( 'heremap-frontend', NBT_GEOLOCAL_URL . 'assets/js/frontend.js', array(), '1.0', true );
                

            }
        }
    }
}

/**
 * Returns the main instance of NBT_Geo_Location_Frontend.
 *
 * @since  1.0.0
 * @return NBT_Geo_Location_Frontend
 */
new NBT_Geo_Location_Frontend();