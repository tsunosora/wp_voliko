<?php
class NBT_Color_Swatches_Settings {

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
            'width' => array(
                'name' => __( 'Width', 'stack-faqs' ),
                'type' => 'number',
                'desc' => 'px',
                'id'   => 'wc_'.NBT_Solutions_Color_Swatches::$plugin_id.'_width',
                'default' => 40,
                'min' => 14,
                'max' => 80,
                'step' => 1
            )
        );
        
        return $settings;
    }
}
