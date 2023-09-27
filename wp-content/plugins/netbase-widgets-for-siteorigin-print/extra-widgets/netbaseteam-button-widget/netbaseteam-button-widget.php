<?php

/*
Widget Name: NetBaseTeam Button
Description: Flat style buttons with rich set of customization options.
Author: NetBaseTeam
Author URI: http://netbaseteam.com/
*/


class NetBaseTeam_Button_Widget extends SiteOrigin_Widget {

    /**
     * Holds the ID for the button element used for generating custom CSS.
     */
    private $element_id = '';

    function __construct() {
        parent::__construct(
            "netbaseteam-button",
            __("NBT Button", "netbaseteam-so-widgets"),
            array(
                "description" => __("Flat style buttons with rich set of customization options.", "netbaseteam-so-widgets"),
                "panels_icon" => "dashicons dashicons-minus",
                "panels_groups" => array('netbaseteam')

            ),
            array(),
            array(
                "widget_title" => array(
                    "type" => "text",
                    "label" => __("Title", "netbaseteam-so-widgets"),
                ),

                "href" => array(
                    "type" => "link",
                    "description" => __("The URL to which button should point to.", "netbaseteam-so-widgets"),
                    "label" => __("Target URL", "netbaseteam-so-widgets"),
                    "default" => __("#", "netbaseteam-so-widgets"),
                ),
                "text" => array(
                    "type" => "text",
                    "description" => __("The button title or text. ", "netbaseteam-so-widgets"),
                    "label" => __("Button Text", "netbaseteam-so-widgets"),
                    "default" => __("Buy Now", "netbaseteam-so-widgets"),
                ),

                'icon_type' => array(
                    'type' => 'select',
                    'label' => __('Choose Icon Type', 'livemesh-so-widgets'),
                    'default' => 'none',
                    'state_emitter' => array(
                        'callback' => 'select',
                        'args' => array('icon_type')
                    ),
                    'options' => array(
                        'none' => __('None', 'livemesh-so-widgets'),
                        'icon' => __('Icon', 'livemesh-so-widgets'),
                        'icon_image' => __('Icon Image', 'livemesh-so-widgets'),
                    )
                ),

                'icon_image' => array(
                    'type' => 'media',
                    'label' => __('Service Image.', 'livemesh-so-widgets'),
                    'state_handler' => array(
                        'icon_type[icon_image]' => array('show'),
                        '_else[icon_type]' => array('hide'),
                    ),
                ),

                'icon' => array(
                    'type' => 'icon',
                    'label' => __('Service Icon.', 'livemesh-so-widgets'),
                    'state_handler' => array(
                        'icon_type[icon]' => array('show'),
                        '_else[icon_type]' => array('hide'),
                    ),
                ),


                'settings' => array(
                    'type' => 'section',
                    'label' => __('Settings', 'livemesh-so-widgets'),
                    'fields' => array(

                        "class" => array(
                            "type" => "text",
                            "description" => __("The CSS class name for the button element.", "netbaseteam-so-widgets"),
                            "label" => __("Class", "netbaseteam-so-widgets"),
                            "default" => "",
                            "optional" => "true"
                        ),
                        "style" => array(
                            "type" => "text",
                            "description" => __("Inline CSS styling for the button element.", "netbaseteam-so-widgets"),
                            "label" => __("Style", "netbaseteam-so-widgets"),
                            "optional" => "true"
                        ),
                        "color" => array(
                            "type" => "select",
                            "description" => __("The color of the button.", "netbaseteam-so-widgets"),
                            "label" => __("Color", "netbaseteam-so-widgets"),
                            "options" => array(
                                "default" => __("Default", "netbaseteam-so-widgets"),
                                "custom" => __("Custom", "netbaseteam-so-widgets"),
                                "black" => __("Black", "netbaseteam-so-widgets"),
                                "blue" => __("Blue", "netbaseteam-so-widgets"),
                                "cyan" => __("Cyan", "netbaseteam-so-widgets"),
                                "green" => __("Green", "netbaseteam-so-widgets"),
                                "orange" => __("Orange", "netbaseteam-so-widgets"),
                                "pink" => __("Pink", "netbaseteam-so-widgets"),
                                "red" => __("Red", "netbaseteam-so-widgets"),
                                "teal" => __("Teal", "netbaseteam-so-widgets"),
                                "trans" => __("Transparent", "netbaseteam-so-widgets"),
                                "semitrans" => __("Semi Transparent", "netbaseteam-so-widgets"),
                            ),
                            'state_emitter' => array(
                                'callback' => 'select',
                                'args' => array('color')
                            ),
                        ),
                        "custom_color" => array(
                            "type" => "color",
                            "description" => __("Custom color of the button.", "netbaseteam-so-widgets"),
                            "label" => __("Custom button color", "netbaseteam-so-widgets"),
                            'state_handler' => array(
                                'color[custom]' => array('show'),
                                '_else[color]' => array('hide'),
                            ),
                        ),
                        "hover_color" => array(
                            "type" => "color",
                            "description" => __("Hover color of the button.", "netbaseteam-so-widgets"),
                            "label" => __("Custom button hover color", "netbaseteam-so-widgets"),
                            "optional" => "true"
                        ),
                        "type" => array(
                            "type" => "select",
                            "label" => __("Button Size", "netbaseteam-so-widgets"),
                            "options" => array(
                                "medium" => __("Medium", "netbaseteam-so-widgets"),
                                "large" => __("Large", "netbaseteam-so-widgets"),
                                "small" => __("Small", "netbaseteam-so-widgets"),
                            )
                        ),

                        'rounded' => array(
                            'type' => 'checkbox',
                            'label' => __('Display rounded button?', 'livemesh-so-widgets'),
                            'default' => false
                        ),
                        "target" => array(
                            "type" => "checkbox",
                            "label" => __("Open the link in new window", "netbaseteam-so-widgets"),
                            "default" => true,
                        ),
                        "align" => array(
                            "type" => "select",
                            "description" => __("Alignment of the button displayed.", "netbaseteam-so-widgets"),
                            "label" => __("Align", "netbaseteam-so-widgets"),
                            "options" => array(
                                "none" => __("None", "netbaseteam-so-widgets"),
                                "center" => __("Center", "netbaseteam-so-widgets"),
                                "left" => __("Left", "netbaseteam-so-widgets"),
                                "right" => __("Right", "netbaseteam-so-widgets"),
                            ),
                            'default' => 'none'
                        ),
                    )
                ),
            )
        );
    }

    function enqueue_frontend_scripts($instance) {

        wp_enqueue_style('netbaseteam-button', siteorigin_widget_get_plugin_dir_url('netbaseteam-button') . 'css/style.css', array(), 'all');

        $custom_css = $this->custom_css($instance);
        if (!empty($custom_css))
            wp_add_inline_style('netbaseteam-button', $custom_css);

        parent::enqueue_frontend_scripts($instance);
    }

    /**
     * Generate the custom layout CSS required
     */
    protected function custom_css($instance) {

        $custom_css = '';

        $this->element_id = uniqid('netbaseteam-button-');

        $id_selector = '#' . $this->element_id;

        $button_color = $instance['settings']["color"];

        $custom_color = $instance['settings']["custom_color"];

        $hover_color = $instance['settings']["hover_color"];

        if ($button_color == "custom") {
            if (!empty($custom_color)) {

                $custom_css .= $id_selector . '.netbaseteam-button { background-color:' . $custom_color . '; }' . "\n";

                // Automatically set a hover color for custom color if none specified by user
                if (empty($hover_color)) {
                    $hover_color = lsow_color_luminance($custom_color, 0.05);
                }
            }
        }

        // Apply the hover color for button of any color provided one is specified
        if (!empty($hover_color)) {
            $custom_css .= $id_selector . '.netbaseteam-button:hover { background-color:' . $hover_color . '; }';
        }

        return $custom_css;
    }

    function get_template_variables($instance, $args) {
        return array(
            "id" => $this->element_id,
            "style" => $instance['settings']["style"],
            "class" => $instance['settings']["class"],
            "color" => $instance['settings']["color"],
            "custom_color" => $instance['settings']["custom_color"],
            "hover_color" => $instance['settings']["hover_color"],
            "type" => $instance['settings']["type"],
            "align" => $instance['settings']["align"],
            "target" => $instance['settings']["target"],
            "rounded" => $instance['settings']["rounded"],
            "href" => (!empty($instance['href'])) ? sow_esc_url($instance['href']) : '',
            "text" => $instance["text"],
            'icon_type' => $instance['icon_type'],
            'icon_image' => $instance['icon_image'],
            'icon' => $instance['icon'],
            'settings' => $instance['settings']
        );
    }

}

siteorigin_widget_register("netbaseteam-button", __FILE__, "NetBaseTeam_Button_Widget");