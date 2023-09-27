<?php
class NBT_Live_Chat_Settings{

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
            'embed_code' => array(
                'name' => __( 'Embed code', 'nbt-pdf-creator' ),
                'desc' => __( '<p>Enter your Live Chat embed code here.</p><p><strong>Recommend:</strong> <a href="https://dashboard.tawk.to/signup" target="_blank">Click here</a> to register a new account with Tawk.to.</p>', 'nbt-solution'),
                'type' => 'textarea',
                'rows' => 15,
                'id'   => 'nbt_'.NBT_Solutions_Live_Chat::$plugin_id.'_embed_code',
                'default' => '<!--Start of Tawk.to Script-->
                    <script type="text/javascript">
                    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
                    (function(){
                    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
                    s1.async=true;
                    s1.src=\'https://embed.tawk.to/59db1b8dc28eca75e4624e2a/default\';
                    s1.charset=\'UTF-8\';
                    s1.setAttribute(\'crossorigin\',\'*\');
                    s0.parentNode.insertBefore(s1,s0);
                    })();
                    </script>
                    <!--End of Tawk.to Script-->'
            ), 
            'always_show' => array(
                'name' => __( 'Visibility Options', 'nbt-solution' ),
                'desc' => __( 'Allow Live chat Widget visible on all pages', 'nbt-solution'),
                'type' => 'checkbox',
                'id'   => 'nbt_'.NBT_Solutions_Live_Chat::$plugin_id.'_always_show',
                'default' => true,
                // 'label' => __('Always show Tawk.To widget on every page', 'nbt-solution')
            ),
            'exclude' => array(
                'name' => __( 'Exclude on specific url', 'nbt-solution' ),
                // 'desc' => __( 'Excl', 'nbt-solution'),
                'type' => 'textarea',
                'id'   => 'nbt_'.NBT_Solutions_Live_Chat::$plugin_id.'_exclude_url',
                'default' => '',
                'desc' => '<p>Press Enter to add a URL new line. <strong>Note:</strong> Use (*) to exclude any url.</p><p><strong>Example:</strong> If you want to disable show Live Chat on all single product page, you can enter '. home_url() .'/product/(*)/</p>'
            ), 
        );
        return apply_filters( 'nbt_'.NBT_Solutions_Live_Chat::$plugin_id.'_settings', $settings );
    }




}
