<div id="color_swatches" class="panel wc-metaboxes-wrapper woocommerce_options_panel hidden"></div>


<script id="msg-js" type="text/template">
	<div id="message" class="inline notice woocommerce-message">
		<p><?php _e( 'Before you can use Price Matrix, you need add minimum of two variation attributes on the <strong>Attributes</strong> tab.', 'woocommerce' ); ?></p>
		<p><a class="button-primary" href="<?php echo esc_url( apply_filters( 'woocommerce_docs_url', 'https://docs.woocommerce.com/document/variable-product/', 'product-variations' ) ); ?>" target="_blank"><?php _e( 'Learn more', 'woocommerce' ); ?></a></p>
	</div>
</script>

<script id="tpl-color-swatches" type="text/template">
	<div class="toolbar toolbar-top">
		<span class="expand-close">
			<a href="#" class="expand_all">Expand</a> / <a href="#" class="close_all">Close</a>
		</span>
		<select name="attribute_taxonomy" class="attribute_taxonomy">
			<option value="">Custom product attribute</option>
			<option value="pa_color" disabled="disabled">color</option><option value="pa_plan" disabled="disabled">Plan</option><option value="pa_quantity" disabled="disabled">Quantity</option>		</select>
		<button type="button" class="button add_attribute">Add</button>
	</div>

	<div class="color_swatches wc-metaboxes">
	</div>
	<div class="toolbar">
		<span class="expand-close">
			<a href="#" class="expand_all">Expand</a> / <a href="#" class="close_all">Close</a>
		</span>
		<button type="button" class="button save_color_swatches button-primary">Save attributes</button>
	</div>
</script>