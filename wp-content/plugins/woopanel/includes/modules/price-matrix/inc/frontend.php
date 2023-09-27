<?php

/**
 * Price Matrix Frontend class
 *
 * @package WooPanel_Modules
 */
class WooPanel_PriceMatrix_Frontend {

    /**
     * WooPanel_PriceMatrix_Frontend Constructor.
     */
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

  /**
   * Display calculator 
   *
   * @var string
   */
	public function getDisplayCalculator() {
		global $price_matrix_settings;


		if( isset($price_matrix_settings['wc_price-matrix_show_calculator']) && ! empty($price_matrix_settings['wc_price-matrix_show_calculator']) ) {
				$html = '<div class="nbpm-calculator"></div>';
			print($html);
		}

	}
	
  /**
   * Add localize script if use NB_Solutions
   *
   * @var string
   */
	public function nbt_solutions_localize($array) {
		global $post, $price_matrix_settings;

		if( is_product() ) {
			
			$product = wc_get_product($post->ID);

			$array['is_scroll'] = isset($price_matrix_settings['wc_price-matrix_is_scroll']) ? $price_matrix_settings['wc_price-matrix_is_scroll'] : false;
			$array['default_attributes'] = $product->get_default_attributes();
			$array['isCalculatorText'] = isset($price_matrix_settings['wc_price-matrix_show_calculator']) ? $price_matrix_settings['wc_price-matrix_show_calculator'] : false;
			$array['format'] = array(
				'precision' => get_option('woocommerce_price_num_decimals'),
				'decimal' => get_option('woocommerce_price_decimal_sep'),
				'thousand' => get_option('woocommerce_price_thousand_sep'),
				'format' => get_option('woocommerce_currency_pos'),
				'symbol' => get_woocommerce_currency_symbol()
			);
			$array['pricematrix']['total_label'] = esc_html__('Total', 'woopanel' );
			$array['ajax_url'] = admin_url('/admin-ajax.php');
		}

		return $array;
	}
	
    /**
     * Display Price Matrix table
     */
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

        	if($product && get_post_meta($product->get_id(), '_enable_price_matrix', true) == 'on' && get_post_meta($product->get_id(), '_pm_num', true) || $product && get_post_meta($product->get_id(), '_enable_price_matrix', true) == 'yes' && get_post_meta($product->get_id(), '_pm_num', true) ){

        		$this->show_table_price_matrix($product);

        	}
        }

        return apply_filters( 'printcart_shortcode_pricematrix', $html );

	}

  /**
   * Add price matrix class in the body_class()
   *
   * @return array
   */
	public function nbt_body_classes( $classes ) {
		global $post, $pm_settings;

		if(isset($post) && get_post_meta($post->ID, '_enable_price_matrix', true) == 'on' || isset($post) && get_post_meta($post->ID, '_enable_price_matrix', true) == 'yes' ){
			$classes[] = 'has-price-matrix';
			if($pm_settings['wc_price-matrix_is_scroll']){
				$classes[] = 'has-pm-scroll';
			}
		}
	     
	    return $classes;  
	}

  /**
   * Only show price matrix if this page is single_product
   */
	public function is_product(){
		global $product, $price_matrix_settings;

		if( get_post_meta($product->get_id(), '_enable_price_matrix', true) == 'on' && get_post_meta($product->get_id(), '_pm_num', true) && $product->is_type('variable') || get_post_meta($product->get_id(), '_enable_price_matrix', true) == 'yes' && get_post_meta($product->get_id(), '_pm_num', true) && $product->is_type('variable') ){

			$_pm_show = $price_matrix_settings['wc_'.WooPanel_Price_Matrix::$plugin_id.'_show_on'];
			if($_pm_show != 'default'){
				remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
				add_action('woocommerce_after_single_product_summary',  'woocommerce_template_single_add_to_cart', 5);
			}
			add_action('woocommerce_before_add_to_cart_button', array($this, 'show_table_price_matrix'), 10 );
			if($price_matrix_settings['wc_'.WooPanel_Price_Matrix::$plugin_id.'_is_heading']){
				add_action('woocommerce_before_add_to_cart_form', array($this, 'show_table_price_matrix_begin') );
				add_action('woocommerce_after_add_to_cart_form', array($this, 'show_table_price_matrix_end'), 10 );
			}else{
				add_action('woocommerce_before_add_to_cart_form', array($this, 'show_table_price_matrix_begin_empty') );
			}
			if( $price_matrix_settings['wc_'.WooPanel_Price_Matrix::$plugin_id.'_hide_info'] == 'yes' ){
				add_action('woocommerce_product_tabs', array($this, 'woo_remove_product_tabs'), 98 );
			}
		}

	}

  /**
   * Remove tab additional_information default of WooCommerce
   */
	public function woo_remove_product_tabs( $tabs ) {
	    unset( $tabs['additional_information'] );
	    return $tabs;
	}

  /**
   * Load Price Matrix table when ajax call
   */
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

  /**
   * Display HTML Price Matrix table
   */
	public function show_table_price_matrix($_product, $deprived = false){
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
			$suffix = '-'.esc_attr($_pm_direction).'-'.esc_attr($count_attr);
		}elseif($count_attr == 4){
			$suffix = '-'.esc_attr($count_attr);
		}
		
		$woocommerce_price_decimal_sep = get_option('woocommerce_price_decimal_sep');
		$woocommerce_price_num_decimals = get_option('woocommerce_price_num_decimals');
		$number_zero = '';
		for ($x = 0; $x < $woocommerce_price_num_decimals; $x++) {
			$number_zero .= '0';
		}

		if( $woocommerce_price_num_decimals > 0 ) {
			$format_price = str_replace( '0' . esc_attr($woocommerce_price_decimal_sep) . esc_attr($number_zero), '{price}', wc_price(0));
		}else {
			$format_price = str_replace( '0', '{price}', wc_price(0));
		}
		
		$format_price = htmlspecialchars($format_price);
		
		if(file_exists(WOOPANEL_PRICEMATRIX_PATH .'tpl/frontend/price-matrix' . esc_attr($suffix).'.php')){
			if( ! $deprived ) {
				echo '<div id="price-matrix-wrapper" data-format_price="'. esc_attr($format_price) .'">';
			}
			
			$get_attributes = $product->get_attributes( 'edit' );
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
				<table class="un-variations" data-suffix="<?php echo esc_attr($suffix);?>" >
					<tbody>
						<?php
						$k = 0;
						foreach ($get_attributes as $k_attributes => $attribute) {
							$tax_attributes = woopanel_price_matrix_attribute_tax($k_attributes, $product->get_id());?>
						<tr>
							<td class="label"><label for="<?php echo esc_attr($k_attributes);?>"><?php echo esc_attr($tax_attributes);?></label></td>
							<td class="value">
								<select id="<?php echo esc_attr($k_attributes);?>" data-attribute_name="<?php echo esc_attr($k_attributes);?>">
									<option value=""><?php esc_html_e('Choose an option', 'woopanel' );?></option>
									<?php if ( $attribute->is_taxonomy() ) : ?>
										<?php foreach ( $attribute->get_terms() as $option ) :
											$attr_json[$k][esc_attr( $option->slug )] = $k_attributes;
											?>
											<option value="<?php echo esc_attr( $option->slug ); ?>"<?php if( is_array($get_default_attributes) && in_array(esc_attr( $option->slug ), $get_default_attributes)){ echo ' selected';}?>><?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $option->name ) ); ?></option>
										<?php endforeach; ?>
									<?php else : ?>
										<?php foreach ( $attribute->get_options() as $option ) :
											$attr_json[$k][esc_attr( $option )] = $k_attributes;
											?>
											<option value="<?php echo esc_attr( $option ); ?>"<?php if( is_array($get_default_attributes) && in_array(esc_attr( $option ), $get_default_attributes)){ echo ' selected';}?>><?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ); ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
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
				<div id="single-product_variations" data-attr="<?php echo htmlspecialchars( wp_json_encode( $_pm_attr ) );?>" data-product_variations="<?php echo htmlspecialchars( wp_json_encode( $attr_json ) );?>" data-count="<?php echo esc_attr($count_array);?>"></div>
				<?php
			}

			/* Show sale price */
			$show_regular_price = '';
			$_show_sales = $pm_settings['wc_'.WooPanel_Price_Matrix::$plugin_id.'_show_sales'];
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

			include(WOOPANEL_PRICEMATRIX_PATH .'tpl/frontend/price-matrix' . esc_attr($suffix).'.php');
			
			if( ! $deprived ) {
				echo '</div>
				</div>';
			}


		}else{
			echo 'Template for table price-matrix' . esc_attr($suffix).'.php exists';
		}
	}

	/**
	 * Enqueue styles.
	 */
	function embed_style() {
		global $pm_settings;
		
		$color_bg = $pm_settings['wc_'.WooPanel_Price_Matrix::$plugin_id.'_color_table'];
		$color_text = $pm_settings['wc_'.WooPanel_Price_Matrix::$plugin_id.'_color_text'];
		$color_border = $pm_settings['wc_'.WooPanel_Price_Matrix::$plugin_id.'_color_border'];
		$font_size = $pm_settings['wc_'.WooPanel_Price_Matrix::$plugin_id.'_font_size'];
		$bg_tooltip = $pm_settings['wc_'.WooPanel_Price_Matrix::$plugin_id.'_bg_tooltip'];
		$color_tooltip = $pm_settings['wc_'.WooPanel_Price_Matrix::$plugin_id.'_color_tooltip'];
		$border_tooltip = $pm_settings['wc_'.WooPanel_Price_Matrix::$plugin_id.'_border_tooltip'];

		$style = '';
		if( $color_bg || $color_text ) {
			$style .= '.attr-name {';

			if( $color_bg ) {
				$style .= 'background: '.esc_attr($color_bg).' !important;';
			}

			if($color_text) {
				$style .= 'color: '.esc_attr($color_text).' !important;';
			}

			$style .= '}';
		}

		if( $color_border ) {
			$style .= '.pure-table, .pure-table th, .pure-table td {border: 1px solid '.esc_attr($color_border).';}';
		}
		if( $font_size ) {
			$style .= '.pure-table .price:hover, .pure-table .price {font-size: '.esc_attr($font_size).'px;}';
		}
		if( $bg_tooltip ) {
			$style .= '.tippy-popper .tippy-tooltip-content table tr td {
				background: '.esc_attr($bg_tooltip).' !important;';

				if( $border_tooltip ) {
					$style .= 'border-bottom: 1px solid '.esc_attr($border_tooltip).' !important;';
				}

			$style .= '
			}
			.tippy-popper[x-placement^=top] [x-arrow] {
				border-top-color: '.esc_attr($bg_tooltip).' !important;
			}
			.tippy-popper[x-placement^=bottom] [x-arrow] {
				border-bottom-color: '.esc_attr($bg_tooltip).' !important;
			}';
		}

		if( $color_tooltip ) {
			$style .= '.tippy-popper .tippy-tooltip-content table tr td {
				color: '.esc_attr($color_tooltip).';
			}';
		}


		if( ! defined('PREFIX_NBT_SOL') ) {
			wp_enqueue_style( 'price-matrix', WOOPANEL_PRICEMATRIX_URL . 'assets/css/frontend.css',false,'1.1','all');
			wp_add_inline_style('price-matrix', $style);
			wp_enqueue_script( 'js-md5', WOOPANEL_PRICEMATRIX_URL . 'assets/js/md5.min.js', '', '', true );	
		}else {
			wp_add_inline_style('frontend-solutions', $style);
			wp_enqueue_script( 'js-md5', PREFIX_NBT_SOL_URL . 'assets/frontend/js/md5.min.js', '', '', true );
		}

		wp_enqueue_script('accounting');
		wp_enqueue_style( 'tippy', WOOPANEL_PRICEMATRIX_URL . 'assets/css/tippy.css',false,'1.1','all');
		wp_enqueue_script( 'tippy', WOOPANEL_PRICEMATRIX_URL . 'assets/js/tippy.min.js', null, null, true );

		//if( ! defined('PREFIX_NBT_SOL') ) {
			wp_enqueue_script( 'frontend.pricematrix', WOOPANEL_PRICEMATRIX_URL . 'assets/js/frontend.js', null, null, true );
			wp_localize_script( 'frontend.pricematrix', 'nbt_solutions', $this->nbt_solutions_localize( array() ) );
		//}
	}
	
	/**
	 * Clear float left by any theme or any plugin
	 */
	public function show_table_price_matrix_begin_empty(){
		echo '<div style="clear:both"></div>';
	}
	
	/**
	 * Show Heading of table
	 */
	public function show_table_price_matrix_begin(){
		global $pm_settings;
		echo '<div style="clear:both"></div><div class="price-matrix-container widget"><h2 class="pm-heading widget-title">'. esc_attr($pm_settings['wc_'.WooPanel_Price_Matrix::$plugin_id.'_heading']) .'</h2>';
	}

	/**
	 * End output table
	 */
	public function show_table_price_matrix_end(){
		echo '</div>';
	}
}

/**
 * Returns the main instance of WooPanel_PriceMatrix_Frontend.
 *
 * @since  1.0.0
 * @return WooPanel_PriceMatrix_Frontend
 */
new WooPanel_PriceMatrix_Frontend();