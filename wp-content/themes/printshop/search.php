<?php
/**
 * The template for displaying search results pages.
 *
 * @package Netbase
 */
$printshop_option = printshop_get_redux_options();

printshop_get_header() ?>
		
		<div id="content-wrap" class="container <?php echo esc_html(printshop_get_layout_class()); ?>">
			<div id="primary" class="content-area">
				<main id="main" class="site-main">

					<?php if ( have_posts() ) : ?>

						<header class="page-header">
							<h1 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'printshop' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
						</header><!-- .page-header -->
						<?php while ( have_posts() ) : the_post(); ?>

							<?php
							get_template_part( 'content', 'search' );
							?>

						<?php endwhile; ?>

						<?php printshop_paging_nav(); ?>

					<?php else : ?>

						<?php get_template_part( 'content', 'none' ); ?>

					<?php endif; ?>

				</main><!-- #main -->
			</div><!-- #primary -->

			<?php echo printshop_get_sidebar(); ?>
					
		</div> <!-- /#content-wrap -->

<?php get_footer(); ?>