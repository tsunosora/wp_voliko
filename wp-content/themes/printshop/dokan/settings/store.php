<?php
/**
 * Dokan Settings Main Template
 *
 * @since 2.4
 *
 * @package dokan
 */

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

    <div class="dokan-dashboard-content dokan-settings-content">
        <?php

            /**
             *  dokan_settings_content_inside_before hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_settings_content_inside_before' );
        ?>
        <article class="dokan-settings-area">

            <?php
                /**
                 * dokan_review_content_area_header hook
                 *
                 * @hooked dokan_settings_content_area_header
                 *
                 * @since 2.4
                 */
                do_action( 'dokan_settings_content_area_header' );


                /**
                 * dokan_settings_content hook
                 *
                 * @hooked render_settings_content_hook
                 */
                do_action( 'dokan_settings_content' );
            ?>

            <!--settings updated content ends-->
        </article>
    </div><!-- .dokan-dashboard-content -->
</div><!-- .dokan-dashboard-wrap -->
