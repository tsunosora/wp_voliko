<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// check if the current user has the ability to manage options in WordPress
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}

// add website style in wp
wp_enqueue_style('bootstrap.css', plugins_url('../admin/css/bootstrap.min.css', __FILE__ ));
wp_enqueue_style('fontawesome.css', plugins_url('../admin/css/fontawesome.css', __FILE__ ));
wp_enqueue_script('boostrap.js', plugins_url('../admin/js/bootstrap.min.js',__FILE__));

wp_enqueue_style('core.css',plugins_url('../admin/css/core.css', __FILE__ ));
wp_enqueue_style('theme-default.css',plugins_url('../admin/css/theme-default.css', __FILE__ ));
wp_enqueue_style('style.css',plugins_url('../admin/css/style.css',__FILE__));

wp_enqueue_style('tabler-icons.css', plugins_url('../admin/css/tabler-icons.css', __FILE__ ));
wp_enqueue_style('animate.css', plugins_url('../admin/css/animate.css', __FILE__ ));

// registers a settings error to be displayed to the user
if ( isset( $_GET['settings-updated'] ) ) {
    add_settings_error( 
        'setting_crm4_messages', //Slug title of the setting to which this error applies
        'setting_crm4_message', //Slug-name to identify the error - id(html)
        __( 'Settings Saved', 'setting_crm4' ), //message text to display to the user - <div> <p>
        'updated' //message type - include 'error', 'success', 'warning', 'info'
    );
}
?>

<!-- Welcome setting -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card-content">
        <div class="card-body mb-2 pt-5 text-primary" style="text-align: left; font-size: 40px; ">
            Setting service for CRM
        </div>
        <div class="card-body" style="font-size: 20px;">
            Please choose service setting
        </div>
    </div>
</div>
<!--/ Welcome setting -->

<?php 
// show error/update messages
settings_errors( 'setting_crm4_messages' );
?>

<!-- show font-end setting -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-5">
        <!-- Group setting menu -->
        <div class="card-header py-2">
            <ul class="nav nav-pills card-header-pills" role="tablist" style="font-weight:600">
                <li class="nav-item">
                    <button
                        type="button"
                        class="nav-link nav-link-primary active"
                        role="tab"
                        data-bs-toggle="tab"
                        data-bs-target="#navs-pills-tab-sync-setting"
                        aria-controls="navs-pills-tab-sync-setting"
                        aria-selected="true"
                    >
                        Sync settings
                    </button>
                </li>
                <li class="nav-item">
                    <button
                        type="button"
                        class="nav-link nav-link-primary"
                        role="tab"
                        data-bs-toggle="tab"
                        data-bs-target="#navs-pills-tab-other-setting"
                        aria-controls="navs-pills-tab-other-setting"
                        aria-selected="false"
                    >
                        Other settings
                    </button>
                </li>
            </ul>
        </div>
        <!--/ Group setting menu -->

        <!-- Sync settings -->
        <div class="tab-content p-0">
            <div class="tab-pane fade show active" id="navs-pills-tab-sync-setting" role="tabpanel">
                <div class="card mt-4" style="max-width: 100%;">
                    

                    <!-- setting execution form -->
                    <form action="options.php" method="POST" id="sync_setting_form">
                        <?php
                        settings_fields( 'setting_crm4' );
                        // do_settings_sections( 'setting_crm4_section_integrated_synchronized_developers' );
                        do_settings_fields( 
                            'setting_crm4', 
                            'setting_crm4_section_integrated_synchronized_developers' 
                        );
                        submit_button( 'Save Settings' );
                        ?>
                    </form>
                    <!--/ setting execution form -->
                </div>
            </div>         
            <!--/ Sync settings -->	

            <!-- Other settings -->
            <div class="tab-content p-0">
                <div class="tab-pane fade show" id="navs-pills-tab-other-setting" role="tabpanel">
                    <label>Coming soon</label>
                </div>	
            </div>
            <!--/ Other settings -->
        </div>
    </div>
</div>
<!--/ show font-end setting -->