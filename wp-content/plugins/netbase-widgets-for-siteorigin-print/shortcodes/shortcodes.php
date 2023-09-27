<?php
function wpnetbase_tab_thumb() {
	add_image_size( 'wpnetbase-tab-thumb', 360, 285, array('center', 'center') );	
}
add_action( 'after_setup_theme', 'wpnetbase_tab_thumb' );

add_shortcode( 'netbase-post-loop', 'wpnetbase_create_shortcode_postloop' );
add_shortcode('wpnetbase_url_base', 'wpnetbase_url_base_function');
add_shortcode('wpnetbase_url_template', 'wpnetbase_url_template_function');
add_shortcode( 'wpnetbase-page-content', 'wpnetbase_page_content_shortcode' );

function wpnetbase_create_shortcode_postloop($atts) {
		ob_start();
		extract( shortcode_atts( array (
			'category' => ''
		), $atts ) );
		$loop_args = array(
			'post_type' => 'post',
			'posts_per_page' => 6,
			'category_name' => $category,
			'ignore_sticky_posts' => 1
		);
		$loop_query = new WP_Query( $loop_args );
		if ( $loop_query->have_posts() ): ?>
			<?php while ( $loop_query->have_posts() ) : $loop_query->the_post(); ?>
				<div class="col-md-4 col-sm-6 col-xs-12 block-recent">
					<div class="w-block-recent">
						<div class="image-recent">
							<?php
							if( has_post_thumbnail( ) ) {
								the_post_thumbnail('wpnetbase-tab-thumb');
							}
							?>
						</div>
						<div class="info-recent">
							<h3 class="title"><?php the_title(); ?></h3>
							<?php
								if ( has_excerpt() ){
									echo '<div class="text-recent"><p>';
									echo wp_trim_words( get_the_excerpt(), 30, '...' ); 
									echo '</p></div>';
								}
							?>
							
							<a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e('read more', 'wpnetbase'); ?></a>
						</div>
					</div>
				</div>
			<?php endwhile;
			wp_reset_postdata();
		endif;
		$myvariable = ob_get_clean();
		return $myvariable;
}

// [url_base]
function wpnetbase_url_base_function() {
	return get_bloginfo( "url" );
	
}

// [url_template]
function wpnetbase_url_template_function() {
	if( get_theme_root_uri() && get_template() ) {
		return get_theme_root_uri() . "/" . get_template();
	}
	else {
		return "";
	}
}

function wpnetbase_page_content_shortcode($atts) {
	extract( shortcode_atts( array (
			'page_slug' => ''
		), $atts ) );
	//$page_slug='create-bottom-design';
	$page = get_page_by_path($page_slug);
    if ($page) {
		$thispage = get_page($page->ID);
		return do_shortcode( $thispage->post_content );
		
    } else {
        return null;
    }
	
}