<?php
/*
Widget Name: NetBaseTeam Tabs
Description: This widget Customs Display Tabs.
Author: NetbaseTeam
Author URI: http://netbaseteam.com/
*/

class WpNetbase_Tabs extends SiteOrigin_Widget {
	function __construct() {

		parent::__construct(
			'wpnetbase-tabs',
			__('NBT Tabs', 'addon-so-widgets-bundle'),
			array(
				'description' => __('Tabs Component.', 'addon-so-widgets-bundle'),
                'panels_icon' => 'dashicons dashicons-welcome-widgets-menus',
                'panels_groups' => array('netbaseteam')
			),
			array(

			),
			array(
				'widget_title' => array(
					'type' => 'text',
					'label' => __('Widget Title', 'addon-so-widgets-bundle'),
					'default' => ''
				),

                'tab_name' => array(
                    'type' => 'text',
                    'label' => __('Tab name  (* Please enter a name for the tab)', 'addon-so-widgets-bundle'),
                    'default' => ''
                ),


                'repeater' => array(
                    'type' => 'repeater',
                    'label' => __( 'Tabs' , 'addon-so-widgets-bundle' ),
                    'item_name'  => __( 'Tab', 'addon-so-widgets-bundle' ),
                    'item_label' => array(
                        'selector'     => "[id*='repeat_text']",
                        'update_event' => 'change',
                        'value_method' => 'val'
                    ),
                    'fields' => array(

                        'tab_title' => array(
                            'type' => 'text',
                            'label' => __('Tab Title', 'addon-so-widgets-bundle'),
                            'default' => ''
                        ),




                        'tab_content' => array(
                            'type' => 'tinymce',
                            'label' => __( 'Tab Content', 'addon-so-widgets-bundle' ),
                            'default' => '',
                            'rows' => 10,
                            'default_editor' => 'html',
                            'button_filters' => array(
                                'mce_buttons' => array( $this, 'filter_mce_buttons' ),
                                'mce_buttons_2' => array( $this, 'filter_mce_buttons_2' ),
                                'mce_buttons_3' => array( $this, 'filter_mce_buttons_3' ),
                                'mce_buttons_4' => array( $this, 'filter_mce_buttons_5' ),
                                'quicktags_settings' => array( $this, 'filter_quicktags_settings' ),
                            ),
                        )



                    )
                ),

                'tabs_selection' => array(
                    'type' => 'radio',
                    'label' => __( 'Choose Tabs Style', 'addon-so-widgets-bundle' ),
                    'default' => 'horizontal',
                    'options' => array(
                        'horizontal' => __( 'Horizontal Tabs', 'addon-so-widgets-bundle' ),
                        'vertical' => __( 'Vertical Tabs', 'addon-so-widgets-bundle' ),
                		'accordion' => __( 'Accordion Tabs', 'addon-so-widgets-bundle' ),
                    )
                ),
				
				
				'tab_block' => array(
                            'type' => 'tinymce',
                            'label' => __( 'Add Block Bottom Tab', 'addon-so-widgets-bundle' ),
                            'default' => '',
                            'rows' => 10,
                            'default_editor' => 'html',
                            'button_filters' => array(
                                'mce_buttons' => array( $this, 'filter_mce_buttons' ),
                                'mce_buttons_2' => array( $this, 'filter_mce_buttons_2' ),
                                'mce_buttons_3' => array( $this, 'filter_mce_buttons_3' ),
                                'mce_buttons_4' => array( $this, 'filter_mce_buttons_5' ),
                                'quicktags_settings' => array( $this, 'filter_quicktags_settings' ),
                            ),
                        ),




                'tabs_styling' => array(
                    'type' => 'section',
                    'label' => __( 'Widget styling' , 'addon-so-widgets-bundle' ),
                    'hide' => true,
                    'fields' => array(

                        'bg_color' => array(
                            'type' => 'color',
                            'label' => __( 'Background Color', 'addon-so-widgets-bundle' ),
                            'default' => ''
                        ),

                        'inactive_tab_color' => array(
                            'type' => 'color',
                            'label' => __( 'Inactive Tab Font Color', 'addon-so-widgets-bundle' ),
                            'default' => ''
                        ),

                        'active_tab_color' => array(
                            'type' => 'color',
                            'label' => __( 'Active Tab Font Color', 'addon-so-widgets-bundle' ),
                            'default' => ''
                        ),

                        'tab_content_color' => array(
                            'type' => 'color',
                            'label' => __( 'Tab Content Color', 'addon-so-widgets-bundle' ),
                            'default' => ''
                        ),



                    )
                ),


			),
			plugin_dir_path(__FILE__)
		);
	}

	function get_template_name($instance) {
		return 'tabs-template';
	}

	function get_style_name($instance) {
		return 'tabs-style';
	}

    function get_less_variables( $instance ) {
        return array(
            'bg_color' => $instance['tabs_styling']['bg_color'],
            'inactive_tab_color' => $instance['tabs_styling']['inactive_tab_color'],
            'active_tab_color' => $instance['tabs_styling']['active_tab_color'],
            'tab_content_color' => $instance['tabs_styling']['tab_content_color'],
        );
    }

}

siteorigin_widget_register('wpnetbase-tabs', __FILE__, 'WpNetbase_Tabs');