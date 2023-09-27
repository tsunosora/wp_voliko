<?php

class WooPanel_Store_Locator_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $wpl_store_locator    The ID of this plugin.
	 */
	private $wpl_store_locator;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	private $max_img_width  = 450;
	private $max_img_height = 450;


	private $max_ico_width  = 75;
	private $max_ico_height = 75;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $wpl_store_locator       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $wpl_store_locator, $version ) {

		$this->wpl_store_locator = $wpl_store_locator;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WooPanel_Store_Locator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WooPanel_Store_Locator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->wpl_store_locator, WOOPANEL_STORE_LOCATOR_URL . 'admin/css/bootstrap.min.css', array(), $this->version, 'all' );//$this->version
		wp_enqueue_style( 'asl_chosen_plugin', WOOPANEL_STORE_LOCATOR_URL . 'admin/css/chosen.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'asl_admin', WOOPANEL_STORE_LOCATOR_URL . 'admin/css/style.css', array(), $this->version, 'all' );
 
		wp_enqueue_style( 'asl_datatable1', WOOPANEL_STORE_LOCATOR_URL . 'admin/datatable/media/css/demo_page.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'asl_datatable2', WOOPANEL_STORE_LOCATOR_URL . 'admin/datatable/media/css/jquery.dataTables.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WooPanel_Store_Locator_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WooPanel_Store_Locator_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->wpl_store_locator.'-lib', WOOPANEL_STORE_LOCATOR_URL . 'admin/js/libs.min.js', array('jquery'), $this->version, false );
		wp_enqueue_script( $this->wpl_store_locator.'-choosen', WOOPANEL_STORE_LOCATOR_URL . 'admin/js/chosen.proto.min.js', array('jquery'), $this->version, false );
		wp_enqueue_script( $this->wpl_store_locator.'-datatable', WOOPANEL_STORE_LOCATOR_URL . 'admin/datatable/media/js/jquery.dataTables.min.js', array('jquery'), $this->version, false );
		wp_enqueue_script( 'asl-bootstrap', WOOPANEL_STORE_LOCATOR_URL . 'admin/js/bootstrap.min.js', array('jquery'), $this->version, false );
		wp_enqueue_script( $this->wpl_store_locator.'-upload', WOOPANEL_STORE_LOCATOR_URL . 'admin/js/jquery.fileupload.min.js', array('jquery'), $this->version, false );
		wp_enqueue_script( $this->wpl_store_locator.'-jscript', WOOPANEL_STORE_LOCATOR_URL . 'admin/js/jscript.js', array('jquery'), $this->version, false );
		wp_enqueue_script( $this->wpl_store_locator.'-draw', WOOPANEL_STORE_LOCATOR_URL . 'admin/js/drawing.js', array('jquery'), $this->version, false );


		$langs = array(
			'select_category' => esc_html__('Select Some Options','asl_admin'),
			'no_category' => esc_html__('Select Some Options','asl_admin'),
			'geocode_fail' => esc_html__('Geocode was not Successful:','asl_admin'),
			'upload_fail'  => esc_html__('Upload Failed! Please try Again.','asl_admin'),
			'delete_category'  => esc_html__('Delete Category','asl_admin'),
			'delete_categories' => esc_html__('Delete Categories','asl_admin'),
			'warn_question'  => esc_html__('Are you sure you want to ','asl_admin'),
			'delete_it'  => esc_html__('Delete it!','asl_admin'),
			'duplicate_it'  => esc_html__('Duplicate it!','asl_admin'),
			'delete_marker'  => esc_html__('Delete Marker','asl_admin'),
			'delete_markers'  => esc_html__('Delete Markers','asl_admin'),
			'delete_logo'  => esc_html__('Delete Logo','asl_admin'),
			'delete_logos'  => esc_html__('Delete Logos','asl_admin'),
			'delete_logos'  => esc_html__('Delete Logos','asl_admin'),
			'delete_store'  => esc_html__('Delete Store','asl_admin'),
			'delete_stores'  => esc_html__('Delete Stores','asl_admin'),
			'duplicate_stores'  => esc_html__('Duplicate Selected Store','asl_admin'),
			'start_time'  => esc_html__('Start Time','asl_admin'),
			'select_logo'  => esc_html__('Select Logo','asl_admin'),
			'select_marker'  => esc_html__('Select Marker','asl_admin'),
			'end_time'  => esc_html__('End Time','asl_admin'),
			'select_country'  => esc_html__('Select Country','asl_admin'),
			'delete_all_stores'  => esc_html__('DELETE ALL STORES','asl_admin'),
			'invalid_file_error'  => esc_html__('Invalid File, Accepts JPG, PNG, GIF or SVG.','asl_admin'),
			'error_try_again'  => esc_html__('Error Occured, Please try Again.','asl_admin'),
			'delete_all'  => esc_html__('DELETE ALL','asl_admin'),
			'pur_title'  => esc_html__('PLEASE VALIDATE PURCHASE CODE!','asl_admin'),
			'pur_text'  => esc_html__('Thank you for purchasing <b>Store Locator for WordPress</b> Plugin, kindly enter your purchase code to unlock the page. <a target="_blank" href="https://netbaseteam.com/wiki/store-locator-purchase-code/">How to Get Your Purchase Code</a>.','asl_admin'),
		);


		// Plugin Validation
		wp_localize_script( $this->wpl_store_locator.'-jscript', 'WPLSL_REMOTE',  array('Com' => get_option('wplsl-compatible'),   'LANG' => $langs, 'URL' => admin_url( 'admin-ajax.php' ),'1.1', true ));
	}

	/**
	 * [upload_logo Upload the Logo]
	 * @return [type] [description]
	 */
	public function upload_logo() {

		$response = new \stdclass();
		$response->success = false;

		if(empty($_POST['data']['logo_name']) || !$_POST['data']['logo_name']) {

			$response->msg = esc_html__("Error! logo name is required.",'asl_admin');
			echo json_encode($response);die;
		}


		$uniqid = uniqid();
		$target_dir  = WOOPANEL_STORE_LOCATOR_PATH."public/Logo/";
	 	//$target_file = $uniqid.'_'. strtolower($_FILES["files"]["name"]);

		$imageFileType = explode('.', $_FILES["files"]["name"]);
		$imageFileType = $imageFileType[count($imageFileType) - 1];

	 	$target_file = $uniqid.'_logo.'.$imageFileType;
	 	$target_name = isset($_POST['data']['logo_name'])?$_POST['data']['logo_name']:('Logo '.time());

	 	// Check the Size of the Image //
	 	$tmp_file = $_FILES["files"]['tmp_name'];
	 	list($width, $height) = getimagesize($tmp_file);

	 	
		// To big size
		if ($_FILES["files"]["size"] >  5000000) {
		    $response->msg = esc_html__("Sorry, your file is too large, sized.",'asl_admin');
		}
		// Not a valid format
		else if(!in_array($imageFileType, array('jpg','png','jpeg','gif','JPG'))) {
		    $response->msg = esc_html__("Sorry, only JPG, JPEG, PNG & GIF files are allowed.",'asl_admin');
		}
		else if($width > $this->max_img_width || $height > $this->max_img_width) {

			$response->msg = esc_html__("Max Image dimensions Width and Height is {$this->max_img_width} x {$this->max_img_height} px.<br> Recommended Logo size is 250 x 250 px",'asl_admin');
		}
		// upload 
		else if(move_uploaded_file($_FILES["files"]["tmp_name"], $target_dir.$target_file)) {

			global $wpdb;
			$wpdb->insert(WOOPANEL_STORE_LOCATOR_PREFIX.'storelogos', array('path'=>$target_file,'name'=>$target_name), array('%s','%s'));

  		$response->list = $wpdb->get_results("SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."storelogos ORDER BY id DESC");
  	 	$response->msg = esc_html__("The file has been uploaded.",'asl_admin');
  	 	$response->success = true;
		}
		//error
		else {

			$response->msg = esc_html__('Some Error Occured','asl_admin');
		}

		echo json_encode($response);
	  die;
	}

	/**
	 * [add_new_store POST METHODS for Add New Store]
	 */
	public function add_new_store() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$form_data = stripslashes_deep($_REQUEST['data']);
		

		
		//insert into stores table
		if($wpdb->insert( WOOPANEL_STORE_LOCATOR_PREFIX.'stores', $form_data))
		{
			$response->success = true;
			$store_id = $wpdb->insert_id;


			/*Save Categories*/
			if(is_array($_REQUEST['category']))
				foreach ($_REQUEST['category'] as $category) {	

				$wpdb->insert(WOOPANEL_STORE_LOCATOR_PREFIX.'stores_categories', 
				 	array('store_id'=>$store_id,'category_id'=>$category),
				 	array('%s','%s'));			
			}

			$response->msg = esc_html__('Store Added Successfully.','asl_admin');
		}
		else {

			$wpdb->show_errors = true;

			$response->error = esc_html__('Error occurred while saving Store','asl_admin');
			$response->msg   = $wpdb->print_error();
		}
		
		echo json_encode($response);die;	
	}

	/**
	 * [update_store update Store]
	 * @return [type] [description]
	 */
	public function update_store() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$form_data = stripslashes_deep($_REQUEST['data']);
		$update_id = $_REQUEST['updateid'];
		
		//update into stores table
		$wpdb->update(WOOPANEL_STORE_LOCATOR_PREFIX."stores",
			apply_filters( 'woopanel_stores_field_update', array(
				'title'			=> $form_data['title'],
				'description'	=> $form_data['description'],
				'phone'			=> $form_data['phone'],
				'fax'			=> $form_data['fax'],
				'email'			=> $form_data['email'],
				'street'		=> $form_data['street'],
				'postal_code'	=> $form_data['postal_code'],
				'city'			=> $form_data['city'],
				'state'			=> $form_data['state'],
				'lat'			=> $form_data['lat'],
				'lng'			=> $form_data['lng'],
				'website'		=> $form_data['website'],
				'country'		=> $form_data['country'],
				'is_disabled'	=> (isset($form_data['is_disabled']) && $form_data['is_disabled'])?'1':'0',
				'description_2'	=> $form_data['description_2'],
				'logo_id'		=> $form_data['logo_id'],
				'marker_id'		=> $form_data['marker_id'],
				/*'start_time'	=> $form_data['start_time'],
				'end_time'		=> $form_data['end_time'],*/
				'banner_id'		=> $form_data['banner_id'],
				'open_hours'	=> $form_data['open_hours'],
				'ordr'			=> $form_data['ordr'],
				/*
				'days'			=> $form_data['days'],
				'time_per_day' => $form_data['time_per_day'],
				*/
				'updated_on' 	=> date('Y-m-d H:i:s')
			), $form_data ),
			array('id' => $update_id)
		);

		$sql = "DELETE FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."stores_categories WHERE store_id = ".$update_id;
		$wpdb->query($sql);

			if(is_array($_REQUEST['category']))
			foreach ($_REQUEST['category'] as $category) {	

			$wpdb->insert(WOOPANEL_STORE_LOCATOR_PREFIX.'stores_categories', 
			 	array('store_id'=>$update_id,'category_id'=>$category),
			 	array('%s','%s'));	
		}


		$response->success = true;


		$response->msg = esc_html__('Store Updated Successfully.','asl_admin');


		echo json_encode($response);die;
	}


	/**
	 * [delete_store To delete the store/stores]
	 * @return [type] [description]
	 */
	public function delete_store() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$multiple = $_REQUEST['multiple'];
		$delete_sql;

		if($multiple) {

			$item_ids 		 = implode(",",$_POST['item_ids']);
			$delete_sql 	 = "DELETE FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."stores WHERE id IN (".$item_ids.")";
		}
		else {

			$store_id 		 = $_REQUEST['store_id'];
			$delete_sql 	 = "DELETE FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."stores WHERE id = ".$store_id;
		}


		if($wpdb->query($delete_sql)) {

			$response->success = true;
			$response->msg = ($multiple) ? esc_html__('Stores deleted successfully.','asl_admin') : esc_html__('Store deleted successfully.','asl_admin');
		}
		else {
			$response->error = esc_html__('Error occurred while saving record','asl_admin');//$form_data
			$response->msg   = $wpdb->show_errors();
		}
		
		echo json_encode($response);die;
	}


	/**
	 * [store_status To Change the Status of Store]
	 * @return [type] [description]
	 */
	public function store_status() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$status = (isset($_REQUEST['status']) && $_REQUEST['status'] == '1')?'0':'1';
		$status_title 	 = ($status == '1')?'Disabled':'Enabled'; 
		$delete_sql;

		$item_ids 		 = implode(",",$_POST['item_ids']);
		$update_sql 	 = "UPDATE ".WOOPANEL_STORE_LOCATOR_PREFIX."stores SET is_disabled = {$status} WHERE id IN (".$item_ids.")";

		if($wpdb->query($update_sql)) {

			$response->success = true;
			$response->msg = esc_html__('Selected Stores','asl_admin').' '.$status_title;
		}
		else {
			$response->error = esc_html__('Error occurred while Changing Status','asl_admin');
			$response->msg   = $wpdb->show_errors();
		}
		
		echo json_encode($response);die;
	}
	
	/**
	 * [duplicate_store to  Duplicate the store]
	 * @return [type] [description]
	 */
	public function duplicate_store() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$store_id = $_REQUEST['store_id'];


		$result = $wpdb->get_results("SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."stores WHERE id = ".$store_id);		

		if($result && $result[0]) {

			$result = (array)$result[0];

			unset($result['id']);
			unset($result['created_on']);
			unset($result['updated_on']);

			//insert into stores table
			if($wpdb->insert( WOOPANEL_STORE_LOCATOR_PREFIX.'stores', $result)){
				$response->success = true;
				$new_store_id = $wpdb->insert_id;

				//get categories and copy them
				$s_categories = $wpdb->get_results("SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."stores_categories WHERE store_id = ".$store_id);

				/*Save Categories*/
				foreach ($s_categories as $_category) {	

					$wpdb->insert(WOOPANEL_STORE_LOCATOR_PREFIX.'stores_categories', 
					 	array('store_id'=>$new_store_id,'category_id'=>$_category->category_id),
					 	array('%s','%s'));			
				}

				//SEnd the response
				$response->msg = esc_html__('Store Duplicated successfully.','asl_admin');
			}
			else
			{
				$response->error = esc_html__('Error occurred while saving Store','asl_admin');//$form_data
				$response->msg   = $wpdb->show_errors();
			}	

		}

		echo json_encode($response);die;
	}

	

	////////////////////////////////
	/////////ALL Category Methods //
	////////////////////////////////
	
	/**
	 * [add_category Add Category Method]
	 */
	public function add_category() {

		global $wpdb;

		$response = new \stdclass();
		$response->success = false;

		$target_dir  = WOOPANEL_STORE_LOCATOR_PATH."public/icon/";
		$namefile 	 = substr(strtolower($_FILES["files"]["name"]), 0, strpos(strtolower($_FILES["files"]["name"]), '.'));
		

		$imageFileType = pathinfo($_FILES["files"]["name"],PATHINFO_EXTENSION);
	 	$target_name   = uniqid();
		
		//add extension
		$target_name .= '.'.$imageFileType;

		///CREATE DIRECTORY IF NOT EXISTS
		if(!file_exists($target_dir)) {

			mkdir( $target_dir, 0775, true );
		}
		
 		// Check the Size of the Image //
 		$tmp_file = $_FILES["files"]['tmp_name'];
 		list($width, $height) = getimagesize($tmp_file);


		//to big size
		if ($_FILES["files"]["size"] >  5000000) {
		    $response->msg = esc_html__("Sorry, your file is too large.",'asl_admin');
		}
		// not a valid format
		else if(!in_array($imageFileType, array('jpg','png','jpeg','JPG','gif','svg','SVG'))) {
		    $response->msg = esc_html__("Sorry, only JPG, JPEG, PNG & GIF files are allowed.",'asl_admin');
		}
		else if($width > $this->max_ico_width || $height > $this->max_ico_width) {

			$response->msg = esc_html__("Max Image dimensions Width and Height is {$this->max_ico_width} x {$this->max_ico_height} px.<br> Recommended Icon size is 20 x 32 px or around it",'asl_admin');
		}
		// upload 
		else if( $uploaded_file  = wp_handle_upload($_FILES["files"], array( 'test_form' => false ) )) {
			if ( $uploaded_file  && ! isset( $uploaded_file ['error'] ) ) {
				$upload_dir   = wp_upload_dir();
				$filename = str_replace($upload_dir['baseurl'] .'/', '', $uploaded_file['url']);

		
				$file_name_and_location = $uploaded_file ["file"];
				$file_title_for_media_library = $title;

				$attachment = array(
					"post_mime_type" => $uploaded_file_type,
					"post_title" => addslashes( $file_title_for_media_library ),
					"post_content" => "",
					"post_status" => "inherit",
					"post_mime_type" => $uploaded_file['type']
				);

				if( ! is_null( $post )) {
					if ( ! is_numeric( $post )) {
						$post = $post->ID;
					} // if ()

					$attachment ['post_parent'] = $post;
				} // if ()

				$id = wp_insert_attachment( $attachment, $file_name_and_location );

				require_once( ABSPATH."wp-admin/includes/image.php" );

				$attach_data = wp_generate_attachment_metadata( $id, $file_name_and_location );
				wp_update_attachment_metadata( $id, $attach_data );


				update_post_meta($id, '_wp_attachment_metadata', array
				(
				    'width' => 75,
				    'height' => 75,
				    'file' => $filename,
				    'sizes' => array(
				    ),
				    'image_meta' => array(
				        'aperture' => 0,
				        'credit' => null,
				        'camera' =>  null,
				        'caption' =>  null,
				        'created_timestamp' => 0,
				        'copyright' =>  null,
				        'focal_length' => 0,
				        'iso' => 0,
				        'shutter_speed' => 0,
				        'title' =>  null,
				        'orientation' => 0,
				        'keywords' => array(
				        )
				    )
				));


				$form_data = $_REQUEST['data'];

				if($wpdb->insert(WOOPANEL_STORE_LOCATOR_PREFIX.'categories', 
			 	array(	'category_name' => $form_data['category_name'],
			 			'slug' => sanitize_title($form_data['category_name']),		 		
						'is_active'		=> 1,
						'icon'			=> $id
			 		),
			 	array('%s','%d','%s'))
				)
				{
					$response->msg = esc_html__("Category Added Successfully",'asl_admin');
	  	 			$response->success = true;
				}
				else
				{
					$response->msg = esc_html__('Error occurred while saving record','asl_admin');//$form_data
					
				}
			}
      	 	
		}
		//error
		else {

			$response->msg = esc_html__('Some error occured','asl_admin');
		}

		echo json_encode($response);
	    die;
	}

	/**
	 * [delete_category delete category/categories]
	 * @return [type] [description]
	 */
	public function delete_category() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$multiple = $_REQUEST['multiple'];
		$delete_sql;$cResults;

		if($multiple) {

			$item_ids 		 = implode(",",$_POST['item_ids']);
			$delete_sql 	 = "DELETE FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."categories WHERE id IN (".$item_ids.")";
			$cResults 		 = $wpdb->get_results("SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."categories WHERE id IN (".$item_ids.")");
		}
		else {

			$category_id 	 = $_REQUEST['category_id'];
			$delete_sql 	 = "DELETE FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."categories WHERE id = ".$category_id;
			$cResults 		 = $wpdb->get_results("SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."categories WHERE id = ".$category_id );
		}


		if(count($cResults) != 0) {
			
			if($wpdb->query($delete_sql))
			{
					$response->success = true;
					foreach($cResults as $c) {

						$inputFileName = WOOPANEL_STORE_LOCATOR_PATH.'public/icon/'.$c->icon;
					
						if(file_exists($inputFileName) && $c->icon != 'default.png') {	
									
							unlink($inputFileName);
						}
					}							
			}
			else
			{
				$response->error = esc_html__('Error occurred while deleting record','asl_admin');//$form_data
				$response->msg   = $wpdb->show_errors();
			}
		}
		else
		{
			$response->error = esc_html__('Error occurred while deleting record','asl_admin');
		}

		if($response->success)
			$response->msg = ($multiple) ? esc_html__('Categories deleted successfully.','asl_admin') : esc_html__('Category deleted successfully.','asl_admin');
		
		echo json_encode($response);die;
	}


	/**
	 * [update_category update category with icon]
	 * @return [type] [description]
	 */
	public function update_category() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$data = $_REQUEST['data'];
		
		
		// with icon
		if($data['action'] == "notsame") {

			$target_dir  = WOOPANEL_STORE_LOCATOR_PATH."public/icon/";

			$namefile 	 = substr(strtolower($_FILES["files"]["name"]), 0, strpos(strtolower($_FILES["files"]["name"]), '.'));
			

			$imageFileType = pathinfo($_FILES["files"]["name"],PATHINFO_EXTENSION);
		 	$target_name   = uniqid();
			
			
			//add extension
			$target_name .= '.'.$imageFileType;

		 	
			$res = $wpdb->get_results( "SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."categories WHERE id = ".$data['category_id']);

			// Check the Size of the Image //
		 	$tmp_file = $_FILES["files"]['tmp_name'];
		 	list($width, $height) = getimagesize($tmp_file);


		 	if ($_FILES["files"]["size"] >  5000000) {
			    $response->msg = esc_html__("Sorry, your file is too large.",'asl_admin');
			}
			// not a valid format
			else if(!in_array($imageFileType, array('jpg','png','gif','jpeg','JPG','svg','SVG'))) {
			    $response->msg = esc_html__("Sorry, only JPG, JPEG, PNG & GIF files are allowed.",'asl_admin');
			    
			}
			else if($width > $this->max_ico_width || $height > $this->max_ico_width) {

				$response->msg = esc_html__("Max Image dimensions Width and Height is {$this->max_ico_width} x {$this->max_ico_height} px.<br> Recommended Category Icon size is 20 x 32 px or around it",'asl_admin');	
			}
			// upload 
			else if( $uploaded_file  = wp_handle_upload($_FILES["files"], array( 'test_form' => false ) )) {
				if ( $uploaded_file  && ! isset( $uploaded_file ['error'] ) ) {
					$upload_dir   = wp_upload_dir();
					$filename = str_replace($upload_dir['baseurl'] .'/', '', $uploaded_file['url']);

			
					$file_name_and_location = $uploaded_file ["file"];
					$file_title_for_media_library = $title;

					$attachment = array(
						"post_mime_type" => $uploaded_file_type,
						"post_title" => addslashes( $file_title_for_media_library ),
						"post_content" => "",
						"post_status" => "inherit",
						"post_mime_type" => $uploaded_file['type']
					);

					if( ! is_null( $post )) {
						if ( ! is_numeric( $post )) {
							$post = $post->ID;
						} // if ()

						$attachment ['post_parent'] = $post;
					} // if ()

					$id = wp_insert_attachment( $attachment, $file_name_and_location );

					require_once( ABSPATH."wp-admin/includes/image.php" );

					$attach_data = wp_generate_attachment_metadata( $id, $file_name_and_location );
					wp_update_attachment_metadata( $id, $attach_data );


					update_post_meta($id, '_wp_attachment_metadata', array
					(
					    'width' => 75,
					    'height' => 75,
					    'file' => $filename,
					    'sizes' => array(
					    ),
					    'image_meta' => array(
					        'aperture' => 0,
					        'credit' => null,
					        'camera' =>  null,
					        'caption' =>  null,
					        'created_timestamp' => 0,
					        'copyright' =>  null,
					        'focal_length' => 0,
					        'iso' => 0,
					        'shutter_speed' => 0,
					        'title' =>  null,
					        'orientation' => 0,
					        'keywords' => array(
					        )
					    )
					));

						
						$update_params = array( 'category_name'=> $data['category_name'], 'icon'=> $id);
					
						$wpdb->update(
							WOOPANEL_STORE_LOCATOR_PREFIX."categories",
							$update_params,
							array('id' => $data['category_id']),array( '%s' ), array( '%d' ));		
						$response->msg = esc_html__('Updated Successfully.','asl_admin');
						$response->success = true;
						if (file_exists($target_dir.$res[0]->icon)) {	
							unlink($target_dir.$res[0]->icon);
						}
				}
				//error
				else {

					$response->msg = esc_html__('Some error occured','asl_admin');
					
				}
			}

		}
		//without icon
		else {

			$wpdb->update(WOOPANEL_STORE_LOCATOR_PREFIX."categories",array( 'category_name'=> $data['category_name']),array('id' => $data['category_id']),array( '%s' ), array( '%d' ));		
			$response->msg = esc_html__('Updated Successfully.','asl_admin');
			$response->success = true;	

		}
		
		echo json_encode($response);die;
	}


	/**
	 * [get_category_by_id get category by id]
	 * @return [type] [description]
	 */
	public function get_category_by_id() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$store_id = $_REQUEST['category_id'];
		

		$response->list = $wpdb->get_results( "SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."categories WHERE id = ".$store_id);

		if(count($response->list)!=0) {

			$response->success = true;

		}
		else{
			$response->error = esc_html__('Error occurred while geting record','asl_admin');//$form_data

		}
		echo json_encode($response);die;
	}


	/**
	 * [get_categories GET the Categories]
	 * @return [type] [description]
	 */
	public function get_categories() {

		global $wpdb;
		$start = isset( $_REQUEST['iDisplayStart'])?$_REQUEST['iDisplayStart']:0;		
		$params  = isset($_REQUEST)?$_REQUEST:null; 	

		$acolumns = array(
			'id','id','category_name','is_active','icon','created_on'
		);

		$columnsFull = $acolumns;

		$clause = array();

		if(isset($_REQUEST['filter'])) {

			foreach($_REQUEST['filter'] as $key => $value) {

				if($value != '') {

					if($key == 'is_active')
					{
						$value = ($value == 'yes')?1:0;
					}

					$clause[] = "$key like '%{$value}%'";
				}
			}	
		} 
		
		
		//iDisplayStart::Limit per page
		$sLimit = "";
		if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".intval( $_REQUEST['iDisplayStart'] ).", ".
				intval( $_REQUEST['iDisplayLength'] );
		}

		/*
		 * Ordering
		 */
		$sOrder = "";
		if ( isset( $_REQUEST['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";

			for ( $i=0 ; $i < intval( $_REQUEST['iSortingCols'] ) ; $i++ )
			{
				if (isset($_REQUEST['iSortCol_'.$i]))
				{
					$sOrder .= "`".$acolumns[ intval( $_REQUEST['iSortCol_'.$i] )  ]."` ".$_REQUEST['sSortDir_0'];
					break;
				}
			}
			

			//$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}


		$sWhere = implode(' AND ',$clause);
		
		if($sWhere != '')$sWhere = ' WHERE '.$sWhere;
		
		$fields = implode(',', $columnsFull);
		
		###get the fields###
		$sql = 	"SELECT $fields FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."categories";

		$sqlCount = "SELECT count(*) 'count' FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."categories";

		/*
		 * SQL queries
		 * Get data to display
		 */
		$sQuery = "{$sql} {$sWhere} {$sOrder} {$sLimit}";
		$data_output = $wpdb->get_results($sQuery);
		
		/* Data set length after filtering */
		$sQuery = "{$sqlCount} {$sWhere}";
		$r = $wpdb->get_results($sQuery);
		$iFilteredTotal = $r[0]->count;
		
		$iTotal = $iFilteredTotal;

	    /*
		 * Output
		 */
		$sEcho = isset($_REQUEST['sEcho']) ? intval($_REQUEST['sEcho']) : 1;
		$output = array(
			"sEcho" => intval($_REQUEST['sEcho']),
			//"test" => $test,
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);
		
		foreach($data_output as $aRow)
	    {
	    	$row = $aRow;

	    	if($row->is_active == 1) {

	        	 $row->is_active = 'Yes';
	        }	       
	    	else {

	    		$row->is_active = 'No';	
	    	}

	    	$row->icon = "<img  src='".WOOPANEL_STORE_LOCATOR_URL."public/icon/".$row->icon."' alt=''  style='width:20px'/>";	

	    	$row->action = '<div class="edit-options"><a data-id="'.$row->id.'" title="Edit" class="edit_category"><svg width="14" height="14"><use xlink:href="#i-edit"></use></svg></a><a title="Delete" data-id="'.$row->id.'" class="delete_category g-trash"><svg width="14" height="14"><use xlink:href="#i-trash"></use></svg></a></div>';
	    	$row->check  = '<div class="custom-control custom-checkbox"><input type="checkbox" data-id="'.$row->id.'" class="custom-control-input" id="asl-chk-'.$row->id.'"><label class="custom-control-label" for="asl-chk-'.$row->id.'"></label></div>';
	        $output['aaData'][] = $row;
	    }

		echo json_encode($output);die;
	}


	/////////////////////////////////////
	///////////////ALL Markers Methods //
	/////////////////////////////////////
	

	/**
	 * [upload_marker upload marker into icon folder]
	 * @return [type] [description]
	 */
	public function upload_marker() {

		$response = new \stdclass();
		$response->success = false;


		if(empty($_POST['data']['marker_name']) || !$_POST['data']['marker_name']) {

			$response->msg = esc_html__("Error! marker name is required.",'asl_admin');
			echo json_encode($response);die;
		}


		$uniqid = uniqid();
		$target_dir  = WOOPANEL_STORE_LOCATOR_PATH."public/icon/";
	 	$target_file = $uniqid.'_'. strtolower($_FILES["files"]["name"]);
	 	$target_name = isset($_POST['data']['marker_name'])?$_POST['data']['marker_name']:('Marker '.time());
		
			
		$imageFileType = explode('.', $_FILES["files"]["name"]);
		$imageFileType = $imageFileType[count($imageFileType) - 1];

		$tmp_file = $_FILES["files"]['tmp_name'];
		list($width, $height) = getimagesize($tmp_file);

	
		//if file not found
		/*
		if (file_exists($target_name)) {
		    $response->msg = "Sorry, file already exists.";
		}
		*/

		//to big size
		if ($_FILES["files"]["size"] >  22085) {
		    $response->msg = esc_html__("Marker Image too Large.",'asl_admin');
		    $response->size = $_FILES["files"]["size"];
		}
		// not a valid format
		else if(!in_array($imageFileType, array('jpg','png','jpeg','gif','JPG'))) {
		    $response->msg = esc_html__("Sorry, only JPG, JPEG, PNG & GIF files are allowed.",'asl_admin');
		}
		else if($width > $this->max_img_width || $height > $this->max_img_width) {

				$response->msg = esc_html__("Max Image dimensions Width and Height is {$this->max_img_width} x {$this->max_img_height} px.<br> Recommended Logo size is 250 x 250 px",'asl_admin');
		}
		// upload 
		else if(move_uploaded_file($_FILES["files"]["tmp_name"], $target_dir.$target_file)) {

			global $wpdb;
			$wpdb->insert(WOOPANEL_STORE_LOCATOR_PREFIX.'markers', 
			 	array('icon'=>$target_file,'marker_name'=>$target_name),
			 	array('%s','%s'));

      		$response->list = $wpdb->get_results( "SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."markers ORDER BY id DESC");
      	 	$response->msg = esc_html__("The file has been uploaded.",'asl_admin');
      	 	$response->success = true;
		}
		//error
		else {

			$response->msg = esc_html__('Some Error Occured','asl_admin');
		}

		echo json_encode($response);die;
	}


	/**
	 * [add_marker Add Marker Method]
	 */
	public function add_marker() {

		global $wpdb;

		$response = new \stdclass();
			$response->success = false;

			$target_dir  = WOOPANEL_STORE_LOCATOR_PATH."public/icon/";
			

			$imageFileType = pathinfo($_FILES["files"]["name"],PATHINFO_EXTENSION);
		 	$target_name   = uniqid();
			
			//add extension
			$target_name .= '.'.$imageFileType;

			///CREATE DIRECTORY IF NOT EXISTS
			if(!file_exists($target_dir)) {

				mkdir( $target_dir, 0775, true );
			}
			
			// Check the Size of the Image //
			$tmp_file = $_FILES["files"]['tmp_name'];
			list($width, $height) = getimagesize($tmp_file);


			//to big size
			if ($_FILES["files"]["size"] >  5000000) {
			    $response->msg = esc_html__("Sorry, your file is too large.",'asl_admin');
			}
			// not a valid format
			else if(!in_array($imageFileType, array('jpg','png','gif','jpeg','JPG'))) {
			    $response->msg = esc_html__("Sorry, only JPG, JPEG, PNG & GIF files are allowed.",'asl_admin');
			}
			else if($width > $this->max_ico_width || $height > $this->max_ico_width) {

				$response->msg = esc_html__("Max Image dimensions Width and Height is {$this->max_ico_width} x {$this->max_ico_height} px.<br> Recommended Icon size is 20 x 32 px or around it",'asl_admin');
			}
			// upload 
			else if(move_uploaded_file($_FILES["files"]["tmp_name"], $target_dir.$target_name)) {
				
				$form_data = $_REQUEST['data'];

				if($wpdb->insert(WOOPANEL_STORE_LOCATOR_PREFIX.'markers', 
			 	array(	'marker_name' => $form_data['marker_name'],			 		
						'is_active'		=> 1,
						'icon'			=> $target_name
			 		),
			 	array('%s','%d','%s'))
				)
				{
					$response->msg = esc_html__("Marker Added Successfully",'asl_admin');
	  	 			$response->success = true;
				}
				else
				{
					$response->msg = esc_html__('Error occurred while saving record','asl_admin');//$form_data
					
				}
	      	 	
			}
			//error
			else {

				$response->msg = esc_html__('Some error occured','asl_admin');
			}

			echo json_encode($response);
		    die;
	}

	/**
	 * [delete_marker delete marker/markers]
	 * @return [type] [description]
	 */
	public function delete_marker() {
		
		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$multiple = $_REQUEST['multiple'];
		$delete_sql;$mResults;

		if($multiple) {

			$item_ids 		 = implode(",",$_POST['item_ids']);
			$delete_sql 	 = "DELETE FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."markers WHERE id IN (".$item_ids.")";
			$mResults 		 = $wpdb->get_results("SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."markers WHERE id IN (".$item_ids.")");
		}
		else {

			$item_id 		 = $_REQUEST['marker_id'];
			$delete_sql 	 = "DELETE FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."markers WHERE id = ".$item_id;
			$mResults 		 = $wpdb->get_results("SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."markers WHERE id = ".$item_id );
		}


		if(count($mResults) != 0) {
			
			if($wpdb->query($delete_sql)) {

					$response->success = true;

					foreach($mResults as $m) {

						$inputFileName = WOOPANEL_STORE_LOCATOR_PATH.'public/icon/'.$m->icon;
					
						if(file_exists($inputFileName) && $m->icon != 'default.png') {	
									
							unlink($inputFileName);
						}
					}							
			}
			else
			{
				$response->error = esc_html__('Error occurred while deleting record','asl_admin');
				$response->msg   = $wpdb->show_errors();
			}
		}
		else
		{
			$response->error = esc_html__('Error occurred while deleting record','asl_admin');
		}

		if($response->success)
			$response->msg = ($multiple) ? esc_html__('Markers deleted successfully.','asl_admin') : esc_html__('Marker deleted successfully.','asl_admin');
		
		echo json_encode($response);die;
	}



	/**
	 * [update_marker update marker with icon]
	 * @return [type] [description]
	 */
	public function update_marker() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$data = $_REQUEST['data'];
		
		
		// with icon
		if($data['action'] == "notsame") {

			$target_dir  = WOOPANEL_STORE_LOCATOR_PATH."public/icon/";

			$namefile 	 = substr(strtolower($_FILES["files"]["name"]), 0, strpos(strtolower($_FILES["files"]["name"]), '.'));
			

			$imageFileType = pathinfo($_FILES["files"]["name"],PATHINFO_EXTENSION);
		 	$target_name   = uniqid();


		 	// Check the Size of the Image //
			$tmp_file 			  = $_FILES["files"]['tmp_name'];
			list($width, $height) = getimagesize($tmp_file);
			
			
			//add extension
			$target_name .= '.'.$imageFileType;

		 	
			$res = $wpdb->get_results( "SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."markers WHERE id = ".$data['marker_id']);

			

		 	if ($_FILES["files"]["size"] >  5000000) {
			    $response->msg = esc_html__("Sorry, your file is too large.",'asl_admin');
			    
			    
			}
			// not a valid format
			else if(!in_array($imageFileType, array('jpg','png','jpeg','gif','JPG'))) {
			    $response->msg = esc_html__("Sorry, only JPG, JPEG, PNG & GIF files are allowed.",'asl_admin');
			    
			}
			else if($width > $this->max_ico_width || $height > $this->max_ico_width) {

				$response->msg = esc_html__("Max Image dimensions Width and Height is {$this->max_ico_width} x {$this->max_ico_height} px.<br> Recommended Icon size is 20 x 32 px or around it",'asl_admin');
			}
			// upload 
			else if(move_uploaded_file($_FILES["files"]["tmp_name"], $target_dir.$target_name)) {
				//delete previous file

				
				
					$wpdb->update(WOOPANEL_STORE_LOCATOR_PREFIX."markers",array( 'marker_name'=> $data['marker_name'], 'icon'=> $target_name),array('id' => $data['marker_id']),array( '%s' ), array( '%d' ));		
					$response->msg = esc_html__('Updated Successfully.','asl_admin');
					$response->success = true;
					if (file_exists($target_dir.$res[0]->icon)) {	
						unlink($target_dir.$res[0]->icon);
					}
			}
			//error
			else {

				$response->msg = esc_html__('Some error occured','asl_admin');
				
			}

		}
		//without icon
		else {

			$wpdb->update(WOOPANEL_STORE_LOCATOR_PREFIX."markers",array( 'marker_name'=> $data['marker_name']),array('id' => $data['marker_id']),array( '%s' ), array( '%d' ));		
			$response->msg = esc_html__('Updated Successfully.','asl_admin');
			$response->success = true;	

		}
		
		echo json_encode($response);die;
	}

	
	/**
	 * [get_markers GET the Markers List]
	 * @return [type] [description]
	 */
	public function get_markers() {

		global $wpdb;
		$start = isset( $_REQUEST['iDisplayStart'])?$_REQUEST['iDisplayStart']:0;		
		$params  = isset($_REQUEST)?$_REQUEST:null; 	

		$acolumns = array(
			'id','id','marker_name','is_active','icon'
		);

		$columnsFull = $acolumns;

		$clause = array();

		if(isset($_REQUEST['filter'])) {

			foreach($_REQUEST['filter'] as $key => $value) {

				if($value != '') {

					if($key == 'is_active')
					{
						$value = ($value == 'yes')?1:0;
					}

					$clause[] = "$key like '%{$value}%'";
				}
			}	
		} 

		
		
		//iDisplayStart::Limit per page
		$sLimit = "";
		if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".intval( $_REQUEST['iDisplayStart'] ).", ".
				intval( $_REQUEST['iDisplayLength'] );
		}

		/*
		 * Ordering
		 */
		$sOrder = "";
		if ( isset( $_REQUEST['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";

			for ( $i=0 ; $i < intval( $_REQUEST['iSortingCols'] ) ; $i++ )
			{
				if (isset($_REQUEST['iSortCol_'.$i]))
				{
					$sOrder .= "`".$acolumns[ intval( $_REQUEST['iSortCol_'.$i] )  ]."` ".$_REQUEST['sSortDir_0'];
					break;
				}
			}
			

			//$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}
		

		$sWhere = implode(' AND ',$clause);
		
		if($sWhere != '')$sWhere = ' WHERE '.$sWhere;
		
		$fields = implode(',', $columnsFull);
		
		###get the fields###
		$sql = 	"SELECT $fields FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."markers";

		$sqlCount = "SELECT count(*) 'count' FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."markers";

		/*
		 * SQL queries
		 * Get data to display
		 */
		$sQuery = "{$sql} {$sWhere} {$sOrder} {$sLimit}";
		$data_output = $wpdb->get_results($sQuery);
		
		/* Data set length after filtering */
		$sQuery = "{$sqlCount} {$sWhere}";
		$r = $wpdb->get_results($sQuery);
		$iFilteredTotal = $r[0]->count;
		
		$iTotal = $iFilteredTotal;

	    /*
		 * Output
		 */
		$sEcho = isset($_REQUEST['sEcho'])?intval($_REQUEST['sEcho']):1;
		$output = array(
			"sEcho" => intval($_REQUEST['sEcho']),
			//"test" => $test,
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);
		
		foreach($data_output as $aRow)
	    {
	    	$row = $aRow;

	    	if($row->is_active == 1) {

	        	 $row->is_active = 'Yes';
	        }	       
	    	else {

	    		$row->is_active = 'No';	
	    	}


	    	$row->icon 	 = "<img  src='".WOOPANEL_STORE_LOCATOR_URL."public/icon/".$row->icon."' alt=''  style='width:20px'/>";	
	    	$row->check  = '<div class="custom-control custom-checkbox"><input type="checkbox" data-id="'.$row->id.'" class="custom-control-input" id="asl-chk-'.$row->id.'"><label class="custom-control-label" for="asl-chk-'.$row->id.'"></label></div>';
	    	$row->action = '<div class="edit-options"><a data-id="'.$row->id.'" title="Edit" class="glyphicon-edit edit_marker"><svg width="14" height="14"><use xlink:href="#i-edit"></use></svg></a><a title="Delete" data-id="'.$row->id.'" class="glyphicon-trash delete_marker"><svg width="14" height="14"><use xlink:href="#i-trash"></use></svg></a></div>';
	        $output['aaData'][] = $row;
	    }

		echo json_encode($output);
		die;
	}

	/**
	 * [get_marker_by_id get marker by id]
	 * @return [type] [description]
	 */
	public function get_marker_by_id() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$store_id = $_REQUEST['marker_id'];
		

		$response->list = $wpdb->get_results( "SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."markers WHERE id = ".$store_id);

		if(count($response->list)!=0){

			$response->success = true;

		}
		else{
			$response->error = esc_html__('Error occurred while geting record','asl_admin');

		}
		echo json_encode($response);die;
	}

	//////////////////////////
	///////Methods for Logo //
	//////////////////////////
	

	/**
	 * [delete_logo Delete a Logo]
	 * @return [type] [description]
	 */
	public function delete_logo() {
		
		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$multiple = $_REQUEST['multiple'];
		$delete_sql;$mResults;

		if($multiple) {

			$item_ids 		 = implode(",",$_POST['item_ids']);
			$delete_sql 	 = "DELETE FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."storelogos WHERE id IN (".$item_ids.")";
			$mResults 		 = $wpdb->get_results("SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."storelogos WHERE id IN (".$item_ids.")");
		}
		else {

			$item_id 		 = $_REQUEST['logo_id'];
			$delete_sql 	 = "DELETE FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."storelogos WHERE id = ".$item_id;
			$mResults 		 = $wpdb->get_results("SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."storelogos WHERE id = ".$item_id );
		}


		if(count($mResults) != 0) {
			
			if($wpdb->query($delete_sql)) {

					$response->success = true;

					foreach($mResults as $m) {

						$inputFileName = WOOPANEL_STORE_LOCATOR_PATH.'public/Logo/'.$m->path;
					
						if(file_exists($inputFileName) && $m->path != 'default.png') {	
									
							unlink($inputFileName);
						}
					}							
			}
			else
			{
				$response->error = esc_html__('Error occurred while deleting record','asl_admin');
				$response->msg   = $wpdb->show_errors();
			}
		}
		else
		{
			$response->error = esc_html__('Error occurred while deleting record','asl_admin');
		}

		if($response->success)
			$response->msg = ($multiple) ? esc_html__('Logos deleted Successfully.','asl_admin') : esc_html__('Logo deleted Successfully.','asl_admin');
		
		echo json_encode($response);die;
	}



	/**
	 * [update_logo update logo with icon]
	 * @return [type] [description]
	 */
	public function update_logo() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$data = $_REQUEST['data'];
		
		
		// with icon
		if($data['action'] == "notsame") {

			$target_dir  = WOOPANEL_STORE_LOCATOR_PATH."public/Logo/";

			$namefile 	 = substr(strtolower($_FILES["files"]["name"]), 0, strpos(strtolower($_FILES["files"]["name"]), '.'));
			

			$imageFileType = pathinfo($_FILES["files"]["name"],PATHINFO_EXTENSION);
		 	$target_name   = uniqid();
			
			$tmp_file = $_FILES["files"]['tmp_name'];
			list($width, $height) = getimagesize($tmp_file);
			
			//add extension
			$target_name .= '.'.$imageFileType;

		 	
			$res = $wpdb->get_results( "SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."storelogos WHERE id = ".$data['logo_id']);

				

		 	if ($_FILES["files"]["size"] >  5000000) {
			    
			    $response->msg = esc_html__("Sorry, your file is too large.",'asl_admin');
			}
			// not a valid format
			else if(!in_array($imageFileType, array('jpg','png','jpeg','gif','JPG'))) {
			    $response->msg = esc_html__("Sorry, only JPG, JPEG, PNG & GIF files are allowed.",'asl_admin');
			    
			}
			else if($width > $this->max_img_width || $height > $this->max_img_width) {

				$response->msg = esc_html__("Max Image dimensions Width and Height is {$this->max_img_width} x {$this->max_img_height} px.<br> Recommended Logo size is 250 x 250 px",'asl_admin');
			}
			// upload 
			else if(move_uploaded_file($_FILES["files"]["tmp_name"], $target_dir.$target_name)) {
				//delete previous file
				
					$wpdb->update(WOOPANEL_STORE_LOCATOR_PREFIX."storelogos",array( 'name'=> $data['logo_name'], 'path'=> $target_name),array('id' => $data['logo_id']),array( '%s' ), array( '%d' ));		
					$response->msg = esc_html__('Updated Successfully.','asl_admin');
					$response->success = true;
					if (file_exists($target_dir.$res[0]->icon)) {	
						unlink($target_dir.$res[0]->icon);
					}
			}
			//error
			else {

				$response->msg = esc_html__('Some error occured','asl_admin');
				
			}

		}
		//without icon
		else {

			$wpdb->update(WOOPANEL_STORE_LOCATOR_PREFIX."storelogos",array( 'name'=> $data['logo_name']),array('id' => $data['logo_id']),array( '%s' ), array( '%d' ));		
			$response->msg = esc_html__('Updated Successfully.','asl_admin');
			$response->success = true;	

		}
		
		echo json_encode($response);die;
	}


	/**
	 * [get_logo_by_id get logo by id]
	 * @return [type] [description]
	 */
	public function get_logo_by_id() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$store_id = $_REQUEST['logo_id'];
		

		$response->list = $wpdb->get_results( "SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."storelogos WHERE id = ".$store_id);

		if(count($response->list)!=0){

			$response->success = true;

		}
		else{
			$response->error = esc_html__('Error occurred while geting record','asl_admin');

		}
		echo json_encode($response);die;
	}


	/**
	 * [get_logos GET the Logos]
	 * @return [type] [description]
	 */
	public function get_logos() {

		global $wpdb;
		$start = isset( $_REQUEST['iDisplayStart'])?$_REQUEST['iDisplayStart']:0;		
		$params  = isset($_REQUEST)?$_REQUEST:null; 	

		$acolumns = array(
			'id','id','name','path'
		);

		$columnsFull = $acolumns;

		$clause = array();

		if(isset($_REQUEST['filter'])) {

			foreach($_REQUEST['filter'] as $key => $value) {

				if($value != '') {

					$clause[] = "$key like '%{$value}%'";
				}
			}	
		} 

		
		
		//iDisplayStart::Limit per page
		$sLimit = "";
		if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".intval( $_REQUEST['iDisplayStart'] ).", ".
				intval( $_REQUEST['iDisplayLength'] );
		}

		/*
		 * Ordering
		 */
		$sOrder = "";
		if ( isset( $_REQUEST['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";

			for ( $i=0 ; $i < intval( $_REQUEST['iSortingCols'] ) ; $i++ )
			{
				if (isset($_REQUEST['iSortCol_'.$i]))
				{
					$sOrder .= "`".$acolumns[ intval( $_REQUEST['iSortCol_'.$i] )  ]."` ".$_REQUEST['sSortDir_0'];
					break;
				}
			}
			

			//$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}
		

		$sWhere = implode(' AND ',$clause);
		
		if($sWhere != '')$sWhere = ' WHERE '.$sWhere;
		
		$fields = implode(',', $columnsFull);
		
		###get the fields###
		$sql = 	"SELECT $fields FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."storelogos";

		$sqlCount = "SELECT count(*) 'count' FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."storelogos";

		/*
		 * SQL queries
		 * Get data to display
		 */
		$sQuery = "{$sql} {$sWhere} {$sOrder} {$sLimit}";
		$data_output = $wpdb->get_results($sQuery);
		
		/* Data set length after filtering */
		$sQuery = "{$sqlCount} {$sWhere}";
		$r = $wpdb->get_results($sQuery);
		$iFilteredTotal = $r[0]->count;
		
		$iTotal = $iFilteredTotal;

	    /*
		 * Output
		 */
		$sEcho = isset($_REQUEST['sEcho'])?intval($_REQUEST['sEcho']):1;
		$output = array(
			"sEcho" => intval($_REQUEST['sEcho']),
			//"test" => $test,
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);
		
		foreach($data_output as $aRow)
	    {
	    	$row = $aRow;

	    	$row->path 	 = '<img src="'.WOOPANEL_STORE_LOCATOR_URL.'public/Logo/'.$row->path.'"  style="max-width:100px"/>';
	    	$row->check  = '<div class="custom-control custom-checkbox"><input type="checkbox" data-id="'.$row->id.'" class="custom-control-input" id="asl-chk-'.$row->id.'"><label class="custom-control-label" for="asl-chk-'.$row->id.'"></label></div>';
	    	$row->action = '<div class="edit-options"><a data-id="'.$row->id.'" title="Edit" class="glyphicon-edit edit_logo"><svg width="14" height="14"><use xlink:href="#i-edit"></use></svg></a><a title="Delete" data-id="'.$row->id.'" class="glyphicon-trash delete_logo"><svg width="14" height="14"><use xlink:href="#i-trash"></use></svg></a></div>';
	        $output['aaData'][] = $row;
	    }

		echo json_encode($output);die;
	}



	/**
	 * [get_store_list GET List of Stores]
	 * @return [type] [description]
	 */
	public function get_store_list() {
		
		global $wpdb;
		$start = isset( $_REQUEST['iDisplayStart'])?$_REQUEST['iDisplayStart']:0;		
		$params  = isset($_REQUEST)?$_REQUEST:null; 	

		$acolumns = array(
			WOOPANEL_STORE_LOCATOR_PREFIX.'stores.id', WOOPANEL_STORE_LOCATOR_PREFIX.'stores.id ',WOOPANEL_STORE_LOCATOR_PREFIX.'stores.id ','title','description',
			'lat','lng','street','state','city',
			'phone','email','website','postal_code','is_disabled',
			WOOPANEL_STORE_LOCATOR_PREFIX.'stores.id','marker_id', 'logo_id',
			WOOPANEL_STORE_LOCATOR_PREFIX.'stores.created_on'/*,'country_id'*/
		);

		
		$columnsFull = array(
			WOOPANEL_STORE_LOCATOR_PREFIX.'stores.id as id',WOOPANEL_STORE_LOCATOR_PREFIX.'stores.id as id',WOOPANEL_STORE_LOCATOR_PREFIX.'stores.id as id','title','description','lat','lng','street','state','city','phone','email','website','postal_code','is_disabled',WOOPANEL_STORE_LOCATOR_PREFIX.'stores.created_on'/*,WOOPANEL_STORE_LOCATOR_PREFIX.'countries.country_name'*/
		);

		//	Show the Category in Grid, make it false for high records and fast query	
		$category_in_grid = true;

		$cat_in_grid_data = $wpdb->get_results("SELECT `value` FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."configs WHERE `key` = 'cat_in_grid'");
		
		if($cat_in_grid_data && $cat_in_grid_data[0] && $cat_in_grid_data[0]->value == '0')
			$category_in_grid = false;		

		

		$clause = array();

		if(isset($_REQUEST['filter'])) {

			foreach($_REQUEST['filter'] as $key => $value) {

				if($value != '') {

					if($key == 'is_disabled')
					{
						$value = ($value == 'yes')?1:0;
					}
					elseif($key == 'marker_id' || $key == 'logo_id')
					{
						
						$clause[] = WOOPANEL_STORE_LOCATOR_PREFIX."stores.{$key} = '{$value}'";
						continue;
					}

						
					$clause[] = WOOPANEL_STORE_LOCATOR_PREFIX."stores.{$key} LIKE '%{$value}%'";
				}
			}	
		}
		

		//iDisplayStart::Limit per page
		$sLimit = "";
		$displayStart = isset($_REQUEST['iDisplayStart']) ? intval($_REQUEST['iDisplayStart']) : 0;
		
		if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".$displayStart.", ".
				intval( $_REQUEST['iDisplayLength'] );
		}
		else
			$sLimit = "LIMIT ".$displayStart.", 20 ";

		/*
		 * Ordering
		 */
		$sOrder = "";
		if ( isset( $_REQUEST['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";

			for ( $i=0 ; $i < intval( $_REQUEST['iSortingCols'] ) ; $i++ )
			{
				if (isset($_REQUEST['iSortCol_'.$i]))
				{
					$sOrder .= $acolumns[ intval( $_REQUEST['iSortCol_'.$i] )  ]." ".$_REQUEST['sSortDir_0'];
					break;
				}
			}
			

			//$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}
		

		$sWhere = implode(' AND ',$clause);
		
		if($sWhere != '')$sWhere = ' WHERE '.$sWhere;
		
		$fields = implode(',', $columnsFull);
		

		$fields .= ($category_in_grid)?',marker_id,logo_id,group_concat(category_id) as categories,iso_code_2': ',marker_id,logo_id';

		###get the fields###
		$sql 			= 	($category_in_grid)? ("SELECT $fields FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."stores LEFT JOIN ".WOOPANEL_STORE_LOCATOR_PREFIX."stores_categories ON ".WOOPANEL_STORE_LOCATOR_PREFIX."stores.id = ".WOOPANEL_STORE_LOCATOR_PREFIX."stores_categories.store_id  LEFT JOIN ".WOOPANEL_STORE_LOCATOR_PREFIX."countries ON ".WOOPANEL_STORE_LOCATOR_PREFIX."stores.country = ".WOOPANEL_STORE_LOCATOR_PREFIX."countries.id "): ("SELECT $fields FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."stores ");


		$sqlCount = "SELECT count(*) 'count' FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."stores";
		

		/*
		 * SQL queries
		 * Get data to display
		 */
		$dQuery = $sQuery = ($category_in_grid)? "{$sql} {$sWhere} GROUP BY ".WOOPANEL_STORE_LOCATOR_PREFIX."stores.id {$sOrder} {$sLimit}" : "{$sql} {$sWhere} {$sOrder} {$sLimit}";
		
		
		$data_output = $wpdb->get_results($sQuery);
		$wpdb->show_errors = false;
		$error = $wpdb->last_error;
		
			
		/* Data set length after filtering */
		$sQuery = "{$sqlCount} {$sWhere}";
		$r = $wpdb->get_results($sQuery);
		$iFilteredTotal = $r[0]->count;
		
		$iTotal = $iFilteredTotal;


	    /*
		 * Output
		 */
		$sEcho  = isset($_REQUEST['sEcho'])?intval($_REQUEST['sEcho']):1;
		$output = array(
			"sEcho" => intval($_REQUEST['sEcho']),
			"iTotalRecords" => $iTotal,
			"iTotalDisplayRecords" => $iFilteredTotal,
			"aaData" => array()
		);

		if($error) {

			$output['error'] = $error;
			$output['query'] = $dQuery;
		}


		$days_in_words = array('0'=>'Sun','1'=>'Mon','2'=>'Tues','3'=>'Wed','4'=>'Thur','5'=>'Fri','6'=>'Sat');
		
		foreach($data_output as $aRow) {
	    	
	    	$row = $aRow;

	    	//	No Category in Grid
	    	if(!$category_in_grid) 
	    		$row->categories = '';

	    	$edit_url = 'admin.php?page=woopanel-edit-store&store_id='.$row->id;

	    	$row->action = '<div class="edit-options"><a class="row-cpy" title="Duplicate" data-id="'.$row->id.'"><svg width="14" height="14"><use xlink:href="#i-clipboard"></use></svg></a><a href="'.$edit_url.'"><svg width="14" height="14"><use xlink:href="#i-edit"></use></svg></a><a title="Delete" data-id="'.$row->id.'" class="glyphicon-trash"><svg width="14" height="14"><use xlink:href="#i-trash"></use></svg></a></div>';
	    	$row->check  = '<div class="custom-control custom-checkbox"><input type="checkbox" data-id="'.$row->id.'" class="custom-control-input" id="asl-chk-'.$row->id.'"><label class="custom-control-label" for="asl-chk-'.$row->id.'"></label></div>';

	    	//Show country with state
	    	if($row->state && $row->iso_code_2)
	    		$row->state = $row->state.', '.$row->iso_code_2;

	        if($row->is_disabled == 1) {

	        	 $row->is_disabled = 'Yes';

	        }	       
	    	else {
	    		$row->is_disabled = 'No';	
	    	}

	        $output['aaData'][] = $row;

	        //get the categories
	     	if($aRow->categories) {

	     		$categories_ = $wpdb->get_results("SELECT category_name FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."categories WHERE id IN ($aRow->categories)");

	     		$cnames = array();
	     		foreach($categories_ as $cat_)
	     			$cnames[] = $cat_->category_name;

	     		$aRow->categories = implode(', ', $cnames);
	     	}   
	    }

		echo json_encode($output);die;
	}


	/**
	 * [save_custom_map save customize map]
	 * @return [type] [description]
	 */
	public function save_custom_map() {

			
	}


	/**
	 * [save_setting save ASL Setting]
	 * @return [type] [description]
	 */
	public function save_setting() {

		global $wpdb;

		$response  = new \stdclass();
		$response->success = false;

		$data_ = $_POST['data'];

		//	Remove Script tag will be saved in wp_options
		$remove_script_tag = $data_['remove_maps_script'];
		unset($data_['remove_maps_script']);

		$keys  =  array_keys($data_);



		foreach($keys as $key) {
			$check = $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM " . WOOPANEL_STORE_LOCATOR_PREFIX . "configs WHERE `key` = %s",
				$key
			) );

			if( empty($check) ) {
				$wpdb->insert(
					WOOPANEL_STORE_LOCATOR_PREFIX . "configs",
					array(
						"key"  => $key,
						"value" => $data_[$key]
					)
				);
			}else {
				$wpdb->update(WOOPANEL_STORE_LOCATOR_PREFIX."configs",
					array('value' => $data_[$key]),
					array('key' => $key)
				);
			}

		}



		$response->msg 	   = esc_html__("Setting has been Updated Successfully.",'asl_admin');
    $response->success = true;

		echo json_encode($response);die;
	}

	
	////////////////////////
	//////////Page Callee //
	////////////////////////
	

	/**
	 * [admin_plugin_settings Admin Plugi]
	 * @return [type] [description]
	 */
	public function admin_plugin_settings() {

		include WOOPANEL_STORE_LOCATOR_PATH.'admin/partials/add_store.php';
	}

	/**
	 * [edit_store Edit a Store]
	 * @return [type] [description]
	 */
	public function edit_store() {

		global $wpdb;

		$countries = $wpdb->get_results("SELECT id,country FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."countries");
		$logos     = $wpdb->get_results( "SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."storelogos ORDER BY name");
		$markers   = $wpdb->get_results( "SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."markers");
        $category  = $wpdb->get_results( "SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."categories");

		
		$store_id = isset($_REQUEST['store_id'])?$_REQUEST['store_id']:null;

		if(!$store_id) {

			die('Invalid Store Id.');
		}

		$store  = $wpdb->get_results("SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."stores WHERE id = $store_id");		

		$storecategory = $wpdb->get_results("SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."stores_categories WHERE store_id = $store_id");

		if(!$store || !$store[0]) {
			die('Invalid Store Id');
		}
		
		$store = $store[0];

		$storelogo = $wpdb->get_results("SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."storelogos WHERE id = ".$store->logo_id);
		

		//api key
		$sql = "SELECT `key`,`value` FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."configs WHERE `key` = 'api_key' || `key` = 'time_format'";
		$all_configs_result = $wpdb->get_results($sql);


		$all_configs = array();

		foreach($all_configs_result as $c) {
			$all_configs[$c->key] = $c->value;
		}

		include WOOPANEL_STORE_LOCATOR_PATH.'admin/partials/edit_store.php';		
	}


	/**
	 * [admin_add_new_store Add a New Store]
	 * @return [type] [description]
	 */
	public function admin_add_new_store() {
		
		global $wpdb;

		//api key
		$sql = "SELECT `key`,`value` FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."configs WHERE `key` = 'api_key' || `key` = 'time_format' || `key` = 'default_lat' || `key` = 'default_lng'";
		$all_configs_result = $wpdb->get_results($sql);


		$all_configs = array();

		foreach($all_configs_result as $c) {
			$all_configs[$c->key] = $c->value;
		}

    $logos     = $wpdb->get_results( "SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."storelogos ORDER BY name");
    $markers   = $wpdb->get_results( "SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."markers");
    $category = $wpdb->get_results( "SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."categories");
		$countries = $wpdb->get_results("SELECT * FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."countries");

		include WOOPANEL_STORE_LOCATOR_PATH.'admin/partials/add_store.php';    
	}


	/**
	 * [admin_dashboard Plugin Dashboard]
	 * @return [type] [description]
	 */
	public function admin_dashboard() {


		global $wpdb;

		$sql = "SELECT `key`,`value` FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."configs WHERE `key` = 'api_key'";
		$all_configs_result = $wpdb->get_results($sql);

		$all_configs = array('api_key' => $all_configs_result[0]->value);
		$all_stats = array();
		
		$temp = $wpdb->get_results( "SELECT count(*) as c FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."markers");;
        $all_stats['markers']	 = $temp[0]->c; 

        $temp = $wpdb->get_results( "SELECT count(*) as c FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."stores");;
        $all_stats['stores']    = $temp[0]->c;

	
		$temp = $wpdb->get_results( "SELECT count(*) as c FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."categories");;
    $all_stats['categories'] = $temp[0]->c;


    //	Ugly HAck
    // require_once WOOPANEL_STORE_LOCATOR_PATH . 'includes/class-store-locator-activator.php';
    // WooPanel_Store_Locator_Activator::upgrade_method();


		include WOOPANEL_STORE_LOCATOR_PATH.'admin/partials/dashboard.php';    
	}

	/**
	 * [get_stat_by_month Get the Stats of the Analytics]
	 * @return [type] [description]
	 */
	public function get_stat_by_month() {

		global $wpdb;

		$month  = $_REQUEST['m'];
		$year   = $_REQUEST['y'];

		$c_m 	= date('m');
		$c_y 	= date('y');

		
		if(!$month || is_nan($month)) {

			//Current
			$month = $c_m;
		}

		if(!$year || is_nan($year)) {

			//Current
			$year = $c_y;
		}


		$date = intval(date('d'));

		//Not Current Month
		if($month != $c_m && $year != $c_y) {

			/*Month last date*/
			$a_date = "{$year}-{$month}-1";
			$date 	= date("t", strtotime($a_date));
		}
		

		//WHERE YEAR(created_on) = YEAR(NOW()) AND MONTH(created_on) = MONTH(NOW())
		$results = $wpdb->get_results("SELECT DAY(created_on) AS d, COUNT(*) AS c FROM `".WOOPANEL_STORE_LOCATOR_PREFIX."stores_view`  WHERE YEAR(created_on) = {$year} AND MONTH(created_on) = {$month} GROUP BY DAY(created_on)");

		
		
		$days_stats = array();

		for($a = 1; $a <= $date; $a++) {

			$days_stats[(string)$a] = 0; 
		}

		foreach($results as $row) {

			$days_stats[$row->d] = $row->c;
		}
	
		echo json_encode(array('data'=>$days_stats));die;
	}


	/**
	 * [admin_delete_all_stores Delete All Stores, Logos and Category Relations]
	 * @return [type] [description]
	 */
	public function admin_delete_all_stores() {
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$response = new \stdclass();
		$response->success = false;
		
		global $wpdb;
		$prefix = WOOPANEL_STORE_LOCATOR_PREFIX;
        
        $wpdb->query("TRUNCATE TABLE `{$prefix}stores_categories`");
        $wpdb->query("TRUNCATE TABLE `{$prefix}stores`");
        $wpdb->query("TRUNCATE TABLE `{$prefix}storelogos`");
     	
     	$response->success  = true;
     	$response->msg 	    = esc_html__('All Stores with Logo Deleted','asl_admin');

     	echo json_encode($response);die;
	}

	/**
	 * [validate_api_key Validateyour Google API Key]
	 * @return [type] [description]
	 */
	public function validate_api_key() {

		global $wpdb;

		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		require_once WOOPANEL_STORE_LOCATOR_PATH . 'includes/class-store-locator-helper.php';

		$response = new \stdclass();
		$response->success = false;

		//Get the API KEY
		$sql 			= "SELECT `key`,`value` FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."configs WHERE `key` = 'server_key'";
		$configs_result = $wpdb->get_results($sql);
		$api_key 		= $configs_result[0]->value;

		if($api_key) {

			//Test Address
			$street 	= '315 West Main Street';
			$city		= 'Walla Walla';
			$state 		= 'WA';
			$zip 		= '45553';
			$country 	= 'US';

			$_address = $street.', '.$zip.'  '.$city.' '.$state.' '.$country;

			$results = WooPanel_Store_Locator_Helper::getLnt($_address,$api_key,true);


			if(isset($results->error_message)) {

				$response->msg 	  = $results->error_message;
			}
			else {

				$response->msg 	   = esc_html__('Valid API Key','asl_admin');	
				$response->success = true;	
			}

			$response->success = false;
		}
		else
			$response->msg = esc_html__('Server Google API Key is Missing','asl_admin');


		echo json_encode($response);die;
	}


	/**
	 * [admin_manage_categories Manage Categories]
	 * @return [type] [description]
	 */
	public function admin_manage_categories() {
		include WOOPANEL_STORE_LOCATOR_PATH.'admin/partials/categories.php';
	}

	/**
	 * [admin_store_markers Manage Markers]
	 * @return [type] [description]
	 */
	public function admin_store_markers() {
		include WOOPANEL_STORE_LOCATOR_PATH.'admin/partials/markers.php';
	}


	/**
	 * [admin_store_logos Manage Logos]
	 * @return [type] [description]
	 */
	public function admin_store_logos() {
		include WOOPANEL_STORE_LOCATOR_PATH.'admin/partials/logos.php';
	}
	
	/**
	 * [admin_manage_store Manage Stores]
	 * @return [type] [description]
	 */
	public function admin_manage_store() {
		include WOOPANEL_STORE_LOCATOR_PATH.'admin/partials/manage_store.php';
	}

	/**
	 * [admin_import_stores Admin Import Store Page]
	 * @return [type] [description]
	 */
	public function admin_import_stores() {

		//Check if ziparhive is installed
		global $wpdb;

		//Get the API KEY
		$sql 			= "SELECT `key`,`value` FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."configs WHERE `key` = 'server_key'";
		$configs_result = $wpdb->get_results($sql);
		$api_key 		= $configs_result[0]->value;

		if(!$api_key) {

			$api_key = esc_html__('Google API Key is Missing','asl_admin');
		}

		include WOOPANEL_STORE_LOCATOR_PATH.'admin/partials/import_store.php';
	}


	/**
	 * [admin_customize_map Customize the Map Page]
	 * @return [type] [description]
	 */
	public function admin_customize_map() {

		global $wpdb;

		$sql = "SELECT `key`,`value` FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."configs WHERE `key` = 'api_key' OR `key` = 'default_lat' OR `key` = 'default_lng' ORDER BY id;";
		$all_configs_result = $wpdb->get_results($sql);

		
		$config_list = array();
		foreach($all_configs_result as $item) {
			$config_list[$item->key] = $item->value;
		}

		$all_configs = array('api_key' => $config_list['api_key'],'default_lat' => $config_list['default_lat'],'default_lng' => $config_list['default_lng']);
		

		$map_customize  = '[]';


		//add_action( 'init', 'my_theme_add_editor_styles' );
		include WOOPANEL_STORE_LOCATOR_PATH.'admin/partials/customize_map.php';
	}

	
	/**
	 * [admin_user_settings ASL Settings Page]
	 * @return [type] [description]
	 */
	public function admin_user_settings() {
	   
	   	global $wpdb;
	   	
		$sql = "SELECT `key`,`value` FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."configs";
		$all_configs_result = $wpdb->get_results($sql);
		
		$all_configs = array();
		foreach($all_configs_result as $config)
		{
			$all_configs[$config->key] = $config->value;	
		}

		///get Countries
		$countries 				= $wpdb->get_results("SELECT country,iso_code_2  as code FROM ".WOOPANEL_STORE_LOCATOR_PREFIX."countries");
		
		// Remove Google Script tags
		$all_configs['remove_maps_script'] = get_option('wplsl-remove_maps_script');

		include WOOPANEL_STORE_LOCATOR_PATH.'admin/partials/user_setting.php';
	}	
}

