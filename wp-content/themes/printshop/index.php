<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Netbase
 */
$printshop_option = printshop_get_redux_options();

printshop_get_header() ?>

		<?php
		// Blog Page Title
		if ( !is_front_page() && is_home() && get_option('page_for_posts') ) {
			if ( $printshop_option['blog_page_title'] ) {
				?>
				<div class="page-title-wrap">
					<div class="container">
						<h1 class="page-entry-title">
							<?php echo esc_html(get_the_title( get_option('page_for_posts') )); ?>
						</h1>
					</div>
				</div>
				<?php
			}
		}
		?>		
		<div id="content-wrap" class="container <?php echo esc_html(printshop_get_layout_class()); ?>">
			<div id="primary" class="content-area">
				<main id="main" class="site-main">
				<?php if ( have_posts() ) : 
					while ( have_posts() ) : the_post(); 
							/* Include the Post-Format-specific template for the content.
							 * If you want to override this in a child theme, then include a file
							 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
							 */
							get_template_part( 'content');
					endwhile;  
					printshop_paging_nav();  
				else :  
					get_template_part( 'content', 'none' );  
				endif; ?>
				</main><!-- #main -->
			</div><!-- #primary -->
			<?php echo printshop_frontpage_sidebar(); ?>
			
		</div> <!-- /#content-wrap -->

<?php get_footer(); ?>
