<?php
if ( ! class_exists( 'Merlin' ) ) {
	return;
}

global $solution_core_settings;

$solution_core_settings 		= trailingslashit( get_template_directory() ) . 'demo/solutions_core_settings.txt';

ini_set('max_execution_time', 300);

/**
 * Set directory locations, text strings, and other settings for Merlin WP.
 */
$wizard = new Merlin(
	// Configure Merlin with custom settings.
	$config = array(
		'directory'            => 'inc/import/merlin', // Location where the 'merlin' directory is placed.
		'merlin_url'           => 'merlin', // The wp-admin page slug where Merlin WP loads.
		'parent_slug'          => 'themes.php', // The wp-admin parent page slug for the admin menu item.
		'capability'           => 'manage_options', // The capability required for this menu to be displayed to the user.
		'child_action_btn_url' => 'https://codex.wordpress.org/child_themes', // URL for the 'child-action-link'.
		'dev_mode'             => true, // Enable development mode for testing.
		'license_step'         => false, // EDD license activation step.
		'license_required'     => false, // Require the license activation step.
		'license_help_url'     => '', // URL for the 'license-tooltip'.
		'edd_remote_api_url'   => '', // EDD_Theme_Updater_Admin remote_api_url.
		'edd_item_name'        => '', // EDD_Theme_Updater_Admin item_name.
		'edd_theme_slug'       => '', // EDD_Theme_Updater_Admin item_slug.
	),
	$strings = array(
		'admin-menu'               => esc_html__( 'Theme Setup', 'printshop' ),
		/* translators: 1: Title Tag 2: Theme Name 3: Closing Title Tag */
		'title%s%s%s%s'            => esc_html__( '%1$s%2$s Themes &lsaquo; Theme Setup: %3$s%4$s', 'printshop' ),
		'return-to-dashboard'      => esc_html__( 'Return to the dashboard', 'printshop' ),
		'ignore'                   => esc_html__( 'Disable this wizard', 'printshop' ),
		'btn-skip'                 => esc_html__( 'Skip', 'printshop' ),
		'btn-next'                 => esc_html__( 'Next', 'printshop' ),
		'btn-start'                => esc_html__( 'Start', 'printshop' ),
		'btn-no'                   => esc_html__( 'Cancel', 'printshop' ),
		'btn-plugins-install'      => esc_html__( 'Install', 'printshop' ),
		'btn-child-install'        => esc_html__( 'Install', 'printshop' ),
		'btn-content-install'      => esc_html__( 'Install', 'printshop' ),
		'btn-import'               => esc_html__( 'Import', 'printshop' ),
		'btn-license-activate'     => esc_html__( 'Activate', 'printshop' ),
		'btn-license-skip'         => esc_html__( 'Later', 'printshop' ),
		/* translators: Theme Name */
		'license-header%s'         => esc_html__( 'Activate %s', 'printshop' ),
		/* translators: Theme Name */
		'license-header-success%s' => esc_html__( '%s is Activated', 'printshop' ),
		/* translators: Theme Name */
		'license%s'                => esc_html__( 'Enter your license key to enable remote updates and theme support.', 'printshop' ),
		'license-label'            => esc_html__( 'License key', 'printshop' ),
		'license-success%s'        => esc_html__( 'The theme is already registered, so you can go to the next step!', 'printshop' ),
		'license-json-success%s'   => esc_html__( 'Your theme is activated! Remote updates and theme support are enabled.', 'printshop' ),
		'license-tooltip'          => esc_html__( 'Need help?', 'printshop' ),
		/* translators: Theme Name */
		'welcome-header%s'         => esc_html__( 'Welcome to %s', 'printshop' ),
		'welcome-header-success%s' => esc_html__( 'Hi. Welcome back', 'printshop' ),
		'welcome%s'                => esc_html__( 'This wizard will set up your theme, install plugins, and import content. It is optional & should take only a few minutes.', 'printshop' ),
		'welcome-success%s'        => esc_html__( 'You may have already run this theme setup wizard. If you would like to proceed anyway, click on the "Start" button below.', 'printshop' ),
		'child-header'             => esc_html__( 'Install Child Theme', 'printshop' ),
		'child-header-success'     => esc_html__( 'You\'re good to go!', 'printshop' ),
		'child'                    => esc_html__( 'Let\'s build & activate a child theme so you may easily make theme changes.', 'printshop' ),
		'child-success%s'          => esc_html__( 'Your child theme has already been installed and is now activated, if it wasn\'t already.', 'printshop' ),
		'child-action-link'        => esc_html__( 'Learn about child themes', 'printshop' ),
		'child-json-success%s'     => esc_html__( 'Awesome. Your child theme has already been installed and is now activated.', 'printshop' ),
		'child-json-already%s'     => esc_html__( 'Awesome. Your child theme has been created and is now activated.', 'printshop' ),
		'plugins-header'           => esc_html__( 'Install Plugins', 'printshop' ),
		'plugins-header-success'   => esc_html__( 'You\'re up to speed!', 'printshop' ),
		'plugins'                  => esc_html__( 'Let\'s install some essential WordPress plugins to get your site up to speed.', 'printshop' ),
		'plugins-success%s'        => esc_html__( 'The required WordPress plugins are all installed and up to date. Press "Next" to continue the setup wizard.', 'printshop' ),
		'plugins-action-link'      => esc_html__( 'Advanced', 'printshop' ),
		'import-header'            => esc_html__( 'Import Content', 'printshop' ),
		'import'                   => esc_html__( 'Let\'s import content to your website, to help you get familiar with the theme.', 'printshop' ),
		'import-action-link'       => esc_html__( 'Advanced', 'printshop' ),
		'ready-header'             => esc_html__( 'All done. Have fun!', 'printshop' ),
		/* translators: Theme Author */
		'ready%s'                  => esc_html__( 'Your theme has been all set up. Enjoy your new theme by %s.', 'printshop' ),
		'ready-action-link'        => esc_html__( 'Extras', 'printshop' ),
		'ready-big-button'         => esc_html__( 'View your website', 'printshop' ),
		'ready-link-1'             => sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'https://wordpress.org/support/', esc_html__( 'Explore WordPress', 'printshop' ) ),
		'ready-link-2'             => sprintf( '<a href="%1$s" target="_blank">%2$s</a>', 'http://cmsmart.net', esc_html__( 'Get Theme Support', 'printshop' ) ),
		'ready-link-3'             => sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'customize.php' ), esc_html__( 'Start Customizing', 'printshop' ) ),
	)
);

function printshop_local_import_files() {

	global $pagenow;

	if ( ( 'themes.php' === $pagenow ) && ( isset($_GET['page']) && 'merlin' === $_GET['page'] ) ) {
		update_solution_core_setting();
	}

	return array(

		array(

			'import_file_name'             		=> 'Home Page 1 - Parallax',
			'local_import_file'            		=> trailingslashit( get_template_directory() ) . 'demo/demo-01-print-parallax/content.xml',
			'local_import_child_theme'          => trailingslashit( get_template_directory() ) . 'demo/demo-01-print-parallax/print-parallax.zip',
			'local_import_megamenu_themes'      => array(
				'megamenu_themes' 				=> trailingslashit( get_template_directory() ) . 'demo/demo-01-print-parallax/megamenu_themes.txt',
				'megamenu_settings' 			=> trailingslashit( get_template_directory() ) . 'demo/demo-01-print-parallax/megamenu_settings.txt',
			),

			'local_import_solutions_core'       => trailingslashit( get_template_directory() ) . 'demo/demo-01-print-parallax/solutions_core_settings.txt',
			'local_import_widget_file'     		=> trailingslashit( get_template_directory() ) . 'demo/demo-01-print-parallax/widgets.wie',

			'local_import_siteorigin_widgets'	=> trailingslashit( get_template_directory() ) . 'demo/siteorigin_widgets_active.txt',
			
			'local_import_redux'           		=> array(
				array(
					'file_path'   				=> trailingslashit( get_template_directory() ) . 'demo/demo-01-print-parallax/printshop_options.json',
					'option_name' 				=> 'printshop_options',
				),
			),
			'import_preview_image_url'     		=> '',
			'import_notice'                		=> '',
			'preview_url'                  		=> '',
			'title_home_page'                  	=> 'home printshop parallax',
			'menu_settings'                  	=> array( 'primary' => 'main-menu' )
		),

		array(

			'import_file_name'             		=> 'Home Page 2 - Transparent Header',
			'local_import_file'            		=> trailingslashit( get_template_directory() ) . 'demo/demo-02-print-cards/content.xml',
			'local_import_child_theme'          => trailingslashit( get_template_directory() ) . 'demo/demo-02-print-cards/printcards.zip',
			'local_import_megamenu_themes'      => array(
				'megamenu_themes' 				=> trailingslashit( get_template_directory() ) . 'demo/demo-02-print-cards/megamenu_themes.txt',
				'megamenu_settings' 			=> trailingslashit( get_template_directory() ) . 'demo/demo-02-print-cards/megamenu_settings.txt',
			),

			'local_import_solutions_core'       => trailingslashit( get_template_directory() ) . 'demo/solutions_core_settings.txt',
			'local_import_widget_file'     		=> trailingslashit( get_template_directory() ) . 'demo/demo-02-print-cards/widgets.wie',

			'local_import_siteorigin_widgets'	=> trailingslashit( get_template_directory() ) . 'demo/siteorigin_widgets_active.txt',
			
			'local_import_rev_slider_file' 		=> trailingslashit( get_template_directory() ) . 'demo/demo-02-print-cards/homecards.zip',
			'local_import_redux'           		=> array(
				array(
					'file_path'   				=> trailingslashit( get_template_directory() ) . 'demo/demo-02-print-cards/printshop_options.json',
					'option_name' 				=> 'printshop_options',
				),
			),
			'import_preview_image_url'     		=> '',
			'import_notice'                		=> '',
			'preview_url'                  		=> '',
			'title_home_page'                  	=> 'Home printshop cards',
			'menu_settings'                  	=> array( 'primary' => 'main-menu' )
		),

		array(

			'import_file_name'             		=> 'Home Page 3 - Home Box',
			'local_import_file'            		=> trailingslashit( get_template_directory() ) . 'demo/demo-03-print-header-boxed/content.xml',
			'local_import_child_theme'          => trailingslashit( get_template_directory() ) . 'demo/demo-03-print-header-boxed/print-header-boxed.zip',
			'local_import_megamenu_themes'      => array(
				'megamenu_themes' 				=> trailingslashit( get_template_directory() ) . 'demo/demo-03-print-header-boxed/megamenu_themes.txt',
				'megamenu_settings' 			=> trailingslashit( get_template_directory() ) . 'demo/demo-03-print-header-boxed/megamenu_settings.txt',
			),

			'local_import_solutions_core'       => trailingslashit( get_template_directory() ) . 'demo/solutions_core_settings.txt',
			'local_import_widget_file'     		=> trailingslashit( get_template_directory() ) . 'demo/demo-03-print-header-boxed/widgets.wie',

			'local_import_siteorigin_widgets'	=> trailingslashit( get_template_directory() ) . 'demo/siteorigin_widgets_active.txt',
			
			'local_import_redux'           		=> array(
				array(
					'file_path'   				=> trailingslashit( get_template_directory() ) . 'demo/demo-03-print-header-boxed/printshop_options.json',
					'option_name' 				=> 'printshop_options',
				),
			),
			'megamenu_locations'				=> trailingslashit( get_template_directory() ) . 'demo/demo-03-print-header-boxed/megamenu_locations.txt',
			'import_preview_image_url'     		=> '',
			'import_notice'                		=> '',
			'preview_url'                  		=> '',
			'title_home_page'                  	=> 'home printshop boxed',
			'menu_settings'                  	=> array( 'primary' => 'main-menu', 'max_mega_menu_1'=>'vertical menu' )
		),
		
		array(

			'import_file_name'             		=> 'Home Page 4 - Full Box',
			'local_import_file'            		=> trailingslashit( get_template_directory() ) . 'demo/demo-04-print-boxed-2/content.xml',
			'local_import_child_theme'          => trailingslashit( get_template_directory() ) . 'demo/demo-04-print-boxed-2/print-boxed-2.zip',
			'local_import_megamenu_themes'      => array(
				'megamenu_themes' 				=> trailingslashit( get_template_directory() ) . 'demo/demo-04-print-boxed-2/megamenu_themes.txt',
				'megamenu_settings' 			=> trailingslashit( get_template_directory() ) . 'demo/demo-04-print-boxed-2/megamenu_settings.txt',
			),

			'local_import_solutions_core'       => trailingslashit( get_template_directory() ) . 'demo/solutions_core_settings.txt',
			'local_import_widget_file'     		=> trailingslashit( get_template_directory() ) . 'demo/demo-04-print-boxed-2/widgets.wie',

			'local_import_siteorigin_widgets'	=> trailingslashit( get_template_directory() ) . 'demo/siteorigin_widgets_active.txt',
			
			'local_import_rev_slider_file' 		=> trailingslashit( get_template_directory() ) . 'demo/demo-04-print-boxed-2/home_boxed.zip',
			'local_import_redux'           		=> array(
				array(
					'file_path'   				=> trailingslashit( get_template_directory() ) . 'demo/demo-04-print-boxed-2/printshop_options.json',
					'option_name' 				=> 'printshop_options',
				),
			),
			'megamenu_locations'				=> trailingslashit( get_template_directory() ) . 'demo/demo-04-print-boxed-2/megamenu_locations.txt',
			'import_preview_image_url'     		=> '',
			'import_notice'                		=> '',
			'preview_url'                  		=> '',
			'title_home_page'                  	=> 'home printshop boxed 10',
			'menu_settings'                  	=> array( 'primary' => 'main-menu', 'max_mega_menu_1'=>'vertical menu' )
		),

		array(

			'import_file_name'             		=> 'Home Page 5 - Full Screen Slider',
			'local_import_file'            		=> trailingslashit( get_template_directory() ) . 'demo/demo-05-print-slider/content.xml',
			'local_import_child_theme'          => trailingslashit( get_template_directory() ) . 'demo/demo-05-print-slider/print-slider.zip',
			'local_import_megamenu_themes'      => array(
				'megamenu_themes' 				=> trailingslashit( get_template_directory() ) . 'demo/demo-05-print-slider/megamenu_themes.txt',
				'megamenu_settings' 			=> trailingslashit( get_template_directory() ) . 'demo/demo-05-print-slider/megamenu_settings.txt',
			),

			'local_import_solutions_core'       => trailingslashit( get_template_directory() ) . 'demo/solutions_core_settings.txt',
			'local_import_widget_file'     		=> trailingslashit( get_template_directory() ) . 'demo/demo-05-print-slider/widgets.wie',

			'local_import_siteorigin_widgets'	=> trailingslashit( get_template_directory() ) . 'demo/siteorigin_widgets_active.txt',
			
			'local_import_rev_slider_file' 		=> trailingslashit( get_template_directory() ) . 'demo/demo-05-print-slider/homslider.zip',
			'local_import_redux'           		=> array(
				array(
					'file_path'   				=> trailingslashit( get_template_directory() ) . 'demo/demo-05-print-slider/printshop_options.json',
					'option_name' 				=> 'printshop_options',
				),
			),
			'import_preview_image_url'     		=> '',
			'import_notice'                		=> '',
			'preview_url'                  		=> '',
			'title_home_page'                  	=> 'home printshop slider',
			'menu_settings'                  	=> array( 'primary' => 'main-menu' )
		),

	);


}
add_filter( 'merlin_import_files', 'printshop_local_import_files' );


/**
 * Execute custom code after the whole import has finished.
 */
function printshop_merlin_after_import_setup( $selected_import_index ) {

	$total_import_array = printshop_local_import_files();
	$current_import_array = $total_import_array[$selected_import_index];

	update_hello_world_post();

	// setting_default();
	

	// Add megamenu location
	if( !empty( $current_import_array['megamenu_locations'] ) ) {
		update_megamenu_locations( $current_import_array['megamenu_locations'] );
	}

	// Assign menus to their locations.
	update_menu_locations($current_import_array['menu_settings']);

	// Assign front page
	update_front_page($current_import_array);

	// Update siteorigin widgets settings 
	if( !empty( $current_import_array['local_import_siteorigin_widgets'] ) ) {
		update_siteorigin_widgets_options( $current_import_array['local_import_siteorigin_widgets'] );
	}

	// Update megamenu options
	if( !empty( $current_import_array['local_import_megamenu_themes'] ) ) {
		update_megamenu_options($current_import_array);
	}

	// Update megamenu metadata
	if( class_exists( 'Mega_Menu' ) ) {
		update_megamenu_metadata();
	}

	// Update solution core settings 
	if( !empty( $current_import_array['local_import_solutions_core'] ) ) {
		update_solution_core_options( $current_import_array['local_import_solutions_core'] );
	}

	update_page_options();

	update_default_woocommerce_options();

	//setup ultimate font icon
	if( !empty( $current_import_array['local_import_font_icon'] ) ) {
		setup_font_icon( $current_import_array['local_import_font_icon'] );
	}

	//setup child theme

	if( !empty( $current_import_array['local_import_child_theme'] ) ) {
		setup_child_theme( $current_import_array['local_import_child_theme'] );
	}

	//update price matrix and color swatch posts
	if( !empty( $current_import_array['product_variation'] ) ) {
		update_variation_posts( $current_import_array['product_variation'] );
	}
	

}
add_action( 'merlin_after_all_import', 'printshop_merlin_after_import_setup' );


/**
 * Get data from a file with WP_Filesystem
 *
 * @param $file
 *
 * @return bool
 */
function printshop_get_file_contents( $file ) {
	WP_Filesystem();
	global $wp_filesystem;
	return $wp_filesystem->get_contents( $file );
}

function update_hello_world_post() {

	$hello_world = get_page_by_title( 'Hello World!', OBJECT, 'post' );
	
	if ( ! empty( $hello_world ) ) {
		$hello_world->post_status = 'draft';
		wp_update_post( $hello_world );

		echo 'Update Hello World post successful <br/>';
	}
}

function setting_default() {

	global $wpdb;
		$arr_setting_default = array(
			"permalink_structure" => "/%postname%/",
			"woocommerce_currency" => "USD", 
			"woocommerce_store_address" => "Số 201 Bạch Mai - Hai Bà Trưng - Hà Nội", 
			"woocommerce_store_city" => "Hà Nội",
			"woocommerce_default_country" => "VN", 
			"woocommerce_store_postcode" => "100000", 
			"woocommerce_all_except_countries" => "a:0:{}", 
			"woocommerce_specific_allowed_countries" => "a:0:{}", 
			"woocommerce_specific_ship_to_countries" => "a:0:{}", 
			"woocommerce_bacs_settings" => "a:11:{s:7:\"enabled\";s:3:\"yes\";s:5:\"title\";s:20:\"Direct bank transfer\";s:11:\"description\";s:176:\"Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.\";s:12:\"instructions\";s:0:\"\";s:15:\"account_details\";s:0:\"\";s:12:\"account_name\";s:0:\"\";s:14:\"account_number\";s:0:\"\";s:9:\"sort_code\";s:0:\"\";s:9:\"bank_name\";s:0:\"\";s:4:\"iban\";s:0:\"\";s:3:\"bic\";s:0:\"\";}",
			"woocommerce_cheque_settings" => "a:4:{s:7:\"enabled\";s:3:\"yes\";s:5:\"title\";s:14:\"Check payments\";s:11:\"description\";s:98:\"Please send a check to Store Name, Store Street, Store Town, Store State / County, Store Postcode.\";s:12:\"instructions\";s:0:\"\";}",
			"woocommerce_cod_settings" => "a:6:{s:7:\"enabled\";s:3:\"yes\";s:5:\"title\";s:16:\"Cash on delivery\";s:11:\"description\";s:28:\"Pay with cash upon delivery.\";s:12:\"instructions\";s:28:\"Pay with cash upon delivery.\";s:18:\"enable_for_methods\";a:0:{}s:18:\"enable_for_virtual\";s:3:\"yes\";}",
			"woocommerce_paypal_settings" => "a:23:{s:7:\"enabled\";s:3:\"yes\";s:5:\"title\";s:6:\"PayPal\";s:11:\"description\";s:85:\"Pay via PayPal; you can pay with your credit card if you don\'t have a PayPal account.\";s:5:\"email\";s:25:\"thanhminh9108@yopmail.com\";s:8:\"advanced\";s:0:\"\";s:8:\"testmode\";s:2:\"no\";s:5:\"debug\";s:2:\"no\";s:16:\"ipn_notification\";s:3:\"yes\";s:14:\"receiver_email\";s:25:\"thanhminh9108@yopmail.com\";s:14:\"identity_token\";s:0:\"\";s:14:\"invoice_prefix\";s:3:\"WC-\";s:13:\"send_shipping\";s:3:\"yes\";s:16:\"address_override\";s:2:\"no\";s:13:\"paymentaction\";s:4:\"sale\";s:10:\"page_style\";s:0:\"\";s:9:\"image_url\";s:0:\"\";s:11:\"api_details\";s:0:\"\";s:12:\"api_username\";s:0:\"\";s:12:\"api_password\";s:0:\"\";s:13:\"api_signature\";s:0:\"\";s:20:\"sandbox_api_username\";s:0:\"\";s:20:\"sandbox_api_password\";s:0:\"\";s:21:\"sandbox_api_signature\";s:0:\"\";}",
			"woocommerce_gateway_order" => "a:4:{s:4:\"bacs\";i:0;s:6:\"cheque\";i:1;s:3:\"cod\";i:2;s:6:\"paypal\";i:3;}",
		);
		foreach ($arr_setting_default as $key => $value) {
			$kq2 = $wpdb->replace($wpdb->prefix.'options', 
				array( 
					'option_name' => $key,
					'option_value' => $value, 
					'autoload' => 'yes'
				), 
				array( 
					'%s',
					'%s', 
					'%s' 
				) 
			);
		}
}

function update_menu_locations( $options = array() ) {

	$menu_location_array 	= array();

	foreach($options as $menu_location => $menu_name) {
		
		$current_menu = get_term_by( 'name', $menu_name, 'nav_menu' );
		$menu_location_array[$menu_location] = $current_menu->term_id;

	}
	set_theme_mod( 'nav_menu_locations', $menu_location_array);

	echo 'Assign menus successful <br/>';
}

function update_front_page( $options = array() ) {

	$front_page_id = get_page_by_title( $options['title_home_page'] );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front_page_id->ID );

	echo 'Assign front page successful <br/>';
}

function update_megamenu_locations($megamenu_locations_file) {

	global $wpdb;

	//update megamenu_locations option
	$megamenu_locations = printshop_get_file_contents($megamenu_locations_file);
	
	$affected_row = $wpdb->replace($wpdb->prefix.'options', 
		array( 
			'option_name' 	=> 'megamenu_locations',
			'option_value' 	=> $megamenu_locations, 
			'autoload' 		=> 'yes'
		), 
		array( 
			'%s',
			'%s',
			'%s' 
		) 
	);

	
	echo 'Update megamenu locations option successful <br/>';
}

function update_megamenu_options($options = array()) {

	global $wpdb;
			
		$megamenu_options = $options['local_import_megamenu_themes'];

		//update megamenu_themes option
		$megamenu_themes = printshop_get_file_contents($megamenu_options['megamenu_themes']);
		
		$affected_row = $wpdb->replace($wpdb->prefix.'options', 
			array( 
				'option_name' => 'megamenu_themes',
				'option_value' => $megamenu_themes, 
				'autoload' => 'yes'
			), 
			array( 
				'%s',
				'%s',
				'%s' 
			) 
		);

		//update megamenu_settings option
		$megamenu_settings = printshop_get_file_contents($megamenu_options['megamenu_settings']);

		$affected_row = $wpdb->replace($wpdb->prefix.'options', 
			array( 
				'option_name' => 'megamenu_settings',
				'option_value' => $megamenu_settings, 
				'autoload' => 'yes'
			), 
			array( 
				'%s',
				'%s', 
				'%s' 
			) 
		);

		do_action( "megamenu_after_save_settings" );
		do_action( "megamenu_delete_cache" );

		echo 'Update megamenu options successful <br/>';
}

function update_megamenu_metadata(){

	$sidebar_widgets    = get_option( 'sidebars_widgets' );

	if( isset( $sidebar_widgets[ 'mega-menu' ] ) ) {

		$mm_sidebar_widgets = $sidebar_widgets[ 'mega-menu' ];


		$mm_imported_widgets  	= get_imported_megamenu_data( $mm_sidebar_widgets );
		$new_mm_widget_id       = rebuild_mmm_widget_id( $mm_sidebar_widgets , $mm_imported_widgets );

		//update megamenu grid type post meta
		update_megamenu_grid_type_post_meta( $new_mm_widget_id );
	}

	echo 'Update megamenu metadata successful <br/>';
}

/**
 * Get all imported megamenu data
 * @param  array $mm_imported_widgets
 */
function get_imported_megamenu_data( $mm_sidebar_widgets ) {

	global $wpdb;

	$mm_imported_widgets = array();

	$megamenu_meta = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key = '_megamenu'" );

	foreach ( $megamenu_meta as $meta ) {

		$meta_value = unserialize( $meta->meta_value );

		if( isset( $meta_value['type'] ) && $meta_value['type'] == 'grid' ) {

			foreach( $meta_value['grid'] as $i => $grids ) {                

				foreach( $grids['columns'] as $j => $column ) {

					foreach ( $column[ 'items' ] as $k => $item ) {                         

						// $meta_value['grid'][$i]['columns'][$j]['items'][$k]['id'] = $new_widget_id;

						$mm_imported_widgets[] = $item[ 'id' ];
					}
				}
			}
		}
	}

	return $mm_imported_widgets;
}

/**
 * Rebuild max megamenu metadata after imported
 * @param  [array] $mm_sidebar_widgets     
 * @param  [array] $mm_imported_widgets 
 * @return [array]                        
 */
function rebuild_mmm_widget_id( $mm_sidebar_widgets, $mm_imported_widgets) {

	$mm_sidebar_widgets_by_key      = array();
	$mm_imported_widgets_by_key    = array();
	$rebuild_array                  = array();

	foreach($mm_sidebar_widgets as $value) {
		$exploded_widgets = explode('-', $value);
		$mm_sidebar_widgets_by_key[$exploded_widgets[0]][] = $exploded_widgets[1];
	}

	foreach($mm_imported_widgets as $value) {
		$exploded_widgets = explode('-', $value);
		$mm_imported_widgets_by_key[$exploded_widgets[0]][] = $exploded_widgets[1];
	}

	foreach($mm_sidebar_widgets_by_key as $key => $a) {
		rsort($a);
		$mm_sidebar_widgets_by_key[$key] = $a;
	}

	foreach($mm_imported_widgets_by_key as $key => $a) {
		rsort($a);
		$mm_imported_widgets_by_key[$key] = $a;
	}

	foreach ($mm_imported_widgets_by_key as $key => $values) {

		foreach($values as $index => $value ) {
			$widget_key = $key . '-' . $value;
			$new_value = $key . '-' . $mm_sidebar_widgets_by_key[$key][$index];
			$rebuild_array[$widget_key] = $new_value;        
		}
	}

	return $rebuild_array;
}

/**
 * Update incorrect wiget name in megamenu post metadata
 */
function update_megamenu_grid_type_post_meta( $new_mm_widget_id ) {

	global $wpdb;

	$megamenu_meta = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_key = '_megamenu'" );

	foreach ( $megamenu_meta as $meta ) {

		$meta_value = unserialize( $meta->meta_value );

		if( isset( $meta_value['type'] ) && $meta_value['type'] == 'grid' ) {

			foreach( $meta_value['grid'] as $i => $grids ) {                

				foreach( $grids['columns'] as $j => $column ) {

					foreach ( $column[ 'items' ] as $k => $item ) {                         

						$meta_value['grid'][$i]['columns'][$j]['items'][$k]['id'] = isset($new_mm_widget_id[ $item[ 'id' ] ]) ? $new_mm_widget_id[ $item[ 'id' ] ] : $item[ 'id' ];
					}
				}
			}

			// update grid post meta data
			update_post_meta( $meta->post_id, '_megamenu', $meta_value);
		}
	}
}

function update_solution_core_options( $solution_core_file ) {

	global $wpdb;

	$solution_settings = json_decode(printshop_get_file_contents($solution_core_file), true);

	if(isset($solution_settings['settings'])) {

		foreach($solution_settings['settings'] as $module_name => $module_setting) {

		$affected_row = $wpdb->replace($wpdb->prefix.'options', 
				array( 
					'option_name' 	=> $module_name. '_settings',
					'option_value' 	=> maybe_serialize($module_setting), 
					'autoload' 		=> 'yes'
				), 
				array( 
					'%s',
					'%s', 
					'%s' 
				)
			);
		}
	}

	echo 'Update Solution Core successful <br/>';
}

function update_siteorigin_widgets_options( $siteorigin_widgets_file ) {

	global $wpdb;

	$affected_row = $wpdb->replace($wpdb->prefix.'options', 
				array( 
					'option_name' => 'siteorigin_widgets_active',
					'option_value' => printshop_get_file_contents($siteorigin_widgets_file),
					'autoload' => 'yes'
				), 
				array( 
					'%s',
					'%s',
					'%s' 
				) 
			);

	echo 'update siteorigin_widgets_active option ok <br/>';
}

function update_page_options() {

	global $wpdb;

	$arr_page_setup = array(
		'woocommerce_cart_page_id' 		=> 'Cart', 
		'woocommerce_checkout_page_id' 	=> 'Checkout', 
		'woocommerce_myaccount_page_id' => 'My account', 
		'woocommerce_terms_page_id' 	=> 'Terms of Service', 
		'woocommerce_shop_page_id' 		=> 'Shop', 
		'yith_wcwl_wishlist_page_id' 	=> 'Wishlist'
	);

	foreach ($arr_page_setup as $key => $value) {
		$page = get_page_by_title(trim($value));
		if($page) {
			$kq = $wpdb->replace($wpdb->prefix.'options', 
				array( 
					'option_name' => $key,
					'option_value' => $page->ID, 
					'autoload' => 'yes'
				), 
				array( 
					'%s',
					'%s', 
					'%s' 
				) 
			);
		}
	}

	echo 'Update Page Options successful <br/>';
}

function update_default_woocommerce_options() {
	
	global $wpdb;
	$arr_setting_default = array(
		"permalink_structure" => "/%postname%/",
		"woocommerce_currency" => "USD",
	);
	foreach ($arr_setting_default as $key => $value) {
		$kq2 = $wpdb->replace($wpdb->prefix.'options', 
			array( 
				'option_name' => $key,
				'option_value' => $value, 
				'autoload' => 'yes'
			), 
			array( 
				'%s',
				'%s', 
				'%s' 
			) 
		);
	}

	echo 'Update Default Woocommerce Options successful <br/>';
}


//update price matrix and color swatch post
function update_variation_posts($options = array()) {
	
	create_all_variation( $options['price_matrix_post_slug'] );
	create_all_variation( $options['color_swatches_post_slug'], 'cs' );

	echo 'Update price matrix and color swatch posts successful <br/>';
}

function create_all_variation($slug = '', $type = 'pm') {

	if($slug!='') {

		$post_id = get_page_by_path( $slug, OBJECT, 'product' )->ID;
		$post_id = intval( $post_id );
		
		if ( $post_id ) {
			
			$product    = wc_get_product( $post_id );
			$attributes = wc_list_pluck( array_filter( $product->get_attributes(), 'wc_attributes_array_filter_variation' ), 'get_slugs' );

			if ( ! empty( $attributes ) ) {

				$existing_variations = array_map( 'wc_get_product', $product->get_children() );
				$existing_attributes = array();

				foreach ( $existing_variations as $existing_variation ) {
					$existing_attributes[] = $existing_variation->get_attributes();
				}

				$added               = 0;
				$possible_attributes = array_reverse( wc_array_cartesian( $attributes ) );

				foreach ( $possible_attributes as $possible_attribute ) {

					if ( in_array( $possible_attribute, $existing_attributes ) ) {
						continue;
					}

					$variation = new WC_Product_Variation();
					$variation->set_parent_id( $post_id );
					$variation->set_attributes( $possible_attribute );
					$price = random_int(1, 100);
					$variation->set_price($price);
					$variation->set_regular_price($price);

					if($type=='cs') {
						$variation->set_stock_quantity(random_int(1, 100));
						$variation->set_stock_status();
					}

					do_action( 'product_variation_linked', $variation->save() );

					if ( ( $added ++ ) > 49 ) {
						break;
					}
				}
			}
			$data_store = $product->get_data_store();
			$data_store->sort_all_product_variations( $product->get_id() );
		}
	}
}

function setup_font_icon( $options = array() ) {

	unzip_printshop_font_package( $options['font_icon_file'] );
	update_smile_fonts_option( $options['font_icon_settings'] );
}

function update_smile_fonts_option( $font_icon_settings ) {

	global $wpdb;

	$affected_row = $wpdb->replace($wpdb->prefix.'options', 
				array( 
					'option_name' => 'smile_fonts',
					'option_value' => printshop_get_file_contents($font_icon_settings),
					'autoload' => 'yes'
				), 
				array( 
					'%s',
					'%s',
					'%s' 
				) 
			);

	echo 'update smile_fonts option ok <br/>';
}

function unzip_printshop_font_package( $font_icon_file = array() ) {

	WP_Filesystem();
	$unzipfile ='';
	$destination 		= wp_upload_dir();
	$base_dir			= $destination['basedir'];
	$smile_fonts_path	= $base_dir . '/smile_fonts';
	
	foreach($font_icon_file as $font_icon){		
		$unzipfile 			= unzip_file( $font_icon, $smile_fonts_path);
		if ( is_wp_error( $unzipfile ) ) {
			echo 'There was an error unzipping the icon file '.$font_icon.' <br/>';
		} 
		else {
			echo 'Successfully unzipped the icon file '.$font_icon.'<br/>';      
		}
	}
}

function update_solution_core_setting() {

	global $wpdb;

	global $solution_core_settings;

	if(! get_option('solutions_core_settings')) {

		// enable netbase solution module before import
		$solution_settings = json_decode(printshop_get_file_contents($solution_core_settings), true);
	
		if(isset($solution_settings['enable'])) {
	
			$affected_row = $wpdb->replace($wpdb->prefix.'options', 
				array( 
					'option_name' 	=> 'solutions_core_settings',
					'option_value' 	=> maybe_serialize($solution_settings['enable']), 
					'autoload' 		=> 'yes'
				), 
				array( 
					'%s',
					'%s', 
					'%s' 
				)
			);
		}
	}
}

function setup_child_theme($child_theme_file) {	

	unzip_child_theme_package( $child_theme_file );
	update_child_theme_option($child_theme_file);
}

function unzip_child_theme_package( $child_theme_file ) {

	WP_Filesystem();

	$theme_root 		= trailingslashit( get_theme_root() );
	$unzipfile 			= unzip_file( $child_theme_file, $theme_root);

	if ( is_wp_error( $unzipfile ) ) {
		echo 'There was an error unzipping the child theme file. <br/>';
	} 
	else {
		echo 'Successfully unzipped the child theme file! <br/>';       
	}
}

function update_child_theme_option( $child_theme_file ) {

	$parent_theme_name 		= get_option('template');
	$child_theme_name 		= pathinfo($child_theme_file, PATHINFO_FILENAME);

	//active child theme
	update_option('stylesheet', $child_theme_name);

	//update child theme mod
	$parent_theme_mod = get_option('theme_mods_' . $parent_theme_name);

	update_option('theme_mods_' . $child_theme_name, $parent_theme_mod);

	echo 'Activate child theme successful';

}

/**
 * Add your widget area to unset the default widgets from.
 * If your theme's first widget area is "sidebar-1", you don't need this.
 *
 * @see https://stackoverflow.com/questions/11757461/how-to-populate-widgets-on-sidebar-on-theme-activation
 *
 * @param  array $widget_areas Arguments for the sidebars_widgets widget areas.
 * @return array of arguments to update the sidebars_widgets option.
 */
function prefix_merlin_unset_default_widgets_args( $widget_areas ) {

	$widget_areas = array(
		'default-sidebar' => array(),
	);
	return $widget_areas;
}
add_filter( 'merlin_unset_default_widgets_args', 'prefix_merlin_unset_default_widgets_args' );

/**
 * Remove the child theme step.
 * @since   0.1.0
 *
 * @return  $array  The merlin import steps.
 */
add_filter( 'printshop_merlin_steps', function( $steps ) {
	unset( $steps['child'] );
	return $steps;
});