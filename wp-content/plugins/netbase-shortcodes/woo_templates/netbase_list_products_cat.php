<?php
global $woocommerce_loop;
extract( $atts = shortcode_atts( array( 			
			'per_page' => '',
			'columns'  => '4',
			'orderby'  => 'title',
			'order'    => 'desc',
			'nbcarousel' => '',
			'style'    => '',
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

		if ( $products->have_posts() ) : 

			if($nbcarousel == 'true' && $atts['style']!='catchild'){
				echo '<div class="shortcodes-lst-products-cat nbcarousel">';
			}
			elseif ($nbcarousel == 'true' && $atts['style']=='catchild') {
				echo '<div class="shortcodes-lst-products-cat catchild-carousel">';
			}
			else{
				echo '<div class="shortcodes-lst-products-cat">';
			}
 			do_action( "woocommerce_shortcode_before_{$loop_name}_loop" ); 
			
			/*start catchild*/
			if ($atts['style']=='catchild'){
				echo '<div class="shortcode-childcat">';
				echo '<ul class="lstchildcat">';
				$idbyslug = get_term_by('slug',$atts['category'], 'product_cat');
				$product_cat_ID = $idbyslug->term_id;
				$args = array(
					'hierarchical' => 1,
					'show_option_none' => '',
					'hide_empty' => 0,
					'parent' => $product_cat_ID,
					'taxonomy' => 'product_cat'
				);

				$subcats = get_categories($args);

				foreach ($subcats as $sc) {

					$link = get_term_link( $sc->slug, $sc->taxonomy );

					echo '<li><a href="'. $link .'">'.$sc->name.'</a></li>';

				}

				echo '</ul>';
				echo '<div class="box-childcat-item">';				
				woocommerce_product_loop_start(); 
				while ($products->have_posts()) : $products->the_post();					
					wc_get_template_part('content', 'product');

				endwhile;
				woocommerce_product_loop_end();
				
				echo '</div>';
				echo '</div>';
			} /*end catchild*/
			else{
				woocommerce_product_loop_start(); 
			 	while ( $products->have_posts() ) : $products->the_post();
						
						wc_get_template_part( 'content', 'product' ); 
				
				endwhile; // end of the loop. 
				woocommerce_product_loop_end(); 

			}	
			
			do_action( "woocommerce_shortcode_after_{$loop_name}_loop" ); 
			woocommerce_reset_loop();
			wp_reset_postdata();		
			echo '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';		
			echo '</div>';

		else :
				echo "<p class='no-posts right-first' >" . __( "Sorry, there are no product at this time." ) . "</p>";	

		endif;
		
		// Remove ordering query arguments
		WC()->query->remove_ordering_args();
?>