<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Netbase
 */
$printshop_option = printshop_get_redux_options();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="<?php echo esc_url('http://gmpg.org/xfn/11'); ?>">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div id="page" class="hfeed site">		

		<div class="col-left" >
		<div id="topbar" class="site-topbar">

			<div class="header-right-widgets">
				<div class="header-right-cart-search">
					<span id="netbase-responsive-toggle"><i class="fa fa-bars"></i></span>
						<div class="header-cart-search">
							<?php if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { ?>
							<?php $count = WC()->cart->cart_contents_count;?>
							<a class="cart-contents" href="<?php echo esc_url(wc_get_cart_url()); ?>" title="<?php esc_html_e( 'View your shopping cart', 'printshop' ); ?>"><span><?php if ( $count > 0 ) echo intval($count) ; ?></span></a>
							<?php } ?>
							<div class="widget_shopping_cart_content"></div>
						</div>
							<?php echo do_shortcode( '[nbt_search]' ); ?>
						</div>
							
				</div>
						

		</div> <!-- /#topbar -->
		<header id="masthead" class="site-header <?php  if ( printshop_get_option('header_fixed') ) echo 'fixed-on' ?>">
			<div class="header-wrap">
				<div class="container">
					<div class="site-branding">
						<?php if ( printshop_get_option('site_logo', false, 'url') !== '' ) { ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
							<img src="<?php echo printshop_logo_render(); ?>" alt="<?php get_bloginfo( 'name' ) ?>" />
						</a>
						<?php } else { ?>
						<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
						<?php } ?>
					</div><!-- /.site-branding -->

					<div class="header-right-wrap-top">
						<div class="nbt-leftlayoutheader" style="display:none;">
							<?php 
							if ( has_nav_menu( 'printshop-sidebar-menu' ) ) {
								wp_nav_menu( array( 'theme_location' => 'printshop-sidebar-menu' ) ); 
							}
							?>
						</div>
											
						<div class="nbt-main-menu">
							<div class="netbase-menu-title">
								<h3><?php esc_html_e('Navigation','printshop'); ?></h3>
								<span id="close-netbase-menu"><i class="fa fa-times-circle"></i></span>
							</div>
							<?php 
							if ( has_nav_menu( 'primary' ) ) {
								wp_nav_menu( array('theme_location' => 'primary', 'container' => '', 'items_wrap' => '%3$s' ) ); 
							}
							?>
						</div>
						
					</div>
					
					
				</div>
			</div>
			<div class="header-left-bottom">
				<?php dynamic_sidebar('topbar-right'); ?>
				<?php if ( $printshop_option['extract_1_value'] ) { ?>
					<div class="extract-element">
								<span class="phone-text">
								<i class="fa fa-phone"></i> <?php echo wp_kses_post( $printshop_option['extract_1_value'] ); ?></span>
					</div>
				<?php } ?>
				<div class="copy_text">
					<?php
					if ( printshop_get_option('footer_copyright') == '' ) {
						printf( esc_html__( 'Central - Copyright &copy; 2015 %2$s. All Rights Reserved', 'printshop' ), get_bloginfo('name'), '<a href="'. esc_url( esc_html__( 'http://www.netbaseteam.com/', 'printshop' ) ) .'">netbaseteam.com</a>' );
					} else {
						echo wp_kses_post( printshop_get_option('footer_copyright') );
					}
					?>
				</div>
			</div>
		</header><!-- #masthead -->

		<script type="text/javascript">
		
		jQuery(document).ready(function($) {
    
        var wpnetbase_checkWidths = $(window).width();
    	if(wpnetbase_checkWidths > 768){
    		jQuery('.nbt-leftlayoutheader').css('display', 'block');
    		jQuery('.nbt-main-menu').css('display', 'none');
    	}
		});
    	</script>

</div>
		
		<div id="content" class="site-content">