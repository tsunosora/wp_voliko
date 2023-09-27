<?php
class NBT_Ajax_Cart_Settings{
	static $id = 'ajax_cart';

	protected static $initialized = false;

	public static function initialize() {
		// Do nothing if pluggable functions already initialized.
		if ( self::$initialized ) {
			return;
		}


		// State that initialization completed.
		self::$initialized = true;
	}

    public static function get_settings() {
        $settings = array(
            'icon' => array(
                'name' => __( 'Cart Icons', 'nbt-ajax-cart' ),
                'type' => 'icon',
                'desc'     => __( 'Choose your cart icon', 'nbt-solution' ),
                'desc_tip' => true,
                'default' => 'nbt-icon-basket-4',
                'id'   => 'wc_'.self::$id.'_icon',
                'options' => NBT_Solutions_Ajax_Cart::set_ajaxcart_icon()
            ),
            'icon_color' => array(
                'name' => __( 'Color Icons', 'nbt-ajax-cart' ),
                'desc'     => __( 'Cart icon color', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'wc_'.self::$id.'_color_icon',
                'default' => '#4285f4'
            ),
            'color_count' => array(
                'name' => __( 'Count: Background', 'nbt-ajax-cart' ),
                'desc'     => __( 'Background color for cart count number', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'wc_'.self::$id.'_color_count',
                'default' => '#e60000',
            ),
            'count_color_text' => array(
                'name' => __( 'Count: Text Color', 'nbt-ajax-cart' ),
                'desc'     => __( 'Text color for cart count number.', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'wc_'.self::$id.'_color_count_text',
                'default' => '#444'
            ),
            'count_color_border' => array(
                'name' => __( 'Count: Border Color', 'nbt-ajax-cart' ),
                'desc'     => __( 'Border color for cart count', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'wc_'.self::$id.'_count_color_border',
                'default' => '#ccc'
            ),
            'primary_color' => array(
                'name' => __( 'Popup Primary Color', 'nbt-ajax-cart' ),
                'desc'     => __( 'Cart popup background color', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'wc_'.self::$id.'_primary_color',
                'default' => '#4285f4'
            ),
            'background_button' => array(
                'name' => __( 'Background Button', 'nbt-ajax-cart' ),
                'desc'     => __( 'Cart popup background button color', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'wc_'.self::$id.'_background_button',
                'default' => '#4285f4'
            ),
            'background_button_hover' => array(
                'name' => __( 'Background Button: Hover', 'nbt-ajax-cart' ),
                'desc'     => __( 'Cart popup background button color when hover', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'wc_'.self::$id.'_background_button_hover',
                'default' => '#1565c0'
            ),
            'top' => array(
                'name' => __( 'Top Notification', 'nbt-ajax-cart' ),
                'desc'     => __( 'Notification Popup position: position from top of the page', 'nbt-solution' ),
                'type' => 'number',
                'id'   => 'wc_'.self::$id.'_top',
                'default' => '40',
                'min' => 0,
                'max' => 200,
                'step' => 1
            ),
            'top_popup' => array(
                'name' => __( 'Top Popup Cart', 'nbt-ajax-cart' ),
                'desc'     => __( 'Cart popup position: position from top of the page', 'nbt-solution' ),
                'type' => 'number',
                'id'   => 'wc_'.self::$id.'_top_popup',
                'default' => '45',
                'min' => 0,
                'max' => 200,
                'step' => 1
            ),
        );
        return apply_filters( 'nbt_'.self::$id.'_settings', $settings );
    }

    public static function show_settings($name) {
        $settings = self::get_settings();

        if( isset($settings[$name]) ) {
            return $settings[$name]['default'];
        }
    }

}
