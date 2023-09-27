<?php
class WooPanel_Customize_Dokan_Store_Header {
	private $dokan_layout_slug = 'dokan-store';

	private $catalog_orderby_options = array();
	
	function __construct() {
		add_filter( 'dokan_get_template_part', array( $this, 'change_template_path'), 999, 3 );
		add_action( 'woopanel_header_after', array($this, 'header_options'), 20, 3 );

		$this->catalog_orderby_options = apply_filters(
			'woocommerce_catalog_orderby',
			array(
				'menu_order' => esc_html__( 'Default sorting', 'woopanel' ),
				'popularity' => esc_html__( 'Sort by popularity', 'woopanel' ),
				'rating'     => esc_html__( 'Sort by average rating', 'woopanel' ),
				'date'       => esc_html__( 'Sort by latest', 'woopanel' ),
				'price'      => esc_html__( 'Sort by price: low to high', 'woopanel' ),
				'price-desc' => esc_html__( 'Sort by price: high to low', 'woopanel' ),
			)
		);


	}

	public function change_template_path( $template, $slug, $name ) {
		global $woopanel_dokan_store;

		if( $slug == 'store-header' ) {
			$layout = '';
			if( isset($woopanel_dokan_store->data['header_style']) && $woopanel_dokan_store->data['header_style'] != 'default' ) {
				$layout = '-' . esc_attr($woopanel_dokan_store->data['header_style']);
			}

			$template = WOODASHBOARD_TEMPLATE_DIR . 'dokan-store/'.esc_attr($slug).esc_attr( $layout).'.php';
		}
        return $template;
	}

	public function header_options($woopanel_dokan_store, $store_tabs, $query) {
		global $current_user, $wp_query;

		if( isset($wp_query->query['woopanel_store_review']) ) {
			return;
		}

		$orderby = isset($_GET['orderby']) ? wc_clean( (string) wp_unslash( $_GET['orderby'] ) ) : '';

		if( isset($woopanel_dokan_store->data['woocommerce_enable_layout']) || isset($woopanel_dokan_store->data['woocommerce_enable_filter']) ) {

			?>
			<div class="woopanel-wc-store" data-store_url="<?php echo dokan_get_store_url(get_query_var( 'author' ));?>">
				<?php

				if( ! empty($query) && isset($store_tabs[$query]['title']) ) {
					printf('<h1>%s</h1>', $store_tabs[$query]['title']);
				}?>
				<div class="woopanel-wc-store-wrap">
					<?php if( isset($woopanel_dokan_store->data['woocommerce_enable_layout']) ) { ?>
					<div class="woopanel-wc-store-layout">
						<span class="wpl-icon-store gird-layout active" data-layout="grid"><i class="wpl-icon-grid"></i></span>
						<span class="wpl-icon-store list-layout" data-layout="list"><i class="wpl-icon-list"></i></span>
					</div>
					<?php }

					if( isset($woopanel_dokan_store->data['woocommerce_enable_filter']) ) {?>
					<form class="woocommerce-ordering" method="get">
						<div class="woopanel-wc-store-filter">
							<select name="orderby" class="form-control wpl-orderby">
								<?php foreach ($this->catalog_orderby_options as $key => $value) {?>
									<option value="<?php echo esc_attr($key);?>"<?php echo ($orderby == $key) ? ' selected' : '';?>><?php echo esc_attr($value);?></option>
									<?php
								}?>
							</select>
						</div>
					</form>
					<?php }?>
				</div>
			</div>
			<?php
		}
	}


}

new WooPanel_Customize_Dokan_Store_Header();