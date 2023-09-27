<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Printing Press
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

<meta http-equiv="Content-Type" content="<?php echo esc_attr(get_bloginfo('html_type')); ?>; charset=<?php echo esc_attr(get_bloginfo('charset')); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.2, user-scalable=yes" />

<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<?php
	if ( function_exists( 'wp_body_open' ) )
	{
		wp_body_open();
	}else{
		do_action('wp_body_open');
	}
?>
<?php if(get_theme_mod('printing_press_preloader_hide','')){ ?>
	<div class="loader">
		<div class="preloader">
			<div class="diamond">
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div>
	</div>
<?php } ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'printing-press' ); ?></a>

<header id="site-navigation" class="header py-2">
	<div class="container">
		<div class="topheader px-4">
			<div class="row">
				<div class="col-lg-7 col-md-12 text-lg-left  text-center align-self-center ">
					<?php if ( get_theme_mod('printing_press_header_advertisement_text') ) : ?>
						<p class="mb-0 adver-text py-2"><?php echo esc_html( get_theme_mod('printing_press_header_advertisement_text' ) ); ?></p>
					<?php endif; ?>
					<?php if ( get_theme_mod('printing_press_header_button_url') || get_theme_mod('printing_press_header_button_text') ) : ?>
						<div class="shop-btn  py-2">
							<a class="border-left pl-1" href="<?php echo esc_html( get_theme_mod('printing_press_header_button_url' ) ); ?>"><?php echo esc_html( get_theme_mod('printing_press_header_button_text' ) ); ?></a>
						</div>
					<?php endif; ?>	
				</div>
				<div class="col-lg-5 col-md-12 align-self-center text-center">
					<div class="row">
						<?php if ( get_theme_mod('printing_press_header_phone_number') ) : ?>
							<div class="col-lg-5 col-md-6 col-12 align-self-center py-2">
								<p class="mb-0 phone-no"><?php esc_html_e('Call Us: ','printing-press'); ?><?php echo esc_html( get_theme_mod('printing_press_header_phone_number' ) ); ?></p>
							</div>
						<?php endif; ?>
						<?php if ( get_theme_mod('printing_press_header_email') ) : ?>
							<div class="col-lg-7 col-md-6 col-12 align-self-center border-side py-2">
								<p class="mb-0 email"><?php esc_html_e('Mail Us: ','printing-press'); ?><?php echo esc_html( get_theme_mod('printing_press_header_email' ) ); ?></p>
							</div>
						<?php endif; ?>	
					</div>
				</div>
			</div>
		</div>
		<div class="bottomheader px-4 py-2 <?php if( get_theme_mod( 'printing_press_sticky_header','on') != '') { ?>sticky-header<?php } else { ?>close-sticky <?php } ?>">
			<div class="row">
				<div class="col-lg-3 col-md-4 align-self-center">
					<div class="logo text-center text-md-left mb-3 mb-md-0">
						<div class="logo-image">
							<?php the_custom_logo(); ?>
						</div>
						<div class="logo-content">
							<?php
								if ( get_theme_mod('printing_press_display_header_title', true) == true ) :
									echo '<a href="' . esc_url(home_url('/')) . '" title="' . esc_attr(get_bloginfo('name')) . '">';
									echo esc_attr(get_bloginfo('name'));
									echo '</a>';
								endif;
								if ( get_theme_mod('printing_press_display_header_text', true) == true ) :
									echo '<span>'. esc_attr(get_bloginfo('description')) . '</span>';
								endif;
							?>
						</div>
					</div>
				</div>
				<div class="col-lg-7 col-md-7 text-center align-self-center">
					<button class="menu-toggle my-2 py-2 px-3" aria-controls="top-menu" aria-expanded="false" type="button">
						<span aria-hidden="true"><?php esc_html_e( 'Menu', 'printing-press' ); ?></span>
					</button>
					<nav id="main-menu" class="close-panal">
						<?php
							wp_nav_menu( array(
								'theme_location' => 'main-menu',
								'container' => 'false'
							));
						?>
						<button class="close-menu my-2 p-2" type="button">
							<span aria-hidden="true"><i class="fa fa-times"></i></span>
						</button>
					</nav>
				</div>
				<div class="col-lg-2 col-md-1 align-self-center text-center">
					<?php if ( get_theme_mod('printing_press_cart_box_enable', 'on' ) == true ) : ?>
						<?php if ( class_exists( 'woocommerce' ) ) {?>
							<span class="header-cart">
								<a class="cart-customlocation" href="<?php if(function_exists('wc_get_cart_url')){ echo esc_url(wc_get_cart_url()); } ?>" title="<?php esc_attr_e( 'View Shopping Cart','printing-press' ); ?>"><i class="fas fa-shopping-bag mr-1"></i></a>
							</span>
						<?php }?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>	
</header>