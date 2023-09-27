<?php
class wpnetbase_popular_posts_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		/*Base ID of your widget*/ 
		'wpnetbase_popular_posts_widget', 

		/*Widget name will appear in UI*/ 
		__('NBT popular posts widget', 'wpb_widget_domain'), 

		/*Widget description*/ 
		array( 'description' => __( 'the most popular posts.', 'wpb_widget_domain' ),  'panels_groups' => array('netbaseteam') ) 
		);
	}
    /**
     * How to display the widget on the screen.
     */
    function widget($args, $instance) {
        extract($args);

        /* Our variables from the widget settings. */
        $title = apply_filters('widget_title', $instance['title']);
        $post_count = $instance['post_count'];

        $loop = new WP_Query(array('orderby' => 'comment_count', 'posts_per_page' => $post_count, 'ignore_sticky_posts' => 1));

        if ($loop->have_posts()) {

            /* Before widget (defined by themes). */
            echo $before_widget;

            /* Display the widget title if one was input (before and after defined by themes). */
            if (trim($title) != '')
                echo $before_title . $title . $after_title;

            $args = array(
                'hide_thumbnail' => ($instance['hide_thumbnail'] ? true : false),
                'excerpt_count' => $instance['excerpt_count'],
                'loop' => $loop
            );
            
            /* Set the default arguments. */
        $defaults = array(
            'loop' => null,
            'image_size' => 'small',
            'style' => null,
            'show_meta' => false,
            'excerpt_count' => 120,
            'hide_thumbnail' => false
        );
			//$output = '';
			/* Merge the input arguments and the defaults. */
        $args = wp_parse_args($args, $defaults);

        /* Extract the array to allow easy use of variables. */
        extract($args);

        if (!$loop)
            $loop = new WP_Query($query_args);
            if ($loop->have_posts()):

            $css_class = $image_size . '-size';

            $style = ($style ? ' ' . $style : '');

            echo '<ul class="post-list' . $style . ' ' . $css_class . '">';
            $hide_thumbnail = wpnetbase_to_boolean($hide_thumbnail);

            $show_meta = wpnetbase_to_boolean($show_meta);
            while ($loop->have_posts()) : $loop->the_post();

                echo '<li>';

                $thumbnail_exists = false;
                echo '<div class="entry-text-wrap ' . ($hide_thumbnail ? '' : 'nothumbnail') . '">';
                if(!$hide_thumbnail){
                	if ( has_post_thumbnail() ) {
							    	the_post_thumbnail();
								} 
                }
                echo '<div class="title-populer-post-widget">';
                the_title();
                echo '</div>';
                
             	echo  '</div><!-- entry-text-wrap -->';

                

                echo  '</li>';

            endwhile;

           echo '</ul>';

        endif;

        wp_reset_postdata();

          //  echo $output;

            /* After widget (defined by themes). */
            echo $after_widget;
        } //endif
    }

    /**
     * Update the widget settings.
     */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        /* Strip tags for title and name to remove HTML (important for text inputs). */
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['post_count'] = strip_tags($new_instance['post_count']);
        $instance['excerpt_count'] = strip_tags($new_instance['excerpt_count']);

        // no stripping tags for checkbox content
        $instance['hide_thumbnail'] = !empty($new_instance['hide_thumbnail']) ? 1 : 0;
       
        return $instance;
    }

    /**
     * Displays the widget settings controls on the widget panel.
     * Make use of the get_field_id() and get_field_name() function
     * when creating your form elements. This handles the confusing stuff.
     */
    function form($instance) {

        /* Set up some default widget settings. */
        $defaults = array('title' => __('Most Popular Posts', 'mo_theme'), 'post_count' => '5', 'excerpt_count' => '100', 'hide_thumbnail' => false, 'show_meta' => false);
        $instance = wp_parse_args((array) $instance, $defaults);

        $show_meta = isset($instance['show_meta']) ? (bool) $instance['show_meta'] : false;

        $hide_thumbnail = isset($instance['hide_thumbnail']) ? (bool) $instance['hide_thumbnail'] : false;
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title:', 'mo_theme'); ?></label>
            <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('post_count'); ?>"><?php _e('Post Count:', 'mo_theme'); ?></label>
            <input type="text" class="smallfat" id="<?php echo $this->get_field_id('post_count'); ?>" name="<?php echo $this->get_field_name('post_count'); ?>" value="<?php echo $instance['post_count']; ?>" />
        </p> 

        <p>
            <input class="checkbox" type="checkbox" id="<?php echo $this->get_field_id('hide_thumbnail'); ?>" name="<?php echo $this->get_field_name('hide_thumbnail'); ?>" <?php checked($hide_thumbnail); ?> /> 
            <label for="<?php echo $this->get_field_id('hide_thumbnail'); ?>"><?php _e('Hide Thumbnail?', 'mo_theme'); ?></label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('excerpt_count'); ?>"><?php _e('Length of Summary:', 'mo_theme'); ?></label>
            <input type="text" class="smallfat" id="<?php echo $this->get_field_id('excerpt_count'); ?>" name="<?php echo $this->get_field_name('excerpt_count'); ?>" value="<?php echo $instance['excerpt_count']; ?>" />
            <small>(0 for no excerpt)</small>
        </p> 

        

        <?php
    }

}
function wpnetbase_load_popular_posts_widget() 
{
	register_widget( 'wpnetbase_popular_posts_widget' );
}
add_action( 'widgets_init', 'wpnetbase_load_popular_posts_widget' );
?>