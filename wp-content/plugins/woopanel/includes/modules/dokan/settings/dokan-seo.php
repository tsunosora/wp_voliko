<?php
class NBT_Solutions_Dokan_Setting_SEO {
	function __construct() {
		add_filter('woopanel_options', array($this, 'get_settings'), 99, 1);
		add_action( 'woopanel_init', array($this, 'save_settings') );
	}
	

	function get_settings( $fields = array() ) {
		global $current_user;
		
		$dokan_settings = get_user_meta($current_user->ID, 'dokan_profile_settings', true);

		$fields['dokan_seo'] = [
			'menu_title' => esc_html__( 'Dokan SEO', 'woopanel' ),
			'title'      => esc_html__( 'Dokan SEO Settings', 'woopanel' ),
			'desc'       => '',
			'parent'     => '',
			'icon'       => '',
			'type'       => 'user_meta',
			'fields'     => array(
				array(
					'id'       => 'dokan_seo[dokan-seo-meta-title]',
					'type'     => 'text',
					'title'    => esc_html__( 'SEO Title :', 'woopanel' ),
					'description' => esc_html__( 'SEO Title is shown as the title of your store page', 'woopanel' ),
					'value' => isset($dokan_settings['store_seo']['dokan-seo-meta-title']) ? $dokan_settings['store_seo']['dokan-seo-meta-title'] : 0
				),
				array(
					'id'       => 'dokan_seo[dokan-seo-meta-desc]',
					'type'     => 'textarea',
					'title'    => esc_html__( 'Meta Description :', 'woopanel' ),
					'description' => esc_html__( 'The meta description is often shown as the black text under the title in a search result. For this to work it has to contain the keyword that was searched for and should be less than 156 chars.', 'woopanel' ),
					'value' => isset($dokan_settings['store_seo']['dokan-seo-meta-desc']) ? $dokan_settings['store_seo']['dokan-seo-meta-desc'] : 0
				),
				array(
					'id'       => 'dokan_seo[dokan-seo-meta-keywords]',
					'type'     => 'text',
					'title'    => esc_html__( 'Meta Keywords :', 'woopanel' ),
					'description' => esc_html__( 'Insert some comma separated keywords for better ranking of your store page.', 'dokan' ),
					'value' => isset($dokan_settings['store_seo']['dokan-seo-meta-keywords']) ? $dokan_settings['store_seo']['dokan-seo-meta-keywords'] : 0
				),
				array(
					'id'       => 'dokan_seo[dokan-seo-og-title]',
					'type'     => 'text',
					'title'    => esc_html__( 'Facebook Title :', 'woopanel' ),
					'value' => isset($dokan_settings['store_seo']['dokan-seo-og-title']) ? $dokan_settings['store_seo']['dokan-seo-og-title'] : 0
				),
				array(
					'id'       => 'dokan_seo[dokan-seo-og-desc]',
					'type'     => 'textarea',
					'title'    => esc_html__( 'Facebook Description :', 'woopanel' ),
					'value' => isset($dokan_settings['store_seo']['dokan-seo-og-desc']) ? $dokan_settings['store_seo']['dokan-seo-og-desc'] : 0
				),
				array(
					'id'       => 'dokan_seo[dokan-seo-og-image]',
					'type'     => 'image',
					'title'    => esc_html__( 'Facebook Image :', 'woopanel'  ),
					'size'	   => 'full',
					'dimensions' => array(
						'width' => 625,
						'height' => 300
					),
					'value' => isset($dokan_settings['store_seo']['dokan-seo-og-image']) ? $dokan_settings['store_seo']['dokan-seo-og-image'] : '-1'
				),
				array(
					'id'       => 'dokan_seo[dokan-seo-twitter-title]',
					'type'     => 'text',
					'title'    => esc_html__( 'Twitter Title :', 'woopanel' ),
					'value' => isset($dokan_settings['store_seo']['dokan-seo-twitter-title']) ? $dokan_settings['store_seo']['dokan-seo-twitter-title'] : 0
				),
				array(
					'id'       => 'dokan_seo[dokan-seo-twitter-desc]',
					'type'     => 'textarea',
					'title'    => esc_html__( 'Twitter Description :', 'woopanel' ),
					'value' => isset($dokan_settings['store_seo']['dokan-seo-twitter-desc']) ? $dokan_settings['store_seo']['dokan-seo-twitter-desc'] : 0
				),
				array(
					'id'       => 'dokan_seo[dokan-seo-twitter-image]',
					'type'     => 'image',
					'title'    => esc_html__( 'Twitter Image :', 'woopanel'  ),
					'size'	   => 'full',
					'dimensions' => array(
						'width' => 625,
						'height' => 300
					),
					'value' => isset($dokan_settings['store_seo']['dokan-seo-twitter-image']) ? $dokan_settings['store_seo']['dokan-seo-twitter-image'] : '-1'
				),
			)
		];
		
		return $fields;
	}
	

	function save_settings() {
		global $current_user;
		
		$dokan_profile_settings = get_user_meta($current_user->ID, 'dokan_profile_settings', true);
		if( empty($dokan_profile_settings) ) {
			$dokan_profile_settings = array();
		}

		if ( is_woopanel_endpoint_url('settings') && ( isset($_POST['save']) || isset($_POST['save1']) ) ) {
			
			$dokan_profile_settings['store_seo'] = $_POST['dokan_seo'];
			update_user_meta($current_user->ID, 'dokan_profile_settings', $dokan_profile_settings );


		}
	}
}

new NBT_Solutions_Dokan_Setting_SEO();