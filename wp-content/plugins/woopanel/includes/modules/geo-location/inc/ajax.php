<?php
/**
 * GEO Location Ajax class
 *
 * @package WooPanel_Modules
 */
class NBT_Geo_Location_Ajax {

    /**
     * Display HTML Map search near store
     */
	public function near_store() {
		$json = array();

		if(isset($_POST['attributes'])) {
			$attributes = woopanel_clean( wp_unslash($_POST['attributes']) );
			$lat = isset($_POST['lat']) ? woopanel_clean( wp_unslash($_POST['lat']) ) : false;
			$lng = isset($_POST['lng']) ? woopanel_clean( wp_unslash($_POST['lng']) ) : false;
			extract($attributes);


			$radius = 35;
			$distance = 50;


			$id = uniqid('netbase_custom_css_');
			$classes = $attr = $classeswp = array();
			$nbcss_custom = $class_avatar='';
			$data_owlcarousel = '';
			if ( ! empty( $slider ) ) {
				if($margin){
					$attr[] = '"margin": "' . ( int ) $margin . '"';
				}
				if ( ! empty( $per_row ) ) {
					$attr[] = '"items": "' . ( int ) $per_row . '"';
				}
				if ( ! empty( $columnstablet ) ) {
					$attr[] = '"tablet_cols": "' . ( int ) $columnstablet . '"';
				}
	
				if ( $pagination ) {
					$attr[] = '"dots": "true"';
				}
	
				if($autoplay){
					$attr[] = '"autoplay": "true"';
				}
	
				if($rtl){
					$attr[] = '"rtl": "true"';
				}
	
				if ( ! empty( $attr ) ) {
					$data_owlcarousel = 'data-owl-options=\'{' . esc_attr( implode( ', ', $attr ) ) . '}\'';
				}
				$classeswp[] = 'nb-fw-vccarousel owl-carousel owl-loaded owl-drag';
			}
	
			if ( $bgcolor_custom ) {
				$nbcss_custom .= 'background:' . esc_attr($bgcolor_custom) . ';';
			}   
			if($bordercolor_box){
				$nbcss_custom .= ' border-color:' . esc_attr($bordercolor_box) . ';';
			}            
				 
					
			if ( $nbcss_custom ) {
				$nbcss_custom = ' style="' . esc_attr($nbcss_custom) . '"';
			}
	
			if($avatar_position){
				$class_avatar = 'avatar-'.esc_attr($avatar_position);
			}
	
			$per_page = absint($per_page);
			
			$paged   = max( 1, get_query_var( 'paged' ) );
			$limit   = $per_page;
			$offset  = ( $paged - 1 ) * $limit;
			
			$seller_args = array(
			'number' => $limit,
			'offset' => $offset
			);
	
			if ( $store_type == 'featured' ) {
				$seller_args['meta_query'][] = array(
					'key'     => 'dokan_feature_seller',
					'value'   => 'yes',
					'compare' => '='
				);
			}

			if( $lat && $lng) {
				$maxLat = ( float ) $lat + rad2deg( $distance / $radius );
				$maxLng = ( float ) $lng + rad2deg( $distance / $radius) / cos( deg2rad( ( float ) $lat ) );

				$seller_args['meta_query'][] = array(
					'key'     => 'user_geo_lat',
					'value'   => $maxLat,
					'compare' => '<'
				);
				$seller_args['meta_query'][] = array(
					'key'     => 'user_geo_lng',
					'value'   => $maxLng,
					'compare' => '<'
				);
			}
	
			if ( trim( $store_ids ) ) {
				$seller_args['include'] = explode(',', $store_ids);
				$seller_args['orderby'] = 'include';
			}

			$sellers = dokan_get_sellers( apply_filters( 'dokan_seller_listing_args', $seller_args, $_GET ) );
			
			ob_start();
			
			global $post;

			$image_size = 'full';
			$search_query=  null;
           
			if ( $sellers['users'] ) : ?>
				<div class="dokan-seller-wrap <?php echo esc_attr( implode( ' ', $classeswp )); ?>" <?php echo esc_attr($data_owlcarousel); ?> >
					<?php
					foreach ( $sellers['users'] as $seller ) {
						$store_info = dokan_get_store_info( $seller->ID );
						$banner_id  = isset( $store_info['banner'] ) ? $store_info['banner'] : 0;
						$store_name = isset( $store_info['store_name'] ) ? esc_html( $store_info['store_name'] ) : esc_html__( 'N/A', 'woopanel' );
						$store_url  = dokan_get_store_url( $seller->ID );

						$store_address = dokan_get_seller_address( $seller->ID , true );
						
						$short_address = array();
						$formatted_address = '';

						if ( ! empty( $store_address['street_1'] ) && empty( $store_address['street_2'] ) ) {
							$short_address[] = $store_address['street_1'];
						} else if ( empty( $store_address['street_1'] ) && ! empty( $store_address['street_2'] ) ) {
							$short_address[] = $store_address['street_2'];
						} else if ( ! empty( $store_address['street_1'] ) && ! empty( $store_address['street_2'] ) ) {
							$short_address[] = $store_address['street_1'];
						}

						if ( ! empty( $store_address['city'] ) && ! empty( $store_address['city'] ) ) {
							$short_address[] = $store_address['city'];
						}

						if ( ! empty( $store_address['state'] ) && ! empty( $store_address['country'] ) ) {
							$short_address[] = $store_address['state'] . ', ' . esc_attr($store_address['country']);
						} else if ( ! empty( $store_address['country'] ) ) {
							$short_address[] = $store_address['country'];
						}
						if ( count( $short_address ) > 1 ) {
							$formatted_address = implode( ', ', $short_address );
						} else {
							$formatted_address = implode( ' ', $short_address );
						}

						$seller_rating  = dokan_get_seller_rating( $seller->ID );
						$banner_url = ( $banner_id ) ? wp_get_attachment_image_src( $banner_id, $image_size ) : DOKAN_PLUGIN_ASSEST . '/images/default-store-banner.png';
						$featured_seller = get_user_meta( $seller->ID, 'dokan_feature_seller', true );
						?>
						<div class="dokan-single-seller woocommerce coloum-<?php echo esc_attr($per_row); ?> <?php echo ( ! $banner_id ) ? 'no-banner-img' : ''; ?>">
							
								<div class="store-content" <?php echo esc_attr($nbcss_custom); ?> >
									<div class="seller-avatar">
										<?php echo get_avatar( $seller->ID, 91 ); ?>
									</div>
									<div class="store-data-container">
											<div class="featured-favourite">
												<?php if ( ! empty( $featured_seller ) && 'yes' == $featured_seller ): ?>
													<div class="featured-label"><span><?php esc_html_e( 'Featured', 'woopanel' ); ?></span></div>
												<?php endif ?>

												<?php do_action( 'dokan_seller_listing_after_featured', $seller, $store_info ); ?>
											</div>

											<div class="store-data">
												<div class="store-name"><a href="<?php echo esc_url($store_url); ?>"><?php echo esc_attr($store_name); ?></a></div>

												<?php if ( !empty( $seller_rating['count'] ) ): ?>
													<div class="star-rating dokan-seller-rating" title="<?php echo sprintf( esc_html__( 'Rated %s out of 5', 'woopanel' ), $seller_rating['rating'] ) ?>">
														<span style="width: <?php echo ( ( $seller_rating['rating']/5 ) * 100 - 1 ); ?>%"><?php printf( esc_html__('%s out of 5', 'woopanel' ), '<strong class="rating">'. esc_attr($seller_rating['rating']).'</strong>' );?>
														</span>
													</div>
												<?php 
												else:
													?>
													<div class="start-rating star-o-rating" >
														<i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i><i class="fa fa-star-o"></i>
													</div>
													<?php
												endif ?>

												<?php if ( $formatted_address ): ?>
													<p class="store-address"><?php echo esc_attr($formatted_address); ?></p>
												<?php endif ?>

												<?php if ( !empty( $store_info['phone'] ) ) { ?>
													<p class="store-phone">
														<?php echo esc_html( $store_info['phone'] ); ?>
													</p>
												<?php } ?>

												<?php do_action( 'dokan_seller_listing_after_store_data', $seller, $store_info ); ?>

											</div>
											<div class="nb-fw-btn">
												<a href="<?php echo esc_url($store_url); ?>" class="dokan-btn dokan-btn-theme"><?php esc_html_e( 'Visit Store', 'woopanel' ); ?></a>
											</div>
										</div>
									
								</div>
								<?php do_action( 'dokan_seller_listing_footer_content', $seller, $store_info ); ?>                                        
							
						</div>
					<?php } ?>                            
				</div> <!-- .dokan-seller-wrap -->                        

			<?php else:  ?>
				<p class="dokan-error"><?php esc_html_e( 'No vendor found!', 'woopanel' ); ?></p>
			<?php endif; ?>
			<?php    
	
			$html .= '<div id="' . esc_attr($id) . '" class="woopanel-near-store nb-fw list-store-dokan '.esc_attr($class_avatar).' '. esc_attr( implode( ' ', $classes ) ) .'">' . ob_get_clean() . '</div>';
			
			// End HTML code.
			wp_reset_postdata();

			$json['complete'] = true;
			$json['html'] = apply_filters('netbase_shortcode_list_store_dokan', force_balance_tags($html));
		}
		
		wp_send_json($json);
	}
	
    /**
     * Display HTML Map search product
     */	
	public function search_products() {
		global $post;

		$json = $error = array();

		$accept_type = array('advanced', 'single');

		$product = isset($_POST['product']) ? woopanel_clean($_POST['product']) : '';
		$cat = isset($_POST['cat']) ? woopanel_clean($_POST['cat']) : '';
		
		$location = isset($_POST['location']) ? woopanel_clean($_POST['location']) : '';
		$type = ( isset($type['type']) && in_array($type['type'], $accept_type) ) ? woopanel_clean($type['type']) : '';

		if(isset($_POST['vendor'])) {
			$vendor = woopanel_clean($_POST['vendor']);



			$locationMap = $this->getMap($location);

			$json['lat'] = $locationMap['lat'];
			$json['lng'] = $locationMap['lng'];

			$args = array();

			
			$paged  = 1;
			$limit  = 10;
			$offset = ( $paged - 1 ) * $limit;

			$seller_args = array(
					'number' => $limit,
					'offset' => $offset,
			);

			$seller_args['meta_query'] = array(
					array(
							'key'     => 'dokan_store_name',
							'value'   => $vendor,
							'compare' => 'LIKE',
					),
			);

			if( isset($_POST['location']) && ! empty($_POST['location']) ) {
				$seller_args['meta_query'] = array_merge($seller_args['meta_query'], array(
					array(
						'key' => 'user_geo_lat',
						'value' => $locationMap['lat'],
						'compare' => '>'
					),
					array(
						'key' => 'user_geo_lng',
						'value' => $locationMap['lng'],
						'compare' => '<'
					),
					'relation' => 'AND'
				));
			}



			$sellers = dokan_get_sellers( $seller_args );
			$page_id = dokan_get_option( 'store_listing', 'dokan_pages' );
			$per_row = 3;
			$template_args = apply_filters( 'dokan_store_list_args', array(
				'sellers'         => $sellers,
				'limit'           => $limit,
				'paged'           => $paged,
				'image_size'      => 'medium',
				'search'          => 'yes',
				'pagination_base' => get_permalink($page_id) . 'page/%#%/',
				'per_row'         => $per_row,
			));

			$base = get_template_directory() . '/netbase-core/core.php';
	        if( file_exists($base) ) {
	        	if( function_exists('multistore_get_options')) {
	        		$template_args['style'] = multistore_get_options('nbcore_vendors_list');
	        	}

	        	if( function_exists('printcart_get_options')) {
	        		$template_args['style'] = printcart_get_options('nbcore_vendors_list');
	        	}
	        }

			ob_start();
			dokan_get_template_part( 'store-lists-loop', false, $template_args );
			$content = ob_get_clean();

			$json['html'] = $content;
		}else {
			$json = $this->proccess_search_products($json, array(
				'product' => $product,
				'cat'=> $cat,
				'type' => $type,
				'location' => $location
			));
		}

		wp_send_json($json);
	}

    /**
     * Display HTML Map search product
     */	
	public function proccess_search_products($json, $args) {
		extract($args);
		// Validate
		if( $type == 'advanced' ) {
			if( empty($product) ) {
				$error['product'] = esc_html__('Please enter a product name.', 'woopanel' );
			}

			if( empty($location) ) {
				$error['location'] = esc_html__('Please enter a location address.', 'woopanel' );
			}
		}else {
			if( empty($product) ) {
				$error['product'] = esc_html__('Please enter a product name.', 'woopanel' );
			}

			if( empty($location) ) {
				$error['location'] = esc_html__('Please enter a location address.', 'woopanel' );
			}

		}


		if( $product ) {

			
			$args = array(
				'post_type' => 'product',
				'post_status' => 'published',
				's' => $product
			);



			$args['meta_query'] = array(
				array(
					'key' => '_product_map_lat',
					'value' => $location_lat,
					'compare' => '>'
				),
				array(
					'key' => '_product_map_lng',
					'value' => $location_lng,
					'compare' => '<'
				),
				'relation' => 'AND'
			);

			$locationMap = $this->getMap($location);

			$json['lat'] = $locationMap['lat'];
			$json['lng'] = $locationMap['lng'];

			if( $cat ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'product_cat',
						'field' => 'term_id',
						'terms' => $cat
					)
				);
			}

			$the_query = new WP_Query( $args );
				$html = '';

				ob_start();

				do_action( 'woocommerce_before_shop_loop' );
				woocommerce_product_loop_start();




				if ( $the_query->have_posts() ) {
					$json['complete'] = true;

					
					$items = array();
					while ( $the_query->have_posts() ) {
						$the_query->the_post();

						$items[$post->ID] = array(
							'name' => $post->post_title,
							'url' => get_permalink($post->ID),
							'lat' => get_post_meta($post->ID, '_product_map_lat', true),
							'lng' => get_post_meta($post->ID, '_product_map_lng', true)
						);
					}

					$json['items'] = $items;

					do_action( 'woocommerce_shop_loop' );

					wc_get_template_part( 'content', 'product' );
					
				wp_reset_postdata();
			} else {
				$multistore = null;
		        $base = get_template_directory() . '/netbase-core/core.php';
		        if( file_exists($base) ) {
		        	$multistore = true;
		        }

		        if( $multistore ) {
		        	echo '<div class="col-12">';
		        }
				
				do_action( 'woocommerce_no_products_found' );
				if( $multistore ) {
					echo '</div>';
				}
				$json['not_found'] = true;
			}

			woocommerce_product_loop_end();

			/**
			 * Hook: woocommerce_after_shop_loop.
			 *
			 * @hooked woocommerce_pagination - 10
			 */
			do_action( 'woocommerce_after_shop_loop' );

			$json['html'] = ob_get_clean();
		}else {
			$json['error'] = '- '.implode("\n- ", $error);
		}

		return $json;
	}

    /**
     * Return lat long when enter location
     */	
	public function getMap($location) {

			$data = [
					'searchtext' => $location,
					'app_id' => 'uPpJlH7GwJ5VFivyyrjn',
					'app_code' => 'jt713YZorNYpkHhGCkelOQ'
			];
			
			$map = json_decode( file_get_contents('https://geocoder.api.here.com/6.2/geocode.json?' . http_build_query($data) ) );


			if( isset($map) ) {
				$location_lat = $map->Response->View[0]->Result[0]->Location->DisplayPosition->Latitude;
				$location_lng = $map->Response->View[0]->Result[0]->Location->DisplayPosition->Longitude;

				return array(
					'lat' => $location_lat,
					'lng' => $location_lng
				);

			}

			return false;
	}
}