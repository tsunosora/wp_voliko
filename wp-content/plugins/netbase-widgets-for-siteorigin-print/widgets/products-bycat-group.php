<?php 
/* Best seller widget */ 
class cawptheme_products_bycat_widget extends WP_Widget {
	
	public function __construct() {
	
		parent::__construct(
			// Base ID of your widget
			'cawptheme_products_bycat_widget', 

			// Widget name will appear in UI
			__('NBT Products by Category Group', 'cawptheme'), 

			// Widget description
			array( 'description' => __( 'CAWP Products by Category Group', 'cawptheme' ))
		);
	}

	public function form($instance) {
		if ($instance) {
			$title = esc_attr($instance['title']);
			$products = esc_attr($instance['products']);
			$products_per_slide = esc_attr($instance['products_per_slide']);		
		} else {
			$title = '';
			$products = 8;
			$products_per_slide = 4;
		}
		if ( isset( $instance[ 'product_category' ] ) ) {
			$product_category = $instance[ 'product_category'];		
		}
		$prod_cat_args = array(
		  'taxonomy'     => 'product_cat', 
		  'orderby'      => 'name',
		  'empty'        => 0
		);

		$woo_categories = get_categories( $prod_cat_args );
		?>

		<p>
			<label for="<?php echo $this->get_field_id('title') ;?>">Widget Title</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
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
			<label for="<?php echo $this->get_field_id('products') ;?>">Number of products</label>
			<input type="number" class="widefat" id="<?php echo $this->get_field_id('products'); ?>" name="<?php echo $this->get_field_name('products'); ?>" value="<?php echo $products; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('products_per_slide') ;?>">Products per slide</label>
			<input type="number" class="widefat" id="<?php echo $this->get_field_id('products_per_slide'); ?>" name="<?php echo $this->get_field_name('products_per_slide'); ?>" value="<?php echo $products_per_slide; ?>" />
		</p>

		<?php
	}

	public function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['products'] = strip_tags($new_instance['products']);
		$instance['products_per_slide'] = strip_tags($new_instance['products_per_slide']);
		$instance['product_category'] = ( ! empty( $new_instance['product_category'] ) ) ? strip_tags( $new_instance['product_category'] ) : '';

		return $instance;
	}

	public function widget($args, $instance) {		
		$title = $instance['title'];
		$products = $instance['products'];
		$products_per_slide = $instance['products_per_slide'];	
		if($instance['product_category'])
		{
			$product_category =  $instance['product_category'];
		}	
		?>

		<div class="nbtsow-products-wrap">
			<?php if($instance['title']){ ?>
			<h2 class="widget-heading"><?php echo $title;?></h2>
			<?php } ?>
			<ul class="capbc_<?php echo $this->id; ?> owl-carousel">
				<?php
				$args = array(
					'post_type' => 'product',
					'product_cat'=>$product_category, 
					'posts_per_page' => $products,					
					'status' => 'publish', 'orderby' => 'desc'
				);

				$loop = new WP_Query( $args );
				if ( $loop->have_posts() ) {		
					$i = 0;					
					while ( $loop->have_posts() ) : $loop->the_post();
						if($i % $products_per_slide == 0) {
					        echo '<li class="carousel-wrap">';
					        echo '<ul>';
					    } ?>        
				        <li class="product">
				            <?php
				            $current_product = new WC_Product(get_the_ID());
				            ?>
				            <div class="product-thumb">
		                        <a href="<?php the_permalink(); ?>">
		                            <?php if (has_post_thumbnail()){
		                                the_post_thumbnail();
		                            } ?>
		                            
		                        </a>
		                    </div>
				            <div class="product-details">
				                <div class="product-meta">
				                    <h4 class="product-title">
				                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				                    </h4>
				                    <span class="product-price"><?php echo $current_product->get_price_html(); ?></span>
				                </div>
			                    <!-- <p class="product-description">
			                    <?php //echo wp_trim_words( get_the_excerpt(), 6, '...' ); ?>
			                    </p> -->
				            </div>
				            
				           
				        </li>
						<?php
					    $i++;
					    if($i % $products_per_slide == 0) {
					        echo '</ul>';
					        echo '</li>';
					    }
					endwhile;
				    if($i % $products_per_slide == 0) {
				        echo '</li>';
				        echo '</ul>';
				    }
				}else{					
					echo __( 'No products found' );				
				}
				?>
			</ul>
		</div>
		<script>
			jQuery(document).ready(function() {
				jQuery('.capbc_<?php echo $this->id; ?>').owlCarousel({
					items: 1,
					autoPlay: 3000,						
					lazyLoad : true,	
					nav : true,
					navText: ['<i class="icon-left-open"></i>', '<i class="icon-right-open"></i>'],
				});
			});
			
		</script>
		<?php	
		
	}

}
	
function cawptheme_products_bycatgroup_load_widget() {
	
	register_widget( 'cawptheme_products_bycat_widget' );

}	
add_action( 'widgets_init', 'cawptheme_products_bycatgroup_load_widget' );