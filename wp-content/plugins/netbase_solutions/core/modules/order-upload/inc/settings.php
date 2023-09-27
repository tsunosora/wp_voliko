<?php
class NBT_Order_Upload_Settings{
	static $id = 'order_upload';

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
            'upload_form_position' => array(
                'name' => __( 'Upload Form Position', 'nbt-solution' ),
                'desc' => __( 'Choose the position which you want the upload form to appear', 'nbt-solution'),
                'type' => 'select',
                'id'   => 'nbt_'.self::$id.'_upload_form_position',
                'default' => 'cart_form',
                'label' => '',
                'options' => array(
                    'woocommerce_single_product_summary' => __('Single Product Summary', 'nbt-solution'),
                    'woocommerce_before_add_to_cart_form' => __('Before Cart Form', 'nbt-solution'),
                    'woocommerce_before_single_variation' => __('Before Add to Cart button', 'nbt-solution')
                ),
            ),
            'enable_require_variations' => array(
                'name' => __( 'Choose variations before "Upload files" is required', 'nbt-solution' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_enable_require_variations',
                'default' => false,
                'label' => ''
            ),
            'enable_require_upload' => array(
                'name' => __( '"Upload files" is required before click Add to Cart', 'nbt-solution' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_enable_require_upload',
                'default' => false,
                'label' => ''
            ),
            'file_extension' => array(
                'name' => __( 'File Extension', 'nbt-solution' ),
                'desc' => __( 'Enter the file extensions allowed for upload, separate by a comma. Example: jpg, zip', 'nbt-solution'),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_file_extension',
                'default' => 'jpg, png, gif',
                'label' => ''
            ),
            'file_of_number' => array(
                'name' => __( 'Limit of Files', 'nbt-solution' ),
                'desc' => __( 'Number of files allowed for upload', 'nbt-solution'),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_file_of_number',
                'default' => 3,
                'label' => ''
            ),
            'file_limitsize' => array(
                'name' => __( 'Limit of filesize (upload max filesize: ' . ini_get('upload_max_filesize').')', 'nbt-solution' ),
                'desc' => __( 'File size limit', 'nbt-solution'),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_file_limitsize',
                'default' => '2M',
                'label' => ''
            ),
            'enable_dropbox_button' => array(
                'name' => __( 'Enable Dropbox Button', 'nbt-solution' ),
                'desc' => __( 'All the uploaded files will be store in Dropbox', 'nbt-solution'),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_enable_dropbox_button',
                'default' => false,
                'label' => ''
            ),
            'dropbox_apikey' => array(
                'name' => __( 'Dropbox API Key', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_dropbox_apikey',
                'default' => '',
                'desc' => '<a target="_blank" href="https://www.dropbox.com/developers/apps/create?app_type_checked=dropins">Get API Key</a> | <a target="_blank" href="https://www.dropbox.com/s/ao6ja26oopnw6tt/how%20to%20get%20dropbox%20API%20key.wmv?dl=0">How to get it?</a>'
            ),
            'enable_box_button' => array(
                'name' => __( 'Enable Box Button', 'nbt-solution' ),
                'desc' => __( 'All the uploaded files will be store in Box', 'nbt-solution'),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_enable_box_button',
                'default' => false,
                'label' => ''
            ),
            'box_apikey' => array(
                'name' => __( 'Box API Key', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_box_apikey',
                'default' => '',
                'desc' => '<a target="_blank" href="https://app.box.com/developers/console/newapp">Get API Key</a> | <a target="_blank" href="https://www.dropbox.com/s/cavuuj93tq13z0l/how%20to%20get%20box%20API%20key.wmv?dl=0">How to get it?</a>'
            ),
            'enable_g_drive_button' => array(
                'name' => __( 'Enable Google Drive Button', 'nbt-solution' ),
                'desc' => __( 'All the uploaded files will be store in Google Drive', 'nbt-solution'),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_enable_g_drive_button',
                'default' => false,
                'label' => ''
            ),
            'g_drive_apikey' => array(
                'name' => __( 'Google Drive API Key', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_g_drive_apikey',
                'desc' => '<a target="_blank" href="https://console.developers.google.com/project">Get API Key & Client ID </a> | <a target="_blank" href="https://www.dropbox.com/s/ikkmwfz41wnrlz7/how%20to%20get%20google%20API%20key%20and%20Client%20ID.wmv?dl=0">How to get it?</a>
                    ',
                'default' => '',
            ),
            'g_drive_clientid' => array(
                'name' => __( 'Google Drive Client ID', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_g_drive_clientid',
                'default' => '',
                'desc' => '<a target="_blank" href="https://console.developers.google.com/project">Get API Key & Client ID </a> | <a target="_blank" href="https://www.dropbox.com/s/ikkmwfz41wnrlz7/how%20to%20get%20google%20API%20key%20and%20Client%20ID.wmv?dl=0">How to get it?</a>
                    '
            )
        );

        return apply_filters( 'nbt_'.self::$id.'_settings', $settings );
    }

}
