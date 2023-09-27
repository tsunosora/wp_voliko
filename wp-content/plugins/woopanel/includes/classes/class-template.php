<?php

/**
 * WooPanel Template class
 *
 * @package WooPanel_Template
 */
class WooPanel_Template {

    /**
     * WooPanel_Template Constructor.
     */
    public function __construct() {
        
        /**
         * Loaded page template
         *
         * @since 1.0.0
         * @hook page_template
         * @param {string} $path Path template
         */
        add_filter( 'body_class', array( $this, 'add_body_class'), 999, 1 );
        add_action( 'page_template', array( &$this, 'woopanel_page_template' ) );
        add_filter('siteorigin_panels_filter_content_enabled', array($this, 'exclude_woopanel_pages') );

        add_action( 'init', array($this, 'add_field_theme') );
    }


    /**
     * Add body class with router
     */
    public function add_body_class($classes) {
        global $wp_query, $current_user;

        $seller_options = get_user_meta( $current_user->ID, 'seller_options', true );

        if( isset($seller_options['sidebar_mode']) ) {
            $sidebar_mode = $seller_options['sidebar_mode'];
        }

        if( isset($_POST['sidebar_mode']) ) {
            $sidebar_mode = esc_attr($_POST['sidebar_mode']);
        }

        if( isset($sidebar_mode) ) {
            $classes['sidebar_mode'] = 'woopanel-sidebar-' . $sidebar_mode; 
        }

        if( isset($wp_query->query['pagename']) ) {
            $classes[] = 'woopanel-' . $wp_query->query['pagename'];
            unset($wp_query->query['pagename']);
        }

        
        $classes[] = 'woopanel-'. implode(' ', array_keys($wp_query->query)) .'-page';
        $classes[] = 'woopanel-' . woopanel_get_layout() .'-layout';

        return $classes;
    }

    /**
     * Template fullwidth or boxed
     */
    public function woopanel_page_template( $page_template ) {
        global $post, $admin_options;

        if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, WooDashboard_Shortcodes::$shortcodes['dashboard']) ) {
            if( is_user_logged_in() && ! is_shop_staff() && empty($admin_options->options['any_access']) )
                wp_die( esc_html__( 'You have no permission to view this page', 'woopanel' ), esc_html__( 'No permission', 'woopanel' ), array('response'=>403) );


            if( woopanel_get_layout() == 'fixed' ) {
                $page_template = woopanel_locate_template( 'page-boxed.php' );
            }else {
                $page_template = woopanel_locate_template( 'page-fullscreen.php' );
            }
        }

        return $page_template;
    }
    
    public function exclude_woopanel_pages( $return ) {
        global $post, $admin_options;

        if( is_page() ) {
            $post_id = absint($post->ID);

            if( ! empty($admin_options->options) && isset($admin_options->options['dashboard_page_id']) && $admin_options->options['dashboard_page_id'] == $post_id ) {
                $return = false;
            }
        }

        return $return;
    }

    function woopanel_page_title( $title ) {
        global $wp, $woopanel_menus, $woopanel_submenus, $woopanel_post_types;

        $current_url = set_url_scheme( 'http://' . esc_attr( $_SERVER['HTTP_HOST'] ) . esc_attr( $_SERVER['REQUEST_URI'] ) );
        $current_url_noquery = home_url( add_query_arg( array(), $wp->request ) );
        parse_str($_SERVER['QUERY_STRING'], $current_query);
    
        return $title;
    }

    function add_field_theme() {
        global $woopanel_options;

        $woopanel_options['general']['fields'][] = array(
            'id'       => 'sidebar_mode',
            'type'     => 'select',
            'title'    => __( 'Sidebar Mode', 'woopanel'  ),
            'options'  => array(
                'black' => __('Black', 'woopanel' ),
                'white' => __('White', 'woopanel' )
            ),
            'value' => ''
        );
    }
}

/**
 * Returns the main instance of WooPanel_Template.
 *
 * @since  1.0.0
 * @return WooPanel_Template
 */
new WooPanel_Template();