<?php
class wpnetbase_featured_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		/*Base ID of your widget*/ 
		'wpnetbase_featured_widget', 

		/*Widget name will appear in UI*/ 
		__('NBT Woo Featured', 'wpb_widget_domain'), 

		/*Widget description*/ 
		array( 'description' => __( 'Featured Products For Woocommerce', 'wpb_widget_domain' ),  'panels_groups' => array('netbaseteam') ) 
		);
	}

 	/*Creating widget front-end*/ 
	public function widget( $args, $instance ){

		if(isset($instance['product_limit'])){
			$product_limit = $instance['product_limit'];
		}else{ $product_limit = 12;}

		if(isset($instance['product_columns'])){
			$product_columns =  $instance['product_columns'];
			
		}else{ $product_columns = 4; }
		
	 ?>
		<div class="featured-products woocommerce">
			<?php if($instance['title'] && $instance['title'] !=''): ?>
			<h2 class="featured-products-heading"><?php echo $instance['title']; ?></h2>  
			<?php endif; ?>
				<ul id="nbt_featured_<?php echo $this->id; ?>" class="products owl-carousel">
				<?php
					if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
					{
						$atts =   array(
										'per_page' => $product_limit,
										'columns'  => '1',
										'orderby'  => 'date',
										'order'    => 'desc'
									);
						$meta_query   = WC()->query->get_meta_query();
						$tax_query[] = array(
							'taxonomy' => 'product_visibility',
							'field'    => 'name',
							'terms'    => 'featured',
							'operator' => 'IN', // or 'NOT IN' to exclude feature products
						);
						$args = array(
									'post_type'           => 'product',
									'post_status'         => 'publish',
									'ignore_sticky_posts' => 1,
									'posts_per_page'      => $atts['per_page'],
									'orderby'             => $atts['orderby'],
									'order'               => $atts['order'],
									'tax_query'           => $tax_query
						);
						$loop = new WP_Query( $args );
							if ( $loop->have_posts() ){
								while ( $loop->have_posts() ) : $loop->the_post();
									include(locate_template('woocommerce/content-newproduct.php'));
								endwhile;
							}else {
								echo '<p>No products found</p>';
							}
							wp_reset_postdata();
					}else { 
						echo '<p>Woocommerce Plugin Doest Not Exist</p>';						
					}
				?>
				</ul>
		</div>
		<?php
		if($instance['nbt_carousel'] == 'yes' ){
		?>	
			<script>
				jQuery(document).ready(function(){
					var item_count= <?php echo $product_columns; ?>;
					jQuery('#nbt_featured_<?php echo $this->id; ?>').owlCarousel({
						items:item_count,
						autoplay: true,
						autoplayTimeout: 3000, loop: true,autoplayHoverPause: true,autoplaySpeed:350,
						<?php if($instance['nbt_pagination'] == 'yes' ) {?>
						pagination:true,
						<?php } else{?>
						nav : true,
						navText: ['<span class="icon-left-open"></span>', '<span class="icon-right-open"></span>'],
						<?php } ?>
						responsive: {
							0: {
								items: 1,
							},
							480: {
								items: 2,
							},
							600: {
								items: 3, margin: 20
							},
							768: {
								items: item_count,
							}
						},
					});
				});
			</script>
			
			<?php
		}
		
	}
			
	/*Widget Backend */ 
	public function form( $instance ) 
	{
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}else {
			$title = __( 'Featured', 'wpb_widget_domain' );
		}
		if ( isset( $instance[ 'product_limit' ] ) ){ $product_limit = $instance[ 'product_limit' ];
		}else { $product_limit = 12; }

		if ( isset( $instance[ 'product_columns' ] ) ) {
				$product_columns = $instance[ 'product_columns' ];
		}
		if ( isset( $instance[ 'nbt_carousel' ] ) )
		{
			$nbt_carousel = $instance[ 'nbt_carousel'];
		}
		else { $nbt_carousel ='yes'; }

		if ( isset( $instance[ 'nbt_pagination' ] ) ){
			$nbt_pagination = $instance[ 'nbt_pagination'];
		}
		else { $nbt_pagination ='no'; }
		/* Widget admin form*/
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			
			<label for="<?php echo $this->get_field_id( 'product_limit' ); ?>"><?php _e( 'No Of Products Show:' ); ?></label> 
			
			<input class="widefat" id="<?php echo $this->get_field_id( 'product_limit' ); ?>" name="<?php echo $this->get_field_name( 'product_limit' ); ?>" type="number" placeholder="" value="<?php echo esc_attr( $product_limit ); ?>" />
		
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
		
	public function update( $new_instance, $old_instance ) 
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

function wpnetbase_load_featured_widget() 
{
	register_widget( 'wpnetbase_featured_widget' );
}
add_action( 'widgets_init', 'wpnetbase_load_featured_widget' );