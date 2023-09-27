<?php
class NBT_WooCommerce_AjaxSearch_Frontend{
	protected $args;
	
	function __construct() {
		add_action('wp_enqueue_scripts', array($this, 'embed_style'));
		add_action( 'wp_ajax_nopriv_nbt_search_now', array($this, 'nbt_search_now') );
		add_action( 'wp_ajax_nbt_search_now', array($this, 'nbt_search_now') );
		add_shortcode( 'nbt_search', array($this, 'shortcode_ajax_search'), 10, 1 );
	}

	public function shortcode_ajax_search($atts) {
		$atts = shortcode_atts( array(
			'layout' => 'popup',
		), $atts, 'nbt_search' );

		$file = AJAX_SEARCH_PATH . 'tpl/search-' . esc_attr($atts['layout']) .'.php';
		
		if( file_exists($file) ) {
			include $file;
		}
	}

	public function nbt_search_now(){
		global $wpdb, $post;
		$json = array();
		$search = $_REQUEST['search'];

		if( $search ){
			$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'product' AND (post_title LIKE %s OR post_content LIKE %s OR post_excerpt LIKE %s)", '%'.$wpdb->esc_like($search).'%', '%'.$wpdb->esc_like($search).'%', '%'.$wpdb->esc_like($search).'%'), OBJECT );
			if( $results ){
				$json['result'] = '';
				foreach ($results as $key => $post) {
					$product = wc_get_product($post->ID);
					ob_start();
					?>
					<div class="nas-items">
						<div class="nas-item-thumb">
							<a href="<?php echo get_permalink($post->ID );?>" class="nas-item-link" title="<?php echo $post->post_title;?>">
							<?php echo $product->get_image( array( 50, 50 ) );?>
							</a>
						</div>
						<div class="nas-item-title">
							<h3 class="nas-item-title-heading"><a href="<?php echo get_permalink($post->ID );?>" title="<?php echo $post->post_title;?>"><?php echo $post->post_title;?></a></h3>
							<?php
								if ( 'no' === get_option( 'woocommerce_enable_review_rating' ) ) {
									return;
								}

								$rating_count = $product->get_rating_count();
								$review_count = $product->get_review_count();
								$average      = $product->get_average_rating();

								if ( $rating_count > 0 ) : ?>
									<div class="woocommerce-product-rating">
										<?php echo wc_get_rating_html( $average, $rating_count ); ?>
									</div>
								<?php else: ?>
									<div class="woocommerce-product-rating">
										<div class="star-rating" title="<?php _e('Rated 0.00 out of 5', 'nbt-solution');?>"><span style="width:0"><strong class="rating">0.00</strong> <?php _e('out of', 'nbt-solution');?> 5</span></div>
									</div>
							<?php endif; ?>
						</div>
						<div class="nas-item-price">
							<div class="product-price">
								<?php
								if( $product->get_price_html() !='' ):
									echo $product->get_price_html();
								endif;
								?>
							</div>
						</div>
					</div>

					<?php
					$json['result'] .= ob_get_clean();
				}
				$json['result'] .= '</ul>';
			}else{
				$json['result'] = '<p class="nas-empty-result">'.__('No results.', 'nbt-solution').'</p>';
			}
			$json['complete'] = true;
		}
		wp_send_json($json);
	}


	function embed_style(){
		wp_enqueue_style( 'jquery.mCustomScrollbar', AJAX_SEARCH_URL . 'assets/css/jquery.mCustomScrollbar.min.css',false,'1.1','all');
		wp_enqueue_script( 'jquery.mCustomScrollbar', AJAX_SEARCH_URL . 'assets/js/jquery.mCustomScrollbar.min.js', null, null, true );

		$layout = NB_Solution::get_setting('ajax-search');

		if( isset($layout['wc_ajax-search_color_icon']) ) {
			$style = '.nbt-icon-search:before { color: '. esc_attr($layout['wc_ajax-search_color_icon']) .';}';
			wp_add_inline_style('frontend-solutions', $style);
		}
	}
}
new NBT_WooCommerce_AjaxSearch_Frontend();