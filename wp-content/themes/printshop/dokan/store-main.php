<?php
/* Template Name: Store */
$tshirt_option = tshirt_get_redux_options();

$page_layout     = get_post_meta( $post->ID, '_wpc_page_layout', true );
$page_breadcrumb = get_post_meta( $post->ID, '_wpc_hide_breadcrumb', true );
$page_comment    = tshirt_get_option('page_comments');
$page_cover      = get_the_post_thumbnail_url($post->ID, 'full');

tshirt_get_header() ?>
    <div class="banner-cover" style="background: url('<?php echo $page_cover;?>');">
        <div class="container">
            <div class="dokan-search-full">
                <div class="dokan-search-wrapper">
                    <form action="" method="POST" id="search-store">
                        <div class="clearfix col-search-row">
                            <div class="col-search">
                                <input type="text" class="search-inout" placeholder="Shop Name" name="name" />
                            </div>

                            <div class="col-search">
                                <div class="select-wrapper">
                                    <?php
                                    $country_obj   = new WC_Countries();
                                    $countries     = $country_obj->countries;
                                    ?>
                                    <select <?php echo $disabled ?> name="address" class="">
                                        <?php dokan_country_dropdown( $countries, '', false ); ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-search">
                                <div class="select-wrapper">
                                    <?php
                                    wp_dropdown_categories( array(
                                        'show_option_none' => __( '- Select a category -', 'printshop' ),
                                        'hierarchical'     => 1,
                                        'hide_empty'       => 0,
                                        'name'             => 'product_cat',
                                        'id'               => 'product_cat',
                                        'taxonomy'         => 'product_cat',
                                        'title_li'         => '',
                                        'class'            => '',
                                        'exclude'          => '',
                                        'selected'         => isset( $_GET['product_cat'] ) ? $_GET['product_cat'] : '-1',
                                    ) );
                                    ?>
                                </div>

                            </div>



                            <div class="col-search">
                                <button class="btn" name="search" type="submit">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
		<?php 
		global $post;
		tshirt_get_page_header($post->ID);
		
		if (!is_page( 'home-t-shirt' ) && !is_page( 'home-t-shirt-2' ) && !is_page( 'home-t-shirt-3' ) && !is_front_page() && !is_home() ) {
			//if ( $tshirt_option['blog_page_title'] ) {
				?>
				<div class="page-title-wrap <?php echo esc_html(get_the_title($post->ID)); ?>">
					<div class="container">
						<h1 class="page-entry-title left">
						<?php echo esc_html(get_the_title($post->ID)); ?></h1>								
				
						<?php tshirt_breadcrumb(); ?>			
					</div>
				</div>
				<?php
			//}
		}
		?>
		<div id="content-wrap" class="<?php echo ( $page_layout == 'full-screen' ) ? '' : 'container'; ?> <?php echo esc_html(tshirt_get_layout_class()); ?>">
			<div id="primary" class="<?php echo ( $page_layout == 'full-screen' ) ? 'content-area-full' : 'content-area'; ?>">
				<main id="main" class="site-main">


					<?php while ( have_posts() ) : the_post(); ?>

						<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

							<div class="entry-content">

								<?php the_content(); 

								// If comments are open or we have at least one comment, load up the comment template.
								if ( comments_open() || get_comments_number() ) :
									comments_template();
								endif;
								?>

								
							</div><!-- .entry-content -->

						</article><!-- #post-## -->

					<?php endwhile; // end of the loop. ?>

				</main><!-- #main -->
			</div><!-- #primary -->
			
			<?php echo tshirt_get_sidebar(); ?>
				
		</div> <!-- /#content-wrap -->

<?php get_footer(); ?>
