<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    wp_enqueue_style('core.css',plugins_url('../admin/css/core.css', __FILE__ ));
    wp_enqueue_style('theme-default.css',plugins_url('../admin/css/theme-default.css', __FILE__ ));
    wp_enqueue_style('style.css',plugins_url('../admin/css/style.css',__FILE__));

    $token = get_option('cloodo_token');

    $user_id = get_current_user_id();
    $user_data = get_userdata($user_id);
    $user_login = sanitize_text_field($user_data->user_login);

    if ($token && $token != null) {

        $author_token = array (
            'headers'=> ['Authorization' => 'Bearer'.' '. $token]
        );
        
        // api tutorial
        $tutorials = wp_remote_get(
            'https://strapi4.cloodo.com/api/posts?filters[tags][alias]=workchat&filters[post_type][alias]=tutorial&populate=tags&populate=user_profile.avatar_new&populate=post_type&pagination[pageSize]=100&sort=createdAt:DESC'
        );

        // api with total
        $api_endpoints = array(
            'lead_v1' => 'https://erp.cloodo.com/api/v1/lead',
            'agent_v2' => 'https://erp.cloodo.com/api/v2/ticket-agents?limit',
            'client_v1' => 'https://erp.cloodo.com/api/v1/client',
            'proposal_v2' => 'https://erp.cloodo.com/api/v2/proposals',
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
        
        $leads_total = isset($data_subset['lead']) ? $data_subset['lead'] : 0;
        $clients_total = isset($data_subset['client']) ? $data_subset['client'] : 0;
        $agent_total = isset($data_totals['agent']) ? $data_totals['agent'] : 0;
        $proposals_total = isset($data_totals['proposal']) ? $data_totals['proposal'] : 0;
        $employees_total = isset($data_totals['employee']) ? $data_totals['employee'] : 0;
        ?>

        <!-- Dashboard -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row" style="border-radius: 0px;">
                <!-- Congratulation -->
                <div class="card-header px-4" style="background-color: white;">
                    <div class="card-body mb-3 text-primary pt-5" style="text-align: left; font-size: 40px; ">Welcome to CRM System</div>
                    <div class="card-body mb-4" style="font-size: 20px;">
                        Congratulations, you have successfully connected to CRM System as 
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
                                <!-- Total Leads -->
                                <div class="col-12 col-sm-4">
                                    <h4 class="my-2 pt-1">Leads <?php echo esc_html($leads_total) ?></h4>
                                    <div class="progress w-75 mb-4" style="height: 4px">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <a href="<?php menu_page_url("crm-4-cf7_leads") ?>" class="btn btn-danger">View leads</a>
                                </div>
                                <!-- /Total Leads -->

                                <!-- Total Agent-->
                                <div class="col-12 col-sm-4">
                                    <h4 class="my-2 pt-1">Agent <?php echo esc_html($agent_total) ?></h4>
                                    <div class="progress w-75 mb-4" style="height: 4px">
                                        <div class="progress-bar" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <a href="<?php menu_page_url("crm-4-cf7_agents") ?>" class="btn btn-primary">
                                        Open agent
                                    </a>
                                </div>
                                <!-- /Total Agent-->

                                <!-- Proposal -->
                                <div class="col-12 col-sm-4">
                                    <h4 class="my-2 pt-1">Proposal <?php echo esc_html($proposals_total) ?></h4>
                                    <div class="progress w-75 mb-4" style="height: 4px">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <a href="<?php menu_page_url("crm-4-cf7_proposal") ?>" class="btn btn-success">
                                        Go to proposal
                                    </a>
                                </div>
                                <!-- /Proposal-->
                            </div>

                            <div class="row gap-4 gap-sm-0 mt-5 mb-2">
                                <!-- Clients -->
                                <div class="col-12 col-sm-4">
                                    <h4 class="my-2 pt-1">Clients <?php echo esc_html($clients_total); ?></h4>
                                    <div class="progress w-75 mb-4" style="height: 4px">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <a href="<?php menu_page_url("crm-4-cf7_clients") ?>" class="btn btn-warning">View Clients</a>
                                </div>
                                <!-- /Clients -->

                                <!-- Employees -->
                                <div class="col-12 col-sm-4">
                                    <h4 class="my-2 pt-1">Employees <?php echo esc_html($employees_total) ?></h4>
                                    <div class="progress w-75 mb-4" style="height: 4px">
                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <a href="<?php menu_page_url("crm-4-cf7_employees") ?>" class="btn btn-secondary">Open Employees </a>
                                </div>
                                <!-- /Employees  -->
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
    } else {
        ?>
        <!-- show register form -->
        <div class="container mt-5">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center h-px-500 ">
                    <form 
                        class="w-px-600 border rounded p-3 p-md-5 border border-primary alert  alert-dismissible " 
                        method="POST"
                    >
                        <div></div>
                        <img class="" src="<?php echo esc_url(plugins_url('../admin/images/icons8-user-groups-50.png', __FILE__)) ?>" alt="">
                        <div class="card bg-primary text-white mb-3">
                            <h4 class="title mt-3 card-title text-white">Hello: <b><?php echo esc_html($user_login) ?></b>, welcome to CRM System</h4>
                        </div>

                        <div class="mb-3" role="">
                            <p class="mb-0" style="text-align: justify; text-align-last: justify;text-justify: inter-word;">
                                Hello! Would you like to experience our CRM system to improve operational efficiency? To get started, you need to sign up for an account. Please click the "Register" button below to begin the <b>Cloodo</b> account registration process. Once you have completed the registration, you will have access to many useful features on our app. Thank you for your interest and registration to use the CRM system!
                            </p>
                        </div>
                        <div class="col-12 d-flex justify-content-between mt-4">
                            <div></div>
                            <button type="submit" class="btn rounded-pill btn-primary waves-effect waves-light" name="cloodo_register">Register</button>
                        </div>
                        <?php cloodo_register_form_nonce(); ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }