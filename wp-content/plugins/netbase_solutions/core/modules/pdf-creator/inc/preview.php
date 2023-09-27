<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!class_exists('NBT_Solutions_PDF_Page')) {
	class NBT_Solutions_PDF_Page{
		public function __construct() {

			$this->templates = array();

			add_filter( 'theme_page_templates', array( $this, 'register_template' ) );
			add_filter('template_include', array($this, 'display'));

            // Add your templates to this array.
            $this->templates = array(
                'preview.php' => __('PDF Preview', 'nbt-solution'),
            );

		}

		public function register_template($post_templates){
			$post_templates = array_merge($this->templates, $post_templates);
			return $post_templates;
		}

		public function display($template){
            global $post;

            if (!isset($post)) {
                return $template;
			}
			
            if (!isset($this->templates[get_post_meta($post->ID, '_wp_page_template', true)])) {
                return $template;
            }

            if ( 'preview.php' === get_post_meta( $post->ID, '_wp_page_template', true ) ) {
            	$file = NBT_PDF_PATH . get_post_meta( $post->ID, '_wp_page_template', true );
				if ( file_exists( $file ) ) {
					return $file;
				}
	        }
		}
	}
}
new NBT_Solutions_PDF_Page();