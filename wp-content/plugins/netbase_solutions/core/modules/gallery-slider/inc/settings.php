<?php
class NBT_Gallery_Slider_Settings{

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
            'direction' => array(
                'name' => __( 'Gallery Layout', 'nbt-ajax-cart' ),
                // 'desc' => __( 'Choose Gallery layou', 'nbt-solution'),
                'type' => 'select',
                'id'   => 'wc_'.NBT_Solutions_Gallery_Slider::$plugin_id.'_direction',
                'default' => 'horizontal',
                'options' => array(
                    'horizontal' => 'Horizontal',
                    'vertical' => 'Vertical'
                )
            ),
        );
        return apply_filters( 'nbt_'.NBT_Solutions_Gallery_Slider::$plugin_id.'_settings', $settings );
    }

}
