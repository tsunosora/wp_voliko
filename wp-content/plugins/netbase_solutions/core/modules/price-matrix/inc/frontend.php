<?php
class NBT_PriceMatrix_Frontend {
	protected $args;
	
	function __construct() {

		add_action('woocommerce_before_single_product', array($this, 'is_product') );
		add_action('wp_enqueue_scripts', array($this, 'embed_style'));

		add_action( 'wp_ajax_nopriv_pm_load_matrix', array($this, 'pm_load_matrix') );
		add_action( 'wp_ajax_pm_load_matrix', array($this, 'pm_load_matrix') );

		add_filter( 'body_class', array($this, 'nbt_body_classes'), 10, 1 );

		add_shortcode( 'nbt_pricematrix', array($this, 'show_price_matrix') );
		
		if( defined('PREFIX_NBT_SOL') ) {
			add_filter('nbt_solutions_localize', array($this, 'nbt_solutions_localize'), 10, 1);
		}

		add_action('woocommerce_after_add_to_cart_button', array($this, 'getDisplayCalculator'));
	}

	public function getDisplayCalculator() {
		global $product;

		$settings = nb_get_setting(NBT_Solutions_Price_Matrix::$plugin_id);

		if( isset($settings['wc_price-matrix_show_calculator']) && ! empty($settings['wc_price-matrix_show_calculator']) ) {
/* 			if( isset($_POST['variation_id']) && isset($_POST['quantity']) ) {
				$variation_id = intval($_POST['variation_id']);
				$quantity = intval($_POST['quantity']);
				
				$price = getPriceVariation($variation_id);
				$total = $price * $quantity;
				$html = '<div class="nbpm-calculator"><p class="nbpm-calculator-price"><label>'. __('Total', 'nbt-solution') .':</label> '. wc_price($price) .' x ' . $quantity .' = '. wc_price($total) .'</p></div>';
			}else { */
				$html = '<div class="nbpm-calculator"></div>';
			//}

			
			echo $html;
		}

	}
	
	public function nbt_solutions_localize($array) {
		global $post;

		if( is_product() ) {

			$product = wc_get_product($post->ID);
			$settings = nb_get_setting(NBT_Solutions_Price_Matrix::$plugin_id);
			$array['is_scroll'] = $settings['wc_price-matrix_is_scroll'];
			$array['default_attributes'] = $product->get_default_attributes();
			$array['isCalculatorText'] = $settings['wc_price-matrix_show_calculator'];
			$array['format'] = array(
				'precision' => get_option('woocommerce_price_num_decimals'),
				'decimal' => get_option('woocommerce_price_decimal_sep'),
				'thousand' => get_option('woocommerce_price_thousand_sep'),
				'format' => get_option('woocommerce_currency_pos'),
				'symbol' => get_woocommerce_currency_symbol()
			);
			$array['pricematrix']['total_label'] = __('Total', 'nbt-solution');
			$array['ajax_url'] = admin_url('/admin-ajax.php');
		}

		return $array;
	}
	


	
	public function recursive($array, $perms = array(), $result = array(), $level = 0) {
		if( isset($array[$level]) ) {
			foreach($array[$level] as $value => $key){
				$perms[$level] = array(
					'name' => $key,
					'value' => $value
				);
				
				$result = $this->recursive($array, $perms, $result, $level + 1);
			}
		}else {
			$result[] = $perms;
		}
		
		return $result;
	}


	public function show_price_matrix($atts, $content = null) {
		global $product, $wpdb;

		$html = $attr_parent = '';

        // Extract shortcode parameters.
        extract(
            shortcode_atts(
                array(
                    'product_id' => ''
                ),
                $atts
            )
        );

        if( ! $product_id ) {
        	$product_id = $product->get_id();
        }

        if( is_numeric($product_id) ) {
        	$product = wc_get_product($product_id);

        	if($product && get_post_meta($product->get_id(), '_enable_price_matrix', true) == 'on' && get_post_meta($product->get_id(), '_pm_num', true)){

        		/* Check is is_multisite */
        		//$pm_settings = NB_Solution(NBT_Solutions_Price_Matrix::$plugin_id);


        		$this->show_table_price_matrix($product);

        	}
        }

        return apply_filters( 'printcart_shortcode_pricematrix', $html );

	}
	public function nbt_body_classes( $classes ) {
		global $post, $pm_settings;

		if(isset($post) && get_post_meta($post->ID, '_enable_price_matrix', true) == 'on'){
			$classes[] = 'has-price-matrix';
			if($pm_settings['wc_price-matrix_is_scroll']){
				$classes[] = 'has-pm-scroll';
			}
		}
	     
	    return $classes;  
	}
	function is_product(){
		global $product, $pm_settings;

		if(get_post_meta($product->get_id(), '_enable_price_matrix', true) == 'on' && get_post_meta($product->get_id(), '_pm_num', true) && $product->is_type('variable') ){

			$_pm_show = $pm_settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_show_on'];
			if($_pm_show != 'default'){
				remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
				add_action('woocommerce_after_single_product_summary',  'woocommerce_template_single_add_to_cart', 5);
			}
			add_action('woocommerce_before_add_to_cart_button', array($this, 'show_table_price_matrix'), 10 );
			if($pm_settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_is_heading']){
				add_action('woocommerce_before_add_to_cart_form', array($this, 'show_table_price_matrix_begin') );
				add_action('woocommerce_after_add_to_cart_form', array($this, 'show_table_price_matrix_end'), 10 );
			}else{
				add_action('woocommerce_before_add_to_cart_form', array($this, 'show_table_price_matrix_begin_empty') );
			}
			if( $pm_settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_hide_info'] == 'yes' ){
				add_action('woocommerce_product_tabs', array($this, 'woo_remove_product_tabs'), 98 );
			}
		}

	}
	function woo_remove_product_tabs( $tabs ) {
	    unset( $tabs['additional_information'] );
	    return $tabs;
	}
	public function pm_load_matrix() {
		$json = array();
		$product_id = intval($_REQUEST['product_id']);
		$attr = $_REQUEST['attr'];
		$product = wc_get_product( $product_id );

		$new_array = array();
		if(is_array($attr)){
			foreach ($attr as $key => $value) {
				$new_array[] = array('name' => $key, 'value' => $value);
			}
		}

		ob_start();
		$this->show_table_price_matrix($product, $new_array);
		$out = ob_get_clean();
		$json['complete'] = true;
		$json['return'] = $out;

		wp_send_json($json);

	}

	function show_table_price_matrix($_product, $deprived = false){
		global $pm_settings;
		if($_product){
			$product = $_product;
		}else{
			global $product;
		}

		$_pm_table_attr = get_post_meta($product->get_id(), '_pm_table_attr', TRUE);
		$_pm_attr = get_post_meta($product->get_id(), '_pm_attr', TRUE);
		$count_attr = get_post_meta($product->get_id(), '_pm_num', TRUE);
		$_pm_direction = get_post_meta($product->get_id(), '_pm_direction', true);

		$_pm_attrs = get_post_meta($product->get_id(), '_pm_attrs', TRUE);

		if( ! $deprived ) {
			echo '<input type="hidden" name="price_attr" id="price_attr" value="'.htmlspecialchars(wp_json_encode($_pm_table_attr)).'" />
			<input type="hidden" name="security" value="'.wp_create_nonce( "_price_matrix_load_table" ).'" />';
		}

		$suffix = '';

		if($count_attr == 3){
			$suffix = '-'.$_pm_direction.'-'.$count_attr;
		}elseif($count_attr == 4){
			$suffix = '-'.$count_attr;
		}
		
		$woocommerce_price_decimal_sep = get_option('woocommerce_price_decimal_sep');
		$woocommerce_price_num_decimals = get_option('woocommerce_price_num_decimals');
		$number_zero = '';
		for ($x = 0; $x < $woocommerce_price_num_decimals; $x++) {
			$number_zero .= '0';
		}

		if( $woocommerce_price_num_decimals > 0 ) {
			$format_price = str_replace( '0' . $woocommerce_price_decimal_sep . $number_zero, '{price}', wc_price(0));
		}else {
			$format_price = str_replace( '0', '{price}', wc_price(0));
		}
		
		$format_price = htmlspecialchars($format_price);
		
		if(file_exists(NBT_PRICEMATRIX_PATH .'tpl/frontend/price-matrix' . $suffix.'.php')){
			if( ! $deprived ) {
				echo '<div id="price-matrix-wrapper" data-format_price="'. $format_price .'">';
			}
			
			$get_attributes = $product->get_attributes( 'edit' );
			$get_variation_attributes = $product->get_variation_attributes();
			$validate_variation_attributes = array();

			foreach($get_variation_attributes as $key => $value) {
				$validate_variation_attributes[sanitize_title($key)] = $value;
			}

			if($get_attributes) {
				foreach ($get_attributes as $k_attributes => $attributes) {
					if(in_array($k_attributes, $_pm_table_attr)){
						unset($get_attributes[$k_attributes]);
					}
				}
			}

			$khuyet = false;
			if($get_attributes && ! $deprived) {
				$khuyet = true;
				$attr_json = array();
				$get_default_attributes = $product->get_default_attributes();
				?>
				<table class="un-variations" data-suffix="<?php echo $suffix;?>" >
					<tbody>
						<?php
						$k = 0;
						foreach ($get_attributes as $k_attributes => $attribute) {
							$tax_attributes = pm_attribute_tax($k_attributes, $product->get_id());?>
						<tr>
							<td class="label"><label for="<?php echo $k_attributes;?>"><?php echo $tax_attributes;?></label></td>
							<td class="value">
								<select id="<?php echo $k_attributes;?>" data-attribute_name="<?php echo $k_attributes;?>">
									<option value=""><?php _e('Choose an option', 'woocommerce');?></option>
									<?php if ( $attribute->is_taxonomy() ) : ?>
										<?php foreach ( $attribute->get_terms() as $option ) :
											$termSlug = esc_attr( $option->slug );

											if( in_array($termSlug, $get_variation_attributes[$k_attributes]) ) {
											$attr_json[$k][$termSlug] = $k_attributes;
											?>
											<option value="<?php echo esc_attr( $option->slug ); ?>"<?php if( is_array($get_default_attributes) && in_array(esc_attr( $option->slug ), $get_default_attributes)){ echo ' selected';}?>><?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $option->name ) ); ?></option>
										<?php }
										endforeach;
									else : ?>										
										<?php foreach ( $attribute->get_options() as $option ) :
											$termSlug = esc_attr( $option );

											if( in_array($termSlug, $validate_variation_attributes[$k_attributes]) ) {
											$attr_json[$k][$termSlug] = $k_attributes;
											?>
											<option value="<?php echo esc_attr( $option ); ?>"<?php if( is_array($get_default_attributes) && in_array(esc_attr( $option ), $get_default_attributes)){ echo ' selected';}?>><?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ); ?></option>
										<?php }
										endforeach;
									endif; ?>
								</select>
							</td>
						</tr>
						<?php 
							$k++;
						}?>
					</tbody>
				</table>
				<?php
				if( !empty($attr_json) ) {
					$count_array = array();
					$count_attr_json = array_values($attr_json);
					foreach( $count_attr_json as $k => $count_j) {
						$count_array[$k] = count($count_j);
					}
					
					$count_array = array_product($count_array);
				}?>
				<div id="single-product_variations" data-attr="<?php echo htmlspecialchars( wp_json_encode( $_pm_attr ) );?>" data-product_variations="<?php echo htmlspecialchars( wp_json_encode( $attr_json ) );?>" data-count="<?php echo $count_array;?>"></div>
				<?php
			}

			/* Show sale price */
			$show_regular_price = '';
			$_show_sales = $pm_settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_show_sales'];
			if( $_show_sales ) {
				$show_regular_price = ' show';
			}
			if( ! $deprived ) {
				if( $get_attributes ) {
					echo '<div class="load-table-pm" style="display: none;">';
				}else {
					echo '<div class="load-table-pm">';
				}
			}

			include(NBT_PRICEMATRIX_PATH .'tpl/frontend/price-matrix' . $suffix.'.php');
			
			if( ! $deprived ) {
				echo '</div>
				</div>';
			}


		}else{
			echo 'Template for table price-matrix' . $suffix.'.php exists';
		}



	}

	function embed_style() {
		global $pm_settings;
		
		$color_bg = $pm_settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_color_table'];
		$color_text = $pm_settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_color_text'];
		$color_border = $pm_settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_color_border'];
		$font_size = $pm_settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_font_size'];
		$bg_tooltip = $pm_settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_bg_tooltip'];
		$color_tooltip = $pm_settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_color_tooltip'];
		$border_tooltip = $pm_settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_border_tooltip'];

		$style = '';
		if( $color_bg || $color_text ) {
			$style .= '.attr-name {';

			if( $color_bg ) {
				$style .= 'background: '.$color_bg.' !important;';
			}

			if($color_text) {
				$style .= 'color: '.$color_text.' !important;';
			}

			$style .= '}';
		}

		if( $color_border ) {
			$style .= '.pure-table, .pure-table th, .pure-table td {border: 1px solid '.$color_border.';}';
		}
		if( $font_size ) {
			$style .= '.pure-table .price:hover, .pure-table .price {font-size: '.$font_size.'px;}';
		}
		if( $bg_tooltip ) {
			$style .= '.tippy-popper .tippy-tooltip-content table tr td {
				background: '.$bg_tooltip.' !important;';

				if( $border_tooltip ) {
					$style .= 'border-bottom: 1px solid '.$border_tooltip.' !important;';
				}

			$style .= '
			}
			.tippy-popper[x-placement^=top] [x-arrow] {
				border-top-color: '.$bg_tooltip.' !important;
			}
			.tippy-popper[x-placement^=bottom] [x-arrow] {
				border-bottom-color: '.$bg_tooltip.' !important;
			}';
		}

		if( $color_tooltip ) {
			$style .= '.tippy-popper .tippy-tooltip-content table tr td {
				color: '.$color_tooltip.';
			}';
		}


		

		if( ! defined('PREFIX_NBT_SOL') ) {
			wp_enqueue_style( 'price-matrix', NBT_PRICEMATRIX_URL . 'assets/css/frontend.css',false,'1.1','all');
			wp_add_inline_style('price-matrix', $style);
			wp_enqueue_script( 'js-md5', NBT_PRICEMATRIX_URL . 'assets/js/md5.min.js', '', '', true );	
		}else {
			wp_add_inline_style('frontend-solutions', $style);
			wp_enqueue_script( 'js-md5', PREFIX_NBT_SOL_URL . 'assets/frontend/js/md5.min.js', '', '', true );
		}

		wp_enqueue_script('accounting');
		wp_enqueue_style( 'tippy', NBT_PRICEMATRIX_URL . 'assets/css/tippy.css',false,'1.1','all');
		wp_enqueue_script( 'tippy', NBT_PRICEMATRIX_URL . 'assets/js/tippy.min.js', null, null, true );

		if( ! defined('PREFIX_NBT_SOL') ) {
			wp_enqueue_script( 'frontend.pricematrix', NBT_PRICEMATRIX_URL . 'assets/js/frontend.js', null, null, true );
			wp_localize_script( 'frontend.pricematrix', 'nbt_solutions', $this->nbt_solutions_localize( array() ) );
		}
	}
	function show_table_price_matrix_begin_empty(){
		echo '<div style="clear:both"></div>';
	}
	function show_table_price_matrix_begin(){
		global $pm_settings;
		echo '<div style="clear:both"></div><div class="price-matrix-container widget"><h2 class="pm-heading widget-title">'. $pm_settings['wc_'.NBT_Solutions_Price_Matrix::$plugin_id.'_heading'] .'</h2>';
	}

	function show_table_price_matrix_end(){
		echo '</div>';
	}
}
new NBT_PriceMatrix_Frontend();