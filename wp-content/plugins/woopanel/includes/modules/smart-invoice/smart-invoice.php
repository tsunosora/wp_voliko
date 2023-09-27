<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

define('WPL_SMART_INVOICE_PATH', plugin_dir_path( __FILE__ ));
define('WPL_SMART_INVOICE_URL', plugin_dir_url( __FILE__ ));

/**
 * This class will display icon loading effect before page loaded.
 *
 * @package WooPanel_Modules
 */
class NBT_Solutions_Smart_Invoice {

  /**
   * Show loading effect.
   *
   * @var boolean
   */
  static $is_show = false;

  /**
   * Show close loading effect.
   *
   * @var boolean
   */
	static $is_closed = false;

  /**
   * Set default loading effect.
   *
   * @var boolean
   */
  static $is_checked = false;

  /**
   * The single instance of the class.
   *
   * @var NBT_Solutions_Loading_Effect
   * @since 1.0
   */
  protected static $initialized = false;
    
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

    add_action('nb_smart_invoice_admin_shortcode', array( __CLASS__, 'html_shortcode') );
    add_filter('nb_smart_invoice_shortcode', array( __CLASS__, 'replace_shortcode' ), 20, 3 );

    add_action('woocommerce_edit_account_form', array( __CLASS__, 'add_account_form') );
    add_action('woocommerce_save_account_details', array( __CLASS__, 'save_account_form' ), 20, 1 );
    add_action( 'woocommerce_before_checkout_form', array(  __CLASS__, 'checkout_form'), 99, 1 );

    add_action('woopanel_order_after_main', array( __CLASS__, 'add_download_pdf' ), 20, 1 );
    add_action( 'wp_enqueue_scripts', array(  __CLASS__, 'main_scripts'), 99, 1 );
  

    // State that initialization completed.
    self::$initialized = true;
  }

  static function html_shortcode() {
    include_once WPL_SMART_INVOICE_PATH . 'html-invoice-shortcode.php';
  }

  static function replace_shortcode( $html, $customer, $order ) {
    $items = $order->get_items();

    $product_author = array();
    foreach ( $items as $item ) {
      $product = get_post($item->get_product_id());

      if( $product ) {
        $product_author[$product->post_author] = $product->post_author;
      }
    }

    $company_name = $contact_address_line1 = $contact_address_line2 = $contact_phone = $contact_email = $contact_website = '';
    if( count($product_author) == 1 ) {
      if( function_exists('array_key_first') ) {
        $current_user = array_key_first($product_author);
      }else {
        reset($product_author);
        $current_user = key($product_author);
      }
      

      $company_name = get_user_meta( $current_user, 'contact_name', true);
      $contact_address_line1 = get_user_meta( $current_user, 'contact_address_line1', true);
      $contact_address_line2 = get_user_meta( $current_user, 'contact_address_line2', true);
      $contact_phone = get_user_meta( $current_user, 'contact_phone', true);
      $contact_email = get_user_meta( $current_user, 'contact_email', true);
      $contact_website = get_user_meta( $current_user, 'contact_website', true);

    }
    // echo $company_name;
    // echo '<br />';
    // echo $contact_adress;
    // echo '<br />';
    // echo $contact_phone;
    // die();

    if( $contact_address_line1 && $contact_address_line2 ) {
      $contact_address = '<table id="table5"><tbody>';
      if( $contact_address_line1 ) {
        $contact_address .= '<tr><td style="text-transform: uppercase; color: #7f7f7f;">'.$contact_address_line1.'</td></tr>';
        $html = str_replace('[contact_address_line1]', $contact_address_line1, $html);
      }
      
      if( $contact_address_line2 ) {
        $contact_address .= '<tr><td style="text-transform: uppercase; color: #7f7f7f;">'.$contact_address_line2.'</td></tr>';
        $html = str_replace('[contact_address_line2]', $contact_address_line2, $html);
      }

      $contact_address .= '</tbody></table>';
    }



    $html = str_replace('[contact_name]', $company_name, $html);
    $html = str_replace('[contact_address]', $contact_address, $html);
    $html = str_replace('[contact_phone]', $contact_phone, $html);
    $html = str_replace('[contact_email]', $contact_email, $html);
    $html = str_replace('[contact_website]', $contact_website, $html);

    return $html;
  }

  static function add_account_form() {
    global $current_user;


    $defaults = array(
      'address' => '',
      'city' => '',
      'country' => '',
      'phone' => ''
    );

    $value_account = get_user_meta($current_user->ID, '_my_account', true);

    $my_account = wp_parse_args($value_account, $defaults);


    $country_setting = (string) $my_account['country'];

    if ( strstr( $country_setting, ':' ) ) {
      $country_setting = explode( ':', $country_setting );
      $country         = current( $country_setting );
      $state           = end( $country_setting );
    } else {
      $country = $country_setting;
      $state   = '*';
    }



    include_once WPL_SMART_INVOICE_PATH . 'html-my-account.php';
  }

  static function save_account_form( $user_id ) {
    $account_address = ! empty( $_POST['account_address'] ) ? wc_clean( wp_unslash( $_POST['account_address'] ) ) : '';
    $account_city = ! empty( $_POST['account_city'] ) ? wc_clean( wp_unslash( $_POST['account_city'] ) ) : '';
    $account_phone = ! empty( $_POST['account_phone'] ) ? wc_clean( wp_unslash( $_POST['account_phone'] ) ) : '';
    $account_country = ! empty( $_POST['account_country'] ) ? wc_clean( wp_unslash( $_POST['account_country'] ) ) : '';

    $extra_data = array(
      'address' => $account_address,
      'city' => $account_city,
      'country' => $account_country,
      'phone' => $account_phone
    );

    update_user_meta( $user_id, '_my_account', $extra_data);
  }

  static function checkout_form() {
    wp_enqueue_script('nb-invoice-checkout');
  }

  static function add_download_pdf( $order ) {
    global $admin_options;

    $url = add_query_arg( array(
      'order_id' => $order->get_id(),
      'type' => 'invoice',
      'key' => get_post_meta($order->get_id(), '_order_key', true)
    ), home_url('nb_invoice/confirm') );

    $handle = curl_init($url);
    curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($handle);
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    curl_close($handle);

    if( $httpCode ) {
        ?>
        <a href="<?php echo esc_url($url);?>" type="submit" name="publish" class="btn btn-primary m-btn m-loader--light m-loader--right" id="publish"><?php echo esc_html__('Download Invoice', 'woopanel');?></a>
        <?php
    }else {
      ?>
      <a href="<?php echo esc_url($url);?>" type="submit" name="publish" class="btn btn-primary m-btn m-loader--light m-loader--right" id="publish">Error: 404</a>
      <?php
    }

  }

  static function main_scripts() {
    global $current_user;

    if( $current_user->exists() ) {
      $_my_account = get_user_meta( $current_user->ID, '_my_account', true);

      if( $_my_account ) {
        wp_register_script('nb-invoice-checkout', WPL_SMART_INVOICE_URL . 'checkout.js', array(), '1.0.0', true);
        
        wp_localize_script( 'nb-invoice-checkout', 'WooPanel_Checkout', array(
          'address' => $_my_account['address'],
          'city' => $_my_account['city'],
          'country' => $_my_account['country'],
          'phone' => $_my_account['phone']
        ) );
      }
    }
  }
}

/**
 * Returns the main instance of NBT_Solutions_Smart_Invoice.
 *
 * @since  1.0.0
 * @return NBT_Solutions_Smart_Invoice
 */
NBT_Solutions_Smart_Invoice::initialize();