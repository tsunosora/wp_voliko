<?php
/**
 * @since 2.2.2 Insert action before social settings form
 */
do_action( 'dokan_profile_settings_before_form', $current_user, $profile_info ); ?>

<form method="post" id="profile-form"  action="" class="dokan-form-horizontal"><?php ///settings-form ?>

    <?php wp_nonce_field( 'dokan_profile_settings_nonce' ); ?>

    <?php foreach( $social_fields as $key => $field ) { ?>
        <div class="row form-group">
            <div class="col-md-3">
                <label><?php echo $field['title']; ?></label>
            </div>


            <div class="col-md-9">
                <div class="dokan-input-group">
                    <span class="dokan-input-group-addon"><i class="fa fa-<?php echo isset( $field['icon'] ) ? $field['icon'] : ''; ?>"></i></span>
                    <input id="settings[social][<?php echo $key; ?>]" value="<?php echo isset( $profile_info['social'][$key] ) ? esc_url( $profile_info['social'][$key] ) : ''; ?>" name="settings[social][<?php echo $key; ?>]" class="dokan-form-control" placeholder="http://" type="url">
                </div>
            </div>
        </div>
    <?php } ?>

    <?php
    /**
     * @since 2.2.2 Insert action on bottom social settings form
     */
    do_action( 'dokan_profile_settings_form_bottom', $current_user, $profile_info ); ?>

    <div class="row form-group dokan-group-button">
        <div class="col-md-3">
            &nbsp;
        </div>
        <div class="col-md-9">
            <div class="dokan-w4 ajax_prev dokan-text-left">
                <input type="submit" name="dokan_update_profile_settings" class="dokan-btn dokan-btn-danger dokan-btn-theme" value="<?php esc_attr_e( 'Update Settings', 'printshop' ); ?>">
            </div>
        </div>

    </div>

</form>

<?php
/**
 * @since 2.2.2 Insert action after social settings form
 */
do_action( 'dokan_profile_settings_after_form', $current_user, $profile_info ); ?>
<!--settings updated content end-->
