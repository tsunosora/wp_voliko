<?php
class NB_Dokan_Review {
	private $limit = 15;
	
	function __construct() {

    }
    
    /**
     * Inistantiate the Dokan_Pro_Coupons class
     *
     * @since 2.4
     *
     * @return object
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new NB_Dokan_Review();
        }

        return $instance;
    }

    /**
     * Comment Query
     *
     * @since 2.4
     *
     * @param  integer $id
     * @param  string $post_type
     * @param  integer $limit
     * @param  string $status
     *
     * @return object
     */
    function comment_query( $id, $post_type, $limit, $status, $offset = false ) {
        global $wpdb;

        $page_number = $offset ? $offset : get_query_var( 'paged' );
        $pagenum     = max( 1, $page_number );
        $offset      = ( $pagenum - 1 ) * $limit;

        $comments = $wpdb->get_results(
            "SELECT c.comment_content, c.comment_ID, c.comment_author,
                c.comment_author_email, c.comment_author_url,
                p.post_title, c.user_id, c.comment_post_ID, c.comment_approved,
                c.comment_date
            FROM $wpdb->comments as c, $wpdb->posts as p
            WHERE p.post_author='$id' AND
                p.post_status='publish' AND
                c.comment_post_ID=p.ID AND
                c.comment_approved='$status' AND
                p.post_type='$post_type'  ORDER BY c.comment_ID DESC
            LIMIT $offset,$limit"
        );

        return $comments;
    }


    /**
     * Review Pagination
     *
     * @since 2.4
     *
     * @param int     $id
     * @param string  $post_type
     * @param int     $limit
     * @param string  $status
     *
     * @return string
     */
    function review_pagination( $id, $post_type, $limit, $status ) {
        global $wpdb;

        $total = $wpdb->get_var(
            "SELECT COUNT(*)
            FROM $wpdb->comments, $wpdb->posts
            WHERE   $wpdb->posts.post_author='$id' AND
            $wpdb->posts.post_status='publish' AND
            $wpdb->comments.comment_post_ID=$wpdb->posts.ID AND
            $wpdb->comments.comment_approved='$status' AND
            $wpdb->posts.post_type='$post_type'"
        );

        $pagenum = max( get_query_var( 'paged' ), 1 );

        $num_of_pages = ceil( $total / $limit );

        $page_links = paginate_links( array(
            'base'      => dokan_get_store_url( $id ) . 'reviews/%_%',
            'format'    => 'page/%#%',
            'prev_text' => esc_html__( '&laquo;', 'woopanel' ),
            'next_text' => esc_html__( '&raquo;', 'woopanel' ),
            'total'     => $num_of_pages,
            'type'      => 'array',
            'current'   => $pagenum
        ) );

        if ( $page_links ) {
            $pagination_links  = '<div class="pagination-wrap">';
            $pagination_links .= '<ul class="pagination"><li>';
            $pagination_links .= join( "</li>\n\t<li>", $page_links );
            $pagination_links .= "</li>\n</ul>\n";
            $pagination_links .= '</div>';

            return $pagination_links;
        }
    }

    function render_store_tab_comment_list( $comments, $store_id ) {

        ob_start();
        if ( count( $comments ) == 0 ) {
            echo '<span colspan="5">' . esc_html__( 'No Reviews Found', 'woopanel' ) . '</span>';
        } else {
            foreach ( $comments as $single_comment ) {
                if ( $single_comment->comment_approved ) {
                    $GLOBALS['comment'] = $single_comment;
                    $comment_date       = get_comment_date( '', $single_comment->comment_ID );
                    $comment_author_img = get_avatar( $single_comment->comment_author_email, 180 );
                    $permalink          = get_comment_link( $single_comment );
                    ?>

                    <li <?php comment_class(); ?> itemtype="http://schema.org/Review" itemscope="" itemprop="reviews">
                        <div class="review_comment_container">
                            <div class="dokan-review-author-img"><?php echo wp_kses($comment_author_img, array(
                                'img' => array(
                                    'src' => array(),
                                    'class' => array(),
                                    'height' => array(),
                                    'width' => array()
                                )
                                )); ?></div>
                            <div class="comment-text">
                                <p class="comment-info">
                                    <strong itemprop="author"><?php echo esc_attr($single_comment->comment_author); ?></strong>
                                    <em class="verified"><?php echo ($single_comment->user_id == 0 ? '(Guest)' : ''); ?></em>
                                    –
                                    <a href="<?php echo esc_url($permalink); ?>">
                                        <time datetime="<?php echo date( 'c', strtotime( $comment_date ) ); ?>" itemprop="datePublished"><?php echo esc_attr($comment_date); ?></time>
                                    </a>
                                </p>
                                <div class="description" itemprop="description">
                                    <?php if( $review_title = get_comment_meta( $single_comment->comment_ID, 'comment_title', true ) ) :
                                        echo '<h5 class="comment-heading">'. esc_attr($review_title) .'</h5>';
                                    endif;?>
                                    <p><?php echo esc_attr($single_comment->comment_content); ?></p>
                                </div>

                                <a href="<?php echo esc_attr($permalink); ?>" class="woopanel-dokan-ratingstar">
                                    <?php
                                    if ( get_option( 'woocommerce_enable_review_rating' ) == 'yes' ) :
                                        $rating = intval( get_comment_meta( $single_comment->comment_ID, 'rating', true ) );
                                    ?>
                                        <div class="dokan-rating">
                                            <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf( esc_html__( 'Rated %d out of 5', 'woopanel' ), $rating ) ?>">
                                                <span style="width:<?php echo ( intval( get_comment_meta( $single_comment->comment_ID, 'rating', true ) ) / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo esc_attr($rating); ?></strong> <?php esc_html_e( 'out of 5', 'woopanel' ); ?></span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </div>
                        </div>
                    </li>

                    <?php
                }
            }
        }

        $review_list = ob_get_clean();

        return apply_filters( 'dokan_seller_tab_reviews_list', $review_list, $store_id );
    }
}