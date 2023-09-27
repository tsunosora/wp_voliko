<?php
/* Sale offer widget  */
class wpnetbase_sale_off_widget extends WP_Widget 
{
	function __construct() 
	{
		parent::__construct(
		// Base ID of your widget
		'wpnetbase_sale_off_widget', 

		// Widget name will appear in UI
		__('NBT Woo Sale Off', 'wpb_widget_domain'), 

		// Widget description
		array( 'description' => __( 'Sale Off Products For Woocommerce', 'wpb_widget_domain' ), 'panels_groups' => array('netbaseteam')) 
		);
	}

	public function widget( $args, $instance )
	{
		if(isset($instance['title'])){
			$title = $instance['title'];
		}
		
		if(isset($instance['product_limit'])){
			$product_limit = $instance['product_limit'];
		}
		else{
			$product_limit = 12;
		}
		if ( isset( $instance[ 'nbt_carousel' ] ) ){
			$nbt_carousel = $instance[ 'nbt_carousel'];
		}
		else {
			$nbt_carousel ='yes';
		}
		?>
		<div class="recent-products woocommerce">
			<h2 class="recent-products-heading"><?php echo $title; ?></h2>  
				<ul id="nbt_sale_<?php echo $this->id; ?>" class="products owl-carousel" style="display:block;">
				<?php
					if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
					{
							$args = array(
									'post_type'      => 'product',
									'posts_per_page' => $product_limit,
									'meta_query'     => array(
											'relation' => 'OR',
											array( 
												'key'           => '_sale_price',
												'value'         => 0,
												'compare'       => '>',
												'type'          => 'numeric'
											),
											array( // Variable products type
												'key'           => '_min_variation_sale_price',
												'value'         => 0,
												'compare'       => '>',
												'type'          => 'numeric'
											)
										)
								);
								$loop = new WP_Query( $args );
								if ( $loop->have_posts() ) 
								{
									while ( $loop->have_posts() ) : $loop->the_post();
										woocommerce_get_template_part( 'content', 'product' );
									endwhile;
								} else 
								{
									echo __( 'No products found' );
								}
								wp_reset_postdata();
					}
					else
					{
						echo __( 'Woocommerce plugin does not exist' );
						
					}
				?>
				</ul>
		</div>
		<?php
		if($instance['nbt_carousel'] == 'yes' )
		{	?>			
			<script>			
				jQuery(document).ready(function(){
					
					jQuery('.panel-grid').each(function(){
						
						var get_length = jQuery(this).find('.panel-grid-cell').length;

						if(get_length == 1)
						{
							
							var item_count = 5;
							
						}
						else if (get_length == 2)
						{
							
							var item_count = 3;
							
						}
						else if (get_length == 3)
						{
							
							var item_count = 2;
							
						}
						else if (get_length == 4)
						{
							
							var item_count = 1;
							
						}

							jQuery(this).find('#nbt_sale_'+<?php echo $this->id; ?>).owlCarousel({
								
								items:item_count,
								
								autoPlay: 3000, 
								
								lazyLoad : true,
								
								nac : true,
								
								responsiveClass:true,
								
								responsive:{

									0:{
										items:1,            
									},
									480:{
										items:2,            
									},
									600:{
										items:3,            
									},
									768:{
										items:5,           
									}
								},
								
							});
	
					}); 
					
				});
				
			</script>
			
			<?php
		}

	}
			
	/*Widget Backend*/  
	public function form( $instance ) 
	{
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		
		if ( isset( $instance[ 'product_limit' ] ) ) {
			$product_limit = $instance[ 'product_limit' ];
		}
		else{
			$product_limit =12;
		}
		if ( isset( $instance[ 'nbt_carousel' ] ) ){
			$nbt_carousel = $instance[ 'nbt_carousel'];
		}
		else {
			$nbt_carousel ='yes';
		}
		
		/*Widget admin form*/ 
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'product_limit' ); ?>"><?php _e( 'No Of Products Show:' ); ?></label> 
			
			<input class="widefat" id="<?php echo $this->get_field_id( 'product_limit' ); ?>" name="<?php echo $this->get_field_name( 'product_limit' ); ?>" type="text" value="<?php echo esc_attr( $product_limit ); ?>" />
		</p>
		<p>

			<label for="<?php echo $this->get_field_id( 'nbt_carousel' ); ?>"><?php _e( 'Carousel Enable:' ); ?></label> 
			<input type="radio" name="<?php echo $this->get_field_name( 'nbt_carousel' ); ?>" id="<?php echo $this->get_field_id( 'nbt_carousel_yes' ); ?>" value="yes" <?php if($nbt_carousel=='yes'){ echo "checked";}?>>Yes
			<input type="radio" name="<?php echo $this->get_field_name( 'nbt_carousel' ); ?>" id="<?php echo $this->get_field_id( 'nbt_carousel_no' ); ?>" value="no" <?php if($nbt_carousel=='no'){ echo "checked";}?>>NO
		</p>
		<?php 
	}
		
	public function update( $new_instance, $old_instance )
	{
		$instance = array();		
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		
		$instance['product_limit'] = ( ! empty( $new_instance['product_limit'] ) ) ? strip_tags( $new_instance['product_limit'] ) : '';
		$instance['nbt_carousel'] = ( ! empty( $new_instance['nbt_carousel'] ) ) ? strip_tags( $new_instance['nbt_carousel'] ) : '';
		
		return $instance;
	}
} 

function wpnetbase_sale_off_load_widget() {
	register_widget( 'wpnetbase_sale_off_widget' );
}
add_action( 'widgets_init', 'wpnetbase_sale_off_load_widget' );