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
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-990JEQ3HWM">
</script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-990JEQ3HWM');
</script>
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
						<?php if ( $printshop_option['extract_1_value'] ) { ?>
							<div class="extract-element">
								<span class="phone-text">
								 <?php esc_html_e( 'Call Support Free: ', 'printshop'); echo wp_kses_post( $printshop_option['extract_1_value'] ); ?></span>
							</div>
						<?php } ?>

						<?php dynamic_sidebar('topbar-left'); ?>
					</div>
					
					<div class="header-right-widgets">
							<?php dynamic_sidebar('topbar-right'); ?>
							<a href="<?php echo esc_url(home_url( '/my-account' )); ?>" title="Customer Register" class="social-lock"><?php esc_html_e('My Account','printshop');?></a> |
							<a href="<?php echo esc_url(home_url( '/wishlist' )); ?>" title="Wishlist" class="social-heart"><?php esc_html_e('Wishlist','printshop');?></a> |
							<?php if ( is_user_logged_in() ) { ?>
								<a href="<?php echo wp_logout_url( home_url() ); ?>" title="Logout" class="social-user"><?php esc_html_e('Logout','printshop');?></a>
							<?php } else { ?>
								<a href="<?php echo esc_url(home_url( '/my-account' )); ?>" title="Login" class="social-user"><?php esc_html_e('Login','printshop');?></a>
							<?php } ?>

							<?php if ( is_active_sidebar( 'currency-1' ) ) : ?>
								<div class="extract-element currency">
									<?php dynamic_sidebar('currency-1'); ?>		
									
								</div>
							<?php endif; ?>
						</div>
				</div>
			</div>
		</div> <!-- /#topbar -->

		<header id="masthead" class="site-header <?php if ( printshop_get_option('header_fixed') ) echo 'fixed-on' ?>">
			<?php 
				$header_background_link = printshop_get_option('header_background_link');
				if($header_background_link != ''):
			?>
				<a href="<?php echo esc_url($header_background_link);?>" target="_blank">
					<div class="header-wrap">
						<div class="container">
							
						</div>
					</div>
				</a>
			<?php else:?>
				<div class="header-wrap">
					<div class="container">
						
					</div>
				</div>	
			<?php endif;?>
			<div class="menu-logo">
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

					<div class="header-right-wrap-top col-sm-8 col-md-7">
						<div class="netbase-menu-title">
							<h3><?php esc_html_e('Navigation','printshop'); ?></h3>
							<span id="close-netbase-menu"><i class="fa fa-times-circle"></i></span>
						</div>
						<?php wp_nav_menu( array('theme_location' => 'primary', 'container' => '', 'items_wrap' => '%3$s' ) ); ?>
					</div>
					<div class="header-right-cart-search col-xs-5 col-sm-2 col-md-1 padding-right-0">
						<span id="netbase-responsive-toggle"><i class="fa fa-bars"></i></span>
						<div class="header-cart-search">
						<?php if ( is_active_sidebar( 'cart-header' ) ){
							
							dynamic_sidebar('cart-header');
							}
							else{							
							?>
							<?php if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { ?>
							<?php $count = WC()->cart->cart_contents_count;?>
							<a class="cart-contents" href="<?php echo esc_url(wc_get_cart_url()); ?>" title="<?php esc_html_e( 'View your shopping cart', 'printshop' ); ?>"><span><?php if ( $count > 0 ) echo intval($count) ; ?></span></a>
							<?php } ?>
							<div class="widget_shopping_cart_content"></div>
							<?php } ?>
						</div>
						<div class="header-search">
							<?php echo do_shortcode( '[nbt_search]' ); ?>
						</div>
					</div>
				</div>
			</div>
		</header><!-- #masthead -->

		<div id="content" class="site-content">