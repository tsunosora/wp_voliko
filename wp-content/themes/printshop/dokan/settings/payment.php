<?php
/**
 * Dokan Settings Payment Template
 *
 * @since 2.2.2 Insert action before payment settings form
 *
 * @package dokan
 */
do_action( 'dokan_payment_settings_before_form', $current_user, $profile_info ); ?>

<form method="post" id="payment-form"  action="" class="dokan-form-horizontal">

    <?php wp_nonce_field( 'dokan_payment_settings_nonce' ); ?>

    <?php foreach ( $methods as $method_key ) {
        $method = dokan_withdraw_get_method( $method_key );
        ?>
        <div class="row form-group payment-field-<?php echo $method_key; ?>">
            <div class="col-md-3">
                <label><?php echo $method['title'] ?></label>
            </div>
            <div class="col-md-9">
                <?php if ( is_callable( $method['callback'] ) ) {
                    call_user_func( $method['callback'], $profile_info );
                } ?>
            </div>
        </div>
    <?php } ?>

    <?php
    /**
     * @since 2.2.2 Insert action on botton of payment settings form
     */
    do_action( 'dokan_payment_settings_form_bottom', $current_user, $profile_info ); ?>
    <div class="row">
        <div class="col-md-3">
            &nbsp;
        </div>
        <div class="col-md-9">
            <div class="dokan-w4 ajax_prev dokan-text-left">
                <input type="submit" name="dokan_update_payment_settings" class="dokan-btn dokan-btn-danger dokan-btn-theme" value="<?php esc_attr_e( 'Update Settings', 'printshop' ); ?>">
            </div>
        </div>
    </div>


</form>

<?php
/**
 * @since 2.2.2 Insert action after social settings form
 */
do_action( 'dokan_payment_settings_after_form', $current_user, $profile_info ); ?>
