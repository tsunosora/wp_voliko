<div id="color_swatches" class="panel wc-metaboxes-wrapper woocommerce_options_panel hidden"></div>


<script id="msg-js" type="text/template">
	<div id="message" class="inline notice woocommerce-message">
	<p><?php echo wp_kses(
				sprintf(
					esc_html__( 'Before you can add a variation you need to add some variation attributes on the %s tab.', 'woopanel' ),
					'<strong>'.esc_html__('Attributes', 'woopanel' ).'</strong>'
				), array(
					'strong' => array()
				) ); ?></p>
		<p><a class="button-primary" href="<?php echo esc_url( apply_filters( 'woocommerce_docs_url', 'https://docs.woocommerce.com/document/variable-product/', 'product-variations' ) ); ?>" target="_blank"><?php esc_html_e( 'Learn more', 'woopanel' ); ?></a></p>
	</div>
</script>

<script id="tpl-color-swatches" type="text/template">
	<div class="color_swatches wc-metaboxes">
	</div>
</script>