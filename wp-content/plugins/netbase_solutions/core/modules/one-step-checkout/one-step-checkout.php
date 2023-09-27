<?php
/**
 * Plug additional sidebars into WordPress.
 *
 * @package  Package Name
 * @since    1.0
 */
define('NBT_OSC_PATH', plugin_dir_path( __FILE__ ));
define('NBT_OSC_URL', plugin_dir_url( __FILE__ ));

class NBT_Solutions_One_Step_Checkout {
	
    /**
     * Variable to hold the initialization state.
     *
     * @var  boolean
     */
    protected static $initialized = false;

    public static $types = array();
    
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

        if ( ! function_exists( 'WC' ) ) {
            add_action( 'admin_notices', array( __CLASS__, 'install_woocommerce_admin_notice') );
        }else{

            // add_action( 'init', array( __CLASS__, 'check_init' ), 9999 );
			
            require_once NBT_OSC_PATH . 'inc/frontend.php';
			
			require_once NBT_OSC_PATH . 'inc/template.php';
			
			//self::install_page_checkout();


        }
        // Register actions to do something.
        //add_action( 'action_name'   , array( __CLASS__, 'method_name'    ) );
        // State that initialization completed.
        self::$initialized = true;
    }
	
	public static function install_page_checkout() {
		
		$checkout = array(
			'name'    => _x( 'nb-checkout', 'Page slug', 'nbt-solution' ),
			'title'   => _x( 'One Page Checkout', 'Page title', 'nbt-solution' ),
			'content' => '[' . apply_filters( 'netbase_checkout_shortcode_tag', 'nb_checkout' ) . ']',
		);
		
		$page_id = self::wc_create_page( esc_sql( $checkout['name'] ), 'netbase_checkout_page_id', $checkout['title'], $checkout['content'], ! empty( $checkout['parent'] ) ? wc_get_page_id( $checkout['parent'] ) : '' );
		update_post_meta($page_id, '_wp_page_template', 'nb-checkout.php');
	}
	
	/**
	 * Retrieve page ids - used for myaccount, edit_address, shop, cart, checkout, pay, view_order, terms. returns -1 if no page is found.
	 *
	 * @param string $page Page slug.
	 * @return int
	 */
	public static function nb_get_page_id( $page ) {
		if ( 'pay' === $page || 'thanks' === $page ) {
			wc_deprecated_argument( __FUNCTION__, '2.1', 'The "pay" and "thanks" pages are no-longer used - an endpoint is added to the checkout instead. To get a valid link use the WC_Order::get_checkout_payment_url() or WC_Order::get_checkout_order_received_url() methods instead.' );

			$page = 'checkout';
		}
		if ( 'change_password' === $page || 'edit_address' === $page || 'lost_password' === $page ) {
			wc_deprecated_argument( __FUNCTION__, '2.1', 'The "change_password", "edit_address" and "lost_password" pages are no-longer used - an endpoint is added to the my-account instead. To get a valid link use the wc_customer_edit_account_url() function instead.' );

			$page = 'myaccount';
		}

		$page = apply_filters( 'netbase_checkout_page_id', get_option( 'netbase_'. $page .'_page_id' ) );

		return $page ? absint( $page ) : -1;
	}
	
	public static function nb_get_checkout_url() {
		$checkout_url = get_permalink(get_option( 'netbase_checkout_page_id' ));
		if ( $checkout_url ) {
			// Force SSL if needed.
			if ( is_ssl() || 'yes' === get_option( 'woocommerce_force_ssl_checkout' ) ) {
				$checkout_url = str_replace( 'http:', 'https:', $checkout_url );
			}
		}
		
		return apply_filters( 'nb_get_checkout_url', $checkout_url );
	}
	
	/**
	 * Create a page and store the ID in an option.
	 *
	 * @param mixed  $slug Slug for the new page
	 * @param string $option Option name to store the page's ID
	 * @param string $page_title (default: '') Title for the new page
	 * @param string $page_content (default: '') Content for the new page
	 * @param int    $post_parent (default: 0) Parent for the new page
	 * @return int page ID
	 */
	public static function wc_create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
		global $wpdb;

		$option_value = get_option( $option );

		if ( $option_value > 0 && ( $page_object = get_post( $option_value ) ) ) {
			if ( 'page' === $page_object->post_type && ! in_array( $page_object->post_status, array( 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
				// Valid page is already in place
				return $page_object->ID;
			}
		}

		if ( strlen( $page_content ) > 0 ) {
			// Search for an existing page with the specified page content (typically a shortcode)
			$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
		} else {
			// Search for an existing page with the specified page slug
			$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
		}

		$valid_page_found = apply_filters( 'woocommerce_create_page_id', $valid_page_found, $slug, $page_content );

		if ( $valid_page_found ) {
			if ( $option ) {
				update_option( $option, $valid_page_found );
			}
			return $valid_page_found;
		}

		// Search for a matching valid trashed page
		if ( strlen( $page_content ) > 0 ) {
			// Search for an existing page with the specified page content (typically a shortcode)
			$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
		} else {
			// Search for an existing page with the specified page slug
			$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
		}

		if ( $trashed_page_found ) {
			$page_id   = $trashed_page_found;
			$page_data = array(
				'ID'          => $page_id,
				'post_status' => 'publish',
			);
			wp_update_post( $page_data );
		} else {
			$page_data = array(
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'post_name'      => $slug,
				'post_title'     => $page_title,
				'post_content'   => $page_content,
				'post_parent'    => $post_parent,
				'comment_status' => 'closed',
			);
			$page_id   = wp_insert_post( $page_data );
		}

		if ( $option ) {
			update_option( $option, $page_id );
		}

		return $page_id;
	}



    public static function check_init(){

        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $page_id = url_to_postid($actual_link);
        if(wc_get_page_id( 'cart' ) == $page_id){
            $option = get_option('one-step-checkout_settings');
            if(isset($option['wc_one_step_checkout_redirect_cart']) && $option['wc_one_step_checkout_redirect_cart']){
                wp_redirect(wc_get_checkout_url());
                die();
            }
        }
    }
}