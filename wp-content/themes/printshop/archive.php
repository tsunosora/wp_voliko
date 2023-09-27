<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Netbase
 */
$printshop_option = printshop_get_redux_options();

printshop_get_header() ?>

<div class="page-title-wrap">
	<div class="container">
		<h1 class="page-entry-title">
			<?php single_term_title(); ?>
			
		</h1>
		<img src="<?php echo get_template_directory_uri(); ?>/assets/images/blog-header-bg.jpg">
	</div>
</div>

<div id="content-wrap" class="container <?php echo esc_html(printshop_get_layout_class()); ?>">
	<div class="row">
		<div id="primary" class="content-area">
			<main id="main" class="site-main">
				<?php if ( have_posts() ) : ?>

					<header class="archive-header">
						<?php									
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
						?>
					</header>

					<?php while ( have_posts() ) : the_post(); ?>
						<div class="category-post-type">
							<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>								

								<header class="entry-header">
									<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

									<?php if ( 'post' == get_post_type() ) : ?>
										<div class="entry-meta">
											<?php printshop_posted_on(); ?>
										</div><!-- .entry-meta -->
									<?php endif; ?>
								</header><!-- .entry-header -->

								<?php
								if( has_post_thumbnail( ) ) {
									echo '<div class="post-thumbnail"><a href="' . esc_url( get_permalink() ) . '">';
									
										the_post_thumbnail( 'blog-large' );
									
									echo '</a></div>';
								}
								?>

								<div class="entry-content">
									<?php the_excerpt(); ?>
								</div><!-- .entry-content -->

							</article><!-- #post-## -->
						</div>
					<?php endwhile; ?>

					<?php printshop_paging_nav(); ?>

				<?php else : ?>

					<?php get_template_part( 'content', 'none' ); ?>

				<?php endif; ?>

			</main><!-- #main -->
		</div><!-- #primary -->

		<?php echo printshop_get_sidebar(); ?> 		
	</div>
</div> <!-- /#content-wrap -->

<?php get_footer(); ?>
