<?php 

/**
 * Display HTML for login page
 */
function woopanel_display_login( $template, $template_name, $template_path ) {
  global $woocommerce, $wp_query;

	if( isset($wp_query->query_vars['pagename']) && $wp_query->query_vars['pagename'] == 'sellercenter' ) {
	  $_template = $template;

	  if ( ! $template_path ) $template_path = $woocommerce->template_url;

	  $plugin_path  = WOODASHBOARD_TEMPLATE_DIR;

	  // Look within passed path within the theme - this is priority
	  $template = locate_template(

	    array(
	      $template_path . esc_attr( $template_name ),
	      $template_name
	    )
	  );

	  // Modification: Get the template from this plugin, if it exists
	  if ( ! $template && file_exists( $plugin_path . esc_attr( $template_name ) ) )
	    $template = $plugin_path . esc_attr( $template_name );

	  // Use default template
	  if ( ! $template )
	    $template = esc_attr( $_template );
	}

  // Return what we found
  return $template;
}
add_filter( 'woocommerce_locate_template', 'woopanel_display_login', 10, 3 );


// function nb_woopanel_login_redirect( $redirect, $user ) {
//     return woopanel_dashboard_url();
// }
// add_filter( 'woocommerce_login_redirect', 'nb_woopanel_login_redirect', 10, 2 );

add_action( 'woopanel_login_form_start', 'woopanel_access_login', 20, 1);
function woopanel_access_login() {

	if( isset($_GET['success']) ) {
		echo sprintf( '<p class="alert alert-sucess">%s</p>', esc_html__('User registered. You may login now!', 'woopanel') );
	}

	if( ! isset($_POST['woopanel-login-nonce']) ) {
		return;
	}

	$nonce = wp_unslash($_POST['woopanel-login-nonce']);

	if ( ! wp_verify_nonce( $nonce, 'woopanel_login' ) ) {
		echo sprintf( '<p class="alert alert-error">%s</p>', esc_html__('Security check!', 'woopanel') );
		return;
	}

	if( ! isset($_POST['login']) ) {
		return;
	}

    $creds = array(
        'user_login'    => sanitize_text_field($_POST['login_name']),
        'user_password' => sanitize_text_field($_POST['login_password']),
        'remember'      => true
    );
 	
 	$user = wp_signon( $creds, false );
    if ( is_wp_error( $user ) ) {
        echo sprintf( '<p class="alert alert-error">%s</p>', $user->get_error_message() );
    }else {
    	wp_redirect(woopanel_dashboard_url());
    	die();
    }
}


add_action( 'woopanel_register_form_start', 'woopanel_access_register', 20, 1);
function woopanel_access_register() {

	if( ! isset($_POST['woopanel-register-nonce']) ) {
		return;
	}

	$nonce = wp_unslash($_POST['woopanel-register-nonce']);

	if ( ! wp_verify_nonce( $nonce, 'woopanel_register' ) ) {
		echo sprintf( '<p class="alert alert-error">%s</p>', esc_html__('Security check!', 'woopanel') );
		return;
	}

	if( ! isset($_POST['register']) ) {
		return;
	}

	$error = '';
	$user_email = sanitize_email( $_POST['email'] );
	$password = sanitize_text_field( $_POST['password'] );
	$confirm_password = sanitize_text_field( $_POST['confirm_password'] );

	$pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
	if( empty($password) || strlen($password) < 6 ) {
		$error = true;
		echo sprintf( '<p class="alert alert-error">%s</p>', esc_html__('Please enter your password.', 'woopanel') );
	}else {
		if( $password != $confirm_password ) {
			$error = true;
			echo sprintf( '<p class="alert alert-error">%s</p>', esc_html__('The password and confirmation password do not match.', 'woopanel') );
		}
	}

	if( ! $error ) {
		$userdata = array(
		    'user_login' => sanitize_text_field( $_POST['username'] ),
		    'user_pass'  => $password,
		    'user_email' => sanitize_email( $_POST['email'] )
		);
		 
		$user = wp_insert_user( $userdata );

		if ( ! is_wp_error( $user ) ) {
			$wp_capabilities = get_user_meta($user, 'wp_capabilities', true);
			$wp_capabilities['wpl_seller'] = 1;

			update_user_meta($user, 'wp_capabilities', $wp_capabilities);

	    	wp_redirect(woopanel_dashboard_url('?success=true'));
	    	die();
			
		}else {
			echo sprintf( '<p class="alert alert-error">%s</p>', $user->get_error_message() );
		}
	}
}