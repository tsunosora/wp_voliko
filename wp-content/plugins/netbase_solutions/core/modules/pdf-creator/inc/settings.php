<?php
class NBT_Pdf_Creator_Settings{
	static $id = 'pdf';

	protected static $initialized = false;

	public static function initialize() {
		// Do nothing if pluggable functions already initialized.
		if ( self::$initialized ) {
			return;
		}


		// State that initialization completed.
		self::$initialized = true;
	}

    public static function get_pages() {
        global $wpdb;

        $sql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_type = %s AND post_status = %s ORDER BY ID DESC", 'page', 'publish');

        $results = $wpdb->get_results($sql);

        $data = array();
        if( $results ) {
            foreach ( $results as $key => $value ) {
                $data[$value->ID] = '[' . $value->ID .'] '.$value->post_title;
            }
        }

        return $data;
    }
    public static function get_settings() {
        $get_pages = self::get_pages();

        $settings = array(
            'pdf_preview_page' => array(
                'name' => __( 'PDF Preview Page', 'nbt-solution' ),
                'desc' => __( 'Choose the position which you want the upload form to appear', 'nbt-solution'),
                'type' => 'select',
                'id'   => 'nbt_'.self::$id.'_pdf_preview_page',
                'default' => 'cart_form',
                'label' => '',
                'options' => $get_pages,
                'value' => get_option('_create_page_pdf')
            ),
            'pdf_template' => array(
                'name' => __( 'PDF Templates', 'nbt-solution' ),
                'desc' => __( 'Pick your template', 'nbt-solution' ),
                'type' => 'radio_image',
                'id'   => 'nbt_'.self::$id.'_template',
                'default' => 'temp1',
                'option' => self::option_template()
            ),
            'logo' => array(
                'name' => __( 'Logo', 'nbt-solution' ),
                'desc' => __( 'Change the invoice logo', 'nbt-solution' ),
                'type' => 'image',
                'id'   => 'nbt_'.self::$id.'_logo',
                'default' => 'http://netbasejsc.com/images/logo.png'
            ),
            'logo_height' => array(
                'name' => __( 'Logo Height', 'nbt-solution' ),
                // 'desc' => __( 'Enter your brand name', 'nbt-solution' ),
                'type' => 'number',
                'id'   => 'nbt_'.self::$id.'_logo_height',
                'default' => 'auto'
            ),
            'brandname' => array(
                'name' => __( 'Brand Name', 'nbt-solution' ),
                // 'desc' => __( 'Enter your brand name', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_brands',
                'default' => 'Netbase JSC,.'
            ),
            'address' => array(
                'name' => __( 'Address', 'nbt-solution' ),
                // 'desc' => __( 'Enter', 'nbt-solution' ),
                'type' => 'textarea',
                'id'   => 'nbt_'.self::$id.'_address',
                'default' => 'Room A702, M3-M4 Building, 91 Nguyen Chi Thanh Str, Dong Da Dist, Hanoi, Vietnam'
            ),
            'fonts' => array(
                'name' => __( 'Language', 'nbt-solution' ),
                'desc' => __( 'Choose the font for PDF', 'nbt-solution'),
                'type' => 'select',
                'id'   => 'nbt_'.self::$id.'_fonts',
                'default' => base64_encode('Roboto:400,500,700&amp;subset=vietnamese'),
                'label' => '',
                'options' => array(
                    'default' => 'Default',
                    'korean' => 'Korean',
                    'chinese' => 'Chinese',

                ),
            ),
            'page_orientation' => array(
                'name' => __( 'Page orientation', 'nbt-solution' ),
                'desc' => __( 'Choose the font for PDF', 'nbt-solution'),
                'type' => 'select',
                'id'   => 'nbt_'.self::$id.'_page_orientation',
                'default' => 'portrait',
                'label' => '',
                'options' => array(
                    'portrait' => 'Portrait',
                    'landscape' => 'Landscape'
                ),
            ),
            'color' => array(
                'name' => __( 'Primary Color', 'nbt-solution' ),
                // 'desc' => __( 'Chọn màu chính cho file PDF', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'nbt_'.self::$id.'_primary_color',
                'default' => '#cd3334'
            ),
            'secondary_color' => array(
                'name' => __( 'Secondary Color', 'nbt-solution' ),
                // 'desc' => __( 'Chọn màu chính cho file PDF', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'nbt_'.self::$id.'_secondary_color',
                'default' => '#0b80ef'
            ),
            'color_text' => array(
                'name' => __( 'Text Color', 'nbt-solution' ),
                // 'desc' => __( 'Chọn màu chữ cho file PDF', 'nbt-solution' ),
                'type' => 'color',
                'id'   => 'nbt_'.self::$id.'_text_color',
                'default' => '#000'
            )
        );
        return $settings;
    }

    public static function option_template(){
        $template_option = array(
            'temp1' => array(
                'name' => 'temp1',
                'src' => NBT_PDF_URL . 'assets/img/temp1.jpg',
                'label' => 'Nhãn'
            ),
            'temp2' => array(
                'name' => 'temp2',
                'src' => NBT_PDF_URL . 'assets/img/temp2.jpg',
                'label' => 'Nhãn'
            ),
            'temp3' => array(
                'name' => 'temp3',
                'src' => NBT_PDF_URL . 'assets/img/temp3.jpg',
                'label' => 'Nhãn'
            ),
        );
        return apply_filters( 'nbt_pdf_creator_template', $template_option );
    }

}
