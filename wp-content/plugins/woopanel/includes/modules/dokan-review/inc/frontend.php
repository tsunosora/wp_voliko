<?php
class NB_Dokan_Review_Frontend {
	private $write_review_text;
	
	function __construct() {
		add_action( 'dokan_rewrite_rules_loaded', array( $this, 'load_rewrite_rules' ) );
		add_filter( 'dokan_query_var_filter', array( $this, 'load_store_review_query_var' ), 999, 2 );
		add_filter( 'dokan_store_tabs', array($this, 'add_review_tab_in_store'), 999, 2 );
		add_filter( 'template_include', array( $this, 'store_review_template' ), 999 );
		add_action( 'dokan_after_store_tabs', array( $this, 'render_write_review_button' ), 1 );
		add_action( 'wp_enqueue_scripts', array($this, 'embed_style'));

        add_action( 'wp_footer', array($this, 'embed_template_popup_rewrite'));
        
        add_filter( 'woopanel_modules_localize', array($this, 'localize'), 20, 1 );
        add_action( 'wp_ajax_woopanel_dokan_review', array($this, 'ajax_dokan_review') );
		
		$this->write_review_text = apply_filters( 'dokan_share_text', esc_html__( 'Write a Review', 'woopanel' ) );
	}

    /**
     * Render Share pop up button
     * 
     * @return void
     */
    function render_write_review_button(){
        global $current_user;

        if( $current_user->exists() ) {
            $all_products = woopanel_dokan_review_query( get_query_var( 'author' ), true );

            if( $all_products ) { ?>
            <li class="dokan-review-btn-wrap dokan-right">
                <button class="dokan-review-btn dokan-btn dokan-btn-theme dokan-btn-sm"><?php echo esc_html( $this->write_review_text ); ?>  <i class="fa fa-edit"></i></button>
            </li>
            <?php
            }
        }
	}
	
    /**
     * Load Store Review query vars for store page
     *
     * @since 2.4.3
     *
     * @param  array $vars
     *
     * @return array
     */
    public function load_store_review_query_var( $vars ) {
        $vars[] = 'woopanel_store_review';

        return $vars;
    }

	public function add_review_tab_in_store( $tabs, $store_id) {
        $tabs['reviews'] = array(
            'title' => esc_html__( 'Reviews', 'woopanel' ),
            'url'   => woopanel_dokan_get_review_url( $store_id )
        );

        return $tabs;
	}

    /**
     * Load Rewrite Rules for store page
     *
     * @since 2.4.3
     *
     * @param  string $custom_store_url
     *
     * @return void
     */
    public function load_rewrite_rules( $custom_store_url ) {
        add_rewrite_rule( $custom_store_url.'/([^/]+)/reviews?$', 'index.php?'.esc_attr($custom_store_url).'=$matches[1]&woopanel_store_review=true', 'top' );
        add_rewrite_rule( $custom_store_url.'/([^/]+)/reviews/page/?([0-9]{1,})/?$', 'index.php?'.esc_attr($custom_store_url).'=$matches[1]&paged=$matches[2]&woopanel_store_review=true', 'top' );
	}
	

    /**
     * Returns the store review template
     *
     * @since 2.4.3
     *
     * @param string  $template
     *
     * @return string
     */
    public function store_review_template( $template ) {

        if ( ! function_exists( 'WC' ) ) {
            return $template;
        }

        if ( get_query_var( 'woopanel_store_review' ) ) {
            return dokan_locate_template( 'store-reviews.php', '', NBT_DOKAN_REVIEW_PATH. 'templates/', true );
        }

        return $template;
    }
    
    public function ajax_dokan_review() {
        global $wpdb, $current_user;

        $json = array();
        if ( ! wp_verify_nonce( $_REQUEST['security'], 'add_dokan_review' ) ) {
            $json['error'] = esc_html__('Security check', 'woopanel' );
        } else {
            if( isset($_REQUEST['data']) ) {
                parse_str($_REQUEST['data'], $data);

                $review_star = isset($data['review_star']) ? absint($data['review_star']) : 0;
                $review_product = isset($data['review_product']) ? absint($data['review_product']) : 0;
                $review_title = isset($data['review_title']) ? woopanel_clean( wp_unslash( $data['review_title'] ) ) : '';
                $review_content = isset($data['review_content']) ? woopanel_clean( wp_unslash( $data['review_content'] ) ) : '';
                $time = current_time('mysql');

                if( empty($review_star) ) {
                    $json['error'][] = esc_html__('Please rate for this prouduct', 'woopanel' );
                    $error = true;
                }

                if( empty($review_product) ) {
                    $json['error'][] = esc_html__('Please select a product', 'woopanel' );
                    $error = true;
                }

                if( empty($review_title) ) {
                    $json['error'][] = esc_html__('Please enter review title', 'woopanel' );
                    $error = true;
                }

                if( empty($review_content) ) {
                    $json['error'][] = esc_html__('Please enter review content', 'woopanel' );
                    $error = true;
                }

                if( ! isset($error) ) {
                    $check = $wpdb->get_var( $wpdb->prepare( 
                        "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_author = %s AND comment_author_email = %s AND comment_type = %s", 
                        $review_product,
                        $current_user->user_login,
                        $current_user->user_email,
                        'review'
                    ) );

                    if( $check <= 0) {
                        $data = array(
                            'comment_post_ID' => $review_product,
                            'comment_author' => $current_user->user_login,
                            'comment_author_email' => $current_user->user_email,
                            'comment_author_url' => 'http://',
                            'comment_content' => $review_content,
                            'comment_type' => 'review',
                            'comment_parent' => 0,
                            'user_id' => $current_user->ID,
                            'comment_author_IP' => woopanel_get_ip(),
                            'comment_agent' => $_SERVER['HTTP_USER_AGENT'],
                            'comment_date' => $time
                        );

                        $comment_id = wp_insert_comment($data);
                        update_comment_meta( $comment_id, 'comment_title', $review_title);
                        update_comment_meta( $comment_id, 'rating', $review_star);

                        $json['complete'] = true;
                        $json['message'] = '<div class="wpl-notice woopanel-success-msg"><ul><li>'. esc_html__('Your comment has been submitted successfully.', 'woopanel' ) .'</li></ul></div>';
                    }else {
                        $json['error'][] = esc_html__('Comment for this product already exists', 'woopanel' );
                    }
                }
                
            }

        }

        if( isset($json['error']) ) {
            $minus = '';
            if( isset($error) && count($json['error']) > 1 ) {
                $minus = '- ';
            }
            $json['error'] = sprintf('<div class="wpl-notice woopanel-error-msg"><ul><li>%s%s</li></ul></div>', $minus, implode('</li><li>- ', $json['error']) );
        }

        wp_send_json($json);
    }

	public function embed_template_popup_rewrite() {
        global $current_user;

        if( $current_user->exists() ) {
      
            $all_products = woopanel_dokan_review_query( get_query_var( 'author' ) );
        ?>
		<script type="text/html" id="tmpl-woopanel-popup-dokanreview">
            <div id="dokan-review-popup" class="white-popup mfp-hide">
                <form id="frmDokanReview" action="" method="POST">
                    <h3 class="woopanel-heading"><?php esc_html_e('Write a Review', 'woopanel' );?></h3>

                    <div class="woopanel-row" style="margin-bottom: 6px;">
                        <div class="dokan-star-rating"></div>
                        <input type="hidden" name="review_star" />
                    </div>

                    <div class="woopanel-row">
                        <label for="woopanel-review-title"><?php esc_html_e('Product:', 'woopanel' );?></label>
                        <div class="woopanel-input">
                            <select name="review_product" class="woopanel-control">
                                <?php foreach( $all_products as $product ) :?>
                                <option value="<?php echo esc_attr($product->ID);?>"><?php echo esc_attr($product->post_title);?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>

                    
                    <div class="woopanel-row">
                        <label for="woopanel-review-title"><?php esc_html_e('Title:', 'woopanel' );?></label>
                        <div class="woopanel-input">
                            <input type="text" name="review_title" class="woopanel-control" />
                        </div>
                    </div>

                    <div class="woopanel-row">
                        <label for="woopanel-review-content"><?php esc_html_e('Your Review:', 'woopanel' );?></label>
                        <div class="woopanel-input">
                            <textarea rows="4" name="review_content" class="woopanel-control"></textarea>
                        </div>
                    </div>

                    <div class="woopanel-row">
                        <button type="submit" name="review_now" class="btn btn-review-now"><?php esc_html_e('Submit', 'jpwork');?></button>
                        <span class="spinner"></span>
                    </div>
                </form>
            </div>
		</script>
        <?php
        }
    }
    
    public function localize($array) {
        $array['dokan_review_nonce'] = wp_create_nonce( 'add_dokan_review' );
        return $array;
    }
	
	public function embed_style() {
		global $wp_query;

    
		if( isset($wp_query->query['store_review']) || isset($wp_query->query['store']) ) {
            wp_enqueue_style( 'jquery-star-rating', NBT_DOKAN_REVIEW_URL . 'assets/css/star-rating-svg.css',false,'1.1','all');
			wp_enqueue_style( 'dokan-magnific-popup', NBT_DOKAN_REVIEW_URL . 'assets/css/magnific-popup.css',false,'1.1','all');
            wp_enqueue_script( 'jquery-star-rating', NBT_DOKAN_REVIEW_URL . 'assets/js/jquery-star-rating/jquery.star-rating-svg.js', '', '', true );	
            wp_enqueue_script( 'dokan-popup', NBT_DOKAN_REVIEW_URL . 'assets/js/jquery.magnific-popup.min.js', '', '', true );	
		}
	}

}
new NB_Dokan_Review_Frontend();