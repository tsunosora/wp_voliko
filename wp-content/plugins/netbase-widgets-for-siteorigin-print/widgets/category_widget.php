<?php
/* product category widget */

class wpnetbase_category_wpb_widget extends WP_Widget
{
	function __construct()
	{
		parent::__construct(
		// Base ID of your widget
		'wpnetbase_category_wpb_widget', 

		// Widget name will appear in UI
		__('NBT Woo Products by Category', 'wpb_widget_domain'), 

		// Widget description
		array( 'description' => __( 'Category Products For Woocommerce', 'wpb_widget_domain' ), 'panels_groups' => array('netbaseteam')) 
		);
	}
	public function widget( $args, $instance ) 
	{

		if($instance['title']){ $title =  $instance['title']; }
		
		if($instance['product_category']){ $product_category =  $instance['product_category']; }
		
		if($instance['product_limit']){
			$product_limit =  $instance['product_limit'];
		}
		else{
			$product_limit = 10;
		}
		if($instance['product_column']){ $product_column =  $instance['product_column'];}
		else { $product_column = 4; }
		?>
		 
			<div class="woocommerce category-widget <?php if($instance['nbt_show_only_thumbnail'] == 'yes' )
			{echo 'show-only-thumbnail';}?>" >
				<?php if($instance['nbt_show_only_thumbnail'] == 'yes' )
				{
					echo '<div class="recent-products-heading">';
				}
				if(isset($title)  && $title !=''){
					echo '<span>'.$title.'</span>';
				}
				if($instance['nbt_show_only_thumbnail'] == 'yes' )
				{
					echo '</div>';
				} 
				if($instance['nbt_show_only_thumbnail'] == 'yes' )
				{
					echo '<div class="p-content">';
				}?>
					<ul id="nbt_category_<?php echo $instance['panels_info']['id']; ?>" class="products owl-carousel nbt_category nbt-cate-widget" style="display:block;">
					
					<?php
						if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
						{
								 $args = array( 'post_type' => 'product', 'posts_per_page' => $product_limit, 'product_cat'=>$product_category, 'status' => 'publish', 'orderby' => 'desc' );
									$loop = new WP_Query( $args );
									
								if ( $loop->have_posts() ) 
								{
									if($instance['nbt_show_only_thumbnail'] == 'yes' )
									{
										while ( $loop->have_posts() ) : $loop->the_post(); global $product;

											?>
											<li>
											    <?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
											    <div class="product-content-top">
											        <a href="<?php the_permalink(); ?>">
											            <?php
											            /**
											             * woocommerce_before_shop_loop_item_title hook
											             *
											             * @hooked woocommerce_show_product_loop_sale_flash - 10
											             * @hooked woocommerce_template_loop_product_thumbnail - 10
											             */
											            do_action( 'woocommerce_before_shop_loop_item_title' );
											            ?>
											        </a>
											        
											    </div>

											</li>
											<?php

										endwhile;
									}else{
										while ( $loop->have_posts() ) : $loop->the_post(); global $product;

										wc_get_template_part( 'content', 'product' );
										endwhile;
									}

								}
								else
								{
									echo '<p>No products found</p>';									
									
								}
								
								wp_reset_query();
						}
						else
						{
							echo '<p>Woocommerce plugin does not exist</p>';							
						}
					?>
					</ul>
					<?php if($instance['nbt_show_only_thumbnail'] == 'yes' )
				{
					echo '</div>';
				}?>
				
			</div>
			
		<?php	
		
		if($instance['nbt_carousel'] == 'yes' )
		{	?>			
			<script>			
				jQuery(document).ready(function(){
							jQuery('#nbt_category_<?php echo $instance['panels_info']['id']; ?>').owlCarousel({			autoplay: true, dots: false,
								autoplayTimeout: 3000, loop: true,autoplayHoverPause: true,autoplaySpeed:350,
								nav : true,								
								navText: ['<span class="icon-left-open"></span>', '<span class="icon-right-open"></span>'],
								
								responsive:{
									0:{
										items:1,            
									},
									480:{
										items:2,            
									},
									600:{
										items:3, margin: 20            
									},
									768:{
										items:<?php echo $product_column; ?>,
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
		
		if ( isset( $instance[ 'title' ] ) ) { $title = $instance[ 'title' ]; }
		
		if ( isset( $instance[ 'product_category' ] ) ) { $product_category = $instance[ 'product_category' ]; }
		if ( isset( $instance[ 'product_limit' ] ) ) 
		{
			$product_limit = $instance[ 'product_limit' ];
		}
		else 
		{
			$product_limit =12;
		}
		if ( isset( $instance[ 'product_column' ] ) )
		{
			$product_column = $instance[ 'product_column' ];
		}
		else
		{
			$product_column =4;
		}
		if ( isset( $instance[ 'nbt_carousel' ] ) )
		{
			$nbt_carousel = $instance[ 'nbt_carousel'];
		}
		else 
		{
		$nbt_carousel ='yes';
		}

		if ( isset( $instance[ 'nbt_show_only_thumbnail' ] ) )
		{
			$nbt_show_only_thumbnail = $instance[ 'nbt_show_only_thumbnail'];
		}
		else
		{
			$nbt_show_only_thumbnail ='no';
		}

		$prod_cat_args = array(
		  'taxonomy'     => 'product_cat', 
		  'orderby'      => 'name',
		  'empty'        => 0
		);

		$woo_categories = get_categories( $prod_cat_args );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p class="default-form">
			<label for="<?php echo $this->get_field_id( 'product_category' ); ?>"><?php _e( 'Product Category:' ); ?></label> 
			<select class="widefat product_category" name="<?php echo $this->get_field_name( 'product_category' ); ?>" id="<?php echo $this->get_field_id( 'product_category' ); ?>">
			<?php
			foreach($woo_categories as $cat)
			{
				?>
					<option value="<?php echo $cat->slug; ?>" <?php if($product_category==$cat->slug){echo 'selected';}?>><?php echo $cat->name; ?></option>
				<?php		
			
			}
			?>
			</select>
		</p>
		<p>

			<label for="<?php echo $this->get_field_id( 'product_limit' ); ?>"><?php _e( 'Number of Product show:' ); ?></label> 
			<input type="number" name="<?php echo $this->get_field_name( 'product_limit' ); ?>" id="<?php echo $this->get_field_id( 'product_limit' ); ?>" value="<?php echo $product_limit;?>">
		</p>
		<p>

			<label for="<?php echo $this->get_field_id( 'product_column' ); ?>"><?php _e( 'Number of Column:' ); ?></label>
			<input type="number" name="<?php echo $this->get_field_name( 'product_column' ); ?>" id="<?php echo $this->get_field_id( 'product_column' ); ?>" value="<?php echo $product_column;?>">
		</p>
		<p>

			<label for="<?php echo $this->get_field_id( 'nbt_carousel' ); ?>"><?php _e( 'Carousel Enable:' ); ?></label> 
			<input type="radio" name="<?php echo $this->get_field_name( 'nbt_carousel' ); ?>" id="<?php echo $this->get_field_id( 'nbt_carousel_yes' ); ?>" value="yes" <?php if($nbt_carousel=='yes'){ echo "checked";}?>>Yes
			<input type="radio" name="<?php echo $this->get_field_name( 'nbt_carousel' ); ?>" id="<?php echo $this->get_field_id( 'nbt_carousel_no' ); ?>" value="no" <?php if($nbt_carousel=='no'){ echo "checked";}?>>NO
		</p>
		<p>

			<label for="<?php echo $this->get_field_id( 'nbt_show_only_thumbnail' ); ?>"><?php _e( 'Show only thumbnail:' ); ?></label>
			<input type="radio" name="<?php echo $this->get_field_name( 'nbt_show_only_thumbnail' ); ?>" id="<?php echo $this->get_field_id( 'nbt_show_only_thumbnail_yes' ); ?>" value="yes" <?php if($nbt_show_only_thumbnail=='yes'){ echo "checked";}?>>Yes
			<input type="radio" name="<?php echo $this->get_field_name( 'nbt_show_only_thumbnail' ); ?>" id="<?php echo $this->get_field_id( 'nbt_show_only_thumbnail_no' ); ?>" value="no" <?php if($nbt_show_only_thumbnail=='no'){ echo "checked";}?>>NO
		</p>
		<?php 
	}
		
	public function nbt_category_pro_update( $new_instance, $old_instance ) 
	{
		$instance = array();
		
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		
		$instance['product'] = ( ! empty( $new_instance['product'] ) ) ? strip_tags( $new_instance['product'] ) : '';
		
		$instance['product_category'] = ( ! empty( $new_instance['product_category'] ) ) ? strip_tags( $new_instance['product_category'] ) : '';
		
		$instance['product_limit'] = ( ! empty( $new_instance['product_limit'] ) ) ? strip_tags( $new_instance['product_limit'] ) : '';

		$instance['product_column'] = ( ! empty( $new_instance['product_column'] ) ) ? strip_tags( $new_instance['product_column'] ) : '';
		
		$instance['nbt_carousel'] = ( ! empty( $new_instance['nbt_carousel'] ) ) ? strip_tags( $new_instance['nbt_carousel'] ) : '';

		$instance['nbt_show_only_thumbnail'] = ( ! empty( $new_instance['nbt_show_only_thumbnail'] ) ) ? strip_tags( $new_instance['nbt_show_only_thumbnail'] ) : '';
		
		return $instance;
	}
}

function wpnetbase_category_load_widget() {
	
	register_widget( 'wpnetbase_category_wpb_widget' );
	
}
add_action( 'widgets_init', 'wpnetbase_category_load_widget' );