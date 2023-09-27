<?php
/**
 * Dokan Review Listing Template
 *
 * @since 2.4
 *
 * @package dokan
 */

?>
<form id="dokan_comments-form" action="" method="post">
    <table id="dokan-comments-table" class="dokan-table dokan-table-striped">
        <thead>
            <tr>
                <th class="col-check"><input class="dokan-check-all" type="checkbox" ></th>
                <th class="col-author"><?php _e( 'Author', 'printshop' ); ?></th>
                <th class="col-content"><?php _e( 'Comment', 'printshop' ); ?></th>
                <th class="col-link"><?php _e( 'Link To', 'printshop' ); ?></th>
                <th class="col-link"><?php _e( 'Rating', 'printshop' ); ?></th>
            </tr>
        </thead>

        <tbody>

            <?php

                /**
                 * dokan_review_listing_table_body hook
                 *
                 * @hooked dokan_render_listing_table_body
                 */
                do_action( 'dokan_review_listing_table_body', $post_type )
            ?>

        </tbody>

    </table>

    <select name="comment_status">
        <?php
            if ( $comment_status == 'hold' ) {
                ?>
                <option value="none"><?php _e( '-None-', 'printshop' ); ?></option>
                <option value="approve"><?php _e( 'Mark Approve', 'printshop' ); ?></option>
                <option value="spam"><?php _e( 'Mark Spam', 'printshop' ); ?></option>
                <option value="trash"><?php _e( 'Mark Trash', 'printshop' ); ?></option>
            <?php } else if ( $comment_status == 'spam' ) { ?>
                <option value="none"><?php _e( '-None-', 'printshop' ); ?></option>
                <option value="approve"><?php _e( 'Mark Not Spam', 'printshop' ); ?></option>
                <option value="delete"><?php _e( 'Delete permanently', 'printshop' ); ?></option>
            <?php } else if ( $comment_status == 'trash' ) { ?>
                <option value="none"><?php _e( '-None-', 'printshop' ); ?></option>
                <option value="approve"><?php _e( 'Resore', 'printshop' ); ?></option>
                <option value="delete"><?php _e( 'Delete permanently', 'printshop' ); ?></option>
            <?php } else { ?>
                <option value="none"><?php _e( '-None-', 'printshop' ); ?></option>
                <option value="hold"><?php _e( 'Mark Pending', 'printshop' ); ?></option>
                <option value="spam"><?php _e( 'Mark Spam', 'printshop' ); ?></option>
                <option value="trash"><?php _e( 'Mark Trash', 'printshop' ); ?></option>
                <?php
            }
        ?>
    </select>

    <?php wp_nonce_field( 'dokan_comment_nonce_action', 'dokan_comment_nonce' ); ?>

    <input type="submit" value="<?php _e( 'Submit', 'printshop' ); ?>" class="dokan-btn  dokan-danger dokan-btn-theme dokan-btn-sm" name="comt_stat_sub">
</form>