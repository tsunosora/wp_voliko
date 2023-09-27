<?php
class wpnetbase_ajaxcart_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		/*Base ID of your widget*/ 
		'wpnetbase_ajaxcart_widget', 

		/*Widget name will appear in UI*/ 
		__('NBT ajax cart', 'wpb_widget_domain'), 

		/*Widget description*/ 
		array( 'description' => __( 'ajax cart.', 'wpb_widget_domain' ),  'panels_groups' => array('netbaseteam') ) 
		);
	}
    /**
     * How to display the widget on the screen.
     */
    function widget($args, $instance) {
        extract($args); // Display arguments including before_title, after_title, before_widget, and after_widget

        
        if (!empty($title)) {
            if ($sidebar_name != 'After Singular') {
                echo $before_title . $title . $after_title;
            }
        }


       echo do_shortcode('[wpnetbase_ajaxcart]'); 

        /* After widget (defined by themes). */
        echo $after_widget;
    }

    /**
     * Update the widget settings.
     */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;

       
        $instance['title'] = strip_tags($new_instance['title']);

       

        return $instance;
    }

    function form($instance) {

        /* Set up some default widget settings. */
        $defaults = array(
            'title' => '',
            
        );
        $instance = wp_parse_args((array)$instance, $defaults); ?>

        <p>
            <label
                for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title:(optional)', 'mo_theme'); ?></label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>"/>
        </p>

       

    <?php
    }
}
function wpnetbase_load_ajaxcart_widget() 
{
	register_widget( 'wpnetbase_ajaxcart_widget' );
}
add_action( 'widgets_init', 'wpnetbase_load_ajaxcart_widget' );