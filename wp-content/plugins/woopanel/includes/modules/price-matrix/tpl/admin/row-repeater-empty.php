<?php
$isWP = (isset($_POST['isWP']) && $_POST['isWP'] == 'false' ) ? false: true;
$class_alert = 'inline notice woocommerce-message';
$class_btn = 'button-primary';
if( class_exists('NBWooCommerceDashboard') ) {
	if( $isWP ) {
		$class_alert = 'm-alert m-alert--outline alert alert-success alert-dismissible fade show';
		$class_btn = 'btn btn-success btn-sm';
	}
}
?>
<div class="<?php echo esc_attr($class_alert);?>" role="alert">
	<p><?php echo wp_kses(
				sprintf(
					esc_html__( 'Before you can add a variation you need to add some variation attributes on the %s tab.', 'woopanel' ),
					'<strong>'.esc_html__('Attributes', 'woopanel' ).'</strong>'
				), array(
					'strong' => array()
				) ); ?></p>
	<p><a class="<?php echo esc_attr($class_btn);?>" href="<?php echo esc_url( apply_filters( 'woocommerce_docs_url', 'https://docs.woocommerce.com/document/variable-product/', 'product-variations' ) ); ?>" target="_blank"><?php esc_html_e( 'Learn more', 'woopanel' ); ?></a></p>
</div>