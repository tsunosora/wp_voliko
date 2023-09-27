<?php
class NBT_Product_Notification_Settings{
	static $id = 'product_notification';

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
        $blogname = get_option('blogname');
        $admin_email = get_option('admin_email');
        $settings = array(
            /*new*/
            array(
                'name' => __( 'EMAIL SETTINGS - Email Sender', 'nbt-solution' ),
                'desc' => '',
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_email_sender',
                'default' => $blogname
            ),
            array(
                'name' => __( 'Email From', 'nbt-solution' ),
                'desc' => '',
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_email_from',
                'default' => $admin_email
            ),
            array(
                'name' => __( 'Email Subject', 'nbt-solution' ),
                'desc' => '',
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_email_subject',
                'default' => 'Your product is on stock now!'
            ),
            array(
                'name' => __( 'Email Message', 'nbt-solution' ),  
                'desc' => 'Get the product title: %product_name% ,Show a link to the product: %product_link%',              
                'type' => 'textarea',
                'id'   => 'nbt_'.self::$id.'_email_message',
                'default' => 'Hello, The product %product_name% is on stock. You can purchase it here: %product_link%'
            ),
            
            
            array(
                'name' => __( 'FORM SETTINGS - Input placeholder', 'nbt-solution' ),
                'desc' => '',
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_form_placeholder',
                'default' => 'Email address'
            ),
            array(
                'name' => __( 'Button label', 'nbt-solution' ),
                // 'desc' => __( 'Text của nút', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_form_button',
                'default' => 'Keep me updated'
            ),

            array(
                'name' => __( 'Description', 'nbt-solution' ),
                'desc' => '', 
                'type' => 'textarea',
                'id'   => 'nbt_'.self::$id.'_desc',
                'default' => 'It seems this one is sold out. Enter your mail and get notified with a gift when it\'s back in stock!'
            ),

            array(
                'name' => __( 'Error Message', 'nbt-solution' ),  
                'desc' => '',              
                'type' => 'textarea',
                'id'   => 'nbt_'.self::$id.'_form_error',
                'default' => 'Invalid email address.'
            ),

            array(
                'name' => __( 'Success Message', 'nbt-solution' ),  
                'desc' => '',              
                'type' => 'textarea',
                'id'   => 'nbt_'.self::$id.'_form_success',
                'default' => 'Thank you. We will notify you when the product is in stock.'
            ),

            /*array(
                'name' => __( 'Disable CSS?', 'nbt-solution' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_disable_css',
                'default' => false,
                
            ),*/
            
            /*end_new*/
            /*array(
                'name' => __( 'Heading Title', 'nbt-solution' ),
                'desc' => __( 'Appear when product is out of stock', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_title',
                'default' => 'Oh my...'
            ),
            array(
                'name' => __( 'Description', 'nbt-solution' ),
                // 'desc' => __( 'Mô tả xuất hiện khi sản phẩm hết hàng', 'nbt-solution' ),
                'type' => 'textarea',
                'id'   => 'nbt_'.self::$id.'_desc',
                'default' => 'It seems this one is sold out. Enter your mail and get notified with a gift when it\'s back in stock!'
            ),*/
            /*array(
                'name' => __( 'Button label', 'nbt-solution' ),
                // 'desc' => __( 'Text của nút', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_button',
                'default' => 'Keep me updated'
            ),*/
            /*array(
                'name' => __( 'Label when user logged in', 'nbt-solution' ),
                // 'desc' => __( 'Text khi khách đã đăng nhập', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_change_email',
                'default' => 'Click here to change your email address'
            ),
            array(
            	'type' => 'border'
            ),
            array(
                'name' => __( 'Email title', 'nbt-solution' ),
                // 'desc' => __( 'Tiêu đề email gửi cho khsach hàng', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_email_title',
                'default' => 'Product Notification: %product%'
            ),
            array(
                'name' => __( 'Email content', 'nbt-solution' ),
                // 'desc' => __( 'Nội dung email gửi cho khsach hàng', 'nbt-solution' ),
                'type' => 'textarea',
                'id'   => 'nbt_'.self::$id.'_email_desc',
                'default' => 'It seems this one is sold out. Enter your mail and get notified with a gift when it\'s back in stock!',
                'rows' => 10,
                'desc_tip' => '<p><strong>%product%</strong>: Show product title</p>'
            ),
            array(
                'name' => __( 'Re-captcha Sitekey', 'nbt-solution' ),
                'desc' => __( 'To get your Sitekey and Secret, please follow this link: https://www.youtube.com/watch?v=xByblMJsA8s', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_sitekey',
                'default' => ''
            ),
            array(
                'type' => 'border'
            ),
            array(
                'name' => __( 'Re-captcha Secret', 'nbt-solution' ),
                // 'desc' => __( 'Hướng dẫn lấy Sitekey và Secret tại https://www.youtube.com/watch?v=xByblMJsA8s', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_secret',
                'default' => ''
            ),*/
        );
        return apply_filters( 'nbt_'.self::$id.'_settings', $settings );
    }

}
