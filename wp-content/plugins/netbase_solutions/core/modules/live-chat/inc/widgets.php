<?php
class NBT_Currency_Switcher_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'nbt-wc-currency-switcher',
			__( 'NBT Currency Switcher', 'nbt-ajax-cart' ),
			array(
				'classname' => 'nbt-currency-switcher',
				'description' => __( 'Enter a custom description for your new widget', 'mycustomdomain' )
			)
		);
	}

	public function form( $instance ) {
		$title = '';
		if(isset($instance['title'])){
			$title = esc_attr( $instance['title'] );
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>: 
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
				echo '<div class="nbtcs-w-title">'.$before_title . $title . ':'.$after_title.'</div>';
			endif;

			echo '<div class="nbt-right-cs">';
				echo do_shortcode('[nbt_currency_switcher]');
			echo '</div>';

		echo $after_widget;

	}

}
