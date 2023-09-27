<?php
/**
 * @package Netbase
 */

$blog_single_thumb = printshop_get_option('blog_single_thumb');

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<div class="entry-meta">
			<?php printshop_posted_on(); ?>
		</div><!-- .entry-meta -->

	</header><!-- .entry-header -->
	
	<div class="entry-content">
		<div class="entry-thumb">
		<?php 
		if( has_post_thumbnail( ) ) {
			the_post_thumbnail();
		}
		?>
		</div>
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'printshop' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

</article><!-- #post-## -->
