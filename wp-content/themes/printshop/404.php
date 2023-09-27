<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package Netbase
 */

printshop_get_header() ?>

	<div class="error-page-wrapper">
		<div class="error-box-wrap">
			<div class="text-center container">
				<h1 class="heading-404"><?php echo esc_html_e('404', 'printshop'); ?></h1>
				<div class="error-box">
					<h3><?php echo esc_html__('Sorry Page Not Found', 'printshop') ?></h3>
					<p><?php echo esc_html__('The page you are looking for does not appear to exist. Please go back or head on over our homepage to choose a new direction.', 'printshop'); ?></p>
					<div class="error-action clearfix">
						<a href="<?php echo esc_url( site_url() ); ?>" class="btn btn-light error-home"><i class="fa fa-home"></i><?php echo esc_html__(' Go to home', 'printshop'); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php get_footer(); ?>
