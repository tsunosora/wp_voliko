<?php
//about theme info
add_action( 'admin_menu', 'printing_press_gettingstarted' );
function printing_press_gettingstarted() {
	add_theme_page( esc_html__('Printing Press', 'printing-press'), esc_html__('Printing Press', 'printing-press'), 'edit_theme_options', 'printing_press_about', 'printing_press_mostrar_guide');
}

// Add a Custom CSS file to WP Admin Area
function printing_press_admin_theme_style() {
	wp_enqueue_style('printing-press-custom-admin-style', esc_url(get_template_directory_uri()) . '/includes/getstart/getstart.css');
	wp_enqueue_script('printing-press-tabs', esc_url(get_template_directory_uri()) . '/includes/getstart/js/tab.js');
	wp_enqueue_style( 'font-awesome-css', get_template_directory_uri().'/assets/css/fontawesome-all.css' );
}
add_action('admin_enqueue_scripts', 'printing_press_admin_theme_style');

// Changelog
if ( ! defined( 'PRINTING_PRESS_CHANGELOG_URL' ) ) {
    define( 'PRINTING_PRESS_CHANGELOG_URL', get_template_directory() . '/readme.txt' );
}

function printing_press_changelog_screen() {	
	global $wp_filesystem;
	$changelog_file = apply_filters( 'printing_press_changelog_file', PRINTING_PRESS_CHANGELOG_URL );
	if ( $changelog_file && is_readable( $changelog_file ) ) {
		WP_Filesystem();
		$changelog = $wp_filesystem->get_contents( $changelog_file );
		$changelog_list = printing_press_parse_changelog( $changelog );
		echo wp_kses_post( $changelog_list );
	}
}

function printing_press_parse_changelog( $content ) {
	$content = explode ( '== ', $content );
	$changelog_isolated = '';
	foreach ( $content as $key => $value ) {
		if (strpos( $value, 'Changelog ==') === 0) {
	    	$changelog_isolated = str_replace( 'Changelog ==', '', $value );
	    }
	}
	$changelog_array = explode( '= ', $changelog_isolated );
	unset( $changelog_array[0] );
	$changelog = '<div class="changelog">';
	foreach ( $changelog_array as $value) {
		$value = preg_replace( '/\n+/', '</span><span>', $value );
		$value = '<div class="block"><span class="heading">= ' . $value . '</span></div><hr>';
		$changelog .= str_replace( '<span></span>', '', $value );
	}
	$changelog .= '</div>';
	return wp_kses_post( $changelog );
}

//guidline for about theme
function printing_press_mostrar_guide() { 
	//custom function about theme customizer
	$printing_press_return = add_query_arg( array()) ;
	$printing_press_theme = wp_get_theme( 'printing-press' );
?>

    <div class="top-head">
		<div class="top-title">
			<h2><?php esc_html_e( 'Printing Press', 'printing-press' ); ?></h2>
		</div>
		<div class="top-right">
			<span class="version"><?php esc_html_e( 'Version', 'printing-press' ); ?>: <?php echo esc_html($printing_press_theme['Version']);?></span>
		</div>
    </div>

    <div class="inner-cont">

	    <div class="tab-sec">
	    	<div class="tab">
				<button class="tablinks" onclick="printing_press_open_tab(event, 'setup_customizer')"><?php esc_html_e( 'Setup With Customizer', 'printing-press' ); ?></button>
				<button class="tablinks" onclick="printing_press_open_tab(event, 'wpelemento_importer_editor')"><?php esc_html_e( 'Demo Import', 'printing-press' ); ?></button>
				<button class="tablinks" onclick="printing_press_open_tab(event, 'changelog_cont')"><?php esc_html_e( 'Changelog', 'printing-press' ); ?></button>
			</div>

			<div id="setup_customizer" class="tabcontent open">
				<div class="tab-outer-box">
				  	<div class="lite-theme-inner">
						<h3><?php esc_html_e('Theme Customizer', 'printing-press'); ?></h3>
						<p><?php esc_html_e('To begin customizing your website, start by clicking "Customize".', 'printing-press'); ?></p>
						<div class="info-link">
							<a target="_blank" href="<?php echo esc_url( admin_url('customize.php') ); ?>"><?php esc_html_e('Customizing', 'printing-press'); ?></a>
						</div>
						<hr>
						<h3><?php esc_html_e('Help Docs', 'printing-press'); ?></h3>
						<p><?php esc_html_e('The complete procedure to configure and manage a WordPress Website from the beginning is shown in this documentation .', 'printing-press'); ?></p>
						<div class="info-link">
							<a href="<?php echo esc_url( PRINTING_PRESS_FREE_THEME_DOC ); ?>" target="_blank"><?php esc_html_e('Documentation', 'printing-press'); ?></a>
						</div>
						<hr>
						<h3><?php esc_html_e('Need Support?', 'printing-press'); ?></h3>
						<p><?php esc_html_e('Our dedicated team is well prepared to help you out in case of queries and doubts regarding our theme.', 'printing-press'); ?></p>
						<div class="info-link">
							<a href="<?php echo esc_url( PRINTING_PRESS_SUPPORT ); ?>" target="_blank"><?php esc_html_e('Support Forum', 'printing-press'); ?></a>
						</div>
						<hr>
						<h3><?php esc_html_e('Reviews & Testimonials', 'printing-press'); ?></h3>
						<p> <?php esc_html_e('All the features and aspects of this WordPress Theme are phenomenal. I\'d recommend this theme to all.', 'printing-press'); ?></p>
						<div class="info-link">
							<a href="<?php echo esc_url( PRINTING_PRESS_REVIEW ); ?>" target="_blank"><?php esc_html_e('Review', 'printing-press'); ?></a>
						</div>
						<hr>
						<div class="link-customizer">
							<h3><?php esc_html_e( 'Link to customizer', 'printing-press' ); ?></h3>
							<div class="first-row">
								<div class="row-box">
									<div class="row-box1">
										<span class="dashicons dashicons-buddicons-buddypress-logo"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[control]=custom_logo') ); ?>" target="_blank"><?php esc_html_e('Upload your logo','printing-press'); ?></a>
									</div>
									<div class="row-box2">
										<span class="dashicons dashicons-menu"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=nav_menus') ); ?>" target="_blank"><?php esc_html_e('Menus','printing-press'); ?></a>
									</div>
								</div>
							
								<div class="row-box">
									<div class="row-box1">
										<span class="dashicons dashicons-align-center"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[section]=header_image') ); ?>" target="_blank"><?php esc_html_e('Header Image','printing-press'); ?></a>
									</div>
									<div class="row-box2">
										<span class="dashicons dashicons-screenoptions"></span><a href="<?php echo esc_url( admin_url('customize.php?autofocus[panel]=widgets') ); ?>" target="_blank"><?php esc_html_e('Footer Widget','printing-press'); ?></a>
									</div>
								</div>
							</div>
						</div>
				  	</div>
				</div>
			</div>

			<div id="wpelemento_importer_editor" class="tabcontent">
				<?php if(!class_exists('WPElemento_Importer_ThemeWhizzie')){
					$plugin_ins = Printing_Press_Plugin_Activation_WPElemento_Importer::get_instance();
					$printing_press_actions = $plugin_ins->recommended_actions;
					?>
					<div class="printing-press-recommended-plugins ">
							<div class="printing-press-action-list">
								<?php if ($printing_press_actions): foreach ($printing_press_actions as $key => $printing_press_actionValue): ?>
										<div class="printing-press-action" id="<?php echo esc_attr($printing_press_actionValue['id']);?>">
											<div class="action-inner plugin-activation-redirect">
												<h3 class="action-title"><?php echo esc_html($printing_press_actionValue['title']); ?></h3>
												<div class="action-desc"><?php echo esc_html($printing_press_actionValue['desc']); ?></div>
												<?php echo wp_kses_post($printing_press_actionValue['link']); ?>
											</div>
										</div>
									<?php endforeach;
								endif; ?>
							</div>
					</div>
				<?php }else{ ?>
					<div class="tab-outer-box">
						<h2><?php esc_html_e( 'Welcome to Elemento Theme!', 'printing-press' ); ?></h2>
						<p><?php esc_html_e( 'For setup the theme, First you need to click on the Begin activating plugins', 'printing-press' ); ?></p>
						<p><?php esc_html_e( '1. Install Kirki Customizer Framework ', 'printing-press' ); ?></p>
						<p><?php esc_html_e( '>> Then click to Return to Required Plugins Installer ', 'printing-press' ); ?></p>
						<p><?php esc_html_e( '2. Install WPElemento Importer', 'printing-press' ); ?></p>
						<p><?php esc_html_e( '>> Then click to Return to Required Plugins Installer ', 'printing-press' ); ?></p>
						<p><?php esc_html_e( '3. Activate Kirki Customizer Framework ', 'printing-press' ); ?></p>
						<p><?php esc_html_e( '4. Activate WPElemento Importer ', 'printing-press' ); ?></p>
						<p><?php esc_html_e( '>> Then click to Return to the Dashboard', 'printing-press' ); ?></p>
						<p><?php esc_html_e( '>> Click on the start now button', 'printing-press' ); ?></p>
						<p><?php esc_html_e( '>> Click install plugins', 'printing-press' ); ?></p>
						<p><?php esc_html_e( '>> Click import demo button to setup the theme and click visit your site button', 'printing-press' ); ?></p>
					</div>
				<?php } ?>
			</div>

			<div id="changelog_cont" class="tabcontent">
				<div class="tab-outer-box">
					<?php printing_press_changelog_screen(); ?>
				</div>
			</div>
			
		</div>

		<div class="inner-side-content">
			<h2><?php esc_html_e('Premium Theme', 'printing-press'); ?></h2>
			<div class="tab-outer-box">
				<img src="<?php echo esc_url(get_template_directory_uri()); ?>/screenshot.png" alt="" />
				<h3><?php esc_html_e('Printing Press WordPress Theme', 'printing-press'); ?></h3>
				<div class="iner-sidebar-pro-btn">
					<span class="premium-btn"><a href="<?php echo esc_url( PRINTING_PRESS_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('Buy Now', 'printing-press'); ?></a>
					</span>
					<span class="demo-btn"><a href="<?php echo esc_url( PRINTING_PRESS_LIVE_DEMO ); ?>" target="_blank"><?php esc_html_e('Live Demo', 'printing-press'); ?></a>
					</span>
					<span class="doc-btn"><a href="<?php echo esc_url( PRINTING_PRESS_PRO_DOC ); ?>" target="_blank"><?php esc_html_e('Pro Doc', 'printing-press'); ?></a>
					</span>
				</div>
				<hr>
				<div class="premium-coupon">
					<div class="premium-features">
						<h3><?php esc_html_e('premium Features', 'printing-press'); ?></h3>
						<ul>
							<li><?php esc_html_e( 'Multilingual', 'printing-press' ); ?></li>
							<li><?php esc_html_e( 'Drag and drop features', 'printing-press' ); ?></li>
							<li><?php esc_html_e( 'Zero Coding Required', 'printing-press' ); ?></li>
							<li><?php esc_html_e( 'Mobile Friendly Layout', 'printing-press' ); ?></li>
							<li><?php esc_html_e( 'Responsive Layout', 'printing-press' ); ?></li>
							<li><?php esc_html_e( 'Unique Designs', 'printing-press' ); ?></li>
						</ul>
					</div>
					<div class="coupon-box">
						<h3><?php esc_html_e('Use Coupon Code', 'printing-press'); ?></h3>
						<a class="coupon-btn" href="<?php echo esc_url( PRINTING_PRESS_BUY_NOW ); ?>" target="_blank"><?php esc_html_e('UPGRADE NOW', 'printing-press'); ?></a>
						<div class="coupon-container">
							<h3><?php esc_html_e( 'elemento20', 'printing-press' ); ?></h3>
						</div>
					</div>
				</div>
			</div>	
		</div>
	</div>

<?php } ?>