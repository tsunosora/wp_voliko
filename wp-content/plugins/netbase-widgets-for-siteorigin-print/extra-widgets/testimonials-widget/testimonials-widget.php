<?php
/*
Widget Name: NetBaseTeam Testimonials Widget
Description: NetBaseTeam Testimonials Widget.
Author: NetBaseTeam
Author URI: https://netbaseteam.com
*/
class Wpnetbase_SiteOrigin_Testimonials_Widget extends SiteOrigin_Widget {
	function __construct() {
		parent::__construct(
			'wpnetbase-testimonials-widget',
			__('NBT Testimonials Widget', 'so-widgets-bundle'),
			array(
				'description' => __('NetBaseTeam Testimonials Widget.', 'so-widgets-bundle'),
				'panels_groups' => array('netbaseteam')
			),
			array(

			),
			false,
			plugin_dir_path(__FILE__).'../'
		);
	}

	function initialize() {
		$this->register_frontend_scripts(
			array(
				array(
					'siteorigin-testimonialswidget',
					plugin_dir_url(__FILE__) . 'js/testimonialswidget' . SOW_BUNDLE_JS_SUFFIX . '.js',
					array( 'jquery' )
				)
			)
		);
	}

	function initialize_form(){
		return array(
			'title' => array(
				'type' => 'text',
				'label' => __('Title', 'so-widgets-bundle'),
			),

			'columns' => array(
				'type' => 'repeater',
				'label' => __('Items', 'so-widgets-bundle'),
				'item_name' => __('Column', 'so-widgets-bundle'),
				'item_label' => array(
					'selector' => "[id*='columns-title']",
					'update_event' => 'change',
					'value_method' => 'val'
				),
				'fields' => array(
					
					'testimonials_name' => array(
						'type' => 'text',
						'label' => __('Name', 'so-widgets-bundle'),
					),
					
					'testimonials_content' => array(
						'type' => 'text',
						'label' => __('Content', 'so-widgets-bundle'),
					),

					'testimonials_avatar' => array(
						'type' => 'media',
						'label' => __('Avarta', 'so-widgets-bundle'),
					),

					'testimonials_avatar_title' => array(
						'type' => 'text',
						'label' => __('Image title', 'so-widgets-bundle'),
					),

					'testimonials_avatar_alt' => array(
						'type' => 'text',
						'label' => __('Image alt text', 'so-widgets-bundle'),
					),

					
					'testimonials_company' => array(
						'type' => 'text',
						'label' => __('Company', 'so-widgets-bundle'),
					),
					'testimonials_company_url' => array(
						'type' => 'link',
						'label' => __('Company URL', 'so-widgets-bundle'),
					),
					
				),
			),

			'theme' => array(
				'type' => 'select',
				'label' => __('Type theme', 'so-widgets-bundle'),
				'options' => array(
					'default' => __('Default', 'so-widgets-bundle'),
					'carousel1' => __('Carosel', 'so-widgets-bundle'),
					'carouselimg' => __('Carosel with imgs', 'so-widgets-bundle'),
				),
			),

			'image_top' => array(
				'type' => 'checkbox',
				'label' => __('Avarta position top', 'so-widgets-bundle'),
			),
		);
	}

	function get_column_classes($column, $i, $columns) {
		$classes = array();
		
		if($i == 0) $classes[] = 'ow-pt-first';
		if($i == count($columns) -1 ) $classes[] = 'ow-pt-last';
		

		
		if($i % 2 == 0) $classes[] = 'ow-pt-even';
		else $classes[] = 'ow-pt-odd';

		return implode(' ', $classes);
	}

	function column_image($column){
		$src = wp_get_attachment_image_src($column['testimonials_avatar'], 'full');
		$img_attrs = array();
		if ( !empty( $column['testimonials_avatar_title'] ) ) $img_attrs['title'] = $column['testimonials_avatar_title'];
		if ( !empty( $column['testimonials_avatar_alt'] ) ) $img_attrs['alt'] = $column['testimonials_avatar_alt'];
		$attr_string = '';
		foreach ( $img_attrs as $attr => $val ) {
			$attr_string .= ' ' . $attr . '="' . esc_attr( $val ) . '"';
		}
		?><img src="<?php echo $src[0] ?>"<?php echo $attr_string ?>/> <?php
	}

	function get_template_name($instance) {
		return $this->get_style_name($instance);
	}

	function get_style_name($instance) {
		if(empty($instance['theme'])) return 'atom';
		return $instance['theme'];
	}

	/**
	 * Get the LESS variables for the price table widget.
	 *
	 * @param $instance
	 *
	 * @return array
	 */
	function get_less_variables($instance){
		$instance = wp_parse_args($instance, array(
			'header_color' => '',
			'featured_header_color' => '',
			'button_color' => '',
			'featured_button_color' => '',
		));

		$colors = array(
			'header_color' => $instance['header_color'],
			'featured_header_color' => $instance['featured_header_color'],
			'button_color' => $instance['button_color'],
			'featured_button_color' => $instance['featured_button_color'],
		);

		if( !class_exists('SiteOrigin_Widgets_Color_Object') ) require plugin_dir_path( SOW_BUNDLE_BASE_FILE ).'base/inc/color.php';

		if( !empty( $instance['button_color'] ) ) {
			$color = new SiteOrigin_Widgets_Color_Object( $instance['button_color'] );
			$color->lum += ($color->lum > 0.75 ? -0.5 : 0.8);
			$colors['button_text_color'] = $color->hex;
		}

		if( !empty( $instance['featured_button_color'] ) ) {
			$color = new SiteOrigin_Widgets_Color_Object( $instance['featured_button_color'] );
			$color->lum += ($color->lum > 0.75 ? -0.5 : 0.8);
			$colors['featured_button_text_color'] = $color->hex;
		}

		return $colors;
	}

}

siteorigin_widget_register('wpnetbase-testimonials-widget', __FILE__, 'Wpnetbase_SiteOrigin_Testimonials_Widget');
