<?php
class NBT_Order_Upload_Ajax{

	protected static $initialized = false;
	
    /**
     * Initialize functions.
     *
     * @return  void
     */
    public static function initialize() {
        if ( self::$initialized ) {
            return;
        }

	    self::admin_hooks();
        self::$initialized = true;
    }


    public static function admin_hooks(){

		add_action( 'wp_ajax_nopriv_nbt_ou_variations_show', array( __CLASS__, 'nbt_ou_variations_show') );
		add_action( 'wp_ajax_nbt_ou_variations_show', array( __CLASS__, 'nbt_ou_variations_show') );


		add_action( 'wp_ajax_nopriv_nbt_order_upload', array( __CLASS__, 'nbt_order_upload') );
		add_action( 'wp_ajax_nbt_order_upload', array( __CLASS__, 'nbt_order_upload') );

		add_action( 'wp_ajax_nopriv_nbt_ou_remove', array( __CLASS__, 'nbt_ou_remove') );
		add_action( 'wp_ajax_nbt_ou_remove', array( __CLASS__, 'nbt_ou_remove') );

		add_action( 'wp_ajax_nopriv_nbtou_remove_files', array( __CLASS__, 'nbtou_remove_files') );
		add_action( 'wp_ajax_nbtou_remove_files', array(  __CLASS__, 'nbtou_remove_files') );

		add_action( 'wp_ajax_nopriv_submit_files', array( __CLASS__, 'nbtou_submit_files') );
		add_action( 'wp_ajax_submit_files', array(  __CLASS__, 'nbtou_submit_files') );

		add_action( 'wp_ajax_nopriv_ou_load_media', array( __CLASS__, 'nbt_ou_load_media') );
		add_action( 'wp_ajax_ou_load_media', array(  __CLASS__, 'nbt_ou_load_media') );
	}
	
	public static function nbt_ou_variations_show() {
		$json = array();
		$variation_id = $_REQUEST['variation_id'];
		$order_upload = @unserialize($_SESSION['order_upload']);
		$upload_dir = wp_upload_dir();
		$destination_folder = $upload_dir['baseurl'].'/nbt-order-uploads/';

		if( isset($order_upload[$variation_id]) ) {
			$li = '';
			$json['complete'] = true;
			
			foreach( $order_upload[$variation_id] as $k => $v ) {
				$attach = get_post($v);
				if( $attach && $attach->post_mime_type != '' ) {
					$extension = explode('/', $attach->post_mime_type);
					$filename = $attach->post_name.'.'.$extension[1];
					$post_thumbnail_url = $destination_folder.'thumb_'.$filename;

					$li .= '
					<div id="'. $attach->post_name .'" class="nbt-file success">
						<div class="nbt-file-left"><img width="50" src="'. $post_thumbnail_url .'"></div>
						<div class="nbt-file-right">
							<div class="name">'. $attach->post_title .' <i class="nbt-icon-cancel"></i></div>
							<div class="size"> '. NBT_Solutions_Order_Upload::format_size(get_post_meta($attach->ID, '_wp_attached_size', true)) .'</div>
							<div class="nbt-ou-msg" style="display: none;"></div>
						</div>
					</div>';
				}
			}
			$json['tpl'] = $li;
		}else {
			$json['error'] = true;
		}


		wp_send_json($json);
	}

    public function nbt_ou_remove(){
    	global $wpdb;
    	$upload_dir = wp_upload_dir();
    	$destination_folder = $upload_dir['basedir'].'/nbt-order-uploads/';
    	$customer_id = $_SESSION['guest_id'];


		$file_id = $_REQUEST['file'];
    	
		$product_id = md5($_REQUEST['product_id'] );
		if( isset($_REQUEST['variation_id']) && ! empty( $_REQUEST['variation_id'] ) ) {
			$product_id = $_REQUEST['variation_id'];
		}
		

		$file = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}posts WHERE post_name LIKE '%".$file_id."%' AND comment_count = 1 AND post_type = 'ou_attachment'" );


		if($file){
			$order_upload = @unserialize($_SESSION['order_upload']);
			if( isset($order_upload[$product_id]) ) {
				if ( ($key = array_search($file->ID, $order_upload[$product_id]) ) !== false) {

					$extension = explode('/', $file->post_mime_type);
					$filename = $file->post_name.'.'.$extension[1];

					$post_url = $destination_folder.$filename;
					$post_thumbnail_url = $destination_folder.'thumb_'.$filename;

					$order_upload[$product_id] = array_values($order_upload[$product_id]);

					unset($order_upload[$product_id][$key]);
					unlink($post_url);
					unlink($post_thumbnail_url);

					wp_delete_post($file->ID);

					/* Find and remove all */
					foreach( $order_upload as $k => $v) {
						if ( !empty($v) && ($key = array_search($file->ID, $v) ) !== false) {
							unset($order_upload[$k][$key]);
						}
					}

					$json['complete'] = true;
					$_SESSION['order_upload'] = serialize($order_upload);
				}
			}
		}else {
			$json['error'] = true;
		}


 		

		$count_files = 0;
		if($order_upload){
			foreach ($order_upload as $key => $value) {
				$count_files += count($value);
			}
		}

		update_post_meta($order_id, 'order_upload', $order_upload);

		
		$json['file_id'] = $file_id;
		$json['count'] = $count_files;

		wp_send_json($json);

    }


    public static function nbt_order_upload() {
    	global $wpdb;

		$response 			= array();
		$get_customer_id 	= $_SESSION['guest_id'];
		$response['customer_id'] = $get_customer_id;
		$product_id 		= absint( $_REQUEST['product_id'] );
		$variation_id 		= isset($_REQUEST['variation_id']) ? $_REQUEST['variation_id'] : "";
		$nbt_id = $_POST['nbt_id'];
		$new_attachment = array();

		$pid = md5($product_id);
		$value_order = @unserialize($_SESSION['order_upload']);
		if( ! empty($variation_id) ) {
			$pid = $variation_id;
		}

		// upload images to host
		$ou_settings = get_option('order-upload_settings');
		$limit_filesize 	= intval($ou_settings['nbt_order_upload_file_limitsize']) * 1048576;
		$settig_file_extension 	= $ou_settings['nbt_order_upload_file_extension'];
		//$limit_filesize 	= $ou_settings['nbt_order_upload_file_limitsize'];

		if( ! isset($_POST['attached_from_outsoure'])) {

			$usingUploader 		= 3;
			$fileErrors 		= array(
				0 => __('ERROR: There is no error, the file uploaded with success', 'nbt-solution'),
				1 => __('ERROR: The uploaded file exceeds the upload_max_files in server settings', 'nbt-solution'),
				2 => __('ERROR: The uploaded file exceeds the MAX_FILE_SIZE from html form', 'nbt-solution'),
				3 => __('ERROR: The uploaded file uploaded only partially', 'nbt-solution'),
				4 => __('ERROR: No file was uploaded', 'nbt-solution'),
				6 => __('ERROR: Missing a temporary folder', 'nbt-solution'),
				7 => __('ERROR: Failed to write file to disk', 'nbt-solution'),
				8 => __('ERROR: A PHP extension stoped file to upload', 'nbt-solution')
			);
			$posted_data 		=  isset( $_POST ) ? $_POST : array();
			$file_data 			= isset( $_FILES ) ? $_FILES : array();
			$data 				= array_merge( $posted_data, $file_data );


			$upload_dir = wp_upload_dir();
			$upload_path = $upload_dir["basedir"]."/nbt-order-uploads/";
			$upload_url = $upload_dir["baseurl"]."/nbt-order-uploads/";
			if(!file_exists($upload_path)){
				mkdir($upload_path);
			}

			$current_maxfilesize = intval(ini_get('upload_max_filesize')) * 1048576;
			if( $limit_filesize > $current_maxfilesize) {
				$response['msg'] = $fileErrors[1];
		    	echo wp_json_encode($response);
		    	wp_die();
			}

			
			foreach ($data['nbt_files']['name'] as $key => $file_name) {
				$file_type = $data['nbt_files']['name'][$key];
				$file_tmp_name = $data['nbt_files']['tmp_name'][$key];
				$file_error = $data['nbt_files']['error'][$key];
				$file_size = $data['nbt_files']['size'][$key];

				$file_extension = pathinfo( $upload_path . "/" . $file_name );

				$response_id = $nbt_id[$key];
				$file_rename = md5($get_customer_id.$file_type);
				$file_full_rename = $file_rename.'.'.$file_extension['extension'];

				$response['response_id'] = $response_id;

				/* Check file exists in database */
				$check_attachments = $wpdb->get_var( "SELECT * FROM {$wpdb->prefix}posts WHERE post_name LIKE '%".$file_rename."%' AND comment_count = 1 AND post_type = 'ou_attachment'" );

				if($check_attachments){
					$response['debug']['attachment_exists'] = 'Đã tồn tại!';

					$attach_id = $check_attachments;


					if( in_array($check_attachments, $value_order[$pid]) ) {
						

						$response['fake'][$fake_id] = true;
						$response["response"][$fake_id] = __('ERROR: File already exists.', 'nbt-solution');
					}else {
						$response["response"][$response_id]['complete'] = true;
						$response["response"][$response_id]['file_id'] = $file_rename;
					}

				}else{
					$response['debug']['attachment_exists'] = 'Chưa tồn tại!';
					if($file_error > 0){
						$response["response"][$response_id] = $fileErrors[$file_error];

					} else {
						if(file_exists($upload_path . "/" . $file_full_rename)){
							$response["response"][$response_id] = __('ERROR: File already exists.', 'nbt-solution');
						} else {
							
							if($file_size <= $limit_filesize) {
								if( move_uploaded_file( $file_tmp_name, $upload_path . "/" . $file_full_rename ) ){
									$response["response"][$response_id]['complete'] = true;
									$response["response"][$response_id]['file_id'] = $file_rename;

				            		$u_path = str_replace('\\', '/', $upload_path . $file_full_rename);
				            		$u_path_gen = str_replace('\\', '/', $upload_path . 'thumb_'.$file_full_rename);
				            		$response["url"][$response_id] =  $upload_url . 'thumb_'.$file_full_rename;

								
						            		
						            if( $file_extension && isset( $file_extension["extension"] ) ){
						            	$type = $file_extension["extension"];
						            	if( $type == "jpeg" || $type == "jpg" || $type == "png" || $type == "gif" ) {
											$type = "image/" . $type;
										}
						            	$response["type"][$response_id] = $type;
						            }


						            if($file_size < 5017470) {
						            	self::make_thumb($u_path, $u_path_gen, 100, $type);
						            }
						            
									$attachment = array(
										'post_mime_type' => $type,
										'post_title' => $file_name,
										'post_content' => '',
										'post_status' => 'inherit',
										'post_name' => $file_rename,
										'guid' => $response["url"]
									 );
									$attach_id = wp_insert_attachment( $attachment, $file_rename, 1 );
									require_once(ABSPATH . 'wp-admin/includes/image.php');
									$attach_data = wp_generate_attachment_metadata( $attach_id, $file_rename );
									wp_update_attachment_metadata( $attach_id, $attach_data );

									update_post_meta($attach_id, 'attachment_order_upload', true);
									update_post_meta($attach_id, '_wp_attached_size', $file_size);

									$wpdb->update( 
										$wpdb->posts, 
										array( 
											'post_type' 	=> 'ou_attachment',
											'comment_count' => 1
										), 
										array( 'ID' => $attach_id ), 
										array( 
											'%s',
											'%d'
										), 
										array( '%d' )
									);

									$response['status'] = 'completed';
									$complete = true;

									
								}else {
									$response["response"][$response_id] = __('ERROR: Upload Failed.', 'nbt-solution');
						        }

							}else{
								if( empty($ou_settings['nbt_order_upload_file_limitsize']) ) {
									$response["response"][$response_id] = __('ERROR: File is too large. Please contact administrator to set max file in NBT Solutions.', 'nbt-solution');
								}else {
									$response["response"][$response_id] = __('ERROR: File is too large. Max file size is', 'nbt-solution') . ' ' . $ou_settings['nbt_order_upload_file_limitsize']  .".";
								}
			            		
							}
						}
					}
				}
				$new_attachment[$attach_id] = $attach_id;
			}
			
		}
		else {

			$os_url 	= $_POST['os_url'];
			$os_name 	= $_POST['os_name'];
			$os_id 	= $_POST['os_id'];

			$attachment = array(
				'post_mime_type' => '',
				'post_title' => $os_name,
				'post_status' => 'inherit',
				'post_name' => $os_id,
				'post_content' => $os_url 		
			 );
			$attach_id = wp_insert_attachment( $attachment, false, 1 );

			$wpdb->update( 
				$wpdb->posts, 
				array( 
					'post_type' 	=> 'ou_attachment',
					'comment_count' => 1
				), 
				array( 'ID' => $attach_id ), 
				array( 
					'%s',
					'%d'
				), 
				array( '%d' )
			);

			$new_attachment[$attach_id] = $attach_id;
			$complete = true;
		}

		$response['debug']['new_attachment'] = $new_attachment;


		if(isset($new_attachment) && is_array($new_attachment) && !empty($new_attachment) ){
			$response['debug']['complete'] = 'true';

			if( isset($value_order[$pid]) ) {
				$old_value_order = $value_order[$pid];
				$new_att = array();
				foreach( $new_attachment as $v) {
					if( ! in_array($v, $old_value_order) ) {
						$new_att[] = $v;
					}
				}
				$value_order[$pid] = array_merge($old_value_order, $new_att);
			}else {
				$value_order[$pid] = $new_attachment;
			}

			$value_order[$pid] = array_filter($value_order[$pid]);

			// echo '<pre>';
			// print_r($new_attachment);
			// print_r($new_att);
			// print_r($value_order);
			// echo '</pre>';


			$_SESSION['order_upload'] = serialize($value_order);
		}

		if( defined('PREFIX_NBT_SOL_DEV') && ! PREFIX_NBT_SOL_DEV ){
			unset($response['debug']);
		}

		wp_send_json($response);
    }

	public static function make_thumb($src, $dest, $desired_width, $ext) {
		switch ( $ext ) 
		{
		  case 'image/gif': $source_image = imagecreatefromgif($src); break;
		  case 'image/jpeg': $source_image = imagecreatefromjpeg($src); break;
		  case 'image/jpg': $source_image = imagecreatefromjpeg($src); break;
		  case 'image/png': $source_image = imagecreatefrompng($src); break;
		  default: trigger_error('Unsupported filetype!', E_USER_WARNING);  break;
		}

		/* read the source image */
		$width = imagesx($source_image);
		$height = imagesy($source_image);
		
		/* find the "desired height" of this thumbnail, relative to the desired width  */
		$desired_height = floor($height * ($desired_width / $width));
		
		/* create a new, "virtual" image */
		$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
		
		/* copy source image at a resized size */
		imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
		
		/* create the physical thumbnail image to its destination */
		imagejpeg($virtual_image, $dest);
	}
	public static function nbtou_remove_files(){

    	$upload_dir = wp_upload_dir();
    	$destination_folder = $upload_dir['basedir'].'/nbt-order-uploads/';

		$order_id = absint($_REQUEST['order_id'] );
		$file_id = absint($_REQUEST['file_number'] );
		$product_id = absint($_REQUEST['product_id'] );

		$order_upload = get_post_meta($order_id, 'order_upload', true);

		if(isset($order_upload[$product_id])){
			if(($key = array_search($file_id, $order_upload[$product_id])) !== false) {
				$attach_id = $order_upload[$product_id][$key];
	    		$path_file = wp_get_attachment_url($attach_id);
	    		$basename = basename($path_file);
	    		$post_url = $destination_folder.$basename;
	    		$post_thumbnail_url = $destination_folder.'thumb_'.$basename;

	    		unlink($post_url);
	    		unlink($post_thumbnail_url);

	    		wp_delete_post($attach_id);

			    unset($order_upload[$product_id][$key]);
			}
		}
		$count_files = 0;
		if($order_upload){
			foreach ($order_upload as $key => $value) {
				$count_files += count($value);
			}

		}

		update_post_meta($order_id, 'order_upload', $order_upload);

		$order_upload = get_post_meta($order_id, 'order_upload', true);
		
		$json['complete'] = true;
		$json['order_id'] = md5($file_id);
		$json['file_id'] = $file_id;
		$json['count'] = $count_files;

		echo wp_json_encode($json, TRUE);
		wp_die();
	}

    public function nbtou_submit_files(){
    	$order_id = absint($_REQUEST['order_id']);
    	$ans = $_REQUEST['ans'];

    	update_post_meta($order_id, '_order_upload_status', $ans);

    	$json['complete'] = true;

    	echo wp_json_encode($json, TRUE);

    	wp_die();
	}
}