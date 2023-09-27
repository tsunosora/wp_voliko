<?php $_sold_individually = get_post_meta( $post_id, '_sold_individually', true ); ?>
<div class="dokan-form-horizontal">
    <div class="row form-group">
        <div class="col-md-3"><label><?php _e( 'Purchase Note', 'printshop' ); ?></label></div>
        <div class="col-md-9">
            <?php dokan_post_input_box( $post->ID, '_purchase_note', array(), 'textarea' ); ?>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-3"><label><?php _e( 'Reviews', 'printshop' ); ?></label></div>
        <div class="col-md-9">
            <?php $_enable_reviews = ( $post->comment_status == 'open' ) ? 'yes' : 'no'; ?>
            <?php dokan_post_input_box( $post->ID, '_enable_reviews', array('value' => $_enable_reviews, 'label' => __( 'Enable Reviews', 'printshop' ) ), 'checkbox' ); ?>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-3"><label><?php _e( 'Visibility', 'printshop' ); ?></label></div>
        <div class="col-md-9">
            <?php dokan_post_input_box( $post->ID, '_visibility', array( 'options' => array(
                'visible' => __( 'Catalog or Search', 'printshop' ),
                'catalog' => __( 'Catalog', 'printshop' ),
                'search' => __( 'Search', 'printshop' ),
                'hidden' => __( 'Hidden', 'printshop')
            ) ), 'select' ); ?>
        </div>
    </div>

    <div class="row form-group">
        <div class="col-md-3"><label><?php _e( 'Sold Individually', 'printshop' ); ?></label></div>
        <div class="col-md-9">
            <input name="_sold_individually" id="_sold_individually" value="yes" type="checkbox" <?php checked( $_sold_individually, 'yes' ); ?>>
            <?php _e( 'Allow only one quantity of this product to be bought in a single order', 'printshop' ) ?>
        </div>
    </div>

</div> <!-- .form-horizontal -->