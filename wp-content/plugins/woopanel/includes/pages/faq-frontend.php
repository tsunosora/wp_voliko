<?php

/**
 * This class will load FAQs Frontend
 *
 * @package WooPanel_Template_FAQ_Frontend
 */
class WooPanel_Template_FAQ_Frontend {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_filter( 'woopanel_product_meta_boxes', array( $this, 'product_faq_meta_boxes'), 10, 1 );
        add_filter( 'woocommerce_product_tabs', array($this, 'faq_product_tab') );
        add_shortcode( 'wpl_faq', array($this, 'shortcode_faqs') );
        add_action('wp_enqueue_scripts', array($this, 'embed_style'));
    }

    public function product_faq_meta_boxes( $meta_boxes ) {
        $meta_boxes['product_faq'] = array(
            'title' => esc_htmL__( 'Product FAQs', 'woopanel' ),
            'content' => array( $this, 'product_faq_metaboxes_content' ),
            'priority' => 3
        );

        return $meta_boxes;
    }

    public function product_faq_metaboxes_content( $post ) {
    	$post_id = $post->ID;
		$data = get_post_meta($post_id, '_nbt_faq', true);
		
		include_once WOODASHBOARD_VIEWS_DIR . 'faqs/admin-repeater.php'; 
    }



    public function faq_product_tab( $tabs ) {
        global $post;
        
		$data = get_post_meta($post->ID, '_nbt_faq', true);

		if($data){
			// Adds the new tab
			$tabs['faq_tab'] = array(
				'title' 	=> esc_html__( 'Product FAQs', 'woopanel' ),
				'priority' 	=> 20,
				'callback' 	=> array($this, 'faq_product_tab_content')
			);
		}

		return $tabs;
    }

	function faq_product_tab_content() {
		global $post;
		echo do_shortcode('[wpl_faq id='.absint($post->ID).']');
    }
    
	public function shortcode_faqs($args, $content){
		global $post;

		if(isset($args['id'])){
			$post->ID = $args['id'];
		}

		$data = get_post_meta($post->ID, '_nbt_faq', true);

		include WOODASHBOARD_VIEWS_DIR .'faqs/frontend-shortcode.php';
    }
    
	/**
	 * Enqueue scripts and stylesheets
	 */
	public function embed_style() {
		wp_enqueue_style( 'faq-frontend', WOODASHBOARD_URL .'assets/css/frontend-faqs.css', array(), '20160615' );
		wp_enqueue_script( 'js-md5', WOODASHBOARD_URL . 'assets/js/md5.min.js', '', '', true );
		wp_enqueue_script( 'faq-frontend', WOODASHBOARD_URL . 'assets/js/frontend-faqs.js', array( 'jquery' ), time(), true );

	}
}

new WooPanel_Template_FAQ_Frontend();