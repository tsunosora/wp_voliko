<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Netbase
 */
/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @param array $args Configuration arguments.
 * @return array
 */
function printshop_page_menu_args($args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'printshop_page_menu_args' );

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function printshop_body_classes($classes ) {

	global $woocommerce;
	global $post;

	if ( is_page_template( 'template-fullwidth.php' ) || is_404() ) {
		$classes[] = 'page-fullwidth';
	}

	// WooCommerce
	if ( $woocommerce ) {
		$woo_layout  =  esc_html( get_post_meta(  wc_get_page_id('shop'), 'sidebar_option', true) );
		if ( $woo_layout == 'right-sidebar' || $woo_layout == 'left-sidebar' ) {
			$classes[] = 'shop-has-sidebar';
		}
	}

	// Boxed Layout
	if ( printshop_get_option('site_boxed') || (isset($_REQUEST['boxed_layout']) && $_REQUEST['boxed_layout'] = 'enable' ) ) {
		$classes[] = 'layout-boxed';
	}

	// Header Style
	if ( printshop_get_option('header_style') || printshop_get_option('header_style') !== '' ) {

		if ( isset( $_REQUEST['header-demo'] ) ) {
			$classes[] = 'header-'.$_REQUEST['header-demo'];
		} else {
			$classes[] = 'header-'.printshop_get_option('header_style');
		}

	} else {
		$classes[] = 'header-default';
	}

	// Fixed Header
	if ( printshop_get_option('header_fixed') ) {
		$classes[] = 'header-fixed-on';
	}
	
	if ( printshop_get_option('hide_header_topbar') ) {
		$classes[] = 'hide-header-topbar';
	}

	
	$classes[] = 'header-normal';

	return $classes;
}
add_filter( 'body_class', 'printshop_body_classes' );

/**
 * Adds custom classes to main content.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function printshop_get_layout_class ( ) {
	global $post;
	global $woocommerce;
	$classes               = 'right-sidebar';
	$page_layout_admin     = printshop_get_option('page_layout');
	$archive_layout_admin  = printshop_get_option('archive_layout');
	$blog_layout_admin     = printshop_get_option('blog_layout');
	$single_shop_layout    = printshop_get_option('single_shop_layout');
	$single_project_layout = printshop_get_option('project_layout');

	$post_type = get_post_type($post);

	// Pages
	if ( is_page() ){
		$page_meta =  esc_html( get_post_meta(  $post->ID, 'sidebar_option', true) );

		if ( $page_meta == 'sidebar-default' || $page_meta == '' ) {
			$classes = $page_layout_admin;
		} else {
			$classes = $page_meta;
		}
	}

	// Single Post
	if ( is_single() ) {

		if ( $blog_layout_admin ) {
			$classes = $blog_layout_admin;
		} 
	}


	
	// Archive
	if ( (is_archive() || is_author()) & !is_front_page() ) {
		if ( $archive_layout_admin !== '' ){
			$classes = $archive_layout_admin;
		} 
		
	}

	// Search
	if ( is_search() ) {
		if ( $archive_layout_admin !== '' ){
			$classes = $archive_layout_admin;
		} 
		
	}

	// Blog Page
	if ( !is_front_page() && is_home() ) {
		if ( $blog_layout_admin ) {
			$classes = $blog_layout_admin;
		} 
	}

	// WooCommerce
	if ( $woocommerce ) {
		$shop_layout_meta = esc_html( get_post_meta(  wc_get_page_id('shop'), 'sidebar_option', true) );
		if ( $woocommerce && is_shop() || $woocommerce && is_product_category() || $woocommerce && is_product_tag() ) {
			if ( $shop_layout_meta ) {
				$classes = $shop_layout_meta;
			}
		}
		if ( $woocommerce && is_product() ) {
			if ( $single_shop_layout ) {
				$classes = $single_shop_layout;
			}
		}
	}

	return $classes;
}


/**
 * Sets the authordata global when viewing an author archive.
 *
 * This provides backwards compatibility with
 * http://core.trac.wordpress.org/changeset/25574
 *
 * It removes the need to call the_post() and rewind_posts() in an author
 * template to print information about the author.
 *
 * @global WP_Query $wp_query WordPress Query object.
 * @return void
 */
function printshop_setup_author() {
	global $wp_query;

	if ( $wp_query->is_author() && isset( $wp_query->post ) ) {
		$GLOBALS['authordata'] = get_userdata( $wp_query->post->post_author );
	}
}
add_action( 'wp', 'printshop_setup_author' );

/**
 * Output the status of widets for footer column.
 *
 */
function printshop_sidebar_desc($sidebar_id ) {

	$desc           = '';
	$column         = str_replace( 'footer-', '', $sidebar_id );
	$footer_columns = printshop_get_option('footer_columns');

	if ( $column > $footer_columns ) {
		$desc = esc_html__( 'This widget area is currently disabled. You can enable it Theme Options - Footer section.', 'printshop' );
	}

	return esc_html( $desc );
}

/**
 * Output the status of widets for topbar.
 *
 */
function printshop_topbar_desc($sidebar_id ) {

	$desc           = '';
	$header_style = printshop_get_option('header_style');

	if ( $header_style == '' || $header_style == 'header-default' ) {
		$desc = esc_html__( 'This widget area is currently disabled because you are using default header ( Theme Option > Header ) and it only available for Header Topbar or Header Centered.', 'printshop' );
	} else {
		$desc = '';
	}

	return esc_html( $desc );
}


/**
 * Browser detection body_class() output
 */
function printshop_browser_body_class($classes) {
        global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
        if($is_lynx) $classes[] = 'lynx';
        elseif($is_gecko) $classes[] = 'gecko';
        elseif($is_opera) $classes[] = 'opera';
        elseif($is_NS4) $classes[] = 'ns4';
        elseif($is_safari) $classes[] = 'safari';
        elseif($is_chrome) $classes[] = 'chrome';
        elseif($is_IE) {
                $classes[] = 'ie';
                if(preg_match('/MSIE ([0-9]+)([a-zA-Z0-9.]+)/', $_SERVER['HTTP_USER_AGENT'], $browser_version))
                $classes[] = 'ie'.$browser_version[1];
        } else $classes[] = 'unknown';
        if($is_iphone) $classes[] = 'iphone';
        if ( stristr( $_SERVER['HTTP_USER_AGENT'],"mac") ) {
                 $classes[] = 'osx';
           } elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"linux") ) {
                 $classes[] = 'linux';
           } elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"windows") ) {
                 $classes[] = 'windows';
           }
        return $classes;
}
add_filter('body_class','printshop_browser_body_class');

