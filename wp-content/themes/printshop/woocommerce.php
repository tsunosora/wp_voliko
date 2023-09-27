<?php
/**
 * Custom Woocommerce shop page.
 *
 * @package Netbase
 */

global $woocommerce;
$page_layout = esc_html( get_post_meta( wc_get_page_id('shop'), 'sidebar_option', true) );

printshop_get_header() ?>
		
		<?php
		if(is_shop() || is_product_category() || is_product_tag() || is_product()) {
			printshop_get_page_header(wc_get_page_id('shop'));
		}
		
		global $post;
		printshop_get_page_header($post->ID);
		
		if ( $woocommerce && is_shop() || $woocommerce && is_product() || $woocommerce && is_product_category() || $woocommerce && is_product_tag() ) {
				?>
				<div class="page-title-wrap">
					<div class="container">
						<h1 class="page-entry-title left">
							<?php woocommerce_page_title(); ?>
						</h1>
						<?php printshop_breadcrumb(); ?>
					</div>
				</div>
		<?php
			}		
		?>	
		
		<div id="content-wrap" class="<?php echo ( $page_layout == 'full-screen' ) ? '' : 'container'; ?> <?php echo esc_html(printshop_get_layout_class()); ?>">
			<div id="primary" class="<?php echo ( $page_layout == 'full-screen' ) ? 'content-area-full' : 'content-area'; ?>">

			<?php 
			$nbdesigner_page_design_tool_class = '';
			$class_is_edit_mode = '';
			if(class_exists('Nbdesigner_Plugin') && is_nbdesigner_product($post->ID)){

				$nbdesigner_page_design_tool = nbdesigner_get_option('nbdesigner_page_design_tool');
				//show design tool in new page
				if($nbdesigner_page_design_tool == 2) {
					$nbdesigner_page_design_tool_class = ' js_open_desginer_in_new_page';
				}

				if ( isset( $_REQUEST['nbo_cart_item_key'] ) && $_REQUEST['nbo_cart_item_key'] != '' ){
					$class_is_edit_mode = ' js_is_edit_mode';
				}
			}
			
			?>
				<main id="main" class="site-main<?php echo esc_attr($nbdesigner_page_design_tool_class);?><?php echo esc_attr($class_is_edit_mode);?>">

					<?php woocommerce_content(); ?>
	
				</main><!-- #main -->
			</div><!-- #primary -->
			
			<?php echo printshop_get_sidebar(); ?>
				
		</div> <!-- /#content-wrap -->

<?php
get_footer();
