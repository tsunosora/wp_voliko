<?php
class NBT_Order_Upload_Frontend {

	public $upload_form;

	/**
	 * Class constructor.
	 */
	public function __construct() {

		$this->upload_form = NB_Solution::get_setting('order-upload');

		add_action('single_template', array($this, 'woocommerce_init'), 10, 1);
		
		add_action('wp_enqueue_scripts', array($this, 'embed_style'));
		add_filter('woocommerce_cart_item_name', array($this, 'woocommerce_cart_item_name'), 10, 3 );
		add_action( 'woocommerce_thankyou', array($this, 'nbt_woocommerce_payment_complete'), 10, 1 );


		add_action('woocommerce_order_item_meta_end', array($this, 'woocommerce_order_items_table'), 20, 3 );
		add_filter( 'body_class', array($this, 'nbt_body_classes'), 10, 1 );
		add_shortcode( 'nbt_upload', array($this, 'shortcode_ajax_upload'), 10, 1 );
		add_shortcode( 'nbt_order_upload', array($this, 'shortcode_nbt_order_upload'), 10, 1 );
		
		if( defined('PREFIX_NBT_SOL') ) {
			add_filter('nbt_solutions_localize', array($this, 'nbt_solutions_localize'), 11, 1);
		}
		
        add_action( 'wp_ajax_nopriv_nbt_ou_show', array($this, 'nbt_ou_show') );
		add_action( 'wp_ajax_nbt_ou_show', array($this, 'nbt_ou_show') );

		add_action ('woocommerce_add_to_cart', array($this, 'woocommerce_add_to_cart'), 0);
		add_action('woocommerce_before_variations_form', array($this, 'remove_selected_variation'), 5);

		
		if( isset($_GET['dev']) ) {
			$order_upload = @unserialize($_SESSION['order_upload']);
			echo '<pre>';
			print_r($order_upload);
			echo '</pre>';
		}
	}



	public function remove_selected_variation() {
		if( isset( $_SESSION['before_selected']) ) {
			$before_selected = $_SESSION['before_selected'];
			if( is_array($before_selected) ) {?>
			<script>
			jQuery(document).ready(function($) {
				<?php foreach($before_selected as $k => $v) {?>
					$('[name="<?php echo $k;?>"]').prop('selectedIndex',0);
				<?php }?>
			});
			</script>
			<?php
			}
		}
	}

	public function woocommerce_add_to_cart() {
		if( isset($_POST['add-to-cart']) || isset( $_POST['nbd-add-to-cart']) ) {
			if( isset($_POST['add-to-cart']) ) {
				$pid = md5($_POST['add-to-cart']);
			}

			if( isset($_POST['nbd-add-to-cart']) ) {
				$pid = md5($_POST['nbd-add-to-cart']);
			}

			if( isset($_POST['variation_id']) ) {
				$new_string = '';
				$new_array = array();
				foreach( $_POST as $key => $val) {
					if( preg_match('/attribute_(.*)/', $key, $output) ) {
						$k = $output[0];
						$new_string .= $output[0] . str_replace('\\', '', $val);
						
						$new_array[$k] = str_replace('\\', '', $val);
					}
				}
				$_SESSION['before_selected'] = $new_array;
				$pid = md5($new_string);
			}

			$lists_file = @unserialize($_SESSION['order_upload']);
			$cart_order_upload = @unserialize($_SESSION['cart_order_upload']);

			if( isset($lists_file[$pid]) ) {
				$cart_order_upload[$pid] = $lists_file[$pid];
				foreach( $cart_order_upload as $k => $file) {
					unset($lists_file[$k]);
				}

				$_SESSION['cart_order_upload'] = serialize($cart_order_upload);
			}
			
			$_SESSION['order_upload'] = serialize($lists_file);
			
		}
	}
	
	public function woocommerce_init( $single_template ) {
		global $post;


		if( is_product() ) {
			$product = wc_get_product($post->ID);

			if( ! $product->is_type( 'variable' ) && $this->upload_form['nbt_order_upload_upload_form_position'] == 'woocommerce_before_single_variation' ) {
				add_action('woocommerce_before_add_to_cart_form', array($this, 'is_single_product'));
			}else {

				$single_hooks = empty($this->upload_form['nbt_order_upload_upload_form_position']) ? "woocommerce_single_product_summary" : $this->upload_form['nbt_order_upload_upload_form_position'];


				if( $single_hooks == 'cart_form' ) {
					$single_hooks = 'woocommerce_before_add_to_cart_form';
				}

				add_action($single_hooks, array($this, 'is_single_product'));
			}
		}else {
			return $single_template;
		}
		
	}

	public function nbt_ou_show() {
		$json = array();
		
		$product_id = (int)$_REQUEST['product_id'];
		
		if( $product_id ) {
			ob_start();
			do_shortcode('[nbt_order_upload product_id="'. $product_id .'"]');
			$json['tpl'] = ob_get_clean();
			$json['complete'] = true;
		}
		
		wp_send_json($json);
		die();
	}
	
	public function nbt_solutions_localize($array) {
		$array['nbt_ou_label'] = __('Order Upload', 'nbt-solution');
		
		return $array;
	}
	
	public function shortcode_nbt_order_upload($atts){
		global $post;

		$atts = shortcode_atts(
		array(
			'product_id' => ''
		), $atts, 'bartag' );
		
		if( isset($atts['product_id']) ) {
			$product = wc_get_product($atts['product_id']);
		}else {
			$product = wc_get_product($post->ID);
		}

		echo '<input type="hidden" name="add-to-cart" value="'.$product->get_id().'">';

		$this->is_single_product($product);
	}

	public function is_single_product($_product = false){

		global $product, $wpdb;

		if($_product){
			$product = $_product;
		}

		
		$lists_file = @unserialize($_SESSION['order_upload']);

		if( ! isset($_SESSION['guest_id']))
 		{
			$rand_customer_id = WC()->session->get_customer_id();
			$_SESSION['guest_id'] = $rand_customer_id;
		}

		if(get_post_meta($product->get_id(), '_order_upload', true) == 'on'){

			$is_hide = '';
			$product_id = md5( $product->get_id() );

			if( $product->is_type('variable') && ! empty($this->upload_form['nbt_order_upload_enable_require_variations']) ) {
				$is_hide = ' style="display: none"';
				//$product_id = md5($product->get_variation_id());
			}
			?>

		<div id="nb-orderupload-wrapper"<?php echo $is_hide;?>>
		
		<?php 
		$ou_settings 				= get_option('order-upload_settings');
		$is_enable_dropbox_button 	= isset($ou_settings['nbt_order_upload_enable_dropbox_button']) && $ou_settings['nbt_order_upload_enable_dropbox_button'];
		$is_enable_box_button		= isset($ou_settings['nbt_order_upload_enable_box_button']) && $ou_settings['nbt_order_upload_enable_box_button'];
		$is_enable_g_drive_button	= isset($ou_settings['nbt_order_upload_enable_g_drive_button']) && $ou_settings['nbt_order_upload_enable_g_drive_button'];

		if(( $is_enable_dropbox_button ) || ( $is_enable_box_button ) || ( $is_enable_g_drive_button ) ):?>
		<div class="sp-ou-meta">
			<p class="sp-ou-title"><?php echo __('Order Upload', 'nbt-solution');?></p>
			<p class="sp-ou-des"><?php echo __('Choose your files to upload', 'nbt-solution');?></p>
		</div>
		<div class="ou-tab">
		  <button class="tablinks activated" tab-rel='from_u_computer'><?php echo __('Your Computer', 'nbt-solution');?></button>
		  <button class="tablinks" tab-rel='from_online_services'><?php echo __('Online Services', 'nbt-solution');?></button>
		</div>

		<?php endif;?>
		
		<div class="ou-tabcontent">
			<div id="from_u_computer">
				<form action="" method="POST" class="ibenic_upload_form" enctype="multipart/form-data">
					<div id="nbt-order-upload">
						<div class="nbt-upload-zone">
							<div class="nbt-oupload-target"><?php echo __('Drop your file(s) here or click to add one!', 'nbt-solution');?></div>
							<input class="nbt-upload-input" type="file" multiple="">
						</div>

					</div>
				</form>
			</div>

			<?php
					$number_of_items_class 	= 'total_items_';
					$count_item 			= 0;
					if($is_enable_dropbox_button) {
						$count_item += 1;
					}
					if($is_enable_box_button) {

						$count_item += 1;
					}
					if($is_enable_g_drive_button) {

						$count_item += 1;
					}

					$number_of_items_class = 'total_items_' . $count_item;
			?>
			<?php if($count_item > 0): ?>
				<div id="from_online_services">
					
					<div class="list_online_services <?php echo esc_attr($number_of_items_class);?>">
						<?php

							if($is_enable_dropbox_button) {

								$this->add_online_services_button(array('name' => 'dropbox'));
							}

							if($is_enable_box_button) {

								$this->add_online_services_button(array('name' => 'box'));
							}

							if($is_enable_g_drive_button) {

								$this->add_online_services_button(array('name' => 'drive'));
							}
							$this->add_google_drive_popup();
						?>
					</div>
				</div>
			<?php endif;?>
		</div>

		<div class="nbt-oupload-output">
			<?php
			if( isset($_GET['dev']) ) {
				echo $product_id;
			}
			?>
			<div class="progress"><div class="progress-bar progress-bar-success progress-bar-striped"></div></div>
			<div class="nbt-oupload-body">
				<?php if(isset($lists_file[$product_id])){
					global $wpdb;
			    	$upload_dir = wp_upload_dir();
			    	$destination_folder = $upload_dir['baseurl'].'/nbt-order-uploads/';
			    	$destination_basedir_folder = $upload_dir['basedir'].'/nbt-order-uploads/';
					$lists_file = $lists_file[$product_id];
					foreach ($lists_file as $key => $attach_id) {

						$file = $wpdb->get_row( "SELECT post_title, post_name, post_mime_type FROM {$wpdb->prefix}posts WHERE ID = '".$attach_id."'" );
						if($file){

							if($file->post_mime_type != '') {
								$extension = explode('/', $file->post_mime_type);
								if( isset($extension[1]) ) {
									$filename = $file->post_name.'.'.$extension[1];
								}else {
									$filename = $file->post_mime_type;
								}
								
								$post_thumbnail_url = $destination_folder.'thumb_'.$filename;
								$post_thumbnail_basedir = $destination_basedir_folder.'thumb_'.$filename;
								if( ! file_exists($post_thumbnail_basedir) ) {
									$post_thumbnail_url = NBT_OUP_URL . 'assets/img/thumb_'.$filename .'.png';
									$post_thumbnail_basedir = NBT_OUP_PATH . 'assets/img/thumb_'.$filename .'.png';
								}

								if( ! file_exists($post_thumbnail_basedir) ) {
									$post_thumbnail_url = NBT_OUP_URL . 'assets/img/thumb_default.png';
								}?>
								<div id="<?php echo $file->post_name;?>" class="nbt-file success">
									<div class="nbt-file-left"><img width="50" src="<?php echo $post_thumbnail_url;?>"></div>
									<div class="nbt-file-right">
										<div class="name"><?php echo $file->post_title;?> <i class="nbt-icon-cancel"></i></div>
										<div class="size"><?php echo NBT_Solutions_Order_Upload::format_size(get_post_meta($attach_id, '_wp_attached_size', true));?></div>
									</div>
								</div>
					<?php
							}
							else { ?>
								
								<div id="<?php echo $file->post_name;?>" class="nbt-file success">
									<div class="nbt-file-left file-icon"><i class="fa fa-file-o fa-3x" aria-hidden="true"></i></div>
									<div class="nbt-file-right">
										<div class="name"><?php echo $file->post_title;?> <i class="nbt-icon-cancel"></i></div>
									</div>
								</div>
							<?php
							}
						}
					}

				}?>
			</div>
		</div>
	</div>

		<?php
		}
	}

	public function woocommerce_cart_item_name( $item_title, $product, $cart_item_key = null ) {
		global $wpdb;
		$return = $return_loop = $item_title;

		$product_id = md5($product['product_id']);
		
		if( ! empty( $product['variation_id']) ) {
			$new_string = '';
			foreach( $product['variation'] as $k => $v ) {
				$new_string .= $k.$v;
			}
			$product_id = md5($new_string);
		}

		$lists_file = @unserialize($_SESSION['cart_order_upload']);

    	$upload_dir = wp_upload_dir();
    	
    	$destination_folder = $upload_dir['baseurl'].'/nbt-order-uploads/';
    	$destination_basedir_folder = $upload_dir['basedir'].'/nbt-order-uploads/';
		if(isset($lists_file[$product_id]) && $lfiles = $lists_file[$product_id]){
			
			
			$count_files = 0;
			foreach ($lfiles as $key => $file) {

				$files = $wpdb->get_row( "SELECT post_title, post_name, post_mime_type FROM {$wpdb->prefix}posts WHERE ID = '".$file."'" );
				if($files) {
					$count_files += 1;

					if($files->post_mime_type != '') {

						$extension = explode('/', $files->post_mime_type);
						if( isset($extension[1]) ) {
							$filename = $files->post_name.'.'.$extension[1];
						}else {
							$filename = $files->post_mime_type;
						}

						$post_thumbnail_url = $destination_folder.'thumb_'.$filename;
						$post_thumbnail_basedir = $destination_basedir_folder.'thumb_'.$filename;
						if( ! file_exists($post_thumbnail_basedir) ) {
							$post_thumbnail_url = NBT_OUP_URL . 'assets/img/thumb_'.$files->post_mime_type.'.png';
							$post_thumbnail_basedir = NBT_OUP_PATH . 'assets/img/thumb_'.$files->post_mime_type.'.png';
						}

						if( ! file_exists($post_thumbnail_basedir) ) {
							$post_thumbnail_url = NBT_OUP_URL . 'assets/img/thumb_default.png';
						}
						
						
						$return_loop .= '<li><div class="left-files"><img src="'.$post_thumbnail_url.'"></div><div class="right-files"><h4>'.$files->post_title.'</h4><p>'.NBT_Solutions_Order_Upload::format_size(get_post_meta($file, '_wp_attached_size', true)).'</p></div></li>';
					}
					else {
						$return_loop .= '<li><div class="left-files"><i class="fa fa-file-o fa-lg" aria-hidden="true"></i></div><div class="right-files"><h4>'.$files->post_title.'</h4></div></li>';
					}
				}
			}

			$return .= '<div class="nbt-show-files"><a class="toggle-order-upload">'.wp_kses_data( sprintf( _n( '%d File', '%d Files', $count_files, 'nbt-solution' ), $count_files ) ).' <i class="nbt-icon-down-open"></i></a><ul style="display: none">';
			$return .= $return_loop;
			$return .= '</ul></div>';
		}

		if(get_post_meta($product_id, '_order_upload', true) == 'on' && NBT_Solutions_Order_Upload::is_osc()){
			$return .= sprintf( '<div id="nbt-upload-cart-%s" class="nbt-ou-fast"><button type="button" class="btn button btn-upload-fast">'. __('Upload Files', 'nbt-solution') .'</button></div>', $product_id );
		}
		
		return $return;
	}	

	public function nbt_woocommerce_payment_complete($order_id){
		global $wpdb;

		$lists_file = @unserialize($_SESSION['cart_order_upload']);

		if( empty($lists_file) && ! empty($_SESSION['order_upload']) ) {
			$lists_file = @unserialize($_SESSION['order_upload']);
		}

		if( !empty($lists_file) ){
			update_post_meta($order_id, 'order_upload', $lists_file);
		}

		unset($_SESSION['cart_order_upload']);
		unset($_SESSION['order_upload']);
		$get_customer_id = $_SESSION['guest_id'];
		$wpdb->delete( $wpdb->prefix.'nbt_order_upload', array( 'ou_key' => $get_customer_id ) );
		error_log( "Payment has been received for order $order_id" );
	}

	public function woocommerce_order_items_table($item_id, $item, $order){
		global $wpdb;

		$return = $return_loop = '';
		$lists_file = get_post_meta($order->get_id(), 'order_upload', true);

    	$upload_dir = wp_upload_dir();
    	$destination_folder = $upload_dir['baseurl'].'/nbt-order-uploads/';


		foreach ( $order->get_items() as $product_info ) {
			$product_id = ( int ) apply_filters ( 'woocommerce_add_to_cart_product_id', $product_info['product_id'] );
			$variation_id = $product_info->get_variation_id();


			if($product_info->get_id() == $item_id && isset($lists_file[$product_id]) && get_post_meta($product_id, '_order_upload', true) == 'on' || $product_info->get_id() == $item_id && isset($lists_file[$variation_id]) && get_post_meta($product_id, '_order_upload', true) == 'on' ){
				
				if( isset($lists_file[$variation_id]) ) {
					$lfiles = $lists_file[$variation_id];
				}else {
					$lfiles = $lists_file[$product_id];
				}

				$count_files = 0;
				foreach ($lfiles as $key => $file) {
					$files = $wpdb->get_row( "SELECT post_title, post_name, post_mime_type FROM {$wpdb->prefix}posts WHERE ID = '".$file."'" );
					
					if($files){
						$count_files += 1;
						$extension = explode('/', $files->post_mime_type);
						$filename = $files->post_name.'.'.$extension[1];
						$post_thumbnail_url = $destination_folder.'thumb_'.$filename;

						$return_loop .= '<li><div class="left-files"><img src="'.$post_thumbnail_url.'"></div><div class="right-files"><h4>'.$files->post_title.'</h4><p>'.NBT_Solutions_Order_Upload::format_size(get_post_meta($file, '_wp_attached_size', true)).'</p></div></li>';
					}
				}

				$return .= '<div class="nbt-show-files"><a class="toggle-order-upload">'.wp_kses_data( sprintf( _n( '%d File', '%d Files', count($lfiles), 'nbt-solution' ), count($lfiles) ) ).' <i class="nbt-icon-down-open"></i></a><ul style="display: none">';
				$return .= $return_loop;
				$return .= '</ul></div>';
			}
		}
		echo $return;
	}

	public function nbt_body_classes( $classes ) {
		global $post;

		if(isset($post) && get_post_meta($post->ID, '_order_upload', true) == 'on'){
			$classes[] = 'has-order-upload';
		}
	     
	    return $classes;  
	}

	public function shortcode_ajax_upload(){
		if(isset($_REQUEST['wc-product']) && is_numeric($_REQUEST['wc-product'])){
			$product_id = $_REQUEST['wc-product'];
			$product = wc_get_product($product_id);
			?>
			<div id="nbt-order-page">
				<?php $this->is_single_product($product);?>
				<div class="nbtou-forward">
					<?php if( !class_exists('NBT_Solutions_One_Step_Checkout') ){?>
					<a class="btn btn-ou-cart" href="<?php echo wc_get_cart_url();?>"><?php echo __('Cart', 'nbt-solution');?></a>
					<?php }?>
					<a class="btn btn-ou-checkout" href="<?php echo wc_get_checkout_url();?>"><?php echo __('Checkout', 'nbt-solution');?></a>
					<input type="hidden" name="add-to-cart" value="<?php echo $product_id;?>" />
				</div>
			</div>

			<?php
		}
	}
	/**
	 * Enqueue scripts and stylesheets
	 */
	public function embed_style() {

		if( ! defined('PREFIX_NBT_SOL')){
			wp_enqueue_style( 'order-upload-frontend', NBT_OUP_URL .'assets/css/frontend.css', array(), '20160615' );
			wp_enqueue_style( 'nbt-fonts-frontend', NBT_OUP_URL .'assets/css/nbt-fonts.css', array(), '20160615' );
		}
		wp_enqueue_style( 'magnific-popup', NBT_OUP_URL .'assets/css/magnific-popup.css', array(), '20160615' );
		
		wp_enqueue_script( 'magnific-popup', NBT_OUP_URL . 'assets/js/jquery.magnific-popup.min.js', array(), time(), true );
		wp_enqueue_script( 'js-slimscroll', PREFIX_NBT_SOL_URL . 'assets/frontend/js/jquery.slimscroll.min.js', array(), time(), true );
		wp_enqueue_script( 'js-md5', PREFIX_NBT_SOL_URL . 'assets/frontend/js/md5.min.js', array( 'jquery' ), time(), true );

		if( ! defined('PREFIX_NBT_SOL')){
			wp_enqueue_script( 'order-upload-frontend', NBT_OUP_URL . 'assets/js/frontend.js', array( 'jquery' ), time(), true );
		}


		$file_extension = isset( $this->upload_form['nbt_order_upload_file_extension'] ) && $this->upload_form['nbt_order_upload_file_extension'] != '' ? $this->upload_form['nbt_order_upload_file_extension'] : 'jpg, png, gif';

		$file_extension_src = array();
		if( $file_extension ) {
			$array_file_extension = explode( ',', str_replace(' ', '', $file_extension) );
			if( $array_file_extension ) {
				foreach ( $array_file_extension as $ext ) {
					$icon_dir = NBT_OUP_PATH . 'assets/img/thumb_'.$ext.'.png';
					if( file_exists($icon_dir) ) {
						$file_extension_src[$ext] = NBT_OUP_URL . 'assets/img/thumb_'.$ext.'.png';
					}
				}
			}
		}

		$file_extension_src['default'] = NBT_OUP_URL . 'assets/img/thumb_default.png';

		wp_localize_script( 'frontend-solutions', 'nbtou', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'require_variation' => isset( $this->upload_form['nbt_order_upload_enable_require_variations'] ) && $this->upload_form['nbt_order_upload_enable_require_variations'] != '' ? $this->upload_form['nbt_order_upload_enable_require_variations'] : '',
			'require_upload' => isset( $this->upload_form['nbt_order_upload_enable_require_upload'] ) && $this->upload_form['nbt_order_upload_enable_require_upload'] != '' ? $this->upload_form['nbt_order_upload_enable_require_upload'] : '',
			'file_extension' => array_map( 'trim', explode(',', $file_extension) ),
			'file_of_number' => isset( $this->upload_form['nbt_order_upload_file_of_number'] ) && $this->upload_form['nbt_order_upload_file_of_number'] != '' ? $this->upload_form['nbt_order_upload_file_of_number'] : '3',
			'customer_id' => isset($_SESSION['guest_id']) ? $_SESSION['guest_id'] : '' ,
			'g_drive_clientid' => isset( $this->upload_form['nbt_order_upload_g_drive_clientid'] ) && $this->upload_form['nbt_order_upload_g_drive_clientid'] != '' ? $this->upload_form['nbt_order_upload_g_drive_clientid'] : '',
			'g_drive_apikey' => isset( $this->upload_form['nbt_order_upload_g_drive_apikey'] ) && $this->upload_form['nbt_order_upload_g_drive_apikey'] != '' ? $this->upload_form['nbt_order_upload_g_drive_apikey'] : '',
			'dropbox_apikey' => isset( $this->upload_form['nbt_order_upload_dropbox_apikey'] ) && $this->upload_form['nbt_order_upload_dropbox_apikey'] != '' ? $this->upload_form['nbt_order_upload_dropbox_apikey'] : '',
			'box_apikey' => isset( $this->upload_form['nbt_order_upload_box_apikey'] ) && $this->upload_form['nbt_order_upload_box_apikey'] != '' ? $this->upload_form['nbt_order_upload_box_apikey'] : '',
			'file_extension_src' => $file_extension_src,
			'label_restrict' => __('Restrict Allowed File Types %s', 'nbt-solution'),
			'label_numfiles' => __('You only upload number of files is %s', 'nbt-solution'),
			'label_variation' =>__( 'Please choose product options&hellip;', 'woocommerce' )
		));
		
	    wp_enqueue_script('thickbox');
	    wp_enqueue_style('thickbox');
	}

	public function add_online_services_button($args = array()) {

		$ou_settings = get_option('order-upload_settings');
		
		if($args['name'] == 'drive') {
			if( (! isset( $ou_settings['nbt_order_upload_g_drive_apikey'] ) &&  ! isset( $ou_settings['nbt_order_upload_g_drive_clientid']) )
				|| ( isset( $ou_settings['nbt_order_upload_g_drive_apikey'] ) && $ou_settings['nbt_order_upload_g_drive_apikey'] == ''  

				&& isset( $ou_settings['nbt_order_upload_g_drive_clientid'] ) && $ou_settings['nbt_order_upload_g_drive_clientid'] == '' ) ) {
			    $buttonid  = 'add-button-no-api';
			}else{
			    $buttonid  = 'add-g-drive';
			}
		}
		else if($args['name'] == 'dropbox') {
			if( ! isset( $ou_settings['nbt_order_upload_dropbox_apikey'] ) || 
				( isset( $ou_settings['nbt_order_upload_dropbox_apikey'] ) && $ou_settings['nbt_order_upload_dropbox_apikey'] == '' ) ) {
			    $buttonid  = 'add-button-no-api';
			}else{
			    $buttonid  = 'add-dropbox';
			}
		}
		else if($args['name'] == 'box') {
			if( ! isset( $ou_settings['nbt_order_upload_box_apikey'] ) || 
				( isset( $ou_settings['nbt_order_upload_box_apikey'] ) && $ou_settings['nbt_order_upload_box_apikey'] == '' ) ) {
			    $buttonid  = 'add-button-no-api';
			}else{
			    $buttonid  = 'add-box';
			}
		}

		$target = is_string($args) ? $args : 'content';
		$args = wp_parse_args($args, array(
		    'target'    => $target,
		    'echo'      => true,
		    'id'        => $buttonid,
		    'shortcode' => false,
		));

		if($args['name'] == 'drive') {
			$args['icon']      = plugins_url('../assets/img/icon-g-drive.png', __FILE__);
			$args['text']      = __('Drive', 'nbt-solution');
			$args['class']     = 'g-drive button';
		}
		else if($args['name'] == 'dropbox') {
			$args['icon']      = plugins_url('../assets/img/icon-dropbox.png', __FILE__);
			$args['text']      = __('Dropbox', 'nbt-solution');
			$args['class']     = 'dropbox button';
		}
		else if($args['name'] == 'box') {
			$args['icon']      = plugins_url('../assets/img/icon-box.png', __FILE__);
			$args['text']      = __('Box.com', 'nbt-solution');
			$args['class']     = 'box button';
		}

		if ($args['icon']) {
		    $args['icon'] = '<img src="' . $args['icon'] . '" /> ';
		}
		
		$button = '<a href="javascript:void(0);" id="' . $args['id'] . '" class="' . $args['class'] . '" title="' . $args['text'] . '" data-target="' . $args['target'] . '" >' . $args['icon'] . '<span>' . $args['text'] . '</span></a>';
		if ($args['echo']) {
		    echo $button;
		}
		return $button;
	}

	public function add_google_drive_popup() {
		include NBT_OUP_PATH . 'inc/popup.php';
	}

	public function add_dropbox_button($args = array()) {

		$ou_settings = get_option('order-upload_settings');
		
		if( ! isset( $ou_settings ) || 
			( isset( $ou_settings ) && $ou_settings['dropbox_apikey'] == '' ) ) {
		    $buttonid  = 'add-dropbox-no-api';
		}else{
		    $buttonid  = 'add-dropbox';
		}

		$target = is_string($args) ? $args : 'content';
		$args = wp_parse_args($args, array(
		    'target'    => $target,
		    'text'      => __('Dropbox', 'nbt-solution'),
		    'class'     => 'dropbox button',
            'icon'      => plugins_url('../assets/img/google-drive-icon.png', __FILE__),
		    'echo'      => true,
		    'id'        => $buttonid,
		    'shortcode' => false,
		));

		if ($args['icon']) {
		    $args['icon'] = '<img src="' . $args['icon'] . '" /> ';
		}

		
		$button = '<a href="javascript:void(0);" id="' . $args['id'] . '" class="' . $args['class'] . '" title="' . $args['text'] . '" data-target="' . $args['target'] . '" >' . $args['icon'] . $args['text'] . '</a>';
		if ($args['echo']) {
		    echo $button;
		}
		return $button;
	}
}
new NBT_Order_Upload_Frontend();