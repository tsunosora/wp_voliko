<?php
/**
 * @version    1.0
 * @package    Package Name
 * @author     Your Team <support@yourdomain.com>
 * @copyright  Copyright (C) 2014 yourdomain.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

/**
 * Plug additional sidebars into WordPress.
 *
 * @package  Package Name
 * @since    1.0
 */
define('NBT_NOTI_PATH', plugin_dir_path( __FILE__ ));
define('NBT_NOTI_URL', plugin_dir_url( __FILE__ ));
if( !defined( 'NBT_NOTI_SETTINGS' ) ) {
    define( 'NBT_NOTI_SETTINGS', 'product-notification_settings' );
}
class NBT_Solutions_Product_Notification {
	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;

	private static $settings_saved;

	/**
	 * Initialize functions.
	 *
	 * @return  void
	 */
	public static function initialize() {
		// Do nothing if pluggable functions already initialized.
		if ( self::$initialized ) {
			return;
		}
		self::$settings_saved = false;
		// DB - Create table on first activation 
		//register_activation_hook( __FILE__, array(__CLASS__, 'instock_email_notification_install')  );
		if (is_admin()) {
            self:: instock_email_notification_install();
        }
		// DB - Versioning
		global $instock_email_notification_db_version;
		$instock_email_notification_db_version = '2.0';
		// DB - Update
		add_action( 'plugins_loaded', array(__CLASS__, 'instock_email_notification_update_db_check'));
		// Email Notifications - Send Email
		add_action('woocommerce_product_set_stock_status', array(__CLASS__, 'instock_email_notification_check_status'), 10, 2);
		/*if ( isset($_POST['alert_email']) && !empty($_POST['alert_email']) ) {
		    $the_email = $_POST['alert_email'];
		    $id = $_POST['alert_id'];
		    if ( filter_var($the_email, FILTER_VALIDATE_EMAIL) && is_numeric($id) ) {
		        self::instock_email_notification_save_email($the_email, $id);
		        add_filter( 'woocommerce_single_product_summary', array(__CLASS__, 'instock_email_notification_save_sent'), 80 );
		    } else {
		        add_filter( 'woocommerce_single_product_summary', array(__CLASS__, 'instock_email_notification_save_error'), 80 );
		    }
		}*/

		// Email Notifications - Remove from DB
		if ( !empty($_POST) && isset($_POST['remove_date']) && isset($_POST['remove_email']) && isset($_POST['remove_product'])) {
		    $date = $_POST['remove_date'];
		    $email = $_POST['remove_email'];
		    $productid = $_POST['remove_product'];
		    global $wpdb;
		    $table_name = $wpdb->prefix . "instock_email_notification";
		    $wpdb->delete ( $table_name, array('date' => $date, 'user_email' => $email, 'product_id' => $productid), array( '%s', '%s', '%d' ) );
		}
		// Add notification form
		add_filter( 'woocommerce_single_product_summary', array(__CLASS__, 'instock_email_notification_form'), 70 );
		
		add_action( 'admin_print_scripts',array(__CLASS__,  'instock_email_notification_include_admin_css') );
		 add_action( 'wp_enqueue_scripts', array(__CLASS__, 'register_frontend_assets') );
		/**
		* Load modules
		*/
		include('inc/frontend.php');

		// State that initialization completed.
		 add_action('admin_menu', array(__CLASS__,  'register_subpage_noti') );
		self::$initialized = true;
	}
	public static function register_frontend_assets() {
        wp_enqueue_script( 'nbtnoti-frontend-js', plugin_dir_url( __FILE__ ) . 'assets/js/frontend.js', array('jquery'), '1.0' );
        wp_enqueue_style( 'nbtnoti-frontend', plugin_dir_url( __FILE__ ) . 'assets/css/frontend.css');
    }
	public static function register_subpage_noti() {
        if( defined('PREFIX_NBT_SOL') && defined('PREFIX_NBT_SOL_DEV') && PREFIX_NBT_SOL_DEV ) {
            add_submenu_page('solutions', 'Product Notification', 'Product Notification', 'manage_options', 'product-notification', array(__CLASS__,  'print_plugin_options')); 
        }else{
            add_submenu_page('solution-dashboard', 'Product Notification', 'Product Notification', 'manage_options', 'product-notification', array(__CLASS__,  'print_plugin_options')); 
        }       

    }
    public static function print_plugin_options() {
        ?>
        <div id="instock_alert_options">
	        <table>
			 <tr valign="top">
	                <td colspan="2">
	                    <div class="filters">
	                        <span>Filters</span>
	                        <input type="radio" name="filter" id="filter_all" class="filter" checked /><label for="filter_all">Show All</label>
	                        <input type="radio" name="filter" id="filter_waiting" class="filter" /><label for="filter_waiting">Waiting</label>
	                        <input type="radio" name="filter" id="filter_sent" class="filter" /><label for="filter_sent">Sent</label>
	                    </div>
	                    <ul id="subscribed_list">
	                         <li class="header">
	                            <div class="date">Date</div>
	                            <div class="email">Email Address</div>
	                            <div class="product">Product</div>
	                            <div class="status">Status</div>
	                            <div class="remove">Remove</div>
	                        </li>
	                        <?php
	                        global $wpdb;
	                        $table_name = $wpdb->prefix . "instock_email_notification";
	                        $users = $wpdb->get_results("SELECT * FROM `".$table_name."`");
	                        foreach ($users as $user) {
	                            $prod_title = get_the_title($user->product_id);
	                            $prod_link = get_the_permalink($user->product_id);
	                        ?>
	                            <li class="user <?php echo $user->status == 1 ? 'sent' : 'waiting'; ?>">
	                                <div class="date"><?php echo $user->date; ?></div>
	                                <div class="email"><?php echo $user->user_email; ?></div>
	                                <div class="product"><a href="<?php echo $prod_link; ?>" title="<?php echo $prod_title; ?>" target="_blank"><?php echo $prod_title; ?></a></div>
	                                <div class="status">    <?php echo $user->status == 1 ? 'Sent' : 'Waiting'; ?></div>
	                                <div class="remove">
	                                    <form action="" method="POST">
	                                        <input type="hidden" name="remove_date" value="<?php echo $user->date; ?>" />
	                                        <input type="hidden" name="remove_email" value="<?php echo $user->user_email; ?>" />
	                                        <input type="hidden" name="remove_product" value="<?php echo $user->product_id; ?>" />
	                                        <input type="submit" name="remove_entry" value="remove" />
	                                    </form>
	                                </div>
	                            </li>
	                        <?php } ?>
	                    </ul>
	                    <div class="expand"><span>Show More</span></div>
	                </td>
	            </tr>
	        </table>
    	</div>
        <?php
    }
	
	public static function instock_email_notification_include_admin_css() {
		wp_enqueue_style('product_noti_admin_style', plugin_dir_url( __FILE__ ) . 'assets/css/backend.css');
	    
	    wp_enqueue_script('jquery');
	    wp_enqueue_script( 'product_noti_admin_js', plugin_dir_url( __FILE__ ) . 'assets/js/product-notification-admin.js');
	}	

	public static function instock_email_notification_form($type = NULL){
	    global $product;
	    $options_settings = get_option( NBT_NOTI_SETTINGS );
	    $stock = $product->get_stock_quantity();
	    if ( !$stock > 0  && !$product->is_in_stock() ) {
	        if ( isset($options_settings['nbt_product_notification_form_placeholder']) && $options_settings['nbt_product_notification_form_placeholder'] ) {
	            $placeholder = $options_settings['nbt_product_notification_form_placeholder'] ;
	        } else {
	            $placeholder = 'Email address';
	        }
	        if (isset($options_settings['nbt_product_notification_form_button']) && $options_settings['nbt_product_notification_form_button'] ) {
	            $submit_value = $options_settings['nbt_product_notification_form_button'];
	        } else {
	            $submit_value = 'Notify me when in stock';
	        }
	        $form_desc = '';
	        if (isset($options_settings['nbt_product_notification_desc']) && $options_settings['nbt_product_notification_desc'] ) {
	        	$form_desc = '<div class="product-noti-desc">'.$options_settings['nbt_product_notification_desc'].'</div>';
	        }
	        $form = ''.$form_desc.'

	            <form action="" method="post" class="alert_wrapper">
	                <input type="email" name="alert_email" id="alert_email" placeholder="' . $placeholder . '" />
	                <input type="hidden" name="alert_id" id="alert_id" value="' . get_the_ID() . '"/>
	                <input type="submit" value="' . $submit_value . '" class="pnotisubmit" />
	            </form> <div class="nbt-alert-msg"></div>
	        ';
	        /*if ($type == 'get') {
	            return $form;
	        } 
	        else {
	            if ( get_option('instock_email_option_shortcode') != 'on' ) {
	                echo $form;
	            }
	        }*/
	        echo $form;
	    }
	}
	public static function instock_email_notification_save_error(){
		$options_settings = get_option( NBT_NOTI_SETTINGS );
		if ( isset($options_settings['nbt_product_notification_form_error']) && $options_settings['nbt_product_notification_form_error'] ) {
			$options_error = $options_settings['nbt_product_notification_form_error'];
		}else{
			$options_error = 'Invalid email address.';
		}
		
		echo '<div class="instock_message error">' . $options_error . '</div>';
	}

	public static function instock_email_notification_save_sent(){
		$options_settings = get_option( NBT_NOTI_SETTINGS );
		if ( isset($options_settings['nbt_product_notification_form_success']) && $options_settings['nbt_product_notification_form_success'] ) {
			$options_success = $options_settings['nbt_product_notification_form_success'];
		}else{
			$options_success = 'Thank you. We will notify you when the product is in stock.';
		}
		
		echo '<div class="instock_message sent">' . $options_success . '</div>';
	}

	public static function instock_email_notification_check_status($productId, $status) {
		$options = get_option( NBT_NOTI_SETTINGS );
	    if ($status == "instock"){	
	        // Product details
	        $prod_title = get_the_title($productId);
	        $prod_link = get_the_permalink($productId);
	        // Options - Sender
	        if ( isset($options['nbt_product_notification_email_sender']) && $options['nbt_product_notification_email_sender'] ) {
	            $options_sender = $options['nbt_product_notification_email_sender'];
	        } else {
	            $options_sender = get_option('blogname');
	        }
	        // Options - From
	        if ( isset($options['nbt_product_notification_email_from']) && $options['nbt_product_notification_email_from']  ) {
	            $options_from = $options['nbt_product_notification_email_from'];
	        } else {
	            $options_from = get_option('admin_email');
	        }
	        // Options - Subject
	        if ( isset($options['nbt_product_notification_email_subject']) && $options['nbt_product_notification_email_subject'] ) {
	            $options_subject = $options['nbt_product_notification_email_subject'];
	        } else {
	            $options_subject = 'Your product is on stock now!';
	        }
			$options_subject = str_replace('%product_name%', $prod_title, $options_subject);
			$options_subject = str_replace('%product_link%', $prod_link, $options_subject);
	        // Options - Message
	        if ( isset($options['nbt_product_notification_email_message']) && $options['nbt_product_notification_email_message'] ) {
	            $options_message = $options['nbt_product_notification_email_message'];
	        } else {
	            $options_message = 'Hello, The product %product_name% is on stock. You can purchase it here: %product_link%';
	        }
	        $options_message = str_replace('%product_name%', $prod_title, $options_message);
	        $options_message = str_replace('%product_link%', $prod_link, $options_message);
	        // If out of stock
	        $users = array();
	        global $wpdb;
	        $table_name = $wpdb->prefix . "instock_email_notification";
	        // Grab all the user emails for this product
	        $emails = $wpdb->get_results("SELECT * FROM `".$table_name."` WHERE product_id = '$productId' AND status = 0");
	        foreach ( $emails as $email ) {
	            $user_email = $email->user_email;
	            $headers = 'From: '.$options_sender.' <'.$options_from.'>' . "\r\n";
	            wp_mail( $user_email, $options_subject, $options_message, $headers);
	            // Set status
	            $status = $wpdb->get_results("UPDATE `".$table_name."` SET status = 1 WHERE product_id = '$productId' AND status = 0 AND user_email = '$user_email'");
	        }
	    }
	}


	// Email Notifications - Save to DB
	public static function instock_email_notification_save_email($email, $productid){
	    global $wpdb;
	    $table_name = $wpdb->prefix . "instock_email_notification";
	    $date = date('d-m-Y h:i:s');
	    $wpdb->insert( $table_name, array( 'date' => $date, 'user_email' => $email, 'product_id' => $productid, 'status' => 0), array( '%s', '%s', '%d', '%d' ) );
	}

	public static function instock_email_notification_update_db_check() {
	    global $instock_email_notification_db_version;
	    if ( get_site_option( 'instock_email_notification_db_version' ) != $instock_email_notification_db_version ) {
	        self::instock_email_notification_install();
	    }
	}
	public static function instock_email_notification_install () {
	    global $wpdb;
	    global $instock_email_notification_db_version;
	    $table_name = $wpdb->prefix . "instock_email_notification";
	    $charset_collate = $wpdb->get_charset_collate();
	    $sql = "CREATE TABLE $table_name (
	        id mediumint(9) NOT NULL AUTO_INCREMENT,
	        date MEDIUMTEXT NOT NULL,
	        user_email MEDIUMTEXT NOT NULL,
	        product_id MEDIUMINT(9) NOT NULL,
	        status TINYINT(1) DEFAULT NULL,
	        UNIQUE KEY id (id)
	    ) $charset_collate;";
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta( $sql );
	    add_option( 'instock_email_notification_db_version', $instock_email_notification_db_version );
	}


}

