<?php
class wpnetbase_social_media_widget extends WP_Widget {

	private $networks;
    protected $socialicon;

	function __construct() {
		parent::__construct(
		// Base ID of your widget
		'wpnetbase_social_media_widget', 

		// Widget name will appear in UI
		__('NBT Social Media', 'wpb_widget_domain'), 

		// Widget description
		array( 'description' => __( 'Add nice little icons that link out to your social media profiles.', 'wpb_widget_domain' ), 'panels_groups' => array('netbaseteam')) 
		);
				
		$this->networks = apply_filters('nbt_social_widget_networks', array(
            'facebook' => __('Facebook', 'nbt'),
            'twitter' => __('Twitter', 'nbt'),
            'google-plus' => __('Google Plus', 'nbt'),
            'rss' => __('RSS', 'nbt'),
        ));
        $this->socialicon = array( 'font', 'images');

	}

    public function widget( $args, $instance ) {
        // outputs the content of the widget
        echo $args['before_widget'];
        if(isset($instance['socialicon']))
        {
            $socialicon =  $instance['socialicon'];
            
        }else{$socialicon = 'images';}

        if(!empty($instance['title'])) {
            echo $args['before_title'].$instance['title'].$args['after_title'];
        }
        ?>
        <div class="sfsi_widget">
        <?php
        if($socialicon=='font'){
            foreach($this->networks as $id => $name) {
            if(!empty($instance[$id])) {
                ?>
                <a class="nbt-social-media-icon social-media-icon-<?php echo $id ?> social-media-icon-<?php echo esc_attr($instance['size']) ?>" href="<?php echo esc_url( $instance[$id], array('http', 'https', 'mailto', 'skype') ) ?>" title="<?php echo esc_html( get_bloginfo('name') . ' ' . $name ) ?>" <?php if(!empty($instance['new_window'])) echo 'target="_blank"'; ?>><?php

                $icon = apply_filters('nbt_social_widget_icon_'.$id, '');
                if(!empty($icon)) echo $icon;
                else echo '<span class="fa fa-' . $id . '"></span>';
                ?></a><?php
            }
            }
        }
        else{
            foreach($this->networks as $id => $name) {
            if(!empty($instance[$id])) {
                ?>
                <a class="nbt-social-media-icon social-media-icon-<?php echo $id ?> social-media-icon-<?php echo esc_attr($instance['size']) ?>" href="<?php echo esc_url( $instance[$id], array('http', 'https', 'mailto', 'skype') ) ?>" title="<?php echo esc_html( get_bloginfo('name') . ' ' . $name ) ?>" <?php if(!empty($instance['new_window'])) echo 'target="_blank"'; ?>>
                
                <?php

                $icon = apply_filters('nbt_social_widget_icon_'.$id, '');
                if(!empty($icon)) echo $icon;
                else echo '<img src="'.plugin_dir_url(__FILE__).'assets/images/default_'.$id.'.png" alt="'. esc_html( get_bloginfo('name') . ' ' . $name ).'" width="40px">';

                ?></a><?php
            }
            }
        }
        ?>
        </div>
        <?php

        echo $args['after_widget'];
    }

	/**
	 * Widget Form.
	 *
	 * Outputs the widget form that allows users to control the output of the widget.
	 *
	 */
	public function form( $instance ) {
        $instance = wp_parse_args($instance, array(
            'size' => 'medium',
            'title' => '',
            'new_window' => false,
        ) );

        if ( isset( $instance[ 'socialicon' ] ) ) 
        {
                $socialicon = $instance[ 'socialicon' ];
        }

        $sizes = apply_filters('nbt_social_widget_sizes', array(
            'medium' => __('Medium', 'nbt'),
        ));

        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title') ?>"><?php _e('Title', 'nbt') ?></label><br/>
            <input type="text" name="<?php echo $this->get_field_name('title') ?>" id="<?php echo $this->get_field_id('title') ?>" value="<?php echo esc_attr($instance['title']) ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('size') ?>"><?php _e('Icon Size', 'nbt') ?></label><br/>
            <select id="<?php echo $this->get_field_id('size') ?>" name="<?php echo $this->get_field_name('size') ?>">
                <?php foreach($sizes as $id => $name) : ?>
                    <option value="<?php echo esc_attr($id) ?>" <?php selected($instance['size'], $id) ?>><?php echo esc_html($name) ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>            
        <label for="<?php echo $this->get_field_id( 'socialicon' ); ?>"><?php _e( 'Social Icon', 'nbt' ); ?>:</label>
            <select id="<?php echo $this->get_field_id( 'socialicon' ); ?>" name="<?php echo $this->get_field_name( 'socialicon' ); ?>">
            <?php
                foreach ( (array) $this->socialicon as $socialicon ) {
                    printf( '<option value="%s" %s>%s</option>', $socialicon, selected( $socialicon, $instance['socialicon'], 0 ), $socialicon );
                }
            ?>
            </select>
        </p>

        </p>
        <?php
        foreach($this->networks as $id => $name) {
            ?>
            <p>
                <label for="<?php echo $this->get_field_id($id) ?>"><?php echo $name ?></label>
                <input type="text" id="<?php echo $this->get_field_id($id) ?>" name="<?php echo $this->get_field_name($id) ?>" value="<?php echo esc_attr(!empty($instance[$id]) ? $instance[$id] : '') ?>" class="widefat"/>
            </p>
        <?php        
        }
        ?>
        <p>
            <input type="checkbox" name="<?php echo $this->get_field_name('new_window') ?>" id="<?php echo $this->get_field_id('new_window') ?>" <?php checked($instance['new_window']) ?> />
            <label for="<?php echo $this->get_field_id('new_window') ?>"><?php _e('Open in New Window', 'nbt') ?></label>

        </p>
        <?php     
            
        
    }
    public function update( $new_instance, $old_instance ) {
        $new_instance['new_window'] = !empty($new_instance['new_window']);
        $instance['socialicon'] = ( ! empty( $new_instance['socialicon'] ) ) ? strip_tags( $new_instance['socialicon'] ) : '';
        
        return $new_instance;
    }	

}

function wpnetbase_social_load_widget() 
{
	register_widget( 'wpnetbase_social_media_widget');
}
add_action( 'widgets_init', 'wpnetbase_social_load_widget' );
