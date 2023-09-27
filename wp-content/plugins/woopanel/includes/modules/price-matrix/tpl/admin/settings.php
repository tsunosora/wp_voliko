<?php
$all_settings = WooPanel_Price_Matrix_Settings::get_settings();
$options = get_option(WooPanel_Price_Matrix::$plugin_id.'_settings');
$key_id = str_replace('-', '_', WooPanel_Price_Matrix::$plugin_id);

// Save settings data

if( isset($_POST['submit-price_matrix']) ) {
	$options = array();


	foreach ($all_settings as $key => $value) {
		if(isset( $value['id'] )) {
			$id = $value['id'];
			if(isset($_POST[$id])){
				$options[$id] = $_POST[$id];
			}
		}
		
	}
	
	printf( '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>%s</strong></p></div>', esc_html__('Settings Saved', 'woopanel' ) );
	update_option(WooPanel_Price_Matrix::$plugin_id.'_settings', $options);
}

// Check file template
$temp_file = WOOPANEL_PRICEMATRIX_PATH . 'tpl/admin/settings/general.php';
$temp_auth = WOOPANEL_PRICEMATRIX_PATH . 'tpl/admin/settings/auth.php';
if( woopanel_price_matrix_check_license() && ! isset($_POST['_license']) && ! isset($_GET['tab']) ) {
	if( file_exists($temp_auth) ) {
		$temp_file = $temp_auth;
	}
}

if( file_exists($temp_auth) ) {
	$auth = true;
}

// Display HTML nav tabs
echo '<nav class="nav-tab-wrapper woo-nav-tab-wrapper">';
	$admin_url = admin_url( 'admin.php?page=' . WooPanel_Price_Matrix::$plugin_id );
	if( isset($auth) && woopanel_price_matrix_check_license() && ! isset($_POST['_license']) ) {
		printf( '<a href="%s" class="nav-tab%s">%s</a>',
			$admin_url,
			! isset($_GET['tab']) ? ' nav-tab-active' : '',
			esc_html__('Authentication', 'woopanel' )
		);
	}
	printf( '<a href="%s&amp;tab=settings" class="nav-tab%s">%s</a>',
		$admin_url,
		(! woopanel_price_matrix_check_license() || isset($_POST['_license']) || isset($_GET['tab']) && $_GET['tab'] == 'settings' || ! isset($auth) ) ? ' nav-tab-active' : '',
		esc_html__('Settings', 'woopanel' )
	);
echo '</nav>';

// Display HTML tab content
include_once $temp_file;