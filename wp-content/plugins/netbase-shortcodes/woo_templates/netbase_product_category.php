<?php
global $woocommerce_loop;
extract( $atts = shortcode_atts( array( 			
			'per_page' => '5',
			'columns'  => '1',
			'orderby'  => 'title',
			'order'    => 'desc',
			'category' => '',  // Slugs
			'operator' => 'IN', // Possible values are 'IN', 'NOT IN', 'AND'.
			'el_class' => ''
		), $atts ));

		if ( ! $atts['category'] ) {
			return '';
		}

		$ordering_args = WC()->query->get_catalog_ordering_args( $atts['orderby'], $atts['order'] );
		$meta_query    = WC()->query->get_meta_query();
		$query_args    = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'orderby'             => $ordering_args['orderby'],
			'order'               => $ordering_args['order'],
			'posts_per_page'      => $atts['per_page'],
			'meta_query'          => $meta_query
		);
		
		if ( ! empty( $atts['category'] ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'product_cat',
					'terms'    => array_map( 'sanitize_title', explode( ',', $atts['category'] ) ),
					'field'    => 'slug',
					'operator' => $atts['operator']
				)
			);
		}		
		
		if ( isset( $ordering_args['meta_key'] ) ) {
			$query_args['meta_key'] = $ordering_args['meta_key'];
		}
		
		$loop_name='product_cat';
		$products                    = new WP_Query( 
		apply_filters( 'woocommerce_shortcode_products_query', $query_args, $atts, $loop_name ) );
		$columns                     = absint( $atts['columns'] );
		$woocommerce_loop['columns'] = $columns;	

		ob_start();

		if ( $products->have_posts() ) : ?>
			<div class="shortcodes-sticky-product">
			<?php do_action( "woocommerce_shortcode_before_{$loop_name}_loop" ); 
			$i=0;
			woocommerce_product_loop_start();  
			while ( $products->have_posts() ) : $products->the_post(); 
				if($i==0){ ?>
					<li class="sticky-product">
						<div class="img-p">
							<?php 
							if($el_class){
								echo '<img src="'.esc_url($el_class).'">';
								
							}else{
								the_post_thumbnail() ;
							}
							?>
						</div>						
						<div class="info">
							<h3 class="title">
								<a href="<?php the_permalink(); ?>"><?php the_title();?></a>
							</h3>
							<?php 
							$currency = get_woocommerce_currency_symbol();
							$price = get_post_meta( get_the_ID(), '_regular_price', true);
							$sale = get_post_meta( get_the_ID(), '_sale_price', true);
							if($sale) : 					
							?>	
								<p class="price"><del><?php echo $currency; echo $price; ?></del> <?php echo $currency; echo $sale; ?></p>    
							<?php elseif($price) : ?>
								<p class="price"><?php echo $currency; echo $price; ?></p>    
							<?php endif; ?>
							<?php woocommerce_template_loop_add_to_cart(); ?>
						</div>
					</li>										
					<?php 
					}else{
						
						wc_get_template_part( 'content', 'product' ); 
						
				}
				
				$i++;
				endwhile; // end of the loop. 

			woocommerce_product_loop_end();  
			do_action( "woocommerce_shortcode_after_{$loop_name}_loop" );
			woocommerce_reset_loop();
			wp_reset_postdata();
			
			echo '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
			echo '</div>';  

		endif;
		
		// Remove ordering query arguments
		WC()->query->remove_ordering_args();
?>