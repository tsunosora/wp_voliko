<?php 
class wpnetbase_related_products_widget extends WP_Widget {
	
	public function __construct() {
	
		// remove related products from the page when this widget is used
		add_action( 'wp', array( $this, 'remove_related_products' ) );
		
		// adjust the display of related products when used in the widget
		/*
		add_action( 'wp_print_footer_scripts', array( $this, 'modify_related_products_styles' ) );*/
		add_filter( 'woocommerce_output_related_products_args', array( $this, 'change_related_product_columns' ) );
		
		// instantiate the widget
		parent::__construct(
			'wpnetbase_related_products_widget', 
			__( 'NetBaseTeam Related Products', 'text_domain' ),
			array( 'description' => __( 'Displays related products when on product pages', 'text_domain' ),
			'panels_groups' => array('netbaseteam') ) 
		); 
		
	}
	
	
	/**
	 * Render the widget
	 *
	 * @since 1.0
	 * @see WP_Widget::widget()
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
	
		// Only show this if we're looking at a product page
		if ( ! is_singular( 'product' ) ) {
			return;
		}
		
		// get the widget configuration
		$title = $instance['title'];
		
		echo $args['before_widget'];
		
		if ( $title ) {
			echo $args['before_title'] . wp_kses_post( $title ) . $args['after_title'];
		}
		
		// Show the product's related items
		woocommerce_output_related_products();
		
		echo $args['after_widget'];
	}
	

	/**
	 * Update the widget title
	 *
	 * @since 1.0
	 * @see WP_Widget::update()
	 * @param array $new_instance new widget settings
	 * @param array $old_instance old widget settings
	 * @return array updated widget settings
	 */
	public function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}
	

	/**
	 * Render the admin form for the widget
	 *
	 * @since 1.0.0
	 * @see WP_Widget::form()
	 * @param array $instance the widget settings
	 * @return string|void
	 */
	public function form( $instance ) {
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'woocommerce-related-products-widget' ) ?>:</label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( isset( $instance['title'] ) ? $instance['title'] : '' ); ?>" />
		</p>
		<?php
	}


	/** 
	 * Removes the related products from their current location on the product page
	 *
	 * @since 1.0.0
	 */
	public function remove_related_products() {
	
		if ( is_singular( 'product' ) ) {
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		}
	}
	
	/**
	 * Changes the number of related products columns when displayed in the widget
	 *
	 * @since 1.0
	 * @param array $args arguments for related display
	 * @return array the updated arguments
	 */
	public function change_related_product_columns( $args ) {
	
		if ( is_singular( 'product' ) ) {
			$args['posts_per_page'] = 3; 
			$args['columns'] = 1; 			
		}
		return $args;
	}
	

	
	/**
	 * Add some specific CSS for the widget
	 *
	 * @since 1.0.0
	 */
	 /*
	public function modify_related_products_styles() {
	
		if ( is_singular( 'product' ) && is_active_widget( false, false, $this->id_base ) ) {
		
			echo '<style>
				.woocommerce .widget_wc_related_products ul.products li.product,
				.woocommerce-page .widget_wc_related_products ul.products li.product {
					width: 48%;
					margin-top: 1em;
				}
			</style>';
		}
	}*/
	
} // end \wpnetbase_related_products_widget class


/**
 * Registers the new widget to add it to the available widgets
 * 
 * @since 1.0.0
 */
function wc_related_products_register_widget() {
	register_widget( 'wpnetbase_related_products_widget' );
}
add_action( 'widgets_init', 'wc_related_products_register_widget' );