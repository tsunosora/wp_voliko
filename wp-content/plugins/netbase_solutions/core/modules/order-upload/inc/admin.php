<?php
class NBT_Order_Upload_Admin {

	public $available_tabs = array();

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'admin_print_scripts', array( $this, 'enqueue_scripts' ) );

		add_action('add_meta_boxes', array($this, 'add_design_box'), 30);

		add_filter( 'product_type_options', array( $this, 'admin_toggle_option' ), 10, 1  );
		add_action('woocommerce_process_product_meta_variable', array($this, 'woocommerce_process_product_meta_variable'), 10, 1);
		add_action('woocommerce_process_product_meta_simple', array($this, 'woocommerce_process_product_meta_variable'), 10, 1);
	}
	
    public function add_design_box() {
        add_meta_box('nbt_orderupload_order', __('Order Upload', 'nbt-solution'), array($this, 'order_upload'), 'shop_order', 'side', 'default');
    }

	public function woocommerce_process_product_meta_variable($post_id) {
		if(isset($_POST['_order_upload'])){
			update_post_meta( $post_id, '_order_upload', $_POST['_order_upload']);
		} else {
			update_post_meta( $post_id, '_order_upload', false);
		}
	}
	public function admin_toggle_option( $options ) {
		global $post;
		$default = 'off ';
		if(get_post_meta($post->ID, '_order_upload', true) == 'on'){
			$default = 'on ';
		}
		$options['order_upload'] = array(
			'id'            => '_order_upload',
			'wrapper_class' => $default.'show_if_variable show_if_simple',
			'label'         => __( 'Order Upload', 'WooCommerce Price Matrix' ),
			'description'   => __( 'Replace front-end dropdowns with a price matrix. This option limits "Used for varations" to 2.', 'WooCommerce Price Matrix' ),
			'default'       => trim($default),
		);

		return $options;
	}
    public function order_upload($post) {
    	global $wpdb;
        $order = new WC_Order($post->ID);
		$items = $order->get_items();
		
		$has_files = false;
		
		if( $items && ! empty($items) ) {
			$items_key = 1;
			$order_upload = get_post_meta($post->ID, 'order_upload', true);
			$_order_upload_status = get_post_meta($post->ID, '_order_upload_status', true);

			foreach( $items as $item) {
				$product = $item->get_product();
				$pid = md5($item->get_product_id());
				if ( $item->get_variation_id() ) {
					$new_string = '';
					$variation_attributes = $product->get_variation_attributes();

					if ( $meta_data = $item->get_formatted_meta_data( '' ) ) {
						foreach ( $meta_data as $meta_id => $meta ) {
							$new_string .= 'attribute_' . $meta->key.$meta->value;
						}
					} else {
						
						foreach( $variation_attributes as $k => $v ) {
							$new_string .= $k.$v;
						}
					}

					$pid = md5($new_string);
				}

				if( isset($order_upload[$pid]) ) {
					$files = $order_upload[$pid];?>
				<div id="file-<?php echo $pid;?>">
					<?php $product_link = $product ? admin_url( 'post.php?post=' . $product->get_id() . '&action=edit' ) : '';
			
					echo $product_link ? '<a href="' . esc_url( $product_link ) . '" class="wc-order-item-name"><h3>'.esc_html( $product->get_name() ) . '</h3></a>' : '<div class="class="wc-order-item-name"">' . esc_html( $product->get_name() ) . '</div>';
					$has_files = true;
					?>
					<span class="order-upload-item-number">Item #<?php echo $items_key;?></span>
					<ul class="admin-lists-files<?php if($_order_upload_status == 'accept'){ echo ' accept';}?>">
						<?php
						foreach ($files as $key => $file) {
							$files = $wpdb->get_row( "SELECT post_title, post_name, post_content, post_mime_type FROM {$wpdb->prefix}posts WHERE ID = '".$file."'" );
							if($files){
							?>
							<li class="clearfix" id="file-<?php echo md5($file);?>">
								<label for="file_<?php echo $file;?>"><?php echo $files->post_title;?></label>
								<div class="order-right">
									<?php if($files->post_mime_type != ''):?>
										<a href="<?php echo NBT_OUP_URL;?>inc/get-file.php?file=<?php echo $file;?>" target="_blank" class="wpf-umf-ou-uploaded-download button button-small button-secondary"> <?php echo __('Download', 'nbt-solution')?></a>
									<?php else:?>
										<a href="<?php echo $files->post_content;?>" target="_blank" class="wpf-umf-ou-uploaded-download button button-small button-secondary"> <?php echo __('View File', 'nbt-solution')?></a>
									<?php endif;?>
										<?php if( !$_order_upload_status ){?>
										<a href="#" target="_blank" data-orderid="<?php echo $order->get_id();?>" data-filenumber="<?php echo $file;?>" data-product-id="<?php echo $product_id;?>" class="nbtou-uploaded-delete button button-small button-secondary admin-red"></a>
										<?php }?>
								</div>
							</li>
							<?php
							}
						}?>
					</ul>
				</div>
				<?php
				$has_files = true;
				}
				$items_key++;
			}

			if($has_files){?>
				<hr />
				<div class="nbt-check_all clearfix">
					<div class="alignleft">
						&nbsp;
					</div>
					<div class="alignright">
						<a href="<?php echo NBT_OUP_URL;?>inc/download.php?order=<?php echo $post->ID;?>" class="button button-small button-secondary btn-download-all">Download all</a>
					</div>
				</div>
				<?php if( !$_order_upload_status ){?>
				<div class="nbt-action clearfix">
					With selected:
					<div class="alignright">
					    <select name="wpf_umf_uploaded_file_approve" class="wpf-umf-select-small" data-id="<?php echo $order->get_id();?>">
					        <option value="accept"<?php if($_order_upload_status == 'accept'){ echo ' selected';}?>>Accept</option>
					        <option value="decline"<?php if($_order_upload_status == 'decline'){ echo ' selected';}?>>Decline</option>
					    </select>
					    <a href="#" class="button button-small button-primary" id="wpf_umf_uploaded_file_submit">GO</a>
					</div>
				</div>
				<?php
				}

			}else {
				echo __('No file attached.', 'nbt-solution');
			}
		}
    }
	/**
	 * Load stylesheet and scripts in edit product attribute screen
	 */
	public function enqueue_scripts() {

		wp_enqueue_style( 'order-upload-admin', NBT_OUP_URL . 'assets/css/admin.css?v=' . time(), array( )  );
		wp_enqueue_script( 'order-upload-admin', NBT_OUP_URL . 'assets/js/admin.js', array(  ));

		wp_localize_script( 'order-upload-admin', 'nbtou', array(
			'ajax_url' => admin_url( 'admin-ajax.php' )
		));
	}


	public function register_subpage_media() {
		if( defined('PREFIX_NBT_SOL') && defined('PREFIX_NBT_SOL_DEV') && PREFIX_NBT_SOL_DEV ) {
			add_submenu_page('solutions', 'Order Upload Library', 'Order Upload Library', 'manage_options', 'order-upload', array($this, 'media_order_admin_page')); 
		}else{
			add_submenu_page('solution-dashboard', 'Order Upload Library', 'Order Upload Library', 'manage_options', 'order-upload', array($this, 'media_order_admin_page')); 
		}
		

	}

	public function media_order_admin_page(){
		global $wpdb, $post;
		if ( !current_user_can('upload_files') ){
			wp_die( __( 'Sorry, you are not allowed to upload files.' ) );
		}

		$querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key = 'attachment_order_upload' AND $wpdb->postmeta.meta_value = '1' AND $wpdb->posts.post_status = 'inherit' AND $wpdb->posts.post_type = 'attachment' ORDER BY $wpdb->posts.post_date DESC";

		 $query_attachments = $wpdb->get_results($querystr, OBJECT);
	
		include NBT_OUP_PATH . 'tpl' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'admin-media.php';
	}



}
new NBT_Order_Upload_Admin();