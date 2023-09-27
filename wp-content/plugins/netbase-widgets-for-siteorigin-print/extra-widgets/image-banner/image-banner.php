<?php
/*
Widget Name: NetBaseTeam image banner
Description: A very simple image widget.
Author: NetBaseTeam
Author URI: http://netbaseteam.com/
*/

class WpNetbase_Image_Banner_Widget extends SiteOrigin_Widget {
	function __construct() {
		parent::__construct(
			'wpnetbase-image-banner',
			__('NBT Image banner', 'addon-so-widgets-bundle'),
			
			array(
				'description' => __('NetBaseTeam image banner.', 'addon-so-widgets-bundle'),
                'panels_icon' => 'dashicons dashicons-welcome-widgets-menus',
                'panels_groups' => array('netbaseteam')
			),
			array(

			),
			false,
			plugin_dir_path(__FILE__)
		);
	}

	function initialize_form() {

		return array(
			'image' => array(
				'type' => 'media',
				'label' => __('Image file', 'so-widgets-bundle'),
				'library' => 'image',
				'fallback' => true,
			),

			'size' => array(
				'type' => 'image-size',
				'label' => __('Image size', 'so-widgets-bundle'),
			),

			'align' => array(
				'type' => 'select',
				'label' => __('Image alignment', 'so-widgets-bundle'),
				'default' => 'default',
				'options' => array(
					'default' => __('Default', 'so-widgets-bundle'),
					'left' => __('Left', 'so-widgets-bundle'),
					'right' => __('Right', 'so-widgets-bundle'),
					'center' => __('Center', 'so-widgets-bundle'),
				),
			),

			'title' => array(
				'type' => 'text',
				'label' => __('Title text', 'so-widgets-bundle'),
			),
			
			'caption' => array(
				'type' => 'text',
				'label' => __('Caption text', 'so-widgets-bundle'),
			),
			'txt_primary' => array(
				'type' => 'text',
				'label' => __('Primary Text', 'so-widgets-bundle'),
			),
			
			'txt_btn' => array(
				'type' => 'text',
				'label' => __('Button text', 'so-widgets-bundle'),
			),
			
			'nbtclass' => array(
				'type' => 'text',
				'label' => __('Add class', 'so-widgets-bundle'),
			),

			'title_position' => array(
				'type' => 'select',
				'label' => __('Title position', 'so-widgets-bundle'),
				'default' => 'hidden',
				'options' => array(
					'hidden' => __( 'Hidden', 'so-widgets-bundle' ),
					'above' => __( 'Above', 'so-widgets-bundle' ),
					'below' => __( 'Below', 'so-widgets-bundle' ),
				),
			),

			'alt' => array(
				'type' => 'text',
				'label' => __('Alt text', 'so-widgets-bundle'),
			),
			'linksc' => array(
				'type' => 'text',
				'label' => __('Add link by shortcode', 'so-widgets-bundle'),
			),
			'url' => array(
				'type' => 'link',
				'label' => __('Destination URL', 'so-widgets-bundle'),
			),
			'new_window' => array(
				'type' => 'checkbox',
				'default' => false,
				'label' => __('Open in new window', 'so-widgets-bundle'),
			),

			'bound' => array(
				'type' => 'checkbox',
				'default' => true,
				'label' => __('Bound', 'so-widgets-bundle'),
				'description' => __("Make sure the image doesn't extend beyond its container.", 'so-widgets-bundle'),
			),
			'full_width' => array(
				'type' => 'checkbox',
				'default' => false,
				'label' => __('Full Width', 'so-widgets-bundle'),
				'description' => __("Resize image to fit its container.", 'so-widgets-bundle'),
			),
			'link_addimg' => array(
				'type' => 'checkbox',
				'default' => false,
				'label' => __('Add link Image', 'so-widgets-bundle'),
				'description' => __("Resize image to fit its container.", 'so-widgets-bundle'),
			),

		);
	}

	function get_style_hash($instance) {
		return substr( md5( serialize( $this->get_less_variables( $instance ) ) ), 0, 12 );
	}

	public function get_template_variables( $instance, $args ) {
		return array(
			'title' => $instance['title'],
			'caption' => $instance['caption'],
			'txt_primary' => $instance['txt_primary'],
			'txt_btn' => $instance['txt_btn'],
			'nbtclass' => $instance['nbtclass'],

			'title_position' => $instance['title_position'],
			'image' => $instance['image'],
			'size' => $instance['size'],
			'image_fallback' => ! empty( $instance['image_fallback'] ) ? $instance['image_fallback'] : false,
			'alt' => $instance['alt'],
			'url' => $instance['url'],
			'linksc' => $instance['linksc'],
			'new_window' => $instance['new_window'],
			'link_addimg' => $instance['link_addimg'],
		);
	}

	function get_less_variables($instance){
		return array(
			'image_alignment' => $instance['align'],
			'image_display' => $instance['align'] == 'default' ? 'block' : 'inline-block',
			'image_max_width' => ! empty( $instance['bound'] ) ? '100%' : '',
			'image_height' => ! empty( $instance['bound'] ) ? 'auto' : '',
			'image_width' => ! empty( $instance['full_width'] ) ? '100%' : '',
		);
	}
}

siteorigin_widget_register('wpnetbase-image-banner', __FILE__, 'WpNetbase_Image_Banner_Widget');
