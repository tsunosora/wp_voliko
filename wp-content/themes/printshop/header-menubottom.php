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
	<h1 class="h1-hidden"><?php esc_html_e('PRINT CARDS', 'printshop');?></h1>
		<div id="topbar" class="site-topbar">
			<div class="container">
				<div class="topbar-inner clearfix">
					<div class="header-left-widgets">
						<div class="topbar-left-widget">		
						<?php dynamic_sidebar('topbar-left'); ?>
						</div>
					</div>
					
					<div class="header-right-widgets">
							<?php dynamic_sidebar('topbar-right'); ?>
							<?php if ( $printshop_option['extract_1_value'] ) { ?>
							<div class="extract-element">
								<span class="phone-text">
								<i class="fa fa-phone"></i> <?php echo wp_kses_post( $printshop_option['extract_1_value'] ); ?></span>
							</div>
							<?php } ?>							
							<?php if ( is_active_sidebar( 'currency-1' ) ) : ?>
								<div class="extract-element currency">

									<?php
									$datacurrency =do_shortcode('[woocs]');  
									$data = str_replace('<select', '<div class="currency-sel"><select', $datacurrency);
									$data = str_replace('</select>', '</select></div>', $data);
									echo wp_kses($data, array(
										'select' => array(),
										'div' => array('class')
									));
									?>
								</div>
							<?php endif; ?>
						</div>
						
					
				</div>
			</div>
		</div> <!-- /#topbar -->

		<header id="masthead" class="site-header <?php if ( printshop_get_option('header_fixed') ) echo 'fixed-on' ?> <?php if ( printshop_get_option('header_bg_transparent') ) echo 'bg-trans' ?>">
			
			<div class="header-wrap">
				<div class="container">
					<div class="site-branding col-xs-6 col-sm-2 col-md-3 padding-left-0">
						<?php if ( printshop_get_option('site_logo', false, 'url') !== '' ) { ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
							<img src="<?php echo printshop_logo_render(); ?>" alt="<?php get_bloginfo( 'name' ) ?>" />
						</a>
						<?php } else { ?>
						<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
						<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
						<?php } ?>
					</div><!-- /.site-branding -->

					
					<div class="header-right-cart-search col-xs-5 col-sm-10 col-md-1 padding-right-0">
						<span id="netbase-responsive-toggle"><i class="fa fa-bars"></i></span>
						<div class="header-cart-search">
							<?php if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { ?>
							<?php $count = WC()->cart->cart_contents_count;?>
							<a class="cart-contents" href="<?php echo esc_url(wc_get_cart_url()); ?>" title="<?php esc_html_e( 'View your shopping cart', 'printshop' ); ?>"><span><?php if ( $count > 0 ) echo intval($count) ; ?></span></a>
							<?php } ?>
							<div class="widget_shopping_cart_content"></div>
						</div>
						<div class="header-search">
							<?php echo do_shortcode( '[nbt_search]' ); ?>
						</div>
					</div>
					<div class="row">
						<div class="header-right-wrap-top col-sm-12 col-md-12">
							<div class="netbase-menu-title">
								<h3><?php esc_html_e('Navigation','printshop'); ?></h3>
								<span id="close-netbase-menu"><i class="fa fa-times-circle"></i></span>
							</div>
							<?php wp_nav_menu( array('theme_location' => 'primary', 'container' => '', 'items_wrap' => '%3$s' ) ); ?>
						</div>
					</div>	
				</div>
			</div>
		</header><!-- #masthead -->

		<div id="content" class="site-content">