<?php 

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if(isset($_REQUEST['key']) && $key = $_REQUEST['key'] ){
    global $wpdb, $PDF_Admin, $current_user;

    if( ! preg_match('/checkout\/order-received\/([0-9]+)\//', $_SERVER['HTTP_REFERER'], $output_array) && ! $current_user->exists() ) {
        wp_die( esc_html__('Please login to view this PDF Invoice!', 'nbt-solution') );
    }

    $order_id = $wpdb->get_var( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_order_key' AND meta_value = '".$key."'" );

    if( $order_id && is_numeric($order_id) ) {
        $order = wc_get_order($order_id);
        $settings = NB_Solution::get_setting('pdf-creator');
        $module_id = NBT_Pdf_Creator_Settings::$id;

        $callback = 'get_template_' . $settings['nbt_'.$module_id.'_template'];

        if( ! method_exists('NBT_Solutions_PDF_Template', $callback) ) {
            $error = true;
            $callback_error = sprintf( __('Callback function %s not exists!', 'nbt-solution'), '<strong>'. $callback .'</strong>' );

            if( $PDF_Admin->is_ajax() ) {
                $json['message'] = $callback_error;
            }else {
                wp_die( $callback_error );
            }
        }



        $loadHtml = $PDF_Admin->display_header($settings, $module_id, false); 

        $loadHtml .= NBT_Solutions_PDF_Template::$callback( $order, $settings, false );
        $loadHtml .= '<style>
        body.preview {
            max-width: 800px;
            margin: 0 auto;
        }
        </style>';
        $loadHtml .= $PDF_Admin->display_footer($settings, $module_id);

        $loadHtml .= sprintf('<button type="button" class="button btn btn-link btn-print-pdf"><i class="nbt-icon-file-pdf"></i> %s</button>', 'Print PDF');


        echo $loadHtml;


    }
}
?>