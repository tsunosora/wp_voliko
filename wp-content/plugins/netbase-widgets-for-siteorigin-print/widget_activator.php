<?php 
/*
Plugin Name: Netbase Widgets for SiteOrigin
Plugin URI: http://www.netbaseteam.com
Description: Netbase Widgets Printshop.
Author: NetbaseTeam
Version: 1.0.6
Text Domain:Custom widget activator
Domain Path: /languages
Author URI: http://www.netbase.vn
*/

if ( ! defined( 'ABSPATH' ) )
{
	exit;   
}	

if(!defined('WPNETBASE_WIDGET_PLUGIN_URL')){
	define('WPNETBASE_WIDGET_PLUGIN_URL',untrailingslashit( plugins_url( '/', __FILE__ ) ));
}
define('WPNETBASE_WIDGET_ASSET_URL', WPNETBASE_WIDGET_PLUGIN_URL.'/asset/');
define('WPNETBASE_WIDGET_PLUGIN_VER','1.0.3');

function wpnetbase_so_widgets_bundle($folders){
	$folders[] = plugin_dir_path(__FILE__).'extra-widgets/';
	return $folders;
}
add_filter('siteorigin_widgets_widget_folders', 'wpnetbase_so_widgets_bundle');

if (!function_exists('wpnetbase_to_boolean')) {

    /*
    * Converting string to boolean is a big one in PHP
    */

    function wpnetbase_to_boolean($value) {
        if (!isset($value))
            return false;
        if ($value == 'true' || $value == '1')
            $value = true;
        elseif ($value == 'false' || $value == '0')
            $value = false;
        return (bool)$value; // Make sure you do not touch the value if the value is not a string
    }
}
include_once(plugin_dir_path(__FILE__).'shortcodes/shortcodes.php');

/**
* Check if WooCommerce is active
**/
if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
function wpnetbase_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php _e( 'Netbase widget is enabled but not effective. It requires WooCommerce in order to work.', 'wpnetbase' ); ?></p>
	</div>
	<?php
}
function wpnetbase_wg_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'wpnetbase_install_woocommerce_admin_notice' );
	}
	
}
add_action( 'plugins_loaded', 'wpnetbase_wg_install', 11 ); 

/**
* WC_List_Grid class
**/
if ( ! class_exists( 'WC_List_Grid' ) ) {

		class WC_List_Grid {

			public function __construct() {
				// Hooks
  				add_action( 'wp' , array( $this, 'setup_gridlist' ) , 20);

  				// Init settings
				$this->settings = array(
					array(
						'name' 	=> __( 'Default catalog view', 'woocommerce-grid-list-toggle' ),
						'type' 	=> 'title',
						'id' 	=> 'wc_glt_options'
					),
					array(
						'name' 		=> __( 'Default catalog view', 'woocommerce-grid-list-toggle' ),
						'desc_tip' 	=> __( 'Display products in grid or list view by default', 'woocommerce-grid-list-toggle' ),
						'id' 		=> 'wc_glt_default',
						'type' 		=> 'select',
						'options' 	=> array(
							'grid'  => __( 'Grid', 'woocommerce-grid-list-toggle' ),
							'list' 	=> __( 'List', 'woocommerce-grid-list-toggle' )
						)
					),
					array( 'type' => 'sectionend', 'id' => 'wc_glt_options' ),
				);

				// Default options
				add_option( 'wc_glt_default', 'grid' );

				// Admin
				add_action( 'woocommerce_settings_image_options_after', array( $this, 'admin_settings' ), 20 );
				add_action( 'woocommerce_update_options_catalog', array( $this, 'save_admin_settings' ) );
				add_action( 'woocommerce_update_options_products', array( $this, 'save_admin_settings' ) );
			}

			/*-----------------------------------------------------------------------------------*/
			/* Class Functions */
			/*-----------------------------------------------------------------------------------*/

			function admin_settings() {
				woocommerce_admin_fields( $this->settings );
			}

			function save_admin_settings() {
				woocommerce_update_options( $this->settings );
			}

			// Setup
			function setup_gridlist() {
				if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
					add_action( 'wp_enqueue_scripts', array( $this, 'setup_scripts_styles' ), 20);
					add_action( 'wp_enqueue_scripts', array( $this, 'setup_scripts_script' ), 20);
					add_action( 'woocommerce_before_shop_loop', array( $this, 'gridlist_toggle_button' ), 1);
					add_action( 'woocommerce_after_shop_loop_item', array( $this, 'gridlist_buttonwrap_open' ), 9);
					add_action( 'woocommerce_after_shop_loop_item', array( $this, 'gridlist_buttonwrap_close' ), 11);
					add_action( 'woocommerce_after_shop_loop_item', array( $this, 'gridlist_hr' ), 30);
					add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_single_excerpt', 5);
					add_action( 'woocommerce_after_subcategory', array( $this, 'gridlist_cat_desc' ) );
				}
			}

			// Scripts & styles
			function setup_scripts_styles() {
				wp_enqueue_style( 'grid-list-layout', plugins_url( '/assests/css/style.css', __FILE__ ) );
				wp_enqueue_style( 'grid-list-button', plugins_url( '/assests/css/button.css', __FILE__ ) );
				wp_enqueue_style( 'dashicons' );
			}

			function setup_scripts_script() {
				wp_enqueue_script( 'cookie', plugins_url( '/assests/js/jquery.cookie.min.js', __FILE__ ), array( 'jquery' ) );
				wp_enqueue_script( 'grid-list-scripts', plugins_url( '/assests/js/jquery.gridlistview.min.js', __FILE__ ), array( 'jquery' ) );
				add_action( 'wp_footer', array( $this, 'gridlist_set_default_view' ) );
			}

			// Toggle button
			function gridlist_toggle_button() {
				?>
					<nav class="gridlist-toggle">
						<a href="#" id="grid" title="<?php _e('Grid view', 'woocommerce-grid-list-toggle'); ?>"><span class="dashicons dashicons-grid-view"></span> <em><?php _e( 'Grid view', 'woocommerce-grid-list-toggle' ); ?></em></a><a href="#" id="list" title="<?php _e('List view', 'woocommerce-grid-list-toggle'); ?>"><span class="dashicons dashicons-exerpt-view"></span> <em><?php _e( 'List view', 'woocommerce-grid-list-toggle' ); ?></em></a>
					</nav>
				<?php
			}

			// Button wrap
			function gridlist_buttonwrap_open() {
				?>
					<div class="gridlist-buttonwrap">
				<?php
			}
			function gridlist_buttonwrap_close() {
				?>
					</div>
				<?php
			}

			// hr
			function gridlist_hr() {
				?>
					<hr />
				<?php
			}

			function gridlist_set_default_view() {
				$default = get_option( 'wc_glt_default' );
				?>
					<script>
						if (jQuery.cookie( 'gridcookie' ) == null) {
					    	jQuery( 'ul.products' ).addClass( '<?php echo $default; ?>' );
					    	jQuery( '.gridlist-toggle #<?php echo $default; ?>' ).addClass( 'active' );
					    }
					</script>
				<?php
			}

			function gridlist_cat_desc( $category ) {
				global $woocommerce;
				echo '<div itemprop="description">';
					echo $category->description;
				echo '</div>';

			}
		}

		$WC_List_Grid = new WC_List_Grid();
	}
	
	add_action('wp_head','wpnetbase_frontend_assests');
 
	function wpnetbase_frontend_assests(){
		
	wp_enqueue_style( 'nbt-owl-css', plugin_dir_url(__FILE__).'assests/css/owl.carousel.min.css' );
	wp_enqueue_style( 'nbt-custom-widget', plugin_dir_url(__FILE__).'assests/css/custom_widget.css' );	
	wp_enqueue_script( 'nbt-owl-js', plugin_dir_url( __FILE__ ).'assests/js/owl.carousel.min.js', array( 'jquery' ), true);
	
	wp_enqueue_script( 'nbt-navAccordion', plugin_dir_url( __FILE__ ).'assests/js/navAccordion.min.js', array( 'jquery' ));

	}
	include_once(plugin_dir_path(__FILE__).'shortcodes/woo_shortcodes.php');		
	include_once(plugin_dir_path(__FILE__).'widgets/top_seller_widget/top_seller_widget.php');	
	include_once(plugin_dir_path(__FILE__).'widgets/featured_widget.php');	
	include_once(plugin_dir_path(__FILE__).'widgets/new_arrival_widget/new_arrival_widget.php');	
	include_once(plugin_dir_path(__FILE__).'widgets/sale_off_widget/sale_off_widget.php');	
	include_once(plugin_dir_path(__FILE__).'widgets/category_widget.php');	
	include_once(plugin_dir_path(__FILE__).'widgets/service_boxes_widget/service_boxes_widget.php');
	include_once(plugin_dir_path(__FILE__).'widgets/post_carousel/post-carousel-widget.php');
	include_once(plugin_dir_path(__FILE__).'widgets/social_media/social_media_widget.php');		
	include_once(plugin_dir_path(__FILE__).'widgets/product_search_form/product_search_form.php');		
	include_once(plugin_dir_path(__FILE__).'widgets/nav_accordion/nav_accordion.php');
	include_once(plugin_dir_path(__FILE__).'widgets/child-category/child-category.php');	
	include_once(plugin_dir_path(__FILE__).'widgets/flickr-widget.php');
	include_once(plugin_dir_path(__FILE__).'widgets/contact-info-widget.php');	
	include_once(plugin_dir_path(__FILE__).'widgets/author-widget.php');
	include_once(plugin_dir_path(__FILE__).'widgets/popular-posts-widget.php');	
	// include_once(plugin_dir_path(__FILE__).'widgets/ajaxcart.php');	
	include_once(plugin_dir_path(__FILE__).'widgets/woo-products-same-category.php');	
	include_once(plugin_dir_path(__FILE__).'widgets/woo-most-viewed-products-widget.php');	
	include_once(plugin_dir_path(__FILE__).'widgets/subscribe-widget.php');	
	include_once(plugin_dir_path(__FILE__).'widgets/products-bycat-group.php');		
	include_once(plugin_dir_path(__FILE__).'wp-tab-widget.php');
	
	
	function wpnetbase_mytheme_add_widget_tabs($tabs) {
		$tabs[] = array(
			'title' => __('NetBaseTeam Widgets', 'netbaseteam'),
			'filter' => array(
				'groups' => array('netbaseteam')
			)
		);

		return $tabs;
	}
	add_filter('siteorigin_panels_widget_dialog_tabs', 'wpnetbase_mytheme_add_widget_tabs', 20);	