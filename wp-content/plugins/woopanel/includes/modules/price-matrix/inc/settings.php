<?php

if( ! class_exists('WooPanel_Price_Matrix_Settings') ) {

    /**
     * This class for Settings Page in WP-Admin
     *
     * @package WooPanel_Modules
     */
    class WooPanel_Price_Matrix_Settings{
        /**
         * Variable to hold the initialization state.
         *
         * @var  boolean
         */
        protected static $initialized = false;

        /**
         * Main WooPanel_Price_Matrix_Settings Instance.
         *
         * Ensures only one instance of WooPanel is loaded or can be loaded.
         *
         * @since 1.0.0
         */
        public static function initialize() {
            // Do nothing if pluggable functions already initialized.
            if ( self::$initialized ) {
                return;
            }

            // State that initialization completed.
            self::$initialized = true;
        }

    /**
     * Set setting fields
     */
        public static function get_settings() {
            $settings = array(
                'show_on' => array(
                    'name' => esc_html__( 'Price matrix table position', 'woopanel' ),
                    'type' => 'select',
                    'id'   => 'wc_price-matrix_show_on',
                    'options'       => array(
                        'default'   => esc_html__( 'Default', 'woopanel' ),
                        'before_tab'    => esc_html__( 'Before Tab', 'woopanel' )
                    ),
                    'default' => 'default'
                ),
                'hide_info' => array(
                    'name' => esc_html__( 'Hide Additional information', 'woopanel' ),
                    'type' => 'checkbox',
                    'id'   => 'wc_price-matrix_hide_info',
                    'default' => false,
                    'desc' => esc_html__('Hide Additional information tab in Product Details', 'woopanel' )
                ),
                'show_calculator' => array(
                    'name' => esc_html__( 'Show calculator text', 'woopanel' ),
                    'type' => 'checkbox',
                    'id'   => 'wc_price-matrix_show_calculator',
                    'default' => false,
                    'desc' => esc_html__('Show calculator text after Add to cart button', 'woopanel' )                
                ),
                'is_heading' => array(
                    'name' => esc_html__( 'Enable heading', 'woopanel' ),
                    'type' => 'checkbox',
                    'id'   => 'wc_price-matrix_is_heading',
                    'default' => false,
                    'desc' => esc_html__('Turn on heading before Price Matrix table', 'woopanel' )                
                ),
                'heading_label' => array(
                    'name' => esc_html__( 'Heading title', 'woopanel' ),
                    'type' => 'text',
                    'id'   => 'wc_price-matrix_heading',
                    'default' => ''
                ),
                'is_scroll' => array(
                    'name' => esc_html__( 'Scroll when select price', 'woopanel' ),
                    'type' => 'checkbox',
                    'id'   => 'wc_price-matrix_is_scroll',
                    'default' => false,
                    'desc' => esc_html__('Scroll the screen to the Price Matrix table when user choose attributes', 'woopanel' )                                
                ),
                'is_show_sales' => array(
                    'name' => esc_html__( 'Display regular & sale price', 'woopanel' ),
                    'type' => 'checkbox',
                    'id'   => 'wc_price-matrix_show_sales',
                    'default' => false,
                    'desc' => esc_html__('Display the sale price in the Price Matrix table', 'woopanel' )                                
                ),
                array(
                    'type' => 'border'
                ),
                'table_bg' => array(
                    'name' => esc_html__( 'Background color of price matrix table', 'woopanel' ),
                    'type' => 'color',
                    'id'   => 'wc_price-matrix_color_table',
                    'default' => '#efefef',
                ),
                'table_color' => array(
                    'name' => esc_html__( 'Table Text color', 'woopanel' ),
                    'type' => 'color',
                    'id'   => 'wc_price-matrix_color_text',
                    'default' => '#333'
                ),
                'border_color' => array(
                    'name' => esc_html__( 'Table Border color', 'woopanel' ),
                    'type' => 'color',
                    'id'   => 'wc_price-matrix_color_border',
                    'default' => '#ccc'
                ),
                array(
                    'type' => 'border'
                ),
                'bg_tooltip' => array(
                    'name' => esc_html__( 'Tooltips background color', 'woopanel' ),
                    'type' => 'color',
                    'id'   => 'wc_price-matrix_bg_tooltip',
                    'default' => '#efefef'
                ),
                'color_tooltip' => array(
                    'name' => esc_html__( 'Tooltips text color', 'woopanel' ),
                    'type' => 'color',
                    'id'   => 'wc_price-matrix_color_tooltip',
                    'default' => '#333'
                ),
                'border_tooltip' => array(
                    'name' => esc_html__( 'Tooltips border color', 'woopanel' ),
                    'type' => 'color',
                    'id'   => 'wc_price-matrix_border_tooltip',
                    'default' => '#ccc'
                ),
                'font_size' => array(
                    'name' => esc_html__( 'Font Size', 'woopanel' ),
                    'type' => 'number',
                    'desc' => 'px',
                    'id'   => 'wc_price-matrix_font_size',
                    'default' => 14,
                    'min' => 14,
                    'max' => 50,
                    'step' => 1
                )
            );
            return $settings;
        }
    }
}