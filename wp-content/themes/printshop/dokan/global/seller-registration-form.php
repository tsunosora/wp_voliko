<?php
/**
 * Dokan Seller registration form
 *
 * @since 2.4
 *
 * @package dokan
 */
?>

<div class="show_if_seller" style="<?php echo esc_attr( $role_style ); ?>">

    <div class="split-row form-row-wide">
        <p class="form-row form-group">
            <label for="first-name"><?php esc_html_e( 'First Name', 'printshop' ); ?> <span class="required">*</span></label>
            <input type="text" class="input-text form-control" name="fname" id="first-name" value="<?php if ( ! empty( $postdata['fname'] ) ) echo esc_attr($postdata['fname']); ?>" required="required" />
        </p>

        <p class="form-row form-group">
            <label for="last-name"><?php esc_html_e( 'Last Name', 'printshop' ); ?> <span class="required">*</span></label>
            <input type="text" class="input-text form-control" name="lname" id="last-name" value="<?php if ( ! empty( $postdata['lname'] ) ) echo esc_attr($postdata['lname']); ?>" required="required" />
        </p>
    </div>

    <p class="form-row form-group form-row-wide">
        <label for="company-name"><?php esc_html_e( 'Shop Name', 'printshop' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text form-control" name="shopname" id="company-name" value="<?php if ( ! empty( $postdata['shopname'] ) ) echo esc_attr($postdata['shopname']); ?>" required="required" />
    </p>

    <p class="form-row form-group form-row-wide">
        <label for="seller-url" class="pull-left"><?php esc_html_e( 'Shop URL', 'printshop' ); ?> <span class="required">*</span></label>
        <strong id="url-alart-mgs" class="pull-right"></strong>
        <input type="text" class="input-text form-control" name="shopurl" id="seller-url" value="<?php if ( ! empty( $postdata['shopurl'] ) ) echo esc_attr($postdata['shopurl']); ?>" required="required" />
        <small><?php echo esc_url( home_url() . '/' . dokan_get_option( 'custom_store_url', 'dokan_general', 'store' ) ); ?>/<strong id="url-alart"></strong></small>
    </p>

    <p class="form-row form-group form-row-wide">
        <label for="shop-phone"><?php esc_html_e( 'Phone Number', 'printshop' ); ?><span class="required">*</span></label>
        <input type="text" class="input-text form-control" name="phone" id="shop-phone" value="<?php if ( ! empty( $postdata['phone'] ) ) echo esc_attr($postdata['phone']); ?>" required="required" />
    </p>

    <?php
    $show_terms_condition = dokan_get_option( 'enable_tc_on_reg', 'dokan_general' );
    $terms_condition_url  = dokan_get_terms_condition_url();

    if ( 'on' === $show_terms_condition && $terms_condition_url ) { ?>
        <p class="form-row form-group form-row-wide">
            <input class="tc_check_box" type="checkbox" id="tc_agree" name="tc_agree" required="required">
            <label style="display: inline" for="tc_agree"><?php echo sprintf( __( 'I have read and agree to the <a target="_blank" href="%s">Terms &amp; Conditions</a>.', 'printshop' ), esc_url( $terms_condition_url ) ); ?></label>
        </p>
    <?php }

    do_action( 'dokan_seller_registration_field_after' );
    ?>
</div>

<?php do_action( 'dokan_reg_form_field' ); ?>

<p class="form-row form-group user-role">
    <label class="radio">
        <input type="radio" name="role" value="customer"<?php checked( $role, 'customer' ); ?>>
        <?php esc_html_e( 'I am a customer', 'printshop' ); ?>
    </label>

    <label class="radio">
        <input type="radio" name="role" value="seller"<?php checked( $role, 'seller' ); ?>>
        <?php esc_html_e( 'I am a vendor', 'printshop' ); ?>
    </label>
    <?php do_action( 'dokan_registration_form_role', $role ); ?>
</p>
