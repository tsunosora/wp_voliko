<?php
// Register and load the widget
function wpl_load_widget()
{
    register_widget('WPL_Vendor_Map_Widget');
    register_sidebar( array(
        'name'          => __( 'WPL Seller Sidebar', 'textdomain' ),
        'id'            => 'wpl-seller-sidebar',
        'description'   => __( 'Widgets in this area will be shown on all posts and pages.', 'textdomain' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>',
    ) );
}
add_action('widgets_init', 'wpl_load_widget');

// Creating the widget 
class WPL_Vendor_Map_Widget extends WP_Widget
{
    
    function __construct()
    {
        parent::__construct(
        // Base ID of your widget
            'wpl_vendor_map_widget', 
        // Widget name will appear in UI
            __('WooPanel: Map Widget', 'wpb_widget_domain'), 
        // Widget description
            array(
            'description' => __('Sample widget based on WPBeginner Tutorial', 'wpb_widget_domain')
        ));
    }
    
    // Creating widget front-end
    
    public function widget($args, $instance)
    {
        global $wp_query;

        if( isset($wp_query->query['wpl_seller']) && ! empty($wp_query->query_vars['store_user'])  ) {
            $apiKey = woopanel_store_config('api_key');

            if( ! $apiKey ) {
                return;
            }

            $store_user = get_query_var('store_user')->data;

            $title = apply_filters('widget_title', $instance['title']);
            
            // before and after widget arguments are defined by themes
            echo $args['before_widget'];
            if (!empty($title))
                echo $args['before_title'] . $title . $args['after_title'];

            
            // This is where you run the code and display the output
            //echo '<div style="width: 100%"><iframe width="100%" height="300" src="'. esc_url($profile_settings['find_address']) .'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"><a href="https://www.maps.ie/draw-radius-circle-map/">km radius map</a></iframe></div><br />';
            printf('<div id="map_canvas" data-lat="%s" data-lng="%s" data-icon="%s" class="map_canvas"></div>', $store_user->lat, $store_user->lng,
                WOODASHBOARD_URL .'assets/images/map-pin.png'
            );

            ?>





            <?php

            echo $args['after_widget'];
        }
    }
    
    // Widget Backend 
    public function form($instance)
    {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('New title', 'wpb_widget_domain');
        }
        
        $iframe = isset($instance['iframe']) ? $instance['iframe'] : '';
        ?>
            <p>
                <label for="<?php echo $this->get_field_id('title');?>"><?php _e('Title:');?></label> 
                <input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php echo esc_attr($title);?>" />
            </p>
        <?php
    }
    
    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {
        $instance          = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
} // Class wpb_widget ends here