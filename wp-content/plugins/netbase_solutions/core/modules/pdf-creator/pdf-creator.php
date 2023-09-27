<?php
/**
 * @version    2.0.0
 * @package    PDF Creator
 * @author     Netbase Team <anh.lt@netbasejsc.com>
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 * @since      05/09/2019
 *
 */

define('NBT_PDF_PATH', plugin_dir_path( __FILE__ ));
define('NBT_PDF_URL', plugin_dir_url( __FILE__ ));

class NBT_Solutions_PDF_Creator {

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

        if ( function_exists( 'WC' ) ) {
            require_once NBT_PDF_PATH . 'inc/admin.php';
            require_once NBT_PDF_PATH . 'inc/template.php';
            require_once NBT_PDF_PATH . 'inc/admin.php';
            require_once NBT_PDF_PATH . 'inc/preview.php';

            if( ! defined('PREFIX_NBT_SOL')){
                require_once NBT_PDF_PATH . '/inc/settings.php';
            }

            if( ! is_admin() ) {
                require_once NBT_PDF_PATH . '/inc/frontend.php';
            }

            if( ! get_option('_create_page_pdf') ) {
                $create = array(
                    'post_title' => 'PDF Preview',
                    'post_type' => 'page',
                    'post_status' => 'publish',
                    'post_content' => '',
                    'post_slug' => 'pdf-preview'
                );
                $post_id = wp_insert_post($create);

                update_post_meta($post_id, '_wp_page_template', 'preview.php');
                update_option('_create_page_pdf', $post_id);
            }

        }
        // State that initialization completed.
        self::$initialized = true;
    }



    public static function get_temp(){
        $upload_dir = wp_upload_dir();
        return trailingslashit( $upload_dir['basedir'] );
    }

    public static function wpdf_headers( $filename, $mode = 'inline', $pdf = null ) {
        switch ($mode) {
            case 'download':
                header('Content-Description: File Transfer');
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="'.$filename.'"'); 
                header('Content-Transfer-Encoding: binary');
                header('Connection: Keep-Alive');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                break;
            case 'inline':
            default:
                header('Content-type: application/pdf');
                header('Content-Disposition: inline; filename="'.$filename.'"');
                break;
        }
    }
    public static function downloadFile($url, $filename) {
        $cURL = curl_init($url);
        curl_setopt_array($cURL, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FILE           => fopen("Downloads/$filename", "w+"),
            CURLOPT_USERAGENT      => $_SERVER["HTTP_USER_AGENT"]
        ]);

        $data = curl_exec($cURL);
        curl_close($cURL);
        header("Content-Disposition: attachment; filename=\"$filename\"");
        echo $data;
    }
    
    public static function convert_price($price, $_preview){
        global $_product;
        if(class_exists('NBT_Solutions_Currency_Switcher')){
            $rate = 1;
            $pos = '%1$s%2$s';
            $symbol = get_woocommerce_currency_symbol(get_option('woocommerce_currency'));
            $decimals = get_option('woocommerce_price_num_decimals' );

            if(method_exists(WC()->session,'get')){
                $ss_wc_currency = WC()->session->get('wc_currency');
                if($ss_wc_currency){
                    $settings_mcs = get_option('settings_mcs');
                    $currency = $settings_mcs[$ss_wc_currency];
                    $rate = $currency['rates'];
                    $position = $currency['position'];
                    $decimals = $currency['decimals'];
                    $symbol = $currency['symbol'];
                    

                    switch ($position) {
                        case 'left' :
                        $pos = '%1$s%2$s';
                        break;
                        case 'right' :
                        $pos = '%2$s%1$s';
                        break;
                        case 'left_space' :
                        $pos = '%1$s&nbsp;%2$s';
                        break;
                        case 'right_space' :
                        $pos = '%2$s&nbsp;%1$s';
                        break;
                    }
                    $final_price = $price * $rate;
                    $final_price = number_format( $final_price, $decimals, wc_get_price_decimal_separator(), wc_get_price_thousand_separator() );
                    $pos = str_replace('%2$s', $final_price, $pos);
                    echo str_replace('%1$s', $symbol, $pos);


                }else{

                }
            }


                            // return ac_get_price($_product, array(
                            //     'rate' => $ajaxcart_rate,
                            //     'position' => $ajaxcart_pos,
                            //     'symbol' => $ajaxcart_symbol,
                            //     'decimals' => $ajaxcart_decimals
                            // ));

        }

        
    }
}

