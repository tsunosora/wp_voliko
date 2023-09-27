<?php
class NBT_Ajax_Search_Settings {

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
            'layout' => array(
                'name' => __( 'Layout Search', 'nbt-solution' ),
                'desc' => __( 'Choose the layout for search form', 'nbt-solution'),
                'type' => 'select',
                'id'   => 'wc_'.NBT_Solutions_Ajax_Search::$plugin_id.'_layout',
                'default' => 'popup',
                'label' => '',
                'options' => array(
                    'popup' => __('Display search icon with popup', 'nbt-solution'),
                    'input' => __('Display search input with Icon', 'nbt-solution'),
                    
                ),
            ),
            'icon_color' => array(
                'name' => __( 'Color of Icon', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'wc_'.NBT_Solutions_Ajax_Search::$plugin_id.'_color_icon',
                'default' => '#444',
                'desc' => __('Search icon color', 'nbt-solution')
            )
        );
        return $settings;
    }

}
