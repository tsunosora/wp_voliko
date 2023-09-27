<?php
global $post;

$user_id                 = get_current_user_id();
$processing_time         = dokan_get_shipping_processing_times();

$_disable_shipping       = get_post_meta( $post_id, '_disable_shipping', true ) ? get_post_meta( $post_id, '_disable_shipping', true ) : 'no';
$_additional_price       = get_post_meta( $post->ID, '_additional_price', true );
$_additional_qty         = get_post_meta( $post->ID, '_additional_qty', true );
$_processing_time        = get_post_meta( $post->ID, '_dps_processing_time', true );

$dps_shipping_type_price = get_user_meta( $user_id, '_dps_shipping_type_price', true );
$dps_additional_qty      = get_user_meta( $user_id, '_dps_additional_qty', true );
$dps_pt                  = get_user_meta( $user_id, '_dps_pt', true );

$porduct_shipping_pt     = ( $_processing_time ) ? $_processing_time : $dps_pt;
?>

<?php do_action( 'dokan_product_options_shipping_before' ); ?>

<div class="dokan-form-horizontal dokan-product-shipping">
    <input type="hidden" name="product_shipping_class" value="0">
    <?php if ( 'yes' == get_option( 'woocommerce_calc_shipping' ) ): ?>
        <div class="row form-group">
            <div class="col-md-3"><label><?php _e( 'Disable Shipping', 'printshop' ); ?></label></div>
            <div class="col-md-9">
                <input type="checkbox" id="_disable_shipping" name="_disable_shipping"  value="yes" <?php checked( $_disable_shipping, 'yes' ); ?>>
                <?php _e( 'Disable shipping for this product', 'printshop' ); ?>
            </div>
        </div>
    <?php endif ?>

    <div class="row form-group">
            <div class="col-md-3"><label><?php echo __( 'Weight', 'printshop' ) . ' (' . get_option( 'woocommerce_weight_unit' ) . ')'; ?></label></div>
            <div class="col-md-9">
            <?php dokan_post_input_box( $post->ID, '_weight' ); ?>
        </div>
    </div>

    <div class="row form-group">
            <div class="col-md-3"><label><?php echo __( 'Dimensions', 'printshop' ) . ' (' . get_option( 'woocommerce_dimension_unit' ) . ')'; ?></label></div>
            <div class="col-md-9 product-dimension">
            <?php dokan_post_input_box( $post->ID, '_length', array( 'class' => 'form-control col-sm-1', 'placeholder' => __( 'length', 'printshop' ) ), 'number' ); ?>
            <?php dokan_post_input_box( $post->ID, '_width', array( 'class' => 'form-control col-sm-1', 'placeholder' => __( 'width', 'printshop' ) ), 'number' ); ?>
            <?php dokan_post_input_box( $post->ID, '_height', array( 'class' => 'form-control col-sm-1', 'placeholder' => __( 'height', 'printshop' ) ), 'number' ); ?>
        </div>
    </div>

    <?php if ( 'yes' == get_option( 'woocommerce_calc_shipping' ) ): ?>
        <div class="row form-group hide_if_disable">
            <div class="col-md-3"><label><?php _e( 'Override Shipping', 'printshop' ); ?></label></div>
            <div class="col-md-9">
                <?php dokan_post_input_box( $post->ID, '_overwrite_shipping', array( 'label' => __( 'Override default shipping cost for this product', 'printshop' ) ), 'checkbox' ); ?>
            </div>
        </div>

        <div class="row form-group dokan-shipping-price dokan-shipping-type-price show_if_override hide_if_disable">
            <div class="col-md-3"><label><?php _e( 'Additional cost', 'printshop' ); ?></label></div>
            <div class="col-md-9">
                <input id="shipping_type_price" value="<?php echo $_additional_price; ?>" name="_additional_price" placeholder="0.00" class="dokan-form-control" type="number" step="any">
            </div>
        </div>

       <div class="row form-group dokan-shipping-price dokan-shipping-add-qty show_if_override hide_if_disable">
            <div class="col-md-3"><label><?php _e( 'Per Qty Additional Price', 'printshop' ); ?></label></div>
            <div class="col-md-9">
                <input id="additional_qty" value="<?php echo ( $_additional_qty ) ? $_additional_qty : $dps_additional_qty; ?>" name="_additional_qty" placeholder="1.99" class="dokan-form-control" type="number" step="any">
            </div>
        </div>

        <div class="row form-group dokan-shipping-price dokan-shipping-add-qty show_if_override hide_if_disable">
            <div class="col-md-3"><label><?php _e( 'Processing Time', 'printshop' ); ?></label></div>
            <div class="col-md-9">
                <select name="_dps_processing_time" id="_dps_processing_time" class="dokan-form-control">
                    <?php foreach ( $processing_time as $processing_key => $processing_value ): ?>
                          <option value="<?php echo $processing_key; ?>" <?php selected( $porduct_shipping_pt, $processing_key ); ?>><?php echo $processing_value; ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
    <?php endif ?>

    <?php do_action( 'dokan_product_options_shipping' ); ?>
</div>
