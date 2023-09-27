<?php
class NBT_GSlider_Frontend {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
		add_action('template_redirect', array($this, 'remove_gallery_and_product_images'));
		add_action( 'woocommerce_before_single_product_summary', array($this, 'woocommerce_show_product_thumbnails'), 20 );
		add_action( 'wp_print_scripts', array($this, 'bhslider_deregister_javascript'), 100 );
		add_action( 'wp_print_styles', array($this, 'bhslider_deregister_styles'), 100 );
		add_action( 'wp_enqueue_scripts', array($this, 'bhslider_scripts_method'), 100 );
	}
	public function remove_gallery_and_product_images() {
		if ( is_product() ) {
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
		}
	}
	public function woocommerce_show_product_thumbnails(){
		global $post, $woocommerce, $product;


		$attachment_ids = $product->get_gallery_image_ids();
		$class = 'wc-product-image-single';
		if($attachment_ids){
			$class = 'wc-product-image-gallery';
		}
		$right_to_left = apply_filters('bh_wc_rtl',  false);
		$attachment_count = 0;

		$settings = get_option( 'gallery-slider_settings' );
		include_once NBT_GSLIDER_PATH .'templates/product-image.php';
	}

	public function bhslider_deregister_javascript(){
		wp_deregister_script( 'prettyPhoto' );
		wp_deregister_script( 'prettyPhoto-init' );
	}

	public function bhslider_deregister_styles(){
		wp_deregister_style( 'photoswipe' );
		wp_deregister_style( 'woocommerce_prettyPhoto_css' );
	}

	public function bhslider_scripts_method(){
		if ( is_woocommerce() && is_product() ) {
			wp_enqueue_script('zoom');

			wp_enqueue_style( 'venobox', NBT_GSLIDER_URL . 'assets/css/venobox.css'  );
			//wp_enqueue_style( 'slick', NBT_GSLIDER_URL . 'assets/css/slick.css'  );
			wp_enqueue_style( 'slick-theme', NBT_GSLIDER_URL . 'assets/css/slick-theme.css'  );
			wp_enqueue_script( 'venobox', NBT_GSLIDER_URL . 'assets/js/venobox.min.js', null, null, true );
			wp_enqueue_script( 'slick', NBT_GSLIDER_URL . 'assets/js/slick.min.js', null, null, true );
			//wp_enqueue_script( 'custom-wgs', NBT_GSLIDER_URL . 'assets/js/frontend.js', null, null, true );
		}
	}
}
new NBT_GSlider_Frontend();