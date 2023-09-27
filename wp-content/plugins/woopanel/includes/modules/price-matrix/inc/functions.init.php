<?php
if( ! function_exists('woopanel_price_matrix_get_direction') ) {
	/**
	 * Return postion will show Price Matrix table in frontend
	 */
	function woopanel_price_matrix_get_direction(){
		return array(
			'default'    => esc_html__( 'Default', 'woopanel' ),
			'before_tab' => esc_html__( 'Before Tab', 'woopanel' ),
		);
	}
}

if( ! function_exists('woopanel_price_matrix_attribute_label') ) {
	/**
	 * Get label by attribute
	 */
	function woopanel_price_matrix_attribute_label($tax, $product_id, $order_attributes){
		$product = wc_get_product($product_id);
		$get_attributes = $product->get_attributes( 'edit' );
		

		if(isset($get_attributes[$tax])) :
			$attribute = $get_attributes[$tax];


			if ( $attribute->is_taxonomy() ) :
				$terms = array();
				foreach($attribute->get_terms() as $k => $term) {
					$terms[$term->slug] = (array) $term;
				}
			else :
				$value_array = $attribute->get_options();
				$terms = array();

				foreach ($value_array as $key => $value) {
					$terms[trim($value)] = array(
						'taxonomy' => $tax,
						'name' => trim($value),
						'slug' => trim($value),
						'is_taxonomy' => false
					);
				}
			endif;



			/* Rewrite */
			if( isset($order_attributes[$tax]) ) {
				$array = array();
				foreach( $order_attributes[$tax] as $k_order => $val_order ) {

					if( isset($terms[$k_order]) ) {
						$array[] = array(
							'taxonomy' => $tax,
							'name' => trim($val_order),
							'slug' => trim($k_order),
							'is_taxonomy' => false
						);
					}
				}

				$terms = $array;
			}

			$terms = array_values($terms);




			$terms = json_decode(json_encode($terms), FALSE);
		endif;

		return $terms;
	}
}

if( ! function_exists('woopanel_price_matrix_attribute_tax') ) {
	/**
	 * Get label by taxonomy
	 */
	function woopanel_price_matrix_attribute_tax($tax, $product_id) {
		global $wpdb;

		$_product_attributes = get_post_meta($product_id, '_product_attributes', TRUE);
		if(isset($_product_attributes[$tax])){
			$data = $_product_attributes[$tax];
			if($data['is_taxonomy']){
				$tax = str_replace('pa_', '', $tax);
				
				$rs =  $wpdb->get_row($wpdb->prepare("SELECT attribute_label FROM ".esc_attr($wpdb->prefix)."woocommerce_attribute_taxonomies WHERE attribute_name = '%s'", $tax), OBJECT);
				if($rs){
					return $rs->attribute_label;
				}
			}else{
				return $data['name'];
			}
		}
	}
}

if( ! function_exists('woopanel_price_matrix_attribute_price') ) {
	/**
	 * Shows price with attribute
	 */
	function woopanel_price_matrix_attribute_price($attrs = array(), $product, $ajax = false){
		global $wpdb;
		
		$where = $name = $price = '';
		$attributes = array();

		$sql = "SELECT posts.ID as id, posts.post_parent as parent FROM {$wpdb->posts} as posts";
		if( is_array($attrs) ) {
			foreach( $attrs as $k => $attr ) {
				$sql .= " INNER JOIN {$wpdb->postmeta} AS postmeta".esc_attr($k)." ON posts.ID = postmeta".esc_attr($k).".post_id";
				$where .= " AND postmeta" . esc_attr($k) . ".meta_key = 'attribute_" . esc_attr($attr['name']) . "' AND postmeta" . esc_attr($k) . ".meta_value = '" . esc_attr($attr['value']) . "'";
				$attributes[$attr['name']] = esc_attr( $attr['value'] );
				$name .= esc_attr( $attr['name'] ) . esc_attr( $attr['value'] );
			}
		}

		$sql .= " WHERE posts.post_parent = '" . absint( $product->get_id() ) . "' AND posts.post_type IN ( 'product', 'product_variation' ) AND posts.post_status = 'publish'";
		$sql .= $where;

		$name = '{pm_' . absint( $product->get_id() ) .'}'.esc_attr($name);
		$variation_id = get_transient( $name );
		if ( false === $variation_id ) {
			$rs = $wpdb->get_row($sql);

			if( $rs ) {
				$variation_id = $rs->id;
			} else {
				$insert_data = array(
					'post_title'    => $product->get_title(),
					'post_comment'  => 'closed',
					'post_status'   => 'publish',
					'ping_status'   => 'closed',
					'post_author'   => get_current_user_id(),
					'post_name'   => $product->get_slug(),
					'post_parent'   => $product->get_id(),
					'guid'   => esc_url( get_permalink($product->get_id()) ),
					'post_type' => 'product_variation'
				);

 				$variation_id = wp_insert_post( $insert_data );

				foreach( $attributes as $attribute_name => $attribute_value ) {
					update_post_meta($variation_id, 'attribute_' . esc_attr($attribute_name), $attribute_value);
				}
				update_post_meta($variation_id, '_stock_status', 'instock');
			}

			set_transient( $name, $variation_id );
		}

		$_regular_price = get_post_meta($variation_id, '_regular_price', true);
		$_sale_price = get_post_meta($variation_id, '_sale_price', true);

		if( $ajax ) {
			if( $_regular_price && $_sale_price ) {
				$price = $_regular_price.'-'.esc_attr($_sale_price);
			}else{
				$price = $_regular_price;
			}
		}else {
			$settings = woopanel_module_get_setting(WooPanel_Price_Matrix::$plugin_id);

			if( $_regular_price && $_sale_price ) {
				if( isset($settings['wc_'.WooPanel_Price_Matrix::$plugin_id.'_show_sales']) && $settings['wc_'.WooPanel_Price_Matrix::$plugin_id.'_show_sales'] == true ) {
					$price = '<del>' . wc_price($_regular_price) .'</del><ins>' . wc_price($_sale_price) .'</ins>';
				}else {
					$price = wc_price($_sale_price);
				}

				$find_price = $_sale_price;
				
			}else{
				$price = wc_price($_regular_price);
				$find_price = $_regular_price;
			}
		}


		if( $ajax ) {
			return $price;
		}else {
			return array(
				'price' => $price,
				'final_price' => $find_price,
				'variation_id' => $variation_id
			);
		}
		
	}
}

if( ! function_exists('woopanel_price_matrix_get_by_name') ) {
	function woopanel_price_matrix_get_by_name($products, $field){
		foreach ($array as $key => $value) {
			if($value['name'] == $field){
				return $key;
			}
		}
	}
}

if( ! function_exists('woopanel_price_matrix_get_price_variation') ) {
	/**
	 * Get regular price and sale price with variation_id
	 */
	function woopanel_price_matrix_get_price_variation($variation_id) {
		$price = get_post_meta($variation_id, '_regular_price', true);
		$_sale_price = get_post_meta($variation_id, '_sale_price', true);

		if( $_sale_price ) {
			$price = $_sale_price;
		}

		return $price;
	}

}

if( ! function_exists('woopanel_module_get_setting')) {
	/**
	 * Save data on setting page Admin
	 */
	function woopanel_module_get_setting( $module, $name = null ) {
		if( defined('PREFIX_NBT_SOL') ) {
			return NB_Solution::get_setting($module, $name);
		}else {
			$option_name = $module . '_settings';
			$settings = get_transient( $option_name );
			
			if ( false === $settings ) {
				$settings = get_option($module . '_settings');
				
				if( ! $settings ) {
					if ( ! empty($class) && class_exists('NBT_' . esc_attr($class) . '_Settings') ) {
                        $module_setting = call_user_func('NBT_' . esc_attr($class) . '_Settings::get_settings');

                        if( is_array($module_setting) ) {
                            $default_setting = array();
                            foreach( $module_setting as $key => $set) {
                                if( isset($set['id']) ) {
                                    $default_setting[$set['id']] = $set['default'];
                                }
                                
                            }

                            $settings = apply_filters( 'nbt_'.esc_attr($option_name), $default_setting );
                        }
                    }
				}

				set_transient( $option_name, $settings );
			}

			return $settings;
		}
	}
}

if( ! function_exists('woopanel_price_matrix_check_license')) {
	/**
	 * Check license if use FREE version
	 */
	function woopanel_price_matrix_check_license() {
		if( class_exists('NBWooCommerceDashboard') ) {
			return true;
		}

		$wppm_license = get_option('wppm_license');
		if( empty($wppm_license) ) {
			$wppm_total_products = get_option('wppm_total_products');

			if( $wppm_total_products >= 3) {
				return 'limit';
			}else {
				return true;
			}
		}else {
			return false;
		}
	}
}


if( ! function_exists('woopanel_price_matrix_product_enables')) {
	/**
	 * Count total product enable Price Matrix
	 */
	function woopanel_price_matrix_product_enables() {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare( 
			"SELECT COUNT(*) AS count FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s", 
			'_enable_price_matrix',
			'on'
		));
	}
}