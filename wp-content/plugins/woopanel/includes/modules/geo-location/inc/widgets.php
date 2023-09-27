<?php
if( ! function_exists('woopanel_register_widgets_display_map') ) {
	add_action( 'widgets_init', 'woopanel_register_widgets_display_map' );
    function woopanel_register_widgets_display_map() { 
        register_widget( 'WooPanel_Geo_Location_Widgets' );
    }

	/**
	 * WooPanel GEO Location Widgets Class
	 *
	 * @package WooPanel_Layout
	 */
	class WooPanel_Geo_Location_Widgets extends WP_Widget {
		public function __construct() {
			$widget_ops = array( 
				'classname' => 'woopanel_product_location_widgets',
				'description' => 'A plugin for Kinsta blog readers',
			);
			parent::__construct( 'woopanel_product_location_widgets', 'Woopanel: Product Location', $widget_ops );
		}

		public function widget( $args, $instance ) {
			global $post;

			$geo_address = get_post_meta($post->ID, 'user_geo_location', true);
			$geo_position = get_post_meta($post->ID, 'user_geo_position', true);
			
			if( $geo_address && $geo_position ) {
				print($args['before_widget']);
				
				if ( ! empty( $instance['title'] ) ) {
					print($args['before_title'] . apply_filters( 'widget_title', esc_attr( $instance['title'] ) ) . esc_attr( $args['after_title']) );
				}
				
				echo do_shortcode('[nb_geolocation address="'. esc_attr($geo_address) .'" position="' . htmlspecialchars($geo_position) .'"]');
				
				
				print($args['after_widget']);
			}
		}

		public function form( $instance ) {
			$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Product Location', 'woopanel' );
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'woopanel' ); ?></label> 
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>
			<?php
		}

		public function update( $new_instance, $old_instance ) {
			$instance = array();
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			return $instance;
		}
	}
}


