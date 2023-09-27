<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly   

$core_version = 9; /* change core version when update */

add_action('init', function() {
	if( ! function_exists('get_plugin_data') ){
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}
	defined('CLOODO_BASE_API') or define('CLOODO_BASE_API','https://erp.cloodo.com/api/v3/');

	if (!function_exists('cloodo_page')) {
		function cloodo_page() {
			do_action('cloodo_page_content');
		}	
	}

	if (!function_exists('cloodo_admin_menu')){
		function cloodo_admin_menu(){
			if (!menu_page_url( 'cloodo', false )){
				add_menu_page('Cloodo', 'Cloodo', 'manage_options', 'cloodo', 'cloodo_page'); 
			}
		}
		add_action('admin_menu', 'cloodo_admin_menu');
	}

	if (!function_exists('cloodo_login')){
		function cloodo_login ($pass_word, $admin_email){
			$arr_login = [
					'method'=> 'POST',
					'body'=>[
					'email'=> $admin_email,
					'password'=> $pass_word,
					],
					'timeout'=> 30,
					'redirection'=> 5,
					'blocking'=> true,
					'headers'=> [],
					'cookie'=> [],
					];
					
			$api_login = CLOODO_BASE_API.'auth/login';
					$option = wp_remote_request( $api_login, $arr_login)['body'];
					$option = json_decode( $option,true);
					$token = (string)$option['data']['token'];
			if ($token && $token != null){
					update_option('cloodo_token',$token);
					update_option( 'cloodo_data', $option['data']['user'] );
					echo "<script>
							location.reload();
					</script>";
			}else{
					cloodo_admin_notice('Can\'t create Cloodo account. Please kindly contact Us for get help.','error');
			}
		}
	}


	if (!function_exists('cloodo_registration')){
		function cloodo_registration(){
			/* get current plugin name */
			$plugin_name = '';
			$plugin_dir = basename(dirname(__DIR__));
			foreach(get_plugins() as $key => $value){
				if (explode('/',$key)[0] == $plugin_dir){
					$plugin_name = $value['Name'];
					break;
				}
			}

			$pass_word = md5(rand(100000,999999));
			$admin_email = get_option('admin_email');
			$arrs_registration =[
				'method'=> 'POST',
				'body'=>[
					'company_name'=>get_option('blogname'),
					'email'=> $admin_email,
					'website' => get_option('siteurl'),
					'password'=> $pass_word,
					'address' => 'wordpress',
					'app_type' => 'wordpress',
					'app_name' => $plugin_name
				],
				'timeout'=> 30,
				'redirection'=> 5,
				'blocking'=> true,
				'headers'=> [],
				'cookie'=> [],
			];
		
			$response_registration =  wp_remote_request(CLOODO_BASE_API .'wordpress/login',$arrs_registration);
			if ($response_registration['response']['code'] != '200'){
				cloodo_admin_notice('Can\'t create Cloodo account. Please kindly contact Us for get help. Error code: '. $response_registration['response']['code'],'error');
			}

			if ( is_wp_error( $response_registration ) ) {
				$error_string = $response_registration->get_error_message();
				cloodo_admin_notice('<div id="message" class="error"><p>' . $error_string . '</p></div>','error');
				return;
			}
			
			$respon_json = json_decode($response_registration['body']);

			if(isset($respon_json->message) && $respon_json->message == 'App login verification email sent'){
				cloodo_admin_notice('We sent an email to verify your App login request. Please kindly check your inbox on email address '. $admin_email, 'info');
			}else if($response_registration['response']['message'] == 'Created' || $response_registration['response']['code'] == 201){
				cloodo_admin_notice('Created Cloodo Account!','success');
				cloodo_login($pass_word, $admin_email);
			}else if(isset($respon_json->error)){
				cloodo_admin_notice('Can\'t create Cloodo account. Please kindly contact Us for get help. Error message: '. esc_html($respon_json->error->message),'error');
			}else{
				cloodo_admin_notice('Can\'t create Cloodo account. Please kindly contact Us for get help.','error');
			}
		}
	}

	if (!function_exists('cloodo_admin_notice')){
		function cloodo_admin_notice($message, $type = 'info'){
			global $mg, $t;
			$mg = $message;
			$t = $type;
			add_action( 'admin_notices', function(){
				global $mg, $t;
				?>
				<div class="notice notice-<?php echo esc_attr($t) ?> is-dismissible">
						<p><?php echo esc_html($mg) ?></p>
				</div>
				<?php
			});
		}
	}

	if (!function_exists('cloodo_register_form_nonce')){
		function cloodo_register_form_nonce(){
			wp_nonce_field('cloodo_register', 'cloodo_nonce');
		}
	}

	/* create admin menu function */
	if (!function_exists('cloodo_admin_page')){
		function cloodo_admin_page($admin_menus){
			add_action('admin_menu',function() use ($admin_menus){
				$token = get_option('cloodo_token');

				foreach($admin_menus as $menu_slug => $am){
					if ($am['parent_slug'] == null && (!$am['token_require'] || ($am['token_require'] && $token))){
						add_menu_page(						
							$am['page_title'],
							$am['menu_title'],
							$am['capability'],
							$menu_slug,
							function() use ($am){
								/* enqueue style*/
								if ($am['enqueue_style']){
									foreach($am['enqueue_style'] as $handle => $src){
										wp_enqueue_style( $handle, $src );
									}
								}
								/* show target url */
								if ($am['target_url']){
									wp_enqueue_style( 'cloodo_core', plugins_url('../admin/css/cloodo_core.css', __FILE__) );
									wp_enqueue_script( 'cloodo_core', plugins_url('../admin/js/cloodo_core.js', __FILE__) );
								?>
								<div id="cloodo_admin_default">
									<span>Click the link to open Cloodo Page Admin: </span>
									<a target="_blank" id="cloodo_link" href="<?php echo esc_url($am['target_url']) ?>" frameborder="0">
										<?php echo esc_html($am['menu_title']) ?>
									</a>
								</div>	
								<?php
								}
								/* include view */
								if ($am['include']){
									include $am['include'];
								}
							},
							$am['icon_url'],
							$am['position']
						);
					}else if (!$am['token_require'] || ($am['token_require'] && $token)){
						add_submenu_page(
							$am['parent_slug'],
							$am['page_title'],
							$am['menu_title'],
							$am['capability'],
							$menu_slug,
							function() use ($am){
								/* enqueue style*/
								if ($am['enqueue_style']){
									foreach($am['enqueue_style'] as $handle => $src){
										wp_enqueue_style( $handle, $src );
									}
								}
								/* show target url */
								if ($am['target_url']){
									wp_enqueue_style( 'cloodo_core', plugins_url('../admin/css/cloodo_core.css', __FILE__) );
									wp_enqueue_script( 'cloodo_core', plugins_url('../admin/js/cloodo_core.js', __FILE__) );
									?>
									<div id="cloodo_admin_default">
										<div>
											<span>Click the link to open Cloodo Page Admin: </span>
											<a target="_blank" id="cloodo_link" href="<?php echo esc_url($am['target_url']) ?>" frameborder="0">
												<?php echo esc_html($am['menu_title']) ?>
											</a>
										</div>										
									</div>									
									<?php
								}
								/* include view */
								if ($am['include']){
									include $am['include'];
								}
							},
							$am['position']
						);
					}
				}
			});
		}
	}

	if (isset($_GET['token'])) {
		$token = sanitize_text_field($_GET['token']);
		
		/* get cloodo_data */
		$author_token = array (
			'headers'=> ['Authorization' => 'Bearer '.$token]
		);

		$response = json_decode(wp_remote_get(CLOODO_BASE_API . 'accounts', $author_token)['body']);
		if (isset($response->message) && ($response->message == 'Retrieved successfully' )){
			update_option('cloodo_token', $token);
			update_option('cloodo_data', $response->data);
			cloodo_admin_notice('Your App Login request is verified!', 'success');
		}else{
			cloodo_admin_notice('Can\'t active your login. Please kindly contact Us for get help.', 'error');
		}
	}

	if (isset($_POST['cloodo_register'])) {
		if (check_admin_referer( 'cloodo_register', 'cloodo_nonce' ) && current_user_can('manage_options') ){
			cloodo_registration();
		}
	}
}, 10 - $core_version);