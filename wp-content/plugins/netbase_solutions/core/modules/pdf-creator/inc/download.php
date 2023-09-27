<?php
$explode = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
include($explode[0] . 'wp-load.php');

$order_id = absint($_REQUEST['order_id'] );
if( is_numeric($order_id) && $order = wc_get_order($order_id) ) {
	$filename = $_REQUEST['filename'];
	$upload_dir = wp_upload_dir();
	$file_download = $upload_dir['path'] . '/' . $filename;

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file_download).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_download));
    flush();
    readfile($file_download);
}