<?php

/*
Widget Name: Livemesh Piecharts
Description: Display one or more piecharts depicting a percentage value in a multi-column grid.
Author: LiveMesh
Author URI: http://portfoliotheme.org
*/

class LSOW_Piechart_Widget extends SiteOrigin_Widget {

    function __construct() {
        parent::__construct(
            'nsow-piecharts',
            __('Livemesh Piecharts', 'netbaseteam-so-widgets'),
            array(
                'description' => __('Display statistics or skills as a percentage piechart.', 'netbaseteam-so-widgets'),
                'panels_icon' => 'dashicons dashicons-minus',
                'panels_groups' => array('netbaseteam')
            ),
            array(),
            array(
                'title' => array(
                    'type' => 'text',
                    'label' => __('Title', 'netbaseteam-so-widgets'),
                ),

                'piecharts' => array(
                    'type' => 'repeater',
                    'label' => __('Piecharts', 'netbaseteam-so-widgets'),
                    'item_name' => __('Piechart', 'netbaseteam-so-widgets'),
                    'item_label' => array(
                        'selector' => "[id*='piecharts-title']",
                        'update_event' => 'change',
                        'value_method' => 'val'
                    ),
                    'fields' => array(
                        'stats_title' => array(
                            'type' => 'text',
                            'label' => __('Stats Title', 'netbaseteam-so-widgets'),
                            'description' => __('The title for the piechart', 'netbaseteam-so-widgets'),
                        ),

                        'percentage' => array(
                            'type' => 'text',
                            'label' => __('Percentage Value', 'netbaseteam-so-widgets'),
                            'description' => __('The percentage value for the stats.', 'netbaseteam-so-widgets'),
                        ),
                    )
                ),

                'settings' => array(
                    'type' => 'section',
                    'label' => __('Settings', 'netbaseteam-so-widgets'),
                    'fields' => array(

                        'bar_color' => array(
                            'type' => 'color',
                            'label' => __('Bar color', 'netbaseteam-so-widgets'),
                            'default' => '#f94213'
                        ),

                        'track_color' => array(
                            'type' => 'color',
                            'label' => __('Track color', 'netbaseteam-so-widgets'),
                            'default' => '#dddddd'
                        ),

                        'per_line' => array(
                            'type' => 'slider',
                            'label' => __( 'Piecharts per row', 'netbaseteam-so-widgets' ),
                            'min' => 1,
                            'max' => 5,
                            'integer' => true,
                            'default' => 4
                        ),
                    )
                ),

            )
        );
    }

    function initialize() {

        $this->register_frontend_scripts(
            array(
                array(
                    'lsow-waypoints',
                    LSOW_PLUGIN_URL . 'assets/js/jquery.waypoints' . LSOW_JS_SUFFIX . '.js',
                    array('jquery'),
                    LSOW_VERSION
                ),
                array(
                    'lsow-stats',
                    LSOW_PLUGIN_URL . 'assets/js/jquery.stats' . LSOW_JS_SUFFIX . '.js',
                    array('jquery'),
                    LSOW_VERSION
                ),
            )
        );


        $this->register_frontend_scripts(
            array(
                array(
                    'nsow-piecharts',
                    plugin_dir_url(__FILE__) . 'js/piechart' . LSOW_JS_SUFFIX . '.js',
                    array('jquery')
                )
            )
        );

        $this->register_frontend_styles(array(
            array(
                'nsow-piecharts',
                plugin_dir_url(__FILE__) . 'css/style.css'
            )
        ));
    }

    function get_template_variables($instance, $args) {
        return array(
            'piecharts' => !empty($instance['piecharts']) ? $instance['piecharts'] : array(),
            'settings' => $instance['settings']
        );
    }

}

siteorigin_widget_register('nsow-piecharts', __FILE__, 'LSOW_Piechart_Widget');