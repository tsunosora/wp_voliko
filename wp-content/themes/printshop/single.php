<?php
/**
 * The template for displaying all single posts.
 *
 * @package Netbase
 */
$printshop_option = printshop_get_redux_options();

printshop_get_header() ?>
	
	<div class="blog-banner">
		<div class="container">
			<img src="<?php echo get_template_directory_uri(); ?>/assets/images/blog-banner.jpg">
		</div>
	</div>

	<div id="content-wrap" class="container <?php echo esc_html(printshop_get_layout_class()); ?>">
		<div class="row">
			<div id="primary" class="content-area">
				<main id="main" class="site-main">

					<?php while ( have_posts() ) : the_post(); ?>

						<?php get_template_part( 'content', 'single' ); ?>

						<?php
							if ( comments_open() || get_comments_number() ) :
								comments_template();
							endif;
						?>

					<?php endwhile;?>

				</main><!-- #main -->
			</div><!-- #primary -->
			<?php echo printshop_get_sidebar(); ?>
		</div>
	</div> <!-- /#content-wrap -->
<?php get_footer(); ?>