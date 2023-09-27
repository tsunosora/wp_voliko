<?php
/**
 * Navigation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woopanel_before_navigation' ); ?>

	<!-- BEGIN: Left Aside -->
	<button class="m-aside-left-close  m-aside-left-close--skin-dark " id="m_aside_left_close_btn">
		<i class="la la-close"></i>
	</button>
	<div id="m_aside_left" class="m-grid__item	m-aside-left  m-aside-left--skin-dark ">
		<!-- BEGIN: Aside Menu -->
        <div id="m_ver_menu" class="m-aside-menu  m-aside-menu--skin-dark m-aside-menu--submenu-skin-dark ">
			<?php echo woopanel_menu_output(); ?>
		</div>
		<!-- END: Aside Menu -->
	</div>
	<!-- END: Left Aside -->

<?php do_action( 'woopanel_after_navigation' ); ?>
