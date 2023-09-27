<?php
class NBT_Ajax_Search_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'nbt-wc-ajaxsearch',
			__( 'NB-Solution: Ajax Search', 'nbt-solution' ),
			array(
				'classname' => 'nbt-wc-ajaxsearch',
				'description' => __( 'Enter a custom description for your new widget', 'nbt-solution' )
			)
		);
	}

	public function form( $instance ) {
		$title = isset($instance['title']) ? $instance['title'] : "";
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>: 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>">
			</label>
		</p>
	<?php
	}

	function update($new_instance, $old_instance) {
        $instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);

        return $instance;
    }

	public function widget( $args, $instance ) {
		global $woocommerce;
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}

			$layout = NB_Solution::get_setting('ajax-search');

			if( isset($layout['wc_ajax-search_layout']) ) {
				echo do_shortcode('[nbt_search layout="' . $layout['wc_ajax-search_layout'] . '"]');
			}
		echo $after_widget;

	}

}
