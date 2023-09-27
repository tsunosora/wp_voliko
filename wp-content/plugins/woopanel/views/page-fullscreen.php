<?php
global $wp_query;

if ( ! defined( 'ABSPATH' ) ) exit;
do_action('woopanel_init');?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php do_action('woopanel_head');?>
	<?php wp_head(); ?>
</head>

<!-- begin::Body -->
<body class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default <?php echo implode(' woopanel-', array_keys($wp_query->query));?>">

	<!-- begin:: Page -->
	<div id="woopanel_main" class="m-grid m-grid--hor m-grid--root m-page">
		<?php
		do_action('woopanel_start');
		global $post;
		echo do_shortcode('[woopanel]');
		do_action('woopanel_end');?>
	</div>

	<!-- begin::Scroll Top -->
	<div id="m_scroll_top" class="m-scroll-top">
		<i class="la la-arrow-up"></i>
	</div>

	<?php do_action('woopanel_footer');
wp_footer(); ?>
</body>
</html>