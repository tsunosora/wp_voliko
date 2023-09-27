<?php
class NBT_PriceMatrix_Admin{
	public $available_tabs = array();
	
	protected $args;
	public $tab_price_matrix = 'price_matrix';
	
	function __construct() {
		add_action( 'admin_enqueue_scripts', array($this, 'price_matrix_scripts_method') );

		add_filter('woocommerce_product_data_tabs', array($this, 'woocommerce_product_tabs_price_matrix'), 50, 1);
		add_action( 'woocommerce_product_data_panels', array($this, 'woocommerce_product_panels_price_matrix') );
		add_action('save_post', array($this, 'pm_woocommerce_process_product_meta_variable'), 10, 1);

/* 		add_action( 'wp_ajax_nopriv_pm_load_variations', array($this, 'pm_load_variations') );
		add_action( 'wp_ajax_pm_load_variations', array($this, 'pm_load_variations') ); */
		add_action( 'wp_ajax_nopriv_pm_save_variations', array($this, 'pm_save_variations') );
		add_action( 'wp_ajax_pm_save_variations', array($this, 'pm_save_variations') );
/* 		add_action( 'wp_ajax_nopriv_pm_enter_price', array($this, 'pm_enter_price') );
		add_action( 'wp_ajax_pm_enter_price', array($this, 'pm_enter_price') ); */

		//add_action( 'wp_ajax_pm_save_price', array('NBT_Solutions_Price_Matrix', 'save_price') );
/* 		add_action( 'wp_ajax_nopriv_pm_load_table', array($this, 'pm_load_table') );
		add_action( 'wp_ajax_pm_load_table', array($this, 'pm_load_table') ); */
		add_action( 'wp_ajax_pm_add_row', array($this, 'pm_add_row') );

		add_filter( 'product_type_options', array( $this, 'admin_toggle_option' ), 10, 1  );

        add_action('admin_notices', array($this, 'general_admin_notice'));



        if( ! defined('PREFIX_NBT_SOL') ) {
            if( !class_exists('NBT_Plugins') ) {
                require_once NBT_PRICEMATRIX_PATH . 'inc/plugins.php';
            }

            add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );
        }else {
        	add_filter( 'nbs_admin_localize_script', array( $this, 'price_matrix_localize'), 99, 1 );
        }


		if(!did_action( 'woocommerce_before_single_product' ) === 1 ){
			add_action( 'admin_notices', array( $this, 'add_disable_hooks_notice') );
		}

		

		//add_action('woocommerce_bulk_edit_variations', array($this, 'clear_cache_remove_variations'), 10, 4);
		add_action('woocommerce_after_product_attribute_settings', array($this, 'clear_cache_attribute'), 10, 2);


		$this->add_ajax_events();
	}



    public function register_panel() {
        $args = array(
            'create_menu_page' => true,
            'parent_slug'   => '',
            'page_title'    => __( 'Price Matrix', 'nbt-plugins' ),
            'menu_title'    => __( 'Price Matrix', 'nbt-plugins' ),
            'capability'    => apply_filters( 'nbt_cs_settings_panel_capability', 'manage_options' ),
            'parent'        => '',
            'parent_page'   => 'ntb_plugin_panel',
            'page'          => NBT_Solutions_Price_Matrix::$plugin_id,
            'admin-tabs'    => $this->available_tabs,
            'functions'     => array(__CLASS__ , 'ntb_cs_page')
        );

        $this->_panel = new NBT_Plugins($args);
    }


	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 */
	public function add_ajax_events() {
		$ajax_events = array(
			'save_price'                          => false,
			'input_price'                         => false,
			'order_attribute'                     => false,
			'load_variations'                     => false,
			'load_table'                	      => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_pricematrix_' . $ajax_event, array( new NBT_Price_Matrix_Ajax(), $ajax_event ) );
		}
	}

	// public function clear_cache_remove_variations($bulk_action, $data, $product_id, $variations) {
		
	// 	if( $bulk_action == 'delete_all') {
	// 		global $wpdb;
	// 		$rs = $wpdb->query($wpdb->prepare("DELETE FROM `wp_options` WHERE `option_name` LIKE %s", '%{pm_' . $product_id . '}%'));
	// 	}
	// }


    public function disable_hooks_notice() {?>
        <div class="error">
            <p><?php _e( 'WooCommerce plugin is not activated. Please install and activate it to use for plugin <strong>Ajax Drop Down Cart for WooCommerce Wordpress</strong>.', 'nbt-ajax-cart' ); ?></p>
        </div>
        <?php    
	}
	
	public function pm_add_row() {
		$json = array();
		$product_id = isset( $_POST['product_id'] ) ? wc_clean( $_POST['product_id'] ) : '';
		$attributes = $_POST['attributes'];


		if( $attributes ) {
			$product = wc_get_product($product_id);
			$attributes = explode(',', $attributes);
			$get_attributes = $product->get_attributes( 'edit' );

			foreach( $attributes as $attribute) {
				unset($get_attributes[$attribute]);
			}

			if( $get_attributes ) {
				reset($get_attributes);
				$first_key = key($get_attributes);

				$new_attribute = array();
				$attribute = $get_attributes[$first_key];
				if ( $attribute->is_taxonomy() && ( $attribute_taxonomy = $attribute->get_taxonomy_object() ) ) {
					$rs = NBT_Solutions_Color_Swatches::get_attribute_taxonomies( $product->get_id(), str_replace('pa_', '', $first_key) );
					
					if( $rs ) {
						$new_attribute = array(
							'label' => $rs->attribute_label,
							'slug' => $first_key
						);
					}
				} else {
					$new_attribute = array(
						'label' => $attribute->get_name(),
						'slug' => $first_key
					);
				}

				if( ! empty($new_attribute) ) {
					ob_start();
					include(NBT_PRICEMATRIX_PATH .'tpl/admin/row-repeater-line.php');
					$json['template'] = ob_get_clean();
					$json['complete'] = true;
				}
			}
		}

		wp_send_json($json);
		
	}


    public static function ntb_cs_page(){
    	include(NBT_PRICEMATRIX_PATH .'tpl/admin/admin.php');
    }

    function general_admin_notice() {
    	global $post;
    	if(isset($post->post_type) && $post->post_type == 'product'){
    		if(get_post_meta($post->ID, '_enable_price_matrix', true) == 'on' && !get_post_meta($post->ID, '_pm_num', true)){
	        ?>
	        <div class="error">
	            <p><?php printf('It seems Price Matrix has been <strong>activated</strong> but you didn\'t set any attributes and prices for this product. You can see <a href="%s" target="_blank">this guide</a> for more details.', home_url() ); ?></p>
	        </div>
    		<?php
    		}
    	}
    }

	/**
	 * Add admin option
	 */
	public function admin_toggle_option( $options ) {
		global $post;
		$default = 'no ';
		if(get_post_meta($post->ID, '_enable_price_matrix', true) == 'on'){
			$default = 'yes ';
		}
		$options['enable_price_matrix'] = array(
			'id'            => '_enable_price_matrix',
			'wrapper_class' => $default.'show_if_variable',
			'label'         => __( 'Price Matrix', 'WooCommerce Price Matrix' ),
			'description'   => __( 'Replace front-end dropdowns with a price matrix. This option limits "Used for varations" to 2.', 'WooCommerce Price Matrix' ),
			'default'       => trim($default),
		);


		return $options;
	}

	function clear_cache_attribute( $attribute, $i ) {
		global $wpdb;
		if( defined('DOING_AJAX') && DOING_AJAX && isset($_POST['data']) && $_POST['product_type'] == 'variable' ) {
			//write_log('trigger-change_attr');

	    	$cache_name = 'cache_attribute_' . $_POST['post_id'];
	    	$cache_attribute_key = get_transient( $cache_name );

	    	if( ! empty($cache_attribute_key) ) {
	    		//write_log('has_variation_id');
				$attr_new = array();
		    	$product = wc_get_product($_POST['post_id']);
		    	$get_attributes = $product->get_attributes( 'edit' );
		    	
				foreach ($get_attributes as $k_attr => $attribute) {
					/* Select other value */
					if ( $attribute->is_taxonomy() ) :
						foreach ( $attribute->get_terms() as $option ) :
							$attr_new[$k_attr][] = esc_attr( $option->slug );
						endforeach;

					else :
						foreach ( $attribute->get_options() as $option ) :
							$attr_new[$k_attr][] = esc_attr( $option );
						endforeach;
					endif;

				}

	    		$array_diff = serialize($attr_new);

	    		if( $array_diff != $cache_attribute_key ) {
	    			$this->delete_cache_variation($_POST['post_id']);
	    			$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->posts WHERE post_parent = %d", $_POST['post_id']) );
	    			delete_transient($cache_name);
	    			//write_log('delete old cache, wait create variation_id');
	    		}
	    	}
		}
	}


    public function remove_variations($post_id){
    	global $wpdb;

    	$product = wc_get_product($post_id);
    	$_pm_table_attr = get_post_meta($post_id, '_product_attributes', true);
    	$get_attributes = $product->get_attributes( 'edit' );

    	$cache_name = 'cache_attribute_' . $post_id;
    	$cache_attribute_key = get_transient( $cache_name );

    	$attr_new = array();
		foreach ($get_attributes as $k_attr => $attribute) {
			/* Select other value */
			if ( $attribute->is_taxonomy() ) :
				foreach ( $attribute->get_terms() as $option ) :
					$attr_new[$k_attr][] = esc_attr( $option->slug );
				endforeach;

			else :
				foreach ( $attribute->get_options() as $option ) :
					$attr_new[$k_attr][] = esc_attr( $option );
				endforeach;
			endif;

		}

    	if( empty($cache_attribute_key) ) {
    		$results =  $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_type = 'product_variation' AND post_parent = '%s'", $post_id), OBJECT);

    		if( ! empty($results) ) {
    			$cache_attribute_key = serialize($attr_new);
    			set_transient( $cache_name, $cache_attribute_key );
    			$this->set_cache_variation($post_id);
    			//write_log('empty: set_cache');
    		}else {
    			//write_log('empty: delete_cache');
    			$this->delete_cache_variation($post_id);	
    		}
    		
    		
    	}else {
    		//write_log('so sánh với cái hiện tại');
    		$array_diff = serialize($attr_new);

    		if( $array_diff != $cache_attribute_key ) {
    			$this->delete_cache_variation($post_id);
    			$this->set_cache_variation($post_id);
    			delete_transient($cache_name);
    			//write_log('delete and set new cache');
    		}
    	}

    	
    }

    public function delete_cache_variation($post_id) {
		global $wpdb;
		$search = '%' . $wpdb->esc_like( '{pm_' . $post_id . '}' ) . '%';
		$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name LIKE %s", $search) );
    }

    public function set_cache_variation($post_id) {
		// Set $post global so its available, like within the admin screens.
		global $post;

		$loop           = 0;

		$product_object = wc_get_product( $post_id );
		$per_page       = 50;
		$page           = 1;
		$variations     = wc_get_products(
			array(
				'status'  => array( 'private', 'publish' ),
				'type'    => 'variation',
				'parent'  => $post_id,
				'limit'   => $per_page,
				'page'    => $page,
				'orderby' => array(
					'menu_order' => 'ASC',
					'ID'         => 'DESC',
				),
				'return'  => 'objects',
			)
		);

		if ( $variations ) {
			wc_render_invalid_variation_notice( $product_object );


			foreach ( $variations as $variation_object ) {
				$cache_name_str = null;
				$variation_id   = $variation_object->get_id();
				$attribute_values = $variation_object->get_attributes( 'edit' );

				foreach ($attribute_values as $ak => $av) {
					$cache_name_str .= $ak.$av;
				}

				$cache_name = '{pm_' . $post_id .'}'.$cache_name_str;
				//write_log($cache_name . ': '. $variation_id);
				set_transient($cache_name, $variation_id);
				$loop++;
			}
		}
    }

	public function pm_save_variations(){
		global $wpdb;
		$error = $success = false;
		$json = array();


		if ( ! wp_verify_nonce( $_REQUEST['security'], '_price_matrix_save' ) ) {
		     die( 'Security check' ); 
		} else {
			$product_id = $_REQUEST['product_id'];
			$pm_attrs = $_REQUEST['pm_attr'];
			$pm_direction = $_REQUEST['pm_direction'];

			
			

			$totals = count($pm_direction);
			if( $totals >= 4 ) {
				$totals = 4;
			}

			$final_attrs = array_slice($pm_attrs, 0, $totals);
			$final_direction = array_slice($pm_direction, 0, $totals);
			$max_direction = array_count_values($final_direction);
			$max_direction_key = array_search(max($max_direction), $max_direction);
			
			switch( $totals ) {
				case 2:
					if( isset($max_direction['horizontal']) && $max_direction['horizontal'] == 1 && isset($max_direction['vertical']) && $max_direction['vertical'] == 1) {
						$success = true;
					}else {
						$json['message'] = 'You must choose the direction of the table, is one horizontal or one vertical';
					}
					break;
				case 3:
					if( isset($max_direction['horizontal']) && $max_direction['horizontal'] == 2 && isset($max_direction['vertical']) && $max_direction['vertical'] == 1
					|| isset($max_direction['horizontal']) && $max_direction['horizontal'] == 1 && isset($max_direction['vertical']) && $max_direction['vertical'] == 2 ) {
						$success = true;
					}else {
						$json['message'] = 'You must choose the direction of the table, is two horizontal or two vertical';
					}
					break;
				case 4:
					if( isset($max_direction['horizontal']) && $max_direction['horizontal'] == 2 && isset($max_direction['vertical']) && $max_direction['vertical'] == 2) {
						$success = true;
					}else {
						$json['message'] = 'You must choose the direction of the table, is two horizontal or two vertical';
					}
					break;
				default:
					break;
			}

			if( $success ) {
				//$count_parent =  (array) $wpdb->get_row($wpdb->prepare("SELECT COUNT(*) AS num_posts FROM ".$wpdb->prefix."posts WHERE post_parent = '%s' AND post_type = 'product_variation' AND post_status = 'publish'", $product_id), ARRAY_A);

				$new_array = array();
				foreach ($final_attrs as $key => $value) {
					$new_array[$final_direction[$key]][] = $value;
				}

				update_post_meta($product_id, '_pm_table_attr', $final_attrs);
				update_post_meta($product_id, '_pm_table_direction', $final_direction);
				update_post_meta($product_id, '_pm_attr', $new_array);
				update_post_meta($product_id, '_pm_direction', $max_direction_key);
				update_post_meta($product_id, '_pm_num', $totals);

				$json['complete'] = true;
				$json['notice'] = $count_parent.'<div id="message" class="inline notice woocommerce-message msg-enter-price">
					<p>Press the <strong>Input Price</strong> button to input price for this product before saving!</p>
				</div>';

			}

			wp_send_json($json);

		}
	}

	public function pm_enter_price($product_id = false, $deprived = false, $load = false){
		if( ! $product_id ) {
			$product_id = $_REQUEST['product_id'];
		}

		$product = wc_get_product($product_id);


		$_pm_table_attr = get_post_meta($product_id, '_pm_table_attr', true);
		$_pm_direction = get_post_meta($product_id, '_pm_direction', true);
		$_pm_num = get_post_meta($product_id, '_pm_num', true);

		$symbol = get_woocommerce_currency_symbol(get_option('woocommerce_currency'));

		$json['complete'] = true;
		$html = '';
		if(empty($deprived)){
			$html .= '<div id="price-matrix-popup" class="white-popup mfp-hide">
			<h2>Price Matrix: Input Price</h2><div class="enter-price-wrap">';
		}
		ob_start();
		$attr = $_POST['attr'];
		foreach ($attr as $key => $value) {
			if(in_array($key, $_pm_table_attr)){
				unset($attr[$key]);
			}
		}

		$total_real_attr = count($_POST['attr']);

		$suffix = '';
		if($_pm_num == 3){
			$suffix = '-'.$_pm_direction.'-'.$_pm_num;
		}elseif($_pm_num == 4){
			$suffix = '-'.$_pm_num;
		}
		$khuyet = false;
		if($total_real_attr != $_pm_num){
			$khuyet = true;
		}

		if(file_exists(NBT_PRICEMATRIX_PATH .'tpl/admin/table-matrix' . $suffix.'.php')){
			
			$_pm_table_direction = get_post_meta($product_id, '_pm_table_direction', true);
			$_pm_attr = get_post_meta($product_id, '_pm_attr', true);

			if( $khuyet && empty($deprived) ) {
				foreach ($attr as $key => $value) {
					$attribute = pm_attribute_label($key, $product_id);
					$tax = pm_attribute_tax($key, $product_id);

					$deprived[] = array('name' => $key, 'value' => $attribute[0]->slug);
					?>
			        <div class="select-wrap">
			        	<label><?php echo $tax;?></label>
			        	<select id="<?php echo $key;?>" name="attr[<?php echo $key;?>]" class="attr-select">
			        		<?php foreach ($attribute as $key => $attr):
			        			printf('<option value="%s">%s</option>', $attr->slug, $attr->name);
			        		endforeach;?>
			        	</select>
			        </div>
				<?php }

				echo '<p class="attribute-p"><strong>Note:</strong> When you finish enter the price for this attribute, please press the Save Price Matrix button before choosing another attribute. Double click to input the price!</p><p>To input the sale price, please use the "-" characters between prices. Eg: original price is $5, sale price is $2, the convention is 5-2</p>';

			}

			if(!$khuyet && !$load){
				echo '<p class="attribute-p nopadding"><strong>Note:</strong> Double click to input the price!</p><p>To input the sale price, please use the "-" characters between prices. Eg: original price is $5, sale price is $2, the convention is 5-2</p>';
			}
			include(NBT_PRICEMATRIX_PATH .'tpl/admin/table-matrix' . $suffix.'.php');
		}else{
			echo 'Sorry, this options not available for enter price';
		}
		
		$html .= ob_get_clean();
		$html .= '</div></div>';
		$json['html'] = $html;

		echo json_encode($json, TRUE);
		wp_die();
	}

/* 	public function pm_load_table(){
		$product_id = $_REQUEST['product_id'];
		$attr = $_REQUEST['attr'];
		$load = $_REQUEST['load'];
		$deprived = array();
		if(is_array($attr)){
			foreach ($attr as $key => $value) {
				$deprived[] = array('name' => $key, 'value' => $value);
			}

		}
		
		ob_start();
		$this->pm_enter_price($product_id, $deprived, $load);
		$out = ob_get_clean();
		$json['complete'] = true;
		$json['return'] = $out;

		echo json_encode($json, TRUE);
		wp_die();
	} */

	public function woocommerce_product_tabs_price_matrix($product_data_tabs){
		$product_data_tabs['price_matrix'] = array(
			'label' => __( 'Price Matrix', 'nbt-woocommerce-price-matrix' ),
			'target' => 'price_matrix',
			'class'  => array( 'hide show_if_variable' ),
		);
		return $product_data_tabs;
	}

	public function woocommerce_product_panels_price_matrix(){
		global $woocommerce, $post;

		$adding_to_cart     	= wc_get_product( $post->ID );
		$variation_attributes	= $adding_to_cart->get_attributes();
		$_pm_table_attr = get_post_meta($post->ID, '_pm_table_attr', true);
		$_pm_table_direction = get_post_meta($post->ID, '_pm_table_direction', true);
		$count_attr = get_post_meta($post->ID, '_pm_num', true);

		include(NBT_PRICEMATRIX_PATH .'tpl/admin/price-matrix-product.php');
	}

	public function pm_woocommerce_process_product_meta_variable($post_id){
		if(isset($_POST['_pm_type'])){
			update_post_meta( $post_id, '_pm_type', stripslashes( $_POST['_pm_type'] ) );
		}
		if(isset($_POST['_pm_vertical'])){
			update_post_meta( $post_id, '_pm_vertical', stripslashes( $_POST['_pm_vertical'] ) );
		}

		if(isset($_POST['_enable_price_matrix'])){
			update_post_meta( $post_id, '_enable_price_matrix', $_POST['_enable_price_matrix']);
			if(is_admin()){
				$this->remove_variations($post_id);
			}
		}else {
			update_post_meta( $post_id, '_enable_price_matrix', false);
		}
	}

	public function price_matrix_scripts_method($hooks){
		$screen = get_current_screen();

		if( empty($screen) ) {
			return;
		}
		
		if( $screen->post_type == 'product' && $hooks == 'post.php' || $screen->post_type == 'product' && $hooks == 'post-new.php') {
			wp_enqueue_style( 'price-matrix-context.standalone', NBT_PRICEMATRIX_URL . 'assets/css/context.standalone.css'  );
			wp_enqueue_style( 'price-matrix-magnific', NBT_PRICEMATRIX_URL . 'assets/css/magnific-popup.css'  );
			wp_enqueue_style( 'price-matrix-product', NBT_PRICEMATRIX_URL . 'assets/css/admin.css'  );
			wp_enqueue_script( 'price-matrix-context', NBT_PRICEMATRIX_URL . 'assets/js/context.js', null, null, true );
			wp_enqueue_script( 'price-matrix-magnific', NBT_PRICEMATRIX_URL . 'assets/js/jquery.magnific-popup.min.js', null, null, true );
			wp_enqueue_script( 'price-matrix-product', NBT_PRICEMATRIX_URL . 'assets/js/admin.js', null, null, true );

			if( ! defined('PREFIX_NBT_SOL') ) {
				wp_localize_script( 'price-matrix-product', 'nbt_solutions', $this->price_matrix_localize( array() ) );
			}
		}
	}

	public function price_matrix_localize( $localize) {
		$localize['price_matrix'] = array(
			'input_price_nonce'       => wp_create_nonce( 'input-price' ),
			'save_price_nonce'        => wp_create_nonce( 'save-price' ),
		);

		return $localize;
	}
}
new NBT_PriceMatrix_Admin();