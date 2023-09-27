<?php
    if ( !function_exists( 'add_action' ) ) {
        echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
        exit;
    }   

    wp_enqueue_style('bootstrap.css', plugins_url('../admin/css/bootstrap.min.css', __FILE__ ));
    wp_enqueue_style('fontawesome.css', plugins_url('../admin/css/fontawesome.min.css', __FILE__ ));
    wp_enqueue_script('boostrap.js', plugins_url('../admin/js/bootstrap.min.js',__FILE__));
    wp_enqueue_style('core.css', plugins_url('../admin/css/core.css', __FILE__ ));
    wp_enqueue_style('theme-default.css', plugins_url('../admin/css/theme-default.css', __FILE__ ));
    wp_enqueue_style('tabler-icons.css', plugins_url('../admin/css/tabler-icons.css', __FILE__ ));
    wp_enqueue_style('animate.css', plugins_url('../admin/css/animate.css', __FILE__ ));
    wp_add_inline_style('overflow_auto.css', '#wpcontent{overflow: auto;}');
    wp_enqueue_style('style.css', plugins_url('../admin/css/style.css',__FILE__));

    $message ="";
    $class="";
    if (isset($_SESSION['success'])) {
        $message = sanitize_text_field($_SESSION['success']);
        unset($_SESSION['success']);
        $class ="success";
    } elseif (isset($_SESSION['error'])) {
        $message = sanitize_text_field($_SESSION['error']);
        unset($_SESSION['error']);
        $class ="danger";
    }
    if ($message) {
        ?>
        <div class="mt-3 alert alert-<?php echo esc_attr($class)?>"> <?php echo esc_attr($message) ?></div>
    <?php } 

    $author_token = array (
        'headers'=> ['Authorization' => 'Bearer '.get_option('cloodo_token')]
    );
    
    // api tutorial of workchat
    $tutorials = wp_remote_get(
        'https://strapi4.cloodo.com/api/posts?filters[$or][0][tags][alias]=workchat&filters[$or][1][tags][alias]=worksuite&filters[post_type][alias]=tutorial&populate=tags&populate=user_profile.avatar_new&populate=post_type&pagination[pageSize]=100&sort=createdAt:DESC'
    );

    $token = get_option('cloodo_token');

    $user_id = get_current_user_id();
    $user_data = get_userdata($user_id);
    $user_login = sanitize_text_field($user_data->user_login);

    if ($token && $token != null) {
        // api data total
        $api_endpoints = array(
            'project_v2' => 'https://erp.cloodo.com/api/v2/projects',
            'lead_v1' => 'https://erp.cloodo.com/api/v1/lead',
            'client_v1' => 'https://erp.cloodo.com/api/v1/client',
            'notice_v1' => 'https://erp.cloodo.com/api/v1/notice?fields=id,heading,description,to',
            'employee_v2' => 'https://erp.cloodo.com/api/v2/employees'
        );
        
        $data_totals = array();
        
        foreach ($api_endpoints as $key => $value) {
            $endpoint_version = substr($key, -3);
            $response = wp_remote_get($value, $author_token);
            $response_body = wp_remote_retrieve_body($response);
            $response_data = json_decode($response_body, true);
        
            if ($response_data && is_array($response_data)) {
                if ($endpoint_version === '_v1') {
                    $data_totals[substr($key, 0, -3)] = isset($response_data['meta']) && isset($response_data['meta']['paging']['total']) ? $response_data['meta']['paging']['total'] : 0;
                } elseif ($endpoint_version === '_v2') {
                    $data_totals[substr($key, 0, -3)] = isset($response_data['meta']) && isset($response_data['meta']['total']) ? $response_data['meta']['total'] : 0;
                }
            }
        }
        
        $projects_total = isset($data_totals['project']) ? $data_totals['project'] : 0;
        $leads_total = isset($data_totals['lead']) ? $data_totals['lead'] : 0;
        $clients_total = isset($data_totals['client']) ? $data_totals['client'] : 0;
        $notices_total = isset($data_totals['notice']) ? $data_totals['notice'] : 0;
        $employees_total = isset($data_totals['employee']) ? $data_totals['employee'] : 0;
        ?>

        <!-- Dashboard -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row" style="border-radius: 0px;">
                <!-- Congratulation -->
                <div class="card-header px-4" style="background-color: white;">
                    <div class="card-body mb-3 text-primary pt-5" style="text-align: left; font-size: 40px; ">Welcome to WorkSuite</div>
                    <div class="card-body mb-4" style="font-size: 20px;">
                        Congratulations, you have successfully connected to WorkSuite as 
                        <span style="font-weight: bold;"><?php echo esc_html(wp_get_current_user()->user_login) ?></span>
                        by email 
                        <span style="font-weight: bold;"><?php echo esc_html(get_option('admin_email')) ?></span>
                        . Your client can start a chat with
                        <span style="font-weight: bold;"> (<?php echo esc_html(get_option('admin_email')) ?>)</span>
                    </div>
                </div>
                <!--/ Congratulation -->

                <div class="card h-100" style="border-radius: 0px;">
                    <div class="card-body">
                        <div class="border rounded p-3 mt-2">
                            <div class="row gap-4 gap-sm-0">
                                <!-- Total Projects-->
                                <div class="col-12 col-sm-4">
                                    <h4 class="my-2 pt-1">Total Projects <?php echo esc_html($projects_total) ?></h4>
                                    <div class="progress w-75 mb-4" style="height: 4px">
                                        <div class="progress-bar" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <a href="<?php menu_page_url("clws_work") ?>" class="btn btn-primary">
                                        Go to work
                                    </a>
                                </div>
                                <!-- /Total Projects-->

                                <!-- Total Leads -->
                                <div class="col-12 col-sm-4">
                                    <h4 class="my-2 pt-1">Total Leads <?php echo esc_html($leads_total) ?></h4>
                                    <div class="progress w-75 mb-4" style="height: 4px">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <a href="<?php menu_page_url("clws_leads") ?>" class="btn btn-danger">Open leads</a>
                                </div>
                                <!-- /Total Leads -->

                                <!-- Total Clients -->
                                <div class="col-12 col-sm-4">
                                    <h4 class="my-2 pt-1">Total Clients <?php echo esc_html($clients_total) ?></h4>
                                    <div class="progress w-75 mb-4" style="height: 4px">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <a href="<?php menu_page_url("clws_clients") ?>" class="btn btn-info">Add clients</a>
                                </div>
                                <!-- /Total Clients -->
                            </div>
                            <div class="row gap-4 gap-sm-0 mt-5 mb-2">
                                <!-- Total Notice -->
                                <div class="col-12 col-sm-4">
                                    <h4 class="my-2 pt-1">Total Notice <?php echo esc_html($notices_total) ?></h4>
                                    <div class="progress w-75 mb-4" style="height: 4px">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <a href="<?php menu_page_url("clws_notice") ?>" class="btn btn-success">
                                        Go to notice
                                    </a>
                                </div>
                                <!-- /Total Notice-->

                                <!-- Messages -->
                                <div class="col-12 col-sm-4">
                                    <h4 class="my-2 pt-1">Chats</h4>
                                    <div class="progress w-75 mb-4" style="height: 4px">
                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <a href="<?php menu_page_url("clws_messages") ?>" class="btn btn-secondary">Open Chats</a>
                                </div>
                                <!-- /Messages -->

                                <!-- Total Employees -->
                                <div class="col-12 col-sm-4">
                                    <h4 class="my-2 pt-1">Total Employees <?php echo esc_html($employees_total); ?></h4>
                                    <div class="progress w-75 mb-4" style="height: 4px">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <a href="<?php menu_page_url("clws_employees") ?>" class="btn btn-warning">Open Employees</a>
                                </div>
                                <!-- /Total Employees -->
                            </div>
                        </div>

                        <!-- Tutorial -->
                        <div class="row mb-5 mt-lg-5">
                            <?php
                            if (is_wp_error($tutorials)) {
                                $_SESSION['error'] = sanitize_text_field($tutorials->get_error_message());
                            } elseif ($tutorials['response']['code'] != 200) {                   
                                $_SESSION['error'] = 'Client sync error!';                    
                            } else {
                                $tutorials_data = json_decode(wp_remote_retrieve_body($tutorials), true)['data'];
                                foreach ($tutorials_data as $tutorial_data) {
                                    $title = $tutorial_data['attributes']['title'];
                                    $short_intro = $tutorial_data['attributes']['short_intro'];
                                    $alias = $tutorial_data['attributes']['alias'];
                                    ?>
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        <?php echo esc_html($title); ?>
                                                    </h5>
                                                    <p class="card-text over_text">
                                                        <?php echo esc_html($short_intro); ?>
                                                    </p>
                                                    <a target="_blank" href="<?php echo esc_url("https://cloodo.com/".$alias); ?>" class="btn btn-outline-primary">
                                                        View detail
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                }
                            } 
                            ?>
                        <!--/ Tutorial -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Dashboard -->
    <?php
    } else {  ?>
        <!-- show register form -->
        <div class="container mt-5">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center h-px-500 ">
                    <form class="w-px-600 border rounded p-3 p-md-5 border border-warning alert  alert-dismissible " method="POST">
                        <div></div>
                        <!-- logo worksuite -->
                        <div style="display: inline-block; padding: 10px;">
                            <span style="font-family: Arial, sans-serif; font-size: 36px; font-weight: bold; color: #000000;">Work<span style="color: #ff8c00;">Suite</span></span>
                        </div>
                        <!-- /logo worksuite -->
                        
                        <div class="card bg-warning text-white mb-3">
                            <h4 class="title mt-3 card-title text-white">
                                Hello: 
                                <b><?php echo esc_html($user_login) ?></b>
                                , Welcome to WorkSuite
                            </h4>
                        </div>

                        <div class="mb-3" role="">
                            <p class="mb-0" style="text-align: justify; text-justify: inter-word;">
                                Unlock the full potential of project and task management with Worksuite, developed by the talented team at Cloodo. Click on "Register" to discover this comprehensive solution that allows you to create tasks, schedule work, manage budgets, track progress, and communicate with colleagues all in one platform. Don't miss out on this opportunity to enhance your work productivity - sign up now!
                            </p>
                        </div>
                        <div class="col-12 d-flex justify-content-between">
                            <div></div>
                            <button type="submit" class="btn rounded-pill btn-warning waves-effect waves-light mt-3" name="cloodo_register">
                                Register
                            </button>
                        </div>
                        <?php cloodo_register_form_nonce(); ?>
                    </form>
                </div>
            </div>
        </div>
        <!--/ show register form -->
    <?php } ?>






     


    