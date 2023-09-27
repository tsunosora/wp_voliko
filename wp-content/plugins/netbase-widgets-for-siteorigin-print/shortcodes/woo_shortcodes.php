<?php
add_shortcode('sale_products', 'wpnetbase_woocommerce_sale_products');
add_shortcode('random_post', 'wpnetbase_create_shortcode_randompost');

function wpnetbase_woocommerce_sale_products( $atts ){
		global $woocommerce_loop;

		extract( shortcode_atts( array(
			'columns'       => '4',
			'orderby'       => 'title',
			'order'         => 'asc'
		), $atts ) );

		$args = array(
			'post_type' => 'product',
			'post_status' => 'publish',
			'ignore_sticky_posts'   => 1,
			'orderby' => $orderby,
			'order' => $order,
			'posts_per_page' => $per_page,
			'meta_query' => array(
				array(
					'key' => '_visibility',
					'value' => array('catalog', 'visible'),
					'compare' => 'IN'
				),
				array(
					'key' => '_sale_price',
					'value' => 0,
					'compare' => '>',
					'type' => 'NUMERIC'
				)
			)
		);
		ob_start();

		$products = new WP_Query( $args );

		$woocommerce_loop['columns'] = $columns;

		if ( $products->have_posts() ) : ?>

			<ul class="products">

				<?php while ( $products->have_posts() ) : $products->the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			</ul>

		<?php endif;

		wp_reset_query();

		return ob_get_clean();
}	

function wpnetbase_create_shortcode_randompost() {

		$random_query = new WP_Query(array(
			'posts_per_page' => 10,
			'orderby' => 'rand'
		));

		ob_start();
		if ( $random_query->have_posts() ) :
			"<ol>";
			while ( $random_query->have_posts() ) :
				$random_query->the_post();?>

				<li><a href="<?php the_permalink(); ?>"><h5><?php the_title(); ?></h5></a></li>

			<?php endwhile;
			"</ol>";
		endif;
		$list_post = ob_get_contents();

		ob_end_clean();

		return $list_post;
}
	