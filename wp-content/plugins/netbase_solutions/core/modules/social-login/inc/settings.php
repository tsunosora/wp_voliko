<?php
class NBT_Social_Login_Settings{
	static $id = 'social-login';

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
            'label_fb' => array(
                'name' => __( 'Facebook', 'nbt-solution' ),
                'type' => 'label',
                'id'   => 'nbt_'.self::$id.'_label_fb',                
            ),

            'facebook_enable' => array(
                'name' => __( 'Enable?', 'nbt-solution' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_facebook_enable',
                'default' => false,
                'label' => ''
            ),
            'facebook_app_id' => array(
                'name' => __( 'Facebook App ID:', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_facebook_app_id',
                'default' => '',
                'label' => '',
                'desc' => '<a target="_blank" href="https://developers.facebook.com/apps">Get App ID</a> | <span>Valid Oauth redirect URIs: '. site_url().'/wp-login.php?nbtsl_login_id=facebook_check</span>'
            ),
            'facebook_app_secret' => array(
                'name' => __( 'Facebook App Secret:', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_facebook_app_secret',
                'default' => '',
                'label' => ''
            ),
            /*'profile_img_width' => array(
                'name' => __( 'Profile picture image size width:', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_profile_img_width',
                'default' => '',
                'label' => ''
            ),
            'profile_img_height' => array(
                'name' => __( 'Profile picture image size height:', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_profile_img_height',
                'default' => '',
                'label' => ''
            ),*/

            'label_twitter' => array(
                'name' => __( 'Twitter', 'nbt-solution' ),
                'type' => 'label',
                'id'   => 'nbt_'.self::$id.'_label_twitter',                
            ),
            'twitter_enable' => array(
                'name' => __( 'Enable?', 'nbt-solution' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_twitter_enable',
                'default' => false,
                'label' => ''
            ),
            'twitter_api_key' => array(
                'name' => __( 'Twitter Api Key:', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_twitter_api_key',
                'default' => '',
                'label' => '',
                'desc' => '<a target="_blank" href="https://apps.twitter.com/">Get Twitter Api Key</a> | <span>Callback URL: '. site_url().'/wp-login.php?nbtsl_login_id=twitter_check</span>'
            ),
            'twitter_api_secret' => array(
                'name' => __( 'Twitter Api Secret:', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_twitter_api_secret',
                'default' => '',
                'label' => ''
            ),

            'label_google' => array(
                'name' => __( 'Google', 'nbt-solution' ),
                'type' => 'label',
                'id'   => 'nbt_'.self::$id.'_label_google',                
            ),
            'google_enable' => array(
                'name' => __( 'Enable?', 'nbt-solution' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_google_enable',
                'default' => false,
                'label' => ''
            ),
            'google_client_id' => array(
                'name' => __( 'Client ID:', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_google_client_id',
                'default' => '',
                'label' => '',
                'desc' => '<a target="_blank" href="https://console.developers.google.com/project">Get Client ID</a> | <span>Rediret uri setup: '. site_url().'/wp-login.php?nbtsl_login_id=google_check</span>'
            ),
            'google_client_secret' => array(
                'name' => __( 'Client Secret:', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_google_client_secret',
                'default' => '',
                'label' => ''
            ),

            'label_other_social ' => array(
                'name' => __( 'Other Settings', 'nbt-solution' ),
                'type' => 'label',
                'id'   => 'nbt_'.self::$id.'_label_other_social',                
            ),

            'title_text_field' => array(
                'name' => __( 'Login text:', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_title_text_field',
                'default' => 'Social connect:',
                'label' => ''
            ),
            /*'custom_logout_redirect' => array(
                'name' => __( 'Logout redirect link', 'nbt-solution' ),
                'type' => 'select',
                'id'   => 'nbt_'.self::$id.'_custom_logout_redirect',
                'default' => 'home',
                'label' => '',
                'options' => array(
                    'home' => __('Home page', 'nbt-solution'),
                    'current_page' => __('Current page', 'nbt-solution'),
                    'custom_page' => __('Custom page', 'nbt-solution'),
                ),
            ),
            'custom_logout_redirect_link' => array(
                'name' => __( 'Logout redirect Custom page:', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_custom_logout_redirect_link',
                'default' => '',
                'label' => ''
            ),*/
            'custom_login_redirect' => array(
                'name' => __( 'Login redirect link', 'nbt-solution' ),
                'type' => 'select',
                'id'   => 'nbt_'.self::$id.'_custom_login_redirect',
                'default' => 'home',
                'label' => '',
                'options' => array(
                    'home' => __('Home page', 'nbt-solution'),
                    'current_page' => __('Current page', 'nbt-solution'),
                    'custom_page' => __('Custom page', 'nbt-solution'),
                ),
            ),

            'custom_login_redirect_link' => array(
                'name' => __( 'Login redirect Custom page:', 'nbt-solution' ),
                'type' => 'text',
                'id'   => 'nbt_'.self::$id.'_custom_login_redirect_link',
                'default' => '',
                'label' => ''
            ),

            'user_avatar' => array(
                'name' => __( 'User avatar', 'nbt-solution' ),
                'type' => 'select',
                'id'   => 'nbt_'.self::$id.'_user_avatar',
                'default' => 'default',
                'label' => 'Please choose the options from where you want your users avatar to be loaded from.',
                'options' => array(
                    'default' => __('Use wordpress default avatar', 'nbt-solution'),
                    'social' => __('Use the profile picture from social media where available', 'nbt-solution'),
                    
                ),
            ),            

            'login_form_enable' => array(
                'name' => __( 'Display Login Form?', 'nbt-solution' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_login_form_enable',
                'default' => true,
                'label' => ''
            ),


            'register_form_enable' => array(
                'name' => __( 'Display Register Form?', 'nbt-solution' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_register_form_enable',
                'default' => true,
                'label' => ''
            ),

            'comment_form_enable' => array(
                'name' => __( 'Display Comment Form?', 'nbt-solution' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_comment_form_enable',
                'default' => true,
                'label' => ''
            ),

            'wc_enable' => array(
                'name' => __( 'Display woocommerce Login?', 'nbt-solution' ),
                'type' => 'checkbox',
                'id'   => 'nbt_'.self::$id.'_wc_enable',
                'default' => true,
                'label' => ''
            ),

            'icon_temp' => array(
                'name' => __( 'Icon Template', 'nbt-solution' ),
                'type' => 'radio_image',
                'id'   => 'nbt_'.self::$id.'_icon_temp',
                'default' => '1',
                'option' => array(
                    '1' => array(
                        'name' => 'temp 1',
                        'src' => NBTSL_IMAGE_DIR . '/preview-1.jpg',
                        'label' => ''
                    ),
                    '2' => array(
                        'name' => 'temp 2',
                        'src' => NBTSL_IMAGE_DIR . '/preview-2.jpg',
                        'label' => ''
                    ),
                    '3' => array(
                        'name' => 'temp 3',
                        'src' => NBTSL_IMAGE_DIR . '/preview-3.jpg',
                        'label' => ''
                    ),
                    '4' => array(
                        'name' => 'temp 4',
                        'src' => NBTSL_IMAGE_DIR . '/preview-4.jpg',
                        'label' => ''
                    ),
                )
            ),

            'send_email_notification' => array(
                'name' => __( 'Send Email Notification', 'nbt-solution' ),
                'type' => 'select',
                'id'   => 'nbt_'.self::$id.'_send_email_notification',
                'default' => 'yes',
                'label' => 'Here you can configure an options to send email notifications about user registration to site admin and user.',
                'options' => array(
                    'yes' => __('Yes', 'nbt-solution'),
                    'no' => __('No', 'nbt-solution'),                    
                ),
            ),
            
        );
        return apply_filters( 'nbt_'.self::$id.'_settings', $settings );
    }
}
