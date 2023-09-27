<?php
class wpnetbase_contact_info_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		/*Base ID of your widget*/ 
		'wpnetbase_contact_info_widget', 

		/*Widget name will appear in UI*/ 
		__('NBT contact info', 'wpb_widget_domain'), 

		/*Widget description*/ 
		array( 'description' => __( 'Contact Info Widget', 'wpb_widget_domain' ),  'panels_groups' => array('netbaseteam') ) 
		);
	}
    /**
     * How to display the widget on the screen.
     */
    function widget($args, $instance) {
        extract($args);

        /* Our variables from the widget settings. */
        $title = apply_filters('widget_title', $instance['title']);

        $title = $instance['title'];
        $street1 = $instance['street1'];
        $street2 = $instance['street2'];
        $city = $instance['city'];
        $state = $instance['state'];
        $zip_code = $instance['zip_code'];
        $phone = $instance['phone'];
        $email = $instance['email'];


        /* Before widget (defined by themes). */
        echo $before_widget;

        /* Display the widget title if one was input (before and after defined by themes). */
        if (trim($title) != '')
            echo $before_title . $title . $after_title;

        echo '<div class="contact-info">';
        
        ?>
        
        <ul class="info">
			<li><i class="fa fa-home"></i>
			<?php if (!empty($street1))
            echo '<span class="street1">'.$street1.'</span>';
            if (!empty($street2))
            echo '<span class="street2">'.$street2.'</span>';

	        if (!empty($city) || !empty($state) || !empty($zip_code)) {
	            echo '<span class="city-info">';
	
	            if (!empty($city))
	                echo ' '.$city;
	            // assume city exists and proceed with next two steps
	            if (!empty($state))
	                echo ', ' . $state;
	            if (!empty($zip_code))
	                echo ', ' . $zip_code;
	
	            echo '</span>';
	        }
            
            ?>
				
			</li>
			<li><i class="fa fa-phone"></i>
			<?php  if (!empty($phone))
            echo '<span class="phone">'.$phone.'</span>';
            ?>
				
			</li>
			<li><i class="fa fa-envelope-o"></i>
			<?php 
			if (!empty($email))
            echo '<span class="email"><a href="mailto:'.$email.'" title="send mail">'.$email.'</a></span>';
			?>				
			</li>
		</ul>
        
        
        <?php 
        

        


        echo '</div>';

        /* After widget (defined by themes). */
        echo $after_widget;
    }

    /**
     * Update the widget settings.
     */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;

        /* Strip tags to remove HTML (important for text inputs). */
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['street1'] = strip_tags($new_instance['street1']);
        $instance['street2'] = strip_tags($new_instance['street2']);
        $instance['city'] = strip_tags($new_instance['city']);
        $instance['state'] = strip_tags($new_instance['state']);
        $instance['zip_code'] = strip_tags($new_instance['zip_code']);
        $instance['phone'] = strip_tags($new_instance['phone']);
        $instance['email'] = strip_tags($new_instance['email']);

        return $instance;
    }

    /**
     * Displays the widget settings controls on the widget panel.
     * Make use of the get_field_id() and get_field_name() function
     * when creating your form elements. This handles the confusing stuff.
     */
    function form($instance) {

        /* Set up some default widget settings. */
        $defaults = array('title' => __('Contact Info', 'mo_theme'), 'title' => '', 'street1' => '', 'street2' => '', 'city' => '', 'state' => '', 'zip_code' => '', 'phone' => '', 'email' => '');
        $instance = wp_parse_args((array)$instance, $defaults); ?>

    <!-- Widget Title: Text Input -->
    <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'mo_theme'); ?></label>
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
               name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>"/>
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('street1'); ?>"><?php _e('Street 1:', 'mo_theme'); ?></label>
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('street1'); ?>"
               name="<?php echo $this->get_field_name('street1'); ?>" value="<?php echo $instance['street1']; ?>"/>
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('street2'); ?>"><?php _e('Street 2:', 'mo_theme'); ?></label>
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('street2'); ?>"
               name="<?php echo $this->get_field_name('street2'); ?>" value="<?php echo $instance['street2']; ?>"/>
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('city'); ?>"><?php _e('City:', 'mo_theme'); ?></label>
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('city'); ?>"
               name="<?php echo $this->get_field_name('city'); ?>" value="<?php echo $instance['city']; ?>"/>
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('state'); ?>"><?php _e('State:', 'mo_theme'); ?></label>
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('state'); ?>"
               name="<?php echo $this->get_field_name('state'); ?>" value="<?php echo $instance['state']; ?>"/>
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('zip_code'); ?>"><?php _e('Zip Code:', 'mo_theme'); ?></label>
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('zip_code'); ?>"
               name="<?php echo $this->get_field_name('zip_code'); ?>" value="<?php echo $instance['zip_code']; ?>"/>
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('phone'); ?>"><?php _e('Phone:', 'mo_theme'); ?></label>
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('phone'); ?>"
               name="<?php echo $this->get_field_name('phone'); ?>" value="<?php echo $instance['phone']; ?>"/>
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('email'); ?>"><?php _e('Email:', 'mo_theme'); ?></label>
        <input type="text" class="widefat" id="<?php echo $this->get_field_id('email'); ?>"
               name="<?php echo $this->get_field_name('email'); ?>" value="<?php echo $instance['email']; ?>"/>
    </p>



    <?php
    }
}
function wpnetbase_load_contact_info_widget() 
{
	register_widget( 'wpnetbase_contact_info_widget' );
}
add_action( 'widgets_init', 'wpnetbase_load_contact_info_widget' );
?>