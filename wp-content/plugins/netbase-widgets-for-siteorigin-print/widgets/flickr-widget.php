<?php
class wpnetbase_flickr_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		/*Base ID of your widget*/ 
		'wpnetbase_flickr_widget', 

		/*Widget name will appear in UI*/ 
		__('NBT flickr widget', 'wpb_widget_domain'), 

		/*Widget description*/ 
		array( 'description' => __( 'A widget that displays the flickr stream.', 'wpb_widget_domain' ),  'panels_groups' => array('netbaseteam') ) 
		);
	}
    /**
     * How to display the widget on the screen.
     */
    function widget($args, $instance) {
        extract($args);

        /* Our variables from the widget settings. */
        $title = apply_filters('widget_title', $instance['title']);
        $flickr_id = $instance['flickr_id'];
        $post_count = $instance['post_count'];
        $type = $instance['type'];
        $display_mode = $instance['display_mode'];


        /* Before widget (defined by themes). */
        echo $before_widget;

        /* Display the widget title if one was input (before and after defined by themes). */
        if (trim($title) != '')
            echo $before_title . $title . $after_title;

        ?>

    <div id="flickr-widget" class="clearfix">
        <script type="text/javascript"
                src="http://www.flickr.com/badge_code_v2.gne?count=<?php echo $post_count; ?>&amp;display=<?php echo $display_mode; ?>&amp;size=s&amp;layout=x&amp;source=<?php echo $type; ?>&amp;<?php echo $type; ?>=<?php echo $flickr_id; ?>"></script>
    </div>

    <?php

        /* After widget (defined by themes). */
        echo $after_widget;
    }

    /**
     * Update the widget settings.
     */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        /* Strip tags for title and name to remove HTML (important for text inputs). */
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['flickr_id'] = strip_tags($new_instance['flickr_id']);
        $instance['post_count'] = strip_tags($new_instance['post_count']);

        /* No need to strip tags for source type and display mode. */
        $instance['type'] = $new_instance['type'];
        $instance['display_mode'] = $new_instance['display_mode'];

        return $instance;
    }

    /**
     * Displays the widget settings controls on the widget panel.
     * Make use of the get_field_id() and get_field_name() function
     * when creating your form elements. This handles the confusing stuff.
     */
    function form($instance) {

        /* Set up some default widget settings. */
        $defaults = array(
            'title' => __('My Photos', 'mo_theme'),
            'flickr_id' => '147088846@N05',
            'post_count' => '6',
            'type' => 'user',
            'display_mode' => 'latest');
        $instance = wp_parse_args((array)$instance, $defaults); ?>

    <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title:', 'mo_theme'); ?></label>
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
               name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>"/>
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('flickr_id'); ?>"><?php _e('Flickr ID:', 'mo_theme'); ?></label>
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('flickr_id'); ?>"
               name="<?php echo $this->get_field_name('flickr_id'); ?>" value="<?php echo $instance['flickr_id']; ?>"/>
    </p>

    <p>
        <label
            for="<?php echo $this->get_field_id('post_count'); ?>"><?php _e('Number of Photos:', 'mo_theme'); ?></label>
        <input type="text" class="smallfat" id="<?php echo $this->get_field_id('post_count'); ?>"
               name="<?php echo $this->get_field_name('post_count'); ?>"
               value="<?php echo $instance['post_count']; ?>"/>
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Source Type:', 'mo_theme'); ?></label>
        <select class="widefat" id="<?php echo $this->get_field_id('type'); ?>"
                name="<?php echo $this->get_field_name('type'); ?>">
            <option <?php if ('user' == $instance['type']) echo 'selected="selected"'; ?>>user</option>
            <option <?php if ('group' == $instance['type']) echo 'selected="selected"'; ?>>group</option>
        </select>
    </p>

    <p>
        <label
            for="<?php echo $this->get_field_id('display_mode'); ?>"><?php _e('Display Mode:', 'mo_theme'); ?></label>
        <select class="widefat" id="<?php echo $this->get_field_id('display_mode'); ?>"
                name="<?php echo $this->get_field_name('display_mode'); ?>">
            <option <?php if ('latest' == $instance['display_mode']) echo 'selected="selected"'; ?>>latest</option>
            <option <?php if ('random' == $instance['display_mode']) echo 'selected="selected"'; ?>>random</option>
        </select>
    </p>


    <?php
    }
}
function wpnetbase_load_flickr_widget() 
{
	register_widget( 'wpnetbase_flickr_widget' );
}
add_action( 'widgets_init', 'wpnetbase_load_flickr_widget' );
?>