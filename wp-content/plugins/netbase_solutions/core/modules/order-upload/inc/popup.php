<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} 
?>
<div id="g-drive-popup-wrap" class="hidden">
    <div id="g-drive-popup" data-loading="0">
        <div class="g-drive-api-key-main" id="g-drive-nokey">
            <p class="key-icon"></p>
            <div class="g-drive-api-key-content">
                <h3><?php _e('API Key not found!', 'netbase-solutions');?></h3>
                <div>
                <?php printf( __('<p>You need to configure the API key in <a href="%1$s" target="_blank">settings page</a>
                first </p>', 'netbase-solutions'), admin_url('admin.php?page=solution-dashboard#/settings'));?>
                </div>
            </div>
            <!-- .g-drive-api-key-content-->
        </div>
        <!-- .g-drive-api-key-main-->
    </div>
    <!--#g-drive-popup-->
</div>
<!--#g-drive-popup-wrap-->