<?php
class NBT_Faqs_Frontend {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action('wp_enqueue_scripts', array($this, 'embed_style'));

		

		add_shortcode( 'nbt_faq', array($this, 'do_shortcode_nbt_faqs') );
		add_filter( 'woocommerce_product_tabs', array($this, 'faq_product_tab') );

	}

	public function faq_product_tab($tabs){
		global $post;
		$data = get_post_meta($post->ID, '_nbt_faq', true);

		if($data){
			// Adds the new tab
			$tabs['faq_tab'] = array(
				'title' 	=> __( 'Product FAQs', 'nbt-solution' ),
				'priority' 	=> 20,
				'callback' 	=> array($this, 'faq_product_tab_content')
			);
		}


		return $tabs;
	}
	function faq_product_tab_content() {
		global $post;
		echo do_shortcode('[nbt_faq id='.$post->ID.']');
	}

	public function do_shortcode_nbt_faqs($args, $content){
		global $post;

		if(isset($args['id'])){
			$post->ID = $args['id'];
		}

		$data = get_post_meta($post->ID, '_nbt_faq', true);

		include NBT_FAQS_PATH .'tpl/shortcode.php';
	}

	/**
	 * Enqueue scripts and stylesheets
	 */
	public function embed_style() {

		if( ! defined('PREFIX_NBT_SOL')){
			wp_enqueue_style( 'order-upload-frontend', NBT_FAQS_URL .'assets/css/frontend.css', array(), '20160615' );
		}

		
		wp_enqueue_script( 'js-md5', PREFIX_NBT_SOL_URL . 'assets/frontend/js/md5.min.js', '', '', true );
		if( ! defined('PREFIX_NBT_SOL')){
			wp_enqueue_script( 'order-upload-frontend', NBT_FAQS_URL . 'assets/js/frontend.js', array( 'jquery' ), time(), true );
			wp_localize_script( 'order-upload-frontend', 'nbtou', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'customer_id' => WC()->session->get_customer_id()
			));
		}
	}
}
new NBT_Faqs_Frontend();