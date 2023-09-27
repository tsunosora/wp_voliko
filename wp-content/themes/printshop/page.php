<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Netbase
 */
$printshop_option = printshop_get_redux_options();

$page_layout     = esc_html( get_post_meta(  $post->ID, 'sidebar_option', true) );
$page_comment    = printshop_get_option('page_comments');

printshop_get_header() ?>
<?php 
if ( !is_front_page() && !is_home() ) {	
?>
	<div class="page-title-wrap">
		<div class="container">
			<h1 class="page-entry-title left"> <?php wp_title(''); ?></h1>		
		</div>
	</div>
<?php	
}
	global $post;
	printshop_get_page_header($post->ID);
?>		
	<div id="content-wrap" class="<?php echo ( $page_layout == 'full-screen' ) ? '' : 'container'; ?> <?php echo esc_html(printshop_get_layout_class()); ?>">
		<div id="primary" class="<?php echo ( $page_layout == 'full-screen' ) ? 'content-area-full' : 'content-area'; ?>">
			<main id="main" class="site-main">
					<?php while ( have_posts() ) : the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
							<div class="entry-content">
								<?php the_content(); ?>								
							</div><!-- .entry-content -->
						</article><!-- #post-## -->
					<?php endwhile; // end of the loop. ?>
			</main><!-- #main -->
		</div><!-- #primary -->			
		<?php echo printshop_get_sidebar(); ?>				
	</div> <!-- /#content-wrap -->
<?php get_footer(); ?>