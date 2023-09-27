<?php
class NBT_Solutions_Vendor_Profile_Social {
	function __construct() {
		add_filter('woopanel_options', array($this, 'get_settings'), 99, 1);
		add_action( 'woopanel_init', array($this, 'save_settings') );
	}
	
	function get_settings( $fields = array() ) {
		global $current_user;
		
		$wpl_settings = get_user_meta($current_user->ID, 'wpl_profile_settings', true);

		$fields['profile_social'] = [
			'menu_title' => esc_html__( 'Profile Social', 'woopanel' ),
			'title'      => esc_html__( 'Profile Social Settings', 'woopanel' ),
			'desc'       => '',
			'parent'     => '',
			'icon'       => '',
			'type'       => 'user_meta',
			'fields'     => array()
		];
		
		if( $dokan_get_social = woopanel_get_social_profile_fields() ) {
			foreach( $dokan_get_social as $key => $social) {
				$fields['profile_social']['fields'][] = array(
					'id'       => 'profile_social['. esc_attr($key) .']',
					'type'     => 'icon',
					'title'    => $social['title'],
					'icon'		=> $social['icon'],
					'placeholder' => 'http://',
					'value' => isset($wpl_settings['social'][$key]) ? $wpl_settings['social'][$key] : 0
				);
			}
		}

		return $fields;
	}
	

	function save_settings() {
		global $current_user;
		
		$wpl_profile_settings = get_user_meta($current_user->ID, 'wpl_profile_settings', true);
		if( empty($wpl_profile_settings) ) {
			$wpl_profile_settings = array();
		}
		


		if ( is_woopanel_endpoint_url('settings') && ( isset($_POST['save']) || isset($_POST['save1']) ) ) {
			
			
			$wpl_profile_settings['social'] = $_POST['profile_social'];
			
			update_user_meta($current_user->ID, 'wpl_profile_settings', $wpl_profile_settings );


		}
	}
}

new NBT_Solutions_Vendor_Profile_Social();