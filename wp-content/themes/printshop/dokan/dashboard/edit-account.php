<?php
/**
 *  Dokan Dashboard Template
 *
 *  Dokan Main Dahsboard template for Front-end
 *
 *  @since 2.5
 *
 *  @package dokan
 */

$user = get_user_by( 'id', get_current_user_id() );
?>
<div class="row">
    <div class="col-md-2 col-dokan-menu">
        <?php

        /**
         *  dokan_dashboard_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
        ?>
    </div>

    <div class="col-md-10">

    <div class="dokan-dashboard-content">

        <?php
            /**
             *  dokan_dashboard_content_before hook
             *
             *  @hooked show_seller_dashboard_notice
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_inside_before' );
        ?>

            <article class="dashboard-content-area woocommerce edit-account-wrap">

                <?php wc_print_notices();?>

                <h1 class="entry-title"><?php _e( 'Edit Account Details', 'printshop' ); ?></h1>

                <form class="edit-account" action="" method="post">

                    <?php do_action( 'woocommerce_edit_account_form_start' ); ?>
                    <div class="row form-group">
                        <div class="col-md-6">
                            <label><?php _e( 'First name', 'printshop' ); ?> <span class="required">*</span></label>
                            <input type="text" class="input-text dokan-form-control" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" />
                        </div>

                        <div class="col-md-6">
                            <label for="account_last_name"><?php _e( 'Last name', 'printshop' ); ?> <span class="required">*</span></label>
                            <input type="text" class="input-text dokan-form-control" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" />
                        </div>
                    </div>

                    <div class="row form-group">
                        <div class="col-md-12">
                            <label for="account_email"><?php _e( 'Email address', 'printshop' ); ?> <span class="required">*</span></label>
                            <input type="email" class="input-text dokan-form-control" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
                        </div>
                    </div>



                    <fieldset>
                        <legend><?php _e( 'Password Change', 'printshop' ); ?></legend>
                        <div class="row form-group">
                            <div class="col-md-12">
                                <label for="password_current"><?php _e( 'Current Password (leave blank to leave unchanged)', 'printshop' ); ?></label>
                                <input type="password" class="input-text dokan-form-control" name="password_current" id="password_current" />
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-12">
                                <label for="password_1"><?php _e( 'New Password (leave blank to leave unchanged)', 'printshop' ); ?></label>
                                <input type="password" class="input-text dokan-form-control" name="password_1" id="password_1" />
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-12">
                                <label for="password_2"><?php _e( 'Confirm New Password', 'printshop' ); ?></label>
                                <input type="password" class="input-text dokan-form-control" name="password_2" id="password_2" />
                            </div>
                        </div>

                    </fieldset>

                    <div class="clear"></div>

                    <?php do_action( 'woocommerce_edit_account_form' ); ?>

                    <p>
                        <?php wp_nonce_field( 'dokan_save_account_details' ); ?>
                        <input type="submit" class="dokan-btn dokan-btn-danger dokan-btn-theme" name="dokan_save_account_details" value="<?php esc_attr_e( 'Save changes', 'printshop' ); ?>" />
                        <input type="hidden" name="action" value="dokan_save_account_details" />
                    </p>

                    <?php do_action( 'woocommerce_edit_account_form_end' ); ?>

                </form>

            </article><!-- .dashboard-content-area -->

         <?php

            /**
             *  dokan_dashboard_content_inside_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_inside_after' );
        ?>

    </div>
    </div><!-- .dokan-dashboard-content -->

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
    ?>

</div><!-- .dokan-dashboard-wrap -->
