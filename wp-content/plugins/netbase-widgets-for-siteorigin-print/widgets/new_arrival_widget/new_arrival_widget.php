<?php
/* New arrival product widget */
class wpnetbase_new_arrival_wpb_widget extends WP_Widget 
{
	function __construct() 
	{
		parent::__construct(
		// Base ID of your widget
		'wpnetbase_new_arrival_wpb_widget', 

		// Widget name will appear in UI
		__('NBT Woo New', 'wpb_widget_domain'), 

		// Widget description
		array( 'description' => __( 'New Arrival Products For Woocommerce', 'wpb_widget_domain' ), 'panels_groups' => array('netbaseteam')) 
		);
	}

	public function widget( $args, $instance ){

		if(isset($instance['title'])){
			$title =  $instance['title'];
		}else{ $title ='';}
		
		if(isset($instance['product_limit'])){
			$product_limit =  $instance['product_limit'];
		}
		else{
			$product_limit =  12;
		}
		if(isset($instance['product_columns'])){
			$product_columns =  $instance['product_columns'];
			
		}else{$product_columns = 4;}
		
		?>
		<div class="recent-products woocommerce">
			<?php if($title!=''){ ?>
			<h3 class="widget-title"><?php echo $title; ?></h3>  
			<?php } ?>
				<ul id="nbt_new_arrival_<?php echo $this->id; ?>" class="products owl-carousel" style="display:block;">
				<?php
					if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
					{
							$args = array(
								'post_type'     => 'product',
								'posts_per_page' => $product_limit,
								'post_status'   => 'publish',								
								'columns'  => '1',
								'orderby' 	=> 'date',
								'order' 	=> 'desc' 
							);
							$loop = new WP_Query( $args );
							if ( $loop->have_posts() ) 
							{
								while ( $loop->have_posts() ) : $loop->the_post();
								
								wc_get_template_part( 'content', 'product' );
								endwhile;
							}
							else 
							{ echo '<p>No products found.</p>';
								
							}
							wp_reset_postdata();
					}
					else{ echo '<p>Woocommerce plugin does not exist</p>'; }
				?>
				</ul>
			
		</div>
		<?php
		if($instance['nbt_carousel'] == 'yes' )
		{	?>
			
			<script>
				jQuery(document).ready(function(){
						var item_count= <?php echo $product_columns; ?>;
						jQuery('#nbt_new_arrival_<?php echo $this->id; ?>').owlCarousel({
							autoPlay: 5000, 
							lazyLoad : true,								
							<?php if($instance['nbt_pagination'] == 'yes' ) {?>
							nav : false,
							pagination:true,
							<?php } else{?>
							nav : true,
							pagination: false,
							navText: ['<span class="icon-left-open"></span>', '<span class="icon-right-open"></span>'],
							<?php } ?>
								
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
										items:item_count,           
									}
								},
								
							});										
							
					}); 
				
			</script>
			
			<?php
		}		
	}
			
	// Widget Backend 
	public function form( $instance )
	{
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		
		if ( isset( $instance[ 'product_limit' ] ) ) {
			$product_limit = $instance[ 'product_limit' ];
		}
		else {
			$product_limit =12;
		}
		if ( isset( $instance[ 'product_columns' ] ) ) {
				$product_columns = $instance[ 'product_columns' ];
		}
		if ( isset( $instance[ 'nbt_carousel' ] ) ){
			$nbt_carousel = $instance[ 'nbt_carousel'];
		}
		else {
			$nbt_carousel ='yes';
		}
		if ( isset( $instance[ 'nbt_pagination' ] ) ){
			$nbt_pagination = $instance[ 'nbt_pagination'];
		}
		else {
			$nbt_pagination ='no';
		}
		// Widget admin form
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'product_limit' ); ?>"><?php _e( 'Products per page:' ); ?></label> 
			
			<input class="widefat" id="<?php echo $this->get_field_id( 'product_limit' ); ?>" name="<?php echo $this->get_field_name( 'product_limit' ); ?>" type="text" value="<?php echo esc_attr( $product_limit ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'product_columns' ); ?>"><?php _e( 'Columns:' ); ?></label> 
			
			<input class="widefat" id="<?php echo $this->get_field_id( 'product_columns' ); ?>" name="<?php echo $this->get_field_name( 'product_columns' ); ?>" type="text" value="<?php echo esc_attr( $product_columns ); ?>" />
		</p>
		<p>

			<label for="<?php echo $this->get_field_id( 'nbt_carousel' ); ?>"><?php _e( 'Carousel Enable:' ); ?></label> 
			<input type="radio" name="<?php echo $this->get_field_name( 'nbt_carousel' ); ?>" id="<?php echo $this->get_field_id( 'nbt_carousel_yes' ); ?>" value="yes" <?php if($nbt_carousel=='yes'){ echo "checked";}?>>Yes
			<input type="radio" name="<?php echo $this->get_field_name( 'nbt_carousel' ); ?>" id="<?php echo $this->get_field_id( 'nbt_carousel_no' ); ?>" value="no" <?php if($nbt_carousel=='no'){ echo "checked";}?>>NO
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'nbt_pagination' ); ?>"><?php _e( 'Show Pagination:' ); ?></label> 
			
			<input type="radio" name="<?php echo $this->get_field_name( 'nbt_pagination' ); ?>" id="<?php echo $this->get_field_id( 'nbt_pagination_yes' ); ?>" value="yes" <?php if($nbt_pagination=='yes'){ echo "checked";}?>>Yes
			
			<input type="radio" name="<?php echo $this->get_field_name( 'nbt_pagination' ); ?>" id="<?php echo $this->get_field_id( 'nbt_pagination_no' ); ?>" value="no" <?php if($nbt_pagination=='no'){ echo "checked";}?>>NO
		</p>
		<?php 
	}
		
	// Updating widget replacing old instances with new
	public function nbt_new_arrival_pro_update( $new_instance, $old_instance ) 
	{
		$instance = array();
		
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';		
		$instance['product_limit'] = ( ! empty( $new_instance['product_limit'] ) ) ? strip_tags( $new_instance['product_limit'] ) : '';
		$instance['product_columns'] = ( ! empty( $new_instance['product_columns'] ) ) ? strip_tags( $new_instance['product_columns'] ) : '';
		$instance['nbt_carousel'] = ( ! empty( $new_instance['nbt_carousel'] ) ) ? strip_tags( $new_instance['nbt_carousel'] ) : '';
		$instance['nbt_pagination'] = ( ! empty( $new_instance['nbt_pagination'] ) ) ? strip_tags( $new_instance['nbt_pagination'] ) : '';
		return $instance;
	}
}
function wpnetbase_new_arrival_load_widget() {
	register_widget( 'wpnetbase_new_arrival_wpb_widget' );
}
add_action( 'widgets_init', 'wpnetbase_new_arrival_load_widget' );