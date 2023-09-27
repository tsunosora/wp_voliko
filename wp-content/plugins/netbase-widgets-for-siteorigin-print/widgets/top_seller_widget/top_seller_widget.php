<?php 
/* Best seller widget */ 
class wpnetbase_best_seller_wpb_widget extends WP_Widget {
	
	public function __construct() {
	
		parent::__construct(
			// Base ID of your widget
			'wpnetbase_best_seller_wpb_widget', 

			// Widget name will appear in UI
			__('NBT Top Seller', 'wpb_widget_domain'), 

			// Widget description
			array( 'description' => __( 'Best Seller Products For Woocommerce', 'wpb_widget_domain' ),  'panels_groups' => array('netbaseteam'))
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
		?>

		<p>
			<label for="<?php echo $this->get_field_id('title') ;?>">Widget Title</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
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

		return $instance;
	}

	public function widget($args, $instance) {		
		$title = $instance['title'];
		$products = $instance['products'];
		$products_per_slide = $instance['products_per_slide'];		
		?>

		<div class="nbtsow-products-wrap">
			<h2 class="widget-heading"><?php echo $title;?></h2>
			<ul class="best-selling-products-<?php echo $this->id; ?> owl-carousel">
				<?php
				$args = array(
					'post_type' => 'product',
					'posts_per_page' => $products,
					'meta_key' => 'total_sales',
					'orderby' => 'meta_value_num',
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
				                        the_post_thumbnail('wpnetbase-best-seller-thumb');
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
			                    <p class="product-description">
			                    <?php echo wp_trim_words( get_the_excerpt(), 6, '...' ); ?>
			                    </p>
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
				jQuery('.best-selling-products-<?php echo $this->id; ?>').owlCarousel({
					items: 1,
					autoPlay: 3000,						
					lazyLoad : true,					
					nav : false,
					dots: true,
				});
			});
			
		</script>
		<?php	
		
	}

}
	
function wpnetbase_best_seller_wpb_load_widget() {
	
	register_widget( 'wpnetbase_best_seller_wpb_widget' );

}	
add_action( 'widgets_init', 'wpnetbase_best_seller_wpb_load_widget' );