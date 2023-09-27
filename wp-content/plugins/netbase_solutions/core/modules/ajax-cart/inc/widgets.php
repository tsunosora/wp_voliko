<?php
class NBT_Ajax_Cart_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'nbt-wc-ajaxcart',
			__( 'NBT WooCommerce Ajax Cart', 'nbt-solution' ),
			array(
				'classname' => 'nbt-wc-ajaxcart',
				'description' => __( 'Enter a custom description for your new widget', 'nbt-solution' )
			)
		);
	}

	public function form( $instance ) {
		$title = '';
		if(isset($instance['title'])){
 			$title = esc_attr( $instance['title'] );
 		}?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>">
			</label>
		</p>
	<?php
	}

	public function widget( $args, $instance ) {
		global $woocommerce;
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
			if ( $title ) :
				echo $before_title . $title . $after_title;
			endif;

			if(function_exists('nbt_ajax_template')){
				nbt_ajax_template();
			}

		echo $after_widget;
	}
}
