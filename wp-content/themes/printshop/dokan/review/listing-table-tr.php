<?php
/**
 * Dokan Listing Table tr template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>
<tr class="<?php echo $comment_status ?>">
    <td class="col-check"><input class="dokan-check-col" type="checkbox" name="commentid[]" value="<?php echo $comment->comment_ID; ?>"></td>
    <td class="col-author">
        <div class="dokan-author-img"><?php echo $comment_author_img; ?></div> <?php echo $comment->comment_author; ?> <br>

        <?php if ( $comment->comment_author_url ) { ?>
            <a href="<?php echo $comment->comment_author_url; ?>"><?php echo $comment->comment_author_url; ?></a><br>
        <?php } ?>
        <?php echo $comment->comment_author_email; ?>
    </td>
    <td class="col-content">
        <div class="dokan-comments-subdate">
            <?php
            _e( 'Submitted on ', 'printshop' );
            echo $comment_date;
            ?>
        </div>

        <div class="dokan-comments-content"><?php echo $comment->comment_content; ?></div>

        <ul class="dokan-cmt-row-actions">
            <?php
            if ( $page_status == '0' ) {
            ?>

                <li><a href="#" data-curr_page="<?php echo $page_status; ?>"  data-post_type="<?php echo $post_type; ?>" data-page_status="0" data-comment_id="<?php echo $comment->comment_ID; ?>" data-cmt_status="1" class="dokan-cmt-action"><?php _e( 'Approve', 'printshop' ); ?></a></li>
                <li><a href="#" data-curr_page="<?php echo $page_status; ?>" data-post_type="<?php echo $post_type; ?>" data-page_status="0" data-comment_id="<?php echo $comment->comment_ID; ?>" data-cmt_status="spam" class="dokan-cmt-action"><?php _e( 'Spam', 'printshop' ); ?></a></li>
                <li><a href="#" data-curr_page="<?php echo $page_status; ?>" data-post_type="<?php echo $post_type; ?>" data-page_status="0" data-comment_id="<?php echo $comment->comment_ID; ?>" data-cmt_status="trash" class="dokan-cmt-action"><?php _e( 'Trash', 'printshop' ); ?></a></li>

            <?php } else if ( $page_status == 'spam' ) { ?>

                <li><a href="#" data-curr_page="<?php echo $page_status; ?>" data-post_type="<?php echo $post_type; ?>" data-page_status="spam" data-comment_id="<?php echo $comment->comment_ID; ?>" data-cmt_status="1" class="dokan-cmt-action"><?php _e( 'Not Spam', 'printshop' ); ?></a></li>
                <li><a href="#" data-curr_page="<?php echo $page_status; ?>" data-post_type="<?php echo $post_type; ?>" data-page_status="spam" data-comment_id="<?php echo $comment->comment_ID; ?>" data-cmt_status="delete" class="dokan-cmt-action"><?php _e( 'Delete Permanently', 'printshop' ); ?></a></li>

            <?php } else if ( $page_status == 'trash' ) { ?>

                <li><a href="#" data-curr_page="<?php echo $page_status; ?>" data-post_type="<?php echo $post_type; ?>" data-page_status="trash" data-comment_id="<?php echo $comment->comment_ID; ?>" data-cmt_status="1" class="dokan-cmt-action"><?php _e( 'Restore', 'printshop' ); ?></a></li>
                <li><a href="#" data-curr_page="<?php echo $page_status; ?>" data-post_type="<?php echo $post_type; ?>" data-page_status="trash" data-comment_id="<?php echo $comment->comment_ID; ?>" data-cmt_status="delete" class="dokan-cmt-action"><?php _e( 'Delete Permanently', 'printshop' ); ?></a></li>

            <?php } else { ?>

                <?php if ( $comment_status == 'approved' ) { ?>
                    <li><a href="#" data-curr_page="<?php echo $page_status; ?>" data-post_type="<?php echo $post_type; ?>" data-page_status="1" data-comment_id="<?php echo $comment->comment_ID; ?>" data-cmt_status="0" class="dokan-cmt-action"><?php _e( 'Unapprove', 'printshop' ); ?></a></li>
                <?php } else { ?>
                    <li><a href="#" data-curr_page="<?php echo $page_status; ?>" data-post_type="<?php echo $post_type; ?>" data-page_status="1" data-comment_id="<?php echo $comment->comment_ID; ?>" data-cmt_status="1" class="dokan-cmt-action"><?php _e( 'Approve', 'printshop' ); ?></a></li>
                <?php } ?>

                <li><a href="#" data-curr_page="<?php echo $page_status; ?>" data-post_type="<?php echo $post_type; ?>" data-page_status="1" data-comment_id="<?php echo $comment->comment_ID; ?>" data-cmt_status="spam" class="dokan-cmt-action"><?php _e( 'Spam', 'printshop' ); ?></a></li>
                <li><a href="#" data-curr_page="<?php echo $page_status; ?>" data-post_type="<?php echo $post_type; ?>" data-page_status="1" data-comment_id="<?php echo $comment->comment_ID; ?>" data-cmt_status="trash" class="dokan-cmt-action"><?php _e( 'Trash', 'printshop' ); ?></a></li>

            <?php
            }
            ?>
        </ul>
    </td>
    <td class="col-link">
        <a href="<?php echo $permalink; ?>"><?php _e( 'View Comment', 'printshop' ); ?></a>

        <div style="display:none">
            <div class="dokan-cmt-hid-status"><?php echo esc_attr( $comment->comment_approved ); ?></div>
        </div>
    </td>
    <td>
    <?php if ( get_option( 'woocommerce_enable_review_rating' ) == 'yes' ) : ?>
        <?php $rating =  intval( get_comment_meta( $comment->comment_ID, 'rating', true ) ); ?>

    <div class="dokan-rating">
        <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf( __( 'Rated %d out of 5', 'printshop' ), $rating ) ?>">
            <span style="width:<?php echo ( intval( get_comment_meta( $comment->comment_ID, 'rating', true ) ) / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'printshop' ); ?></span>
        </div>
    </div>

    <?php endif; ?>
    </td>
</tr>
