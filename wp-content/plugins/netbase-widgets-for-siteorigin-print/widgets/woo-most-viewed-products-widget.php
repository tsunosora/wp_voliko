<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Most Viewed Products Widget
 *
 * @extends  WP_Widget
 *
 * @since 1.0.0
 */
function wpnetbase_widget_most_viewed_load() 
{
	register_widget( 'wpnetbase_widget_most_viewed' );
}
add_action( 'widgets_init', 'wpnetbase_widget_most_viewed_load' );



/**
 * Set view count for a product
 *
 * @param $post_id product id
 *
 * @since 1.0.0
 */
function wcmvp_set_view_count( $post_id ) {
	$count_key = 'wcmvp_product_view_count';
	$count     = get_post_meta( $post_id, $count_key, true );
	if ( $count == '' ) {
		delete_post_meta( $post_id, $count_key );
		update_post_meta( $post_id, $count_key, '1' );
	} else {
		$count ++;
		update_post_meta( $post_id, $count_key, (string) $count );
	}
}

/**
 * Get the view count for a particular product
 *
 * @param $post_id product id
 *
 * @return mixed|string product view count
 *
 * @since 1.0.0
 */
function wcmvp_get_view_count( $post_id ) {
	$count_key = 'wcmvp_product_view_count';
	$count     = get_post_meta( $post_id, $count_key, true );
	if ( empty( $count ) ) {
		delete_post_meta( $post_id, $count_key );
		add_post_meta( $post_id, $count_key, '0' );
		$count = '0';
	}

	return $count;
}

/**
 * Get the WP_Query instance for most viewed products
 *
 * @param int $num_posts number of postst to display
 *
 * @return WP_Query most viewed products query
 *
 * @since 1.0.0
 */
function wcmvp_get_most_viewed_products( $num_posts = 10 ) {
	$count_key                = 'wcmvp_product_view_count';
	$query_args               = array(
		'posts_per_page' => $num_posts,
		'no_found_rows'  => 1,
		'post_status'    => 'publish',
		'post_type'      => 'product',
		'orderby'        => 'meta_value_num',
		'order'          => 'DESC',
		'meta_key'       => $count_key,
	);
	$query_args['meta_query'] = array(
		array(
			'key'     => $count_key,
			'value'   => '0',
			'type'    => 'numeric',
			'compare' => '>',
		),
	);
	$wcmvp_query              = new WP_Query( $query_args );

	return $wcmvp_query;
}

/**
 * Get the product view count html text
 *
 * @param int $product_id
 *
 * @return string product view count html
 *
 * @since 1.0.0
 */
function wcmvp_get_view_count_html( $product_id = 0 ) {
	if ( empty( $product_id ) ) {
		return '';
	}
	$view_count      = wcmvp_get_view_count( $product_id );
	$view_count_html = '<span class="product-views">' . $view_count . ' ' . __( 'Views', 'woo-most-viewed-products' ) . '  </span>';

	return apply_filters( 'wcmvp_view_count_html', $view_count_html, $product_id, $view_count );
}

/**
 * @param int $num_posts
 *
 * @return string
 *
 * @since 1.0.0
 */
function wcmvp_render_most_viewed_products( $num_posts = 10 ) {
	$r = wcmvp_get_most_viewed_products( $num_posts );
	ob_start();
	if ( $r->have_posts() ) {
		echo '<ul class="woo-most-viewed product_list_widget">';
		while ( $r->have_posts() ) {
			$r->the_post();
			global $product;
			?>
			<li>
				<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>"
				   title="<?php echo esc_attr( $product->get_title() ); ?>">
					<?php echo $product->get_image(); ?>
					<span class="product-title"><?php echo $product->get_title(); ?></span>
				</a>
				<?php echo wcmvp_get_view_count_html( $product->get_id() ); ?>
				<?php echo $product->get_price_html(); ?>
			</li>
			<?php
		}
		echo '</ul>';
	} else {
		echo '<ul class="woo-most-viewed wcmvp-not-found product_list_widget">';
		echo '<li>' . __( 'No products have been viewed yet !!', 'woo-most-viewed-products' ) . '</li>';
		echo '</ul>';
	}
	wp_reset_postdata();
	$content = ob_get_clean();

	return $content;
}

/**
 * Display popular products
 *
 * @param int $num_posts number of products to display
 *
 * @since 1.0.0
 */
function wcmvp_display_most_viewed_products( $num_posts = 10 ) {
	$content = wcmvp_render_most_viewed_products( $num_posts );
	echo $content;
}

/**
 * Set view counts for all products once viewed
 *
 * @since 1.0.0
 */
function wcmvp_set_view_count_products() {
	global $product;
	wcmvp_set_view_count( $product->get_id() );
}



add_action( 'woocommerce_after_single_product', 'wcmvp_set_view_count_products' );
/**
 * Shortcode
 *
 * @param $atts attributes
 *
 * @return string rendered products output
 *
 * @since 1.0.0
 */
function wpnetbase_most_viewed_products_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'product_count' => '10',
		),
		$atts
	);

	$content = wcmvp_render_most_viewed_products( $atts['product_count'] );

	return $content;
}

add_shortcode( 'nbmostviewd', 'wpnetbase_most_viewed_products_shortcode' );
	

class wpnetbase_widget_most_viewed extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		parent::__construct(
		/*Base ID of your widget*/ 
		'wpnetbase_widget_most_viewed', 

		/*Widget name will appear in UI*/ 
		__('NBT products most viewed', 'wpb_widget_domain'), 

		/*Widget description*/ 
		array( 'description' => __( 'Woocommerce products most viewed.', 'wpb_widget_domain' ),  'panels_groups' => array('netbaseteam') ) 
		);
	}	

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget']; 
		ob_start();
		if(isset($instance['product_count'])){
            $product_count = $instance['product_count'];
        }else{ $product_count = 3; }
		
		$count_key                = 'wcmvp_product_view_count';
		$query_args               = array(
			'posts_per_page' => $product_count,
			'no_found_rows'  => 1,
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
			'meta_key'       => $count_key,
		);
		$query_args['meta_query'] = array(
			array(
				'key'     => $count_key,
				'value'   => '0',
				'type'    => 'numeric',
				'compare' => '>',
			),
		);
		$r                        = new WP_Query( $query_args );
		if ( $r->have_posts() ) {
			
			echo '<div class="nbtsow-products-wrap">';
				if(isset($instance['title'])){ $title =  $instance['title'];
					echo '<h2 class="widget-heading">'; 
						echo $title ;
					echo '</h2>';
				}

				echo '<ul class="product_list_widget">';
				while ( $r->have_posts() ) {
					$r->the_post();
					global $product;
					?>
					<li class="product">
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
                                <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php echo $product->get_title(); ?></a>
                            </h4>
                            <?php echo wcmvp_get_view_count_html( $product->get_id() ); ?>
                            
                            <span class="product-price"><?php echo $product->get_price_html(); ?></span>
                        </div>
                          <p class="product-description">
                          <?php echo wp_trim_words( get_the_excerpt(), 6, '...' ); ?>
                          </p> 
                    </div>

						<!-- <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>"
						   title="<?php echo esc_attr( $product->get_title() ); ?>">
							<?php //echo $product->get_image(); ?>
							<span class="product-title"><?php echo $product->get_title(); ?></span>
						</a> -->
						<?php //echo wcmvp_get_view_count_html( $product->id ); ?>
						<?php //echo $product->get_price_html(); ?>
					</li>
					<?php
				}
				echo '</ul>';
			echo '</div>';
			
		} else {
			echo '<ul class="product_list_widget">';
			echo '<li>' . __( 'No products have been viewed yet !!', 'woo-most-viewed-products' ) . '</li>';
			echo '</ul>';
		}
		wp_reset_postdata();
		$content = ob_get_clean();
		echo $content;
		echo $args['after_widget'];
	}
	 /**
     * Update the widget settings.
     */
    function update($new_instance, $old_instance) {
        
        $instance = $old_instance;
        $instance['title'] =  empty($new_instance['title']) ? '' : $new_instance['title'];
        $instance['product_count'] =  empty($new_instance['product_count'])
                                      ? 0
                                      : (int) $new_instance['product_count'];
        return $instance;
    }
	function form($instance) {
      $product_count =  isset($instance['product_count']) ? $instance['product_count'] : 3;
      $title =  isset($instance['title']) ? $instance['title'] : '';
      ?>
      <p>
           <label for="<?= $this->get_field_id( 'product_count' ); ?>">
                      Title
                    </label>
                    <input type="text" name="<?= $this->get_field_name( 'title' ); ?>"
                          value="<?= $title; ?>" class="widefat"
                          id="<?= $this->get_field_id( 'title' ); ?>" />
                </p>
         
            
                <p>
                    <label for="<?= $this->get_field_id( 'product_count' ); ?>">
                      Number of Products To Show
                    </label>
                    <input type="text" name="<?= $this->get_field_name( 'product_count' ); ?>"
                          value="<?= $product_count; ?>" class="widefat"
                          id="<?= $this->get_field_id( 'product_count' ); ?>" />
                </p>
    <?php
       
    }
}