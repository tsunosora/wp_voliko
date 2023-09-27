<?php
$explode = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
include($explode[0] . 'wp-load.php');

$file = absint($_REQUEST['file']);

if(is_numeric($file)){
	global $wpdb;
	$files = $wpdb->get_row( "SELECT post_title, post_name, post_mime_type FROM {$wpdb->prefix}posts WHERE ID = '".$file."'" );
	if($files){
    	$upload_dir = wp_upload_dir();
    	$basedir_folder = $upload_dir['basedir'].'/nbt-order-uploads/';
		$extension = explode('/', $files->post_mime_type);

		if( isset($extension[1]) ) {
			$filename = $files->post_name.'.'.$extension[1];
		}else {
			$filename = $files->post_name.'.'.$files->post_mime_type;
		}

		$path_file = $basedir_folder.$filename;


		$mime_type = "application/octet-stream";


		if (file_exists($path_file)) {

			if ( ob_get_level() ) {
				ob_end_clean();
			}

		    header("Pragma: public");
		    header("Expires: 0");
		    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		    header("Cache-Control: public");
		    header("Content-Description: File Transfer");
		    header("Content-Type: " . $mime_type);
		    header("Content-Length: " .(string)(filesize($path_file)) );
		    header('Content-Disposition: attachment; filename="'.basename($path_file).'"');
		    header("Content-Transfer-Encoding: binary\n");
		    readfile($path_file); // outputs the content of the file
		} else {
		    die('File could not be found, is it deleted?');
		}
	}
}
?>