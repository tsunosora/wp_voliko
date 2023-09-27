<?php
$explode = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
include($explode[0] . 'wp-load.php');

$order = absint($_REQUEST['order']);

$order = new WC_Order($order);
if ($order) {
    $order_upload = get_post_meta($order->get_id(), 'order_upload', true);
    if ($order_upload) {
        $zip_files = array();
        $upload_dir = wp_upload_dir();
        $basedir_folder = $upload_dir['basedir'] . '/nbt-order-uploads/';
        foreach ($order_upload as $product_id => $files) {
            foreach ($files as $key => $file) {
                $files = $wpdb->get_row("SELECT post_title, post_name, post_mime_type FROM {$wpdb->prefix}posts WHERE ID = '" . $file . "'");
                if ($files) {
                    $extension = explode('/', $files->post_mime_type);
                    if( isset($extension[1]) ) {
                        $filename = $files->post_name.'.'.$extension[1];
                    }else {
                        $filename = $files->post_name.'.'.$files->post_mime_type;
                    }
                    $path_file = $basedir_folder . $filename;

                    $zip_files[$path_file] = $files->post_title;
                }
            }

        }

        if (!empty($zip_files)) {
            NBT_Solutions_Order_Upload::zip_and_download($zip_files, 'order-uploads-' . $order->get_id() . '.zip');
        }
    }
}


?>