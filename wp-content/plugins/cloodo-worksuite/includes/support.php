<?php
$token = get_option('cloodo_token');

// api tutorial
$support_service_cloodo = wp_remote_get(
    'https://data2.cloodo.com/api/services?filters[service_agency][$containsi]=%22Cloodo.com%22&populate=*&sort=createdAt:DESC&pagination[pageSize]=100'
);

if ($token && $token != null) {
    wp_enqueue_style('bootstrap.css', plugins_url('../admin/css/bootstrap.min.css', __FILE__ ));
    wp_enqueue_style('fontawesome.css', plugins_url('../admin/css/fontawesome.min.css', __FILE__ ));
    wp_enqueue_script('boostrap.js', plugins_url('../admin/js/bootstrap.min.js',__FILE__));
    wp_enqueue_style('core.css', plugins_url('../admin/css/core.css', __FILE__ ));
    wp_enqueue_style('theme-default.css', plugins_url('../admin/css/theme-default.css', __FILE__ ));
    wp_enqueue_style('tabler-icons.css', plugins_url('../admin/css/tabler-icons.css', __FILE__ ));
    wp_enqueue_style('animate.css', plugins_url('../admin/css/animate.css', __FILE__ ));
    wp_add_inline_style('overflow_auto.css', '#wpcontent{overflow: auto;}');
    wp_enqueue_style('style.css', plugins_url('../admin/css/style.css',__FILE__));
    
    $support_service_cloodo_data = json_decode(wp_remote_retrieve_body($support_service_cloodo), true)['data'];
    ?>

    <!-- Welcome support -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card-content">
            <div class="card-body mb-2 pt-5 text-primary" style="text-align: left; font-size: 40px; ">Support service for WorkSuite</div>
            <div class="card-body" style="font-size: 20px;">Please choose service support</div>
        </div>
    </div>
    <!--/ Welcome support -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row" style="border-radius: 0px;">
            <div class="row mb-5">
                <?php
                foreach ($support_service_cloodo_data as $service) {
                    $image_url = $service['attributes']['image']['data']['0']['attributes']['url'];
                    $title = $service['attributes']['title'];
                    $description = $service['attributes']['description'];
                    $alias = $service['attributes']['alias']; ?>
                        <!-- Support -->
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card h-100">
                                <img class="card-img-top mt-3" src="<?php echo esc_url($image_url); ?>" alt="card image cap" />
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?php echo esc_html($title); ?>
                                    </h5>
                                    <div class="card-text mb-3 over_text">
                                        <?php echo esc_html(wpautop($description)); ?>
                                    </div>
                                    <a href="<?php echo esc_url("https://cloodo.com/service/".$alias) ?>" class="btn btn-outline-primary" target="_blank">Get support</a>
                                </div>
                            </div>
                        </div>
                        <!-- Support -->
                    <?php 
                } ?>
            </div>
        </div>
    </div>
<?php }
