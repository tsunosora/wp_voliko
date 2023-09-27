<?php
/**
 * wpnetbase functions and definitions
 *
 * @package Netbase
 */
if ( ! defined( 'PRINTSHOP_THEME_URL' ) ) {
	define( 'PRINTSHOP_THEME_URL', get_stylesheet_directory() );
}
$theme_data  = wp_get_theme();
if ( $theme_data->exists() ) {
	if ( ! defined( 'PRINTSHOP_THEME_NAME' ) ) {
		define( 'PRINTSHOP_THEME_NAME', $theme_data->get( 'Name' ) );
	}
	if ( ! defined( 'PRINTSHOP_THEME_VERSION' ) ) {
		define( 'PRINTSHOP_THEME_VERSION', $theme_data->get( 'Version' ) );
	}
}

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 800; /* pixels */
}

// function remove_core_updates(){
//     global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
// }
// add_filter('pre_site_transient_update_core','remove_core_updates');
// add_filter('pre_site_transient_update_plugins','remove_core_updates');
// add_filter('pre_site_transient_update_themes','remove_core_updates');

if ( ! function_exists('printshop_setup') ) :

function printshop_setup() {

	$language_folder = PRINTSHOP_THEME_URL . '/languages';
	load_theme_textdomain( 'printshop', $language_folder );

	add_theme_support( 'automatic-feed-links' );
	
	add_theme_support( 'title-tag' );

	add_filter( 'widget_text', 'do_shortcode' );

	add_theme_support( 'post-thumbnails' );
	
	add_image_size( 'printshop-medium-thumb', 600, 300, true );
	add_image_size( 'printshop-sidebar-blog', 100, 100, array('center', 'center') );
    add_image_size( 'customs-thumb', 99, 99 );
	
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'printshop' ),
		'footer' => esc_html__( 'Footer', 'printshop' ),
		) );

	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
		) );

	add_post_type_support('page', 'excerpt');

    add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	//add_theme_support( 'wc-product-gallery-slider' );

}
endif;
add_action( 'after_setup_theme', 'printshop_setup' );

function setup_after_switch_theme() {
	//update revslider-templates-check option to prevent download rev templates
	update_option( 'revslider-templates-check', strtotime(date("Y-m-d H:i:s")), 'yes' );
}
add_action( 'after_switch_theme', 'setup_after_switch_theme' );

// since woo 3.6, need this function to activate plugin below Woo in Merlin Import
function woo_prevent_automatic_wizard_redirect() {
	return true;
}

add_filter( 'woocommerce_prevent_automatic_wizard_redirect', 'woo_prevent_automatic_wizard_redirect');


/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function printshop_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Blog Sidebar', 'printshop' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__('Sidebar in the blog pages', 'printshop'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
		) );
	register_sidebar( array(
		'name'          => esc_html__( 'Top Language', 'printshop' ),
		'id'            => 'language-1',
		'description'   => esc_html__('Widget area for language change in header', 'printshop'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
		) );
	register_sidebar( array(
		'name'          => esc_html__( 'Top Currency', 'printshop' ),
		'id'            => 'currency-1',
		'description'   => esc_html__('Widget area for currency change in header', 'printshop'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
		) );
	register_sidebar( array(
		'name'          => esc_html__( 'Cart Header', 'printshop' ),
		'id'            => 'cart-header',
		'description'   => esc_html__('Cart Header', 'printshop'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
		) );
	register_sidebar( array(
		'name'          => esc_html__( 'Page Sidebar', 'printshop' ),
		'id'            => 'sidebar-2',
		'description'   => esc_html__('Sidebar for pages', 'printshop'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
		) );
	register_sidebar( array(
		'name'          => esc_html__( 'WooCommerce Sidebar', 'printshop' ),
		'id'            => 'sidebar-woo',
		'description'   => esc_html__('Sidebar for WooCommerce pages', 'printshop'),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
		) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer 1', 'printshop' ),
		'id'            => 'footer-1',
		'description'   => printshop_sidebar_desc( 'footer-1' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
		) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 2', 'printshop' ),
		'id'            => 'footer-2',
		'description'   => printshop_sidebar_desc( 'footer-2' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
		) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 3', 'printshop' ),
		'id'            => 'footer-3',
		'description'   => printshop_sidebar_desc( 'footer-3' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
		) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer 4', 'printshop' ),
		'id'            => 'footer-4',
		'description'   => printshop_sidebar_desc( 'footer-4' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
		) );

	register_sidebar( array(
		'name'          => esc_html__( 'Top Bar Left', 'printshop' ),
		'id'            => 'topbar-left',
		'description'   => esc_html__('Widget area for top bar left', 'printshop'),
		'before_widget' => '<aside class="topbar-widget widget %2$s">',
		'after_widget'  => '</aside>',

		) );
	register_sidebar( array(
		'name'          => esc_html__( 'Top Bar Right', 'printshop' ),
		'id'            => 'topbar-right',
		'description'   => esc_html__('Widget area for top bar right', 'printshop'),
		'before_widget' => '<aside class="topbar-widget widget %2$s">',
		'after_widget'  => '</aside>',

		) );
		
		register_sidebar( array(
		'name'          => esc_html__( 'Menu Header Creative', 'printshop' ),
		'id'            => 'menu-header-creative',
		'description'   => esc_html__('Widget area for menu header creative', 'printshop'),
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
		) );
		register_sidebar( array(
		'name'          => esc_html__( 'Footer bottom', 'printshop' ),
		'id'            => 'footer-bottom',
		'description'   => printshop_sidebar_desc( 'footer-bottom' ),
		'before_widget' => '<aside id="%1$s" class="footer_parallax widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
		) );
}
add_action( 'widgets_init', 'printshop_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function printshop_scripts() {
	global $printshop_option;
	
	wp_enqueue_style( 'printshop-fontello', get_template_directory_uri() .'/assets/css/fontello.css', array(), '1.0.0' );
	wp_enqueue_style( 'printshop-compare', get_template_directory_uri() .'/woocommerce/compare.css', 'all' );
	
	wp_enqueue_style( 'printshop-style-home', get_template_directory_uri() .'/style.css', 'all' );
	wp_enqueue_style( 'printshop-style-dokan', get_template_directory_uri() .'/assets/css/dokan.css', 'all' );

	wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/assets/js/modernizr.min.js', array(), '2.6.2', true );
	wp_enqueue_script( 'printshop-libs', get_template_directory_uri() . '/assets/js/libs.js', array(), '', true );
	wp_enqueue_script( 'bootstrap_js', get_template_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), '3.4.1', true );

	if(function_exists('is_product') && is_product()) {
		wp_enqueue_script( 'product-zoom', get_template_directory_uri() . '/assets/js/jquery.elevateZoom-3.0.8.min.js', array('jquery'), '1.0.0', true );
	}

	wp_enqueue_style( 'swiper', get_template_directory_uri() .'/assets/css/swiper.min.css', 'all' );

	wp_enqueue_script( 'swiper', get_template_directory_uri() . '/assets/js/swiper.min.js', array(), '', true );
	
	wp_enqueue_script( 'printshop-theme', get_template_directory_uri() . '/assets/js/theme.js', array('jquery', 'nbt-owl-js'), '', true );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	$is_fixed_header = array('fixed_header' => ( isset($printshop_option['header_fixed']) ? $printshop_option['header_fixed'] : ''));
	wp_localize_script('printshop-theme', 'header_fixed_setting', $is_fixed_header);
	
	wp_localize_script('printshop-theme', 'nb_printshop', array(
        'label' => array(
            'wishlist' => __('Wishlist', 'printshop'),
            'quickview' => __('Quickview', 'printshop'),
            'browse_wishlist' => __('Browse Wishlist', 'printshop'),
            'start_desgin' => __('Start Design', 'printshop'),
            'compare' => __('Compare', 'printshop'),
		),
    ));
}
add_action( 'wp_enqueue_scripts', 'printshop_scripts' );

/**
 * Theme Options
 */

if ( !isset( $redux_demo ) ) {
	require_once( get_template_directory() . '/inc/options-config.php' );
}
/**
 * Recomend plugins via TGM activation class
 */

require get_template_directory() . '/inc/tgm/plugin-activation.php';

require_once get_parent_theme_file_path( '/inc/import/merlin/vendor/autoload.php' );
require_once get_parent_theme_file_path( '/inc/import/merlin/class-merlin.php' );
require_once get_parent_theme_file_path( '/inc/import/merlin-config.php' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';
require get_template_directory() . '/inc/class.woocommerce-advanced-reviews.php';
require get_template_directory() . '/inc/dokan.php';


/**
 * The theme fully support WooCommerce, Awesome huh?.
 */
add_theme_support( 'woocommerce' );
require get_template_directory() . '/inc/woo-config.php';
require get_template_directory() . '/inc/apis/mailchimp-api.php';

/*
* Recent post
*/
class printshop_recent_posts extends WP_Widget {
	function __construct() {
		parent::__construct(
			'netbase-recent-posts',
			'Recent Post Thumbnail',
			array( 'description' => 'List Recent Post', )
			);
	}
	public function widget($args, $instance) {
		extract($args);
		$title = apply_filters( 'widget_title', $instance['title'] );
		$number = $instance['number'];
		printf("%s", $before_widget) ;
		printf("%s", $before_title) ;
		if ( ! empty ( $title ) ) {
			printf("%s", $title) ;
		}
		printf("%s", $after_title) ;		
		$args = array (
			'posts_per_page' => $number,
			);
		$neatly_posts = new WP_Query($args);
		if( $neatly_posts->have_posts() ) {
			echo '<ul>';
			while( $neatly_posts->have_posts() ) : $neatly_posts->the_post(); ?>
			<li>
				<div class="post-thumb"><a href="<?php the_permalink(); ?>"><?php echo the_post_thumbnail('printshop-sidebar-blog'); ?></a></div>
				<div class="post-thumb-info">
					<p class="post-info-top"><a href="<?php the_permalink(); ?>"><?php echo the_title(); ?></a></p>					
					<?php
					if ( has_excerpt() ){
						echo '<p class="post-info-excerpt">';
						echo wp_trim_words( get_the_excerpt(), 10, '...' ); 
						echo '</p>';
					}
					?>
					<a class="post-info-readmore" href="<?php the_permalink(); ?>"><?php esc_html_e('Read More', 'printshop')?><i class="fa fa-chevron-right" ></i></a>
				</div>
			</li>
			<?php endwhile;
			echo '</ul>';
		}
		wp_reset_postdata();
		printf("%s", $after_widget) ;		
}
public function form( $instance ) {
	$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Recent Post', 'printshop' );
	if (isset( $instance['number' ] ) ) {
		$number = $instance['number'];
	} else { $number = '5'; }
	?>
	<p>
		<label for="<?php echo esc_html($this->get_field_id('title')); ?>"><?php esc_html__('Title:', 'printshop'); ?></label>
		<input class="widefat" id="<?php echo esc_html($this->get_field_id('title')); ?>" name="<?php echo esc_html($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
	</p>
	<p><em><?php esc_html_e('Use the following options to customize the display.', 'printshop'); ?></em></p>
	<p style="border-bottom:4px double #eee;padding: 0 0 10px;">
		<label for="<?php echo esc_html($this->get_field_id( 'number' )); ?>"><?php esc_html_e('Number of posts:', 'printshop');?></label>
		<input id="<?php echo esc_html($this->get_field_id( 'number')); ?>" name="<?php echo esc_html($this->get_field_name( 'number' )); ?>" value="<?php echo esc_attr($number); ?>" type="number" style="width:100%;" /><br>
	</p>
	<?php }
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = strip_tags($new_instance['number']);
		return $instance;
	}
}

function printshop_register_recent_posts() {
	register_widget( 'printshop_recent_posts' );
}
add_action( 'widgets_init', 'printshop_register_recent_posts' );

remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

add_filter('loop_shop_columns', 'printshop_loop_columns');
if (!function_exists('printshop_loop_columns')) {
 function printshop_loop_columns() {
  return 3; 
 }
}

function printshop_search_filter($query) {
    	if( $query->is_admin ) {
    		return $query;
    	}	
	if( $query->is_search ) {
		//$query->set( 'post__not_in' , array( 2537,2129, 2128, 2209 ) ); // Page ID
		$query->set('post_type',array('product','post'));
	}
	
	return $query;
}
add_filter('pre_get_posts','printshop_search_filter');

function printshop_get_redux_options() {
	global $printshop_option;
	return $printshop_option;
}

if ( in_array( 'siteorigin-panels/siteorigin-panels.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
{

	function printshop_custom_row_style_fields($fields) {
		$fields['nbparallax'] = array(
			'name'        => __('Wpnetbase Parallax', 'printshop'),
			'type'        => 'checkbox',
			'group'       => 'design',
			'description' => __('If enabled, the background image will have a parallax effect.custom by netbaseteam', 'printshop'),
			'priority'    => 8,
		);

		return $fields;
	}

	add_filter( 'siteorigin_panels_row_style_fields', 'printshop_custom_row_style_fields' );

	function printshop_custom_row_style_attributes($attributes, $args ) {
		if( !empty( $args['nbparallax'] ) ) {
			array_push($attributes['class'], 'nbparallax');
		}

		return $attributes;
	}

	add_filter('siteorigin_panels_row_style_attributes', 'printshop_custom_row_style_attributes', 10, 2);

}
function printshop_register_sidebar_menu() {
	register_nav_menu('printshop-sidebar-menu',__( 'Menu Sidebar Main' ));
	if ( class_exists('ReduxFrameworkPlugin') ) {
        remove_filter( 'plugin_row_meta', array( ReduxFrameworkPlugin::get_instance(), 'plugin_metalinks'), null, 2 );
	}
	if ( class_exists('ReduxFrameworkPlugin') ) {
	   remove_action('admin_notices', array( ReduxFrameworkPlugin::get_instance(), 'admin_notices' ) );    
	}
}
add_action( 'init', 'printshop_register_sidebar_menu' );

function printshop_custom_css_admin() {
  echo '<style>
    .redux-message.redux-notice.notice {
      display:none;
    } 
  </style>';
}
add_action('admin_head', 'printshop_custom_css_admin');

function printshop_admin_style() {
  wp_enqueue_style('admin-styles', get_template_directory_uri().'/admin.css');
}
add_action('admin_enqueue_scripts', 'printshop_admin_style');

/*
	 * Get woocommerce version 
	 */
function printshop_get_woo_version_number() {
       
	if ( ! function_exists( 'get_plugins' ) )
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
       
	$plugin_folder = get_plugins( '/' . 'woocommerce' );
	$plugin_file = 'woocommerce.php';	
	
	   if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
		  return $plugin_folder[$plugin_file]['Version'];

	   } else {
	
		return NULL;
	   }
}
function printshop_wcremove_items() {
	
	if ( ! current_user_can( 'update_core') ) {
		
		remove_menu_page('Reviews');
		remove_menu_page('yit_plugin_panel');

	}
}
add_action( 'admin_menu', 'printshop_wcremove_items', 99, 0 );

// Defer jQuery Parsing using the HTML5 defer property
if (!(is_admin() )) {
    function defer_parsing_of_js ( $url ) {
        if ( FALSE === strpos( $url, '.js' ) ) return $url;
        if ( strpos( $url, 'jquery.js' ) ) return $url;
        // return "$url' defer ";
        return "$url' defer onload='";
    }
    //add_filter( 'clean_url', 'defer_parsing_of_js', 11, 1 );
}

/* Metabox Manage Sidebars per posts/page*/

if( !function_exists('printshop_sidebar_box') ):
function printshop_sidebar_box($post) {

    global $post;
    global $wp_registered_sidebars ;
    
    $sidebar_option = get_post_meta($post->ID, 'sidebar_option', true);
    
    $sidebar_values = array(   'sidebar-default'=>'Default', 
                               'right-sidebar'=>'Right Sidebar',
                               'left-sidebar'=>'Left Sidebar',
                               'no-sidebar'=>'No Sidebar', 
                               'full-screen'=>'Full Screen');
    
    $option         = '';    
    
    foreach ($sidebar_values as $key=>$value) {
        $option.='<option value="' . $key . '"';
        if ($key == $sidebar_option) {
            $option.=' selected="selected"';
        }
        $option.='>' . $value . '</option>';
    }

    print '   
    <p class="meta-options"><label for="sidebar_option">'.__('Page Layout ( Set the page layout, inherit from Theme Option by default ) ','printshop').' </label><br />
        <select id="sidebar_option" name="sidebar_option" style="width: 200px;">
        ' . $option . '
        </select>
    </p>';
        
}
endif; // end   printshop_sidebar_box  

if( !function_exists('printshop_sidebar_meta') ):

function printshop_sidebar_meta() {
global $post;  
	// add_meta_box('wpestate-sidebar-post',  __('Sidebar Settings',  'printshop'), 'printshop_sidebar_box', 'post');
    add_meta_box('printshop-sidebar-page', __('Page Settings',  'printshop'), 'printshop_sidebar_box', 'page');
}
endif;

add_action('add_meta_boxes', 'printshop_sidebar_meta');
add_action('save_post', 'printshop_save_postdata', 1, 2);

/*Saving of custom data*/

if( !function_exists('printshop_save_postdata') ):
function printshop_save_postdata($post_id) {
    global $post;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    ///////////////////////////////////// Check permissions   
    if(isset($_POST['post_type'])){       
            if ('page' == $_POST['post_type'] or 'post' == $_POST['post_type']) {
                if (!current_user_can('edit_page', $post_id))
                    return;
            }
            else {
                if (!current_user_can('edit_post', $post_id))
                    return;
            }
    }
     
    $allowed_keys=array(
        'sidebar_option'
        
    );
   
    foreach ($_POST as $key => $value) {
        if( !is_array ($value) ){
           
            if (in_array ($key, $allowed_keys)) {
                $postmeta = wp_filter_kses( $value ); 
                update_post_meta($post_id, sanitize_key($key), $postmeta );
            }
        }       
    }
    
}
endif; // end   printshop_save_postdata  

remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 ); 

add_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );

/**
 * Show the product title in the product loop. By default this is an H2.
 */
function woocommerce_template_loop_product_title() {
	echo '<h3 class="woocommerce-loop-product__title">' . get_the_title() . '</h3>';
}


function bamboo_request($query_string )
{

    if( isset( $query_string['paged'] ) ) {
    	$dokan_general = get_option('dokan_general' );
    	if($dokan_general && isset($query_string[$dokan_general['custom_store_url']]) ){
    		$query_string['dokan_per_page'] = $query_string['paged'];
    		unset($query_string['paged']);
    	}
	}
	
    return $query_string;
}
add_filter('request', 'bamboo_request');

// function test_qv() {
// 	echo '<div class="test-qv">';
// 	woocommerce_show_product_thumbnails();
// 	echo '</div>';
// }
// add_action('yith_wcqv_product_image', 'test_qv', 20);
if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}

add_filter( 'yith-wcwl-browse-wishlist-label', 'browse_wishlist_label_custom', 10,  3);
function browse_wishlist_label_custom($browse_wishlist_text, $product_id, $icon) {
	return '<span>' . $browse_wishlist_text . '</span>';
}