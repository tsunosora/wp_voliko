<?php
/**
 * wpnetbase functions and definitions
 *
 * @package Netbase
 */

/**
 * Define theme constants
 */
function printshop_gift_enqueue_styles() {
	wp_enqueue_style( 'child-style',get_stylesheet_directory_uri() . '/style.css',array());
 	if( is_front_page () ){
 		wp_enqueue_script( 'printshop-gift-customize', get_stylesheet_directory_uri() . '/js/customize.js', array(), '', true );
 	}
 
}
add_action( 'wp_enqueue_scripts', 'printshop_gift_enqueue_styles',99 );

function printshop_menu_shortcode($atts, $content = null) {
	extract(shortcode_atts(array( 'name' => null, ), $atts));
	return wp_nav_menu( array( 'menu' => $name, 'echo' => false ) );
}
add_shortcode('menu', 'printshop_menu_shortcode');








