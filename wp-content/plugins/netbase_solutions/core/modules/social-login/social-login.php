<?php defined( 'ABSPATH' ) or die( "No script kiddies please!" );
/*
  Social Login WordPress 
  URI: http://netbaseteam.com
  Description: A plugin to add various social logins to a site.
  Author: Tungpk
*/

if( !defined( 'NBTSL_VERSION' ) ) {
    define( 'NBTSL_VERSION', '3.3.8' );
}

if( !defined( 'NBTSL_IMAGE_DIR' ) ) {
    define( 'NBTSL_IMAGE_DIR', plugin_dir_url( __FILE__ ) . 'assets/images' );
}

if( !defined( 'NBTSL_JS_DIR' ) ) {
    define( 'NBTSL_JS_DIR', plugin_dir_url( __FILE__ ) . 'assets/js' );
}

if( !defined( 'NBTSL_CSS_DIR' ) ) {
    define( 'NBTSL_CSS_DIR', plugin_dir_url( __FILE__ ) . 'assets/css' );
}

if( !defined( 'NBTSL_SETTINGS' ) ) {
    define( 'NBTSL_SETTINGS', 'social-login_settings' );
}

if( !defined( 'NBTSL_PLUGIN_DIR' ) ) {
    define( 'NBTSL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if (version_compare(get_bloginfo('version'), '4.3.1', '>=')){
	// Redefine user notification function
	if ( !function_exists('wp_new_user_notification') ) {

	    function wp_new_user_notification( $user_id, $deprecated = null, $notify = 'both' ) {
	    if ( $deprecated !== null ) {
	        _deprecated_argument( __FUNCTION__, '4.3.1' );
	    }

	    global $wpdb, $wp_hasher;
	    $user = get_userdata( $user_id );
	    if ( empty ( $user ) )
	        return;

	    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
	    // we want to reverse this for the plain text arena of emails.
	    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	    $message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
	    $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
	    $message .= sprintf(__('E-mail: %s'), $user->user_email) . "\r\n";

	    @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

	    if ( 'admin' === $notify || empty( $notify ) ) {
	        return;
	    }

	    // Generate something random for a password reset key.
	    $key = wp_generate_password( 20, false );

	    /** This action is documented in wp-login.php */
	    do_action( 'retrieve_password_key', $user->user_login, $key );

	    // Now insert the key, hashed, into the DB.
	    if ( empty( $wp_hasher ) ) {
	        require_once ABSPATH . WPINC . '/class-phpass.php';
	        $wp_hasher = new PasswordHash( 8, true );
	    }
	    $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
	    $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

	    $message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
	    $message .= __('To set your password, visit the following address:') . "\r\n\r\n";
	    $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";

	    $message .= wp_login_url() . "\r\n\r\n";
	        $message .= sprintf( __('If you have any problems, please contact us at %s.'), get_option('admin_email') ) . "\r\n\r\n";
	    $message .= __('Adios!') . "\r\n\r\n";

	    wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);
	    }
	}
}else{
	// for wordpress version less than 4.3.1
    // Redefine user notification function
	if(!function_exists( 'wp_new_user_notification' )){
	    function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
	        $user = new WP_User($user_id);

	        $user_login = stripslashes($user->user_login);
	        $user_email = stripslashes($user->user_email);

	        $message  = sprintf(__('New user registration on your site %s:'), get_option('blogname')) . "\r\n\r\n";
	        $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	        $message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";
	        $message .= __('Thanks!');

	        $headers = 'From:'.get_option('blogname').' <'.get_option('admin_email').'>' . "\r\n";
	        @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message, $headers);

	        if ( empty($plaintext_pass) )
	            return;

	        $message  = __('Hi there,') . "\r\n\r\n";
	        $message .= sprintf(__("Welcome to %s! Here's how to log in:"), get_option('blogname')) . "\r\n\r\n";
	        $message .= wp_login_url() . "\r\n";
	        $message .= sprintf(__('Username: %s'), $user_login) . "\r\n";
	        $message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n\r\n";
	        $message .= sprintf(__('If you have any problems, please contact me at %s.'), get_option('admin_email')) . "\r\n\r\n";
	        $message .= __('Thanks!');

	        $headers = 'From:'.get_option('blogname').' <'.get_option('admin_email').'>' . "\r\n";

	        wp_mail($user_email, sprintf(__('[%s] Your username and password'), get_option('blogname')), $message, $headers);

	    }
	}
}

class NBT_Solutions_Social_Login {
    /**
     * Variable to hold the initialization state.
     *
     * @var  boolean
     */
    protected static $initialized = false;
    
    public static function initialize()
    {
        if (self::$initialized) {
            return;
        }

        if (is_admin()) {
            self:: nbtsl_database_install();
        }
        require_once 'inc/frontend/login_check.php';        
        $options = get_option( NBTSL_SETTINGS );

        add_action( 'init', array(__CLASS__, 'session_init') ); //start the session if not started yet.           
        
        add_action( 'wp_enqueue_scripts', array(__CLASS__, 'register_frontend_assets') ); // registers all the assets required for the frontend
        
        if( isset($options['nbt_social-login_login_form_enable']) && $options['nbt_social-login_login_form_enable'] == '1' ) {
            add_action( 'login_form', array(__CLASS__, 'add_social_login')  ); // add the social logins to the login form
            add_action( 'woocommerce_login_form',array(__CLASS__, 'add_social_login_form_to_comment')  );
        }
        if( isset($options['nbt_social-login_register_form_enable']) && $options['nbt_social-login_register_form_enable'] == '1' ) {
            add_action( 'register_form',array(__CLASS__, 'add_social_login')  ); //add the social logins to the registration form
            add_action( 'after_signup_form',array(__CLASS__, 'add_social_login')  );
        }
        if( isset($options['nbt_social-login_comment_form_enable']) && $options['nbt_social-login_comment_form_enable'] == '1' ) {
            add_action( 'comment_form_top',array(__CLASS__, 'add_social_login_form_to_comment')  ); //add the social logins to the comment form
            add_action( 'comment_form_must_log_in_after',array(__CLASS__, 'add_social_login_form_to_comment')  );
        }
        add_shortcode( 'nbtsl-login-lite',array(__CLASS__, 'nbtsl_shortcode')  ); //adds a shortcode
            
        add_action( 'widgets_init',array(__CLASS__, 'register_nbtsl_widget')  ); //register the widget of a plugin
        add_action( 'login_enqueue_scripts', array(__CLASS__, 'nbtsl_login_form_enqueue_style'), 10 );        
        
        //woocommerce compactibility check        
        if( isset($options['nbt_social-login_wc_enable']) && $options['nbt_social-login_wc_enable'] == '1' ) {

            add_action( 'woocommerce_after_template_part', array(__CLASS__, 'wc_social_buttons_in_checkout') );
            add_action( 'woocommerce_login_form', array(__CLASS__, 'add_social_login_form_to_comment') );
        }
            
        /**
        * Hook to display custom avatars
        */
        add_filter( 'get_avatar', array(__CLASS__, 'nbtsl_social_login_custom_avatar'), 10, 5 );

        //add delete action when user is deleted from wordpress backend.
        add_action( 'delete_user', array(__CLASS__, 'nbtsl_delete_user') );

        self::$initialized = true;
    }   

    public static function wc_social_buttons_in_checkout( $template_name ) {
            if( $template_name == 'checkout/form-login.php' ) {
                $options = get_option( NBTSL_SETTINGS );
                $login_text = $options['nbt_social-login_title_text_field'];
                if( !is_user_logged_in() ) { ?>
                    <p class="woocommerce-info"><?php
                    echo $options['nbt_social-login_title_text_field']; ?> <a href="#" class="show-nbtsl-container"><?php
                    echo 'Click here to login'; ?></a> </p>
                    <form class="login nbtsl-container" style="display: none;">
                        <?php
                    echo do_shortcode( "[nbtsl-login-lite login_text='{$login_text}']" ); ?>
                    </form>
                    <?php
                }
            }
        }
        
        public static function nbtsl_social_login_custom_avatar( $avatar, $mixed, $size, $default, $alt = '' ) {
            $options = get_option( NBTSL_SETTINGS );
            //Check if we have an user identifier
            if( is_numeric( $mixed ) AND $mixed > 0 ) {
                $user_id = $mixed;
            }
            //Check if we have an user email
            elseif( is_string( $mixed ) AND( $user = get_user_by( 'email', $mixed ) ) ) {
                $user_id = $user->ID;
            }
            //Check if we have an user object
            elseif( is_object( $mixed ) AND property_exists( $mixed, 'user_id' ) AND is_numeric( $mixed->user_id ) ) {
                $user_id = $mixed->user_id;
            }
            //None found
            else {
                $user_id = null;
            }
            //User found?
            if( !empty( $user_id ) ) {
                //Override current avatar ?
                $override_avatar = true;
                //Read the avatar
                $user_meta_thumbnail = get_user_meta( $user_id, 'deuimage', true );
                //read user details
                $user_meta_name = get_user_meta( $user_id, 'first_name', true );
                
                if( isset($options['nbt_social-login_user_avatar']) && $options['nbt_social-login_user_avatar'] == 'social' ) {
                    $user_picture =( !empty( $user_meta_thumbnail ) ? $user_meta_thumbnail : '' );
                    //Avatar found?
                    if( $user_picture !== false AND strlen( trim( $user_picture ) ) > 0 ) {
                        return '<img alt="' . $user_meta_name . '" src="' . $user_picture . '" class="avatar nbtsl-avatar-social-login avatar-' . $size . ' photo" height="' . $size . '" width="' . $size . '" />';
                    }
                }
            }
            return $avatar;
        }
        //starts the session with the call of init hook
        public static function session_init() {
            if( !session_id() && !headers_sent() ) {
                session_start();
            }
        }        

        //create the table to store the user details to the plugin
        public static function nbtsl_database_install() {
            global $wpdb;

            // create user details table
            
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            if ( is_multisite() ) {
                $current_blog = $wpdb->blogid;
                // Get all blogs in the network and activate plugin on each one
                $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
                foreach ( $blog_ids as $blog_id ) {
                    switch_to_blog( $blog_id );
                    $nbtsl_userdetails = "{$wpdb->prefix}nbt_users_social";
                    $sql = "CREATE TABLE IF NOT EXISTS `$nbtsl_userdetails` (
                            id int(11) NOT NULL AUTO_INCREMENT,
                            user_id int(11) NOT NULL,
                            provider_name varchar(50) NOT NULL,
                            identifier varchar(255) NOT NULL,
                            unique_verifier varchar(255) NOT NULL,
                            email varchar(255) NOT NULL,
                            email_verified varchar(255) NOT NULL,
                            first_name varchar(150) NOT NULL,
                            last_name varchar(150) NOT NULL,
                            profile_url varchar(255) NOT NULL,
                            website_url varchar(255) NOT NULL,
                            photo_url varchar(255) NOT NULL,
                            display_name varchar(150) NOT NULL,
                            description varchar(255) NOT NULL,
                            gender varchar(10) NOT NULL,
                            language varchar(20) NOT NULL,
                            age varchar(10) NOT NULL,
                            birthday int(11) NOT NULL,
                            birthmonth int(11) NOT NULL,
                            birthyear int(11) NOT NULL,
                            phone varchar(75) NOT NULL,
                            address varchar(255) NOT NULL,
                            country varchar(75) NOT NULL,
                            region varchar(50) NOT NULL,
                            city varchar(50) NOT NULL,
                            zip varchar(25) NOT NULL,
                            UNIQUE KEY id (id),
                            KEY user_id (user_id),
                            KEY provider_name (provider_name)
                        )";
                    dbDelta( $sql );
                    restore_current_blog();
                }
            }else{
                $nbtsl_userdetails = "{$wpdb->prefix}nbt_users_social";
                $sql = "CREATE TABLE IF NOT EXISTS `$nbtsl_userdetails` (
                    id int(11) NOT NULL AUTO_INCREMENT,
                    user_id int(11) NOT NULL,
                    provider_name varchar(50) NOT NULL,
                    identifier varchar(255) NOT NULL,
                    unique_verifier varchar(255) NOT NULL,
                    email varchar(255) NOT NULL,
                    email_verified varchar(255) NOT NULL,
                    first_name varchar(150) NOT NULL,
                    last_name varchar(150) NOT NULL,
                    profile_url varchar(255) NOT NULL,
                    website_url varchar(255) NOT NULL,
                    photo_url varchar(255) NOT NULL,
                    display_name varchar(150) NOT NULL,
                    description varchar(255) NOT NULL,
                    gender varchar(10) NOT NULL,
                    language varchar(20) NOT NULL,
                    age varchar(10) NOT NULL,
                    birthday int(11) NOT NULL,
                    birthmonth int(11) NOT NULL,
                    birthyear int(11) NOT NULL,
                    phone varchar(75) NOT NULL,
                    address varchar(255) NOT NULL,
                    country varchar(75) NOT NULL,
                    region varchar(50) NOT NULL,
                    city varchar(50) NOT NULL,
                    zip varchar(25) NOT NULL,
                    UNIQUE KEY id (id),
                    KEY user_id (user_id),
                    KEY provider_name (provider_name)
                )";
                dbDelta( $sql );
            }            
        }        
        
        
        //registration of the plugins frontend assets
        public static function register_frontend_assets() {
            //register frontend scripts
            wp_enqueue_script( 'nbtsl-frontend-js', NBTSL_JS_DIR . '/frontend.js', array('jquery'), NBTSL_VERSION );
            wp_enqueue_style( 'nbtsl-frontend-css', NBTSL_CSS_DIR . '/frontend.css', '', NBTSL_VERSION );
        }
        
        //function to add the social login in the login and registration form.
        public static function add_social_login() {
            if( !is_user_logged_in() ) {
                include( 'inc/frontend/login_integration.php' );
            }
        }
        //function to add the social login in the comment form.
        public static function add_social_login_form_to_comment() {
            $options = get_option( NBTSL_SETTINGS );
            
            $login_text = $options['nbt_social-login_title_text_field'];
            if( !is_user_logged_in() ) {            
                echo do_shortcode( "[nbtsl-login-lite login_text='{$login_text}']" );
            }
        }
        //function for adding shortcode of a plugin
        public static function nbtsl_shortcode( $attr ) {
            ob_start();            
            include( 'inc/frontend/shortcode.php' );
            $html = ob_get_contents();
            ob_get_clean();
            return $html;
        }
        
        //registration of the social login widget
        public static function register_nbtsl_widget() {
            register_widget( 'NBTSL_Lite_Widget' );
        }
        
        public static function nbtsl_login_form_enqueue_style() {
            wp_enqueue_style( 'nbtsl-frontend-css', NBTSL_CSS_DIR . '/frontend.css', '', NBTSL_VERSION );
        }

        function curlPageURL() {
            $pageURL = 'http';
            if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ) {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            if ( $_SERVER["SERVER_PORT"] != "80" ) {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }
            return $pageURL;
        }


        public static function nbtsl_delete_user( $user_id ) {
            global $wpdb;
            $table_name = $nbtsl_userdetails = "{$wpdb->prefix}nbt_users_social";
            $user_obj = get_userdata( $user_id );
            $result = $wpdb->delete( $table_name, array( 'user_id' => $user_id ) );
        }
        //Sanitizes field by converting line breaks to <br /> tags
        function sanitize_escaping_linebreaks($text) {
            $text = implode( "<br \>", explode( "\n", $text ));
            return $text;
        }

        //outputs by converting <Br/> tags into line breaks
        function output_converting_br($text) {
            $text = implode( "\n", explode( "<br \>", $text ) );
            return $text;
        }
} 
