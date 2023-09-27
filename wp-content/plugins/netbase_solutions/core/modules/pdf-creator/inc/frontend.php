<?php
class NBT_PDF_Frontend {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'woocommerce_thankyou', array($this, 'nbt_woocommerce_payment_complete'), 10, 1 );

	}

	public function nbt_woocommerce_payment_complete($order_id){
		$key = get_post_meta($order_id, '_order_key', true);
		$settings = get_option('pdf-creator_settings');
		
		$style = '';
		if( isset($settings['nbt_pdf_primary_color']) ) {
			$style .= 'background-color: '. esc_attr($settings['nbt_pdf_primary_color']) .'; ';
		}
		
		if( isset($settings['nbt_pdf_text_color']) ) {
			$style .= 'color: '. esc_attr($settings['nbt_pdf_text_color']) .';';
		}

		$createPDF = parse_url( get_permalink( get_option('_create_page_pdf') ) );

		if( ! empty($createPDF) ) {
			$pageURL = $createPDF['scheme'] . '://'. $createPDF['host'] . $createPDF['path'];

			echo sprintf('<a href="%s?key=%s" class="button btn btn-link btn-pdf-preview" target="_blank" style="%s">%s</a>', $pageURL,
				$key,
				$style,
				__('View or Print PDF Invoice', 'nbt-solution')
			);
		}



	}

}
new NBT_PDF_Frontend();