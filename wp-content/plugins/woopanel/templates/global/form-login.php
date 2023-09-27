<?php
/**
 * Login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @package 	WooCommerce/Templates
 
 * @version     3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( is_user_logged_in() ) {
	return;
}

$imageID = WooPanel_Admin_Options::get_option('bg_images');
$image_src = wp_get_attachment_image_src($imageID, 'full');
if( isset($image_src[0]) ) {
	$image_src = $image_src[0];
}
$colorID =  WooPanel_Admin_Options::get_option('color_pages');

if( empty($imageID) ) {
	$image_src = esc_url( WooDashboard()->plugin_url() .'assets/images/default-login.jpg');
}
?> 

<section class="confix-form-woopanel" style="background:<?php echo esc_attr($colorID);?>">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 hidden-xs pdd-0">
				<div class="img-holder">
					<div class="bg"> 
						<picture>
							<source media="(min-width: 480px)" srcset="<?php echo esc_url($image_src);?>">
							<img src="<?php echo esc_url($image_src);?>" alt="">
						</picture>
					</div> 
				</div>
			</div>
			<div class="col-xl-7 col-lg-7 col-md-7 col-sm-12 col-xs-12 pdd-0">
				<div class="box-bg-form">
					<div class="bg-xs">
						<picture>
							<source media="(max-width: 767px)" srcset="<?php echo esc_url($image_src);?>">
							<img src="<?php echo esc_url($image_src);?>" alt="">
						</picture>
					</div>
					<form class="woocommerce-form woocommerce-form-login login" method="post" <?php echo isset( $hidden ) ? 'style="display:none;"' : ''; ?>>

						<h3>
							<?php esc_html_e('Get more things done with Loggin platform.', 'woopanel' );?>
								
						</h3>

						<p class="desc-woopanel">
							<?php esc_html_e('Access to the most powerfull tool in the entire design and web industry.', 'woopanel' );?> 
						</p>

						<div class="page-links">
							<a href="#login"<?php echo empty($register) ? ' class="active"' : '';?>><?php esc_html_e('Login', 'woopanel' );?></a>
							<a href="#register"<?php echo ! empty($register) ? ' class="active"' : '';?>><?php esc_html_e('Register', 'woopanel' );?></a>
						</div>

						<div class="page-tab-content">
							<div id="login" class="page-tab-panel<?php echo empty($register) ? ' active' : '';?>">
								<?php do_action( 'woopanel_login_form_start' ); ?>

								<p class="form-row form-row-first">
									<input type="text" class="input-text" placeholder="<?php esc_html_e('E-mail Address', 'woopanel' );?>" name="login_name" id="username" autocomplete="username" />
								</p>

								<p class="form-row form-row-last">
									<input class="input-text" type="password" placeholder="<?php esc_html_e('Password', 'woopanel' );?>" name="login_password" id="password" autocomplete="current-password" />
								</p>

								<div class="clear"></div>
								<?php do_action( 'woocommerce_login_form' ); ?>
								<p class="form-row confix-button">
									<label class="woocommerce-form__label hidden woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
										<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woopanel' ); ?></span>
									</label>
									<?php wp_nonce_field( 'woopanel_login', 'woopanel-login-nonce' ); ?>
									<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ) ?>" />
									<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e( 'Login', 'woopanel' ); ?>"><?php esc_html_e( 'Login', 'woopanel' ); ?>
									</button>
									<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woopanel' ); ?></a>
								</p>
								<?php do_action( 'woopanel_login_form_end' ); ?>
							</div>

							<div id="register" class="page-tab-panel<?php echo ! empty($register) ? ' active' : '';?>">
								<?php do_action( 'woopanel_register_form_start' ); ?>

								<p class="form-row form-row-first">
									<input type="text" class="input-text" placeholder="<?php esc_html_e('User Name', 'woopanel' );?>" name="username" id="username" autocomplete="username" value="<?php echo esc_attr($username);?>" />
								</p>

								<p class="form-row form-row-first">
									<input type="email" class="input-text" placeholder="<?php esc_html_e('E-mail Address', 'woopanel' );?>" name="email" id="email" autocomplete="email" value="<?php echo esc_attr($email);?>" />
								</p>

								<p class="form-row form-row-last">
									<input class="input-text" type="password" placeholder="<?php esc_html_e('Password', 'woopanel' );?>" name="password" id="password" autocomplete="current-password" value="<?php echo esc_attr($password);?>" />
								</p>

								<p class="form-row form-row-last">
									<input class="input-text" type="password" placeholder="<?php esc_html_e('Confirm Password', 'woopanel' );?>" name="confirm_password" id="confirm_password" autocomplete="current-password" />
								</p>

								<p class="form-row confix-button">
									<?php wp_nonce_field( 'woopanel_register', 'woopanel-register-nonce' ); ?>
									<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ) ?>" />
									<button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="register" value="<?php esc_attr_e( 'Register', 'woopanel' ); ?>"><?php esc_html_e( 'Register', 'woopanel' ); ?>
									</button>
								</p>

								<?php do_action( 'woopanel_register_form_end' ); ?>

							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section> 