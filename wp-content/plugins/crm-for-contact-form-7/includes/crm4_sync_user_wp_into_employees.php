<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
// get token
$token = get_option('cloodo_token');

// get ccf7_setting_crm4_options
$ccf7_setting_crm4_options = get_option( 'ccf7_setting_crm4_options' );

// convert to array visible on wp
function ccf7_swap_json($json) {
    return json_decode($json, true);
}

// call api with get method
function ccf7_call_api_get($url) {
    $arrs = [
        'method' => 'GET',
        'timeout' => 10,
        'redirection' => 5,
        'blocking' => true,
        'cookie' => [],
        'headers' => [
            'X-requested-Width'=> 'XMLHttpRequest',
            'Authorization'=> 'Bearer '.sanitize_text_field(get_option('cloodo_token'))
        ],
        'body' => [
        ]
    ];
    $res = wp_remote_request($url, $arrs);
    return $res;
}

// call api with post method
function ccf7_call_api_post($url, $data) {
    $arrs = [
        'method' => 'POST',
        'timeout' => 10,
        'redirection' => 10,
        'blocking' => true,
        'cookie' => [],
        'headers' => [
            'X-requested-Width'=>'XMLHttpRequest',
            'Authorization'=>'Bearer '.sanitize_text_field(get_option('cloodo_token'))
        ],
        'body' => $data
    ];
    $res = wp_remote_request($url, $arrs);
    return $res;
}

// check before sync
if ($token && $token != null && admin_url('users.php')) {
    $employees = ccf7_call_api_get(CCF7_API_GET_EMPLOYEES_URL);
    $department = ccf7_call_api_get(CCF7_API_GET_DEPARTMENT_URL);
    $designation = ccf7_call_api_get(CCF7_API_GET_DESIGNATION_URL);
    $company = ccf7_call_api_get(CCF7_API_GET_COMPANY_URL);
    $company_body = ccf7_swap_json(wp_remote_retrieve_body($company));
    $company_date_format = $company_body['data']['date_format'];

    if (is_wp_error($employees)) {
        $_SESSION['error'] = sanitize_text_field($employees->get_error_message());
    } elseif ($employees['response']['code'] != 200) {                   
        $_SESSION['error'] = 'Employees sync error!';                    
    } else {
        // create default department - example: Marketing
        if (!is_wp_error($department) && $department['response']['code'] == 200) {
            $department_body = ccf7_swap_json($department['body']);

            $departNameArr = [];
            foreach ($department_body['data'] as $value) {
                $key = $value['name'];
                $departNameArr[] = $key;
            }

            if (!in_array("Marketing", $departNameArr)) {
                $data_depart = [
                    'name' => "Marketing"
                ];
                ccf7_call_api_post(CCF7_API_POST_DEPARTMENT_URL,$data_depart);
            }
        } 

        // default sync when not settings with role: author
        if ($ccf7_setting_crm4_options == null) {
            // get wp-user data by user role
            $args_user = array(
                'role' => 'author',
                'order' => 'ASC'
            );
            $users = get_users( $args_user );
            $role_users = $args_user['role'];

            // create designation by user role - example: author
            if (!is_wp_error($designation) && $designation['response']['code'] == 200) {
                $designation_body = ccf7_swap_json($designation['body']);

                $designationNameArr = [];
                foreach ($designation_body['data'] as $value) {
                    $key = $value['name'];
                    $designationNameArr[] = $key;
                }

                if (!in_array($role_users, $designationNameArr)) {
                    $data_designation = [
                        'name' => $role_users
                    ];
                    ccf7_call_api_post(CCF7_API_POST_DESIGNATION_URL,$data_designation);
                }
            }

            // synchronous processing of wp users into employees
            $arr = ccf7_swap_json($employees['body']);

            $employeeArr = [];
            foreach( $arr['data'] as $value) {
                $key = $value['email'];
                $employeeArr[] = $key;
            }

            foreach ($department_body['data'] as $row_depart) {
                if ($row_depart['name'] == "Marketing") {
                    $marketing_depart_id = $row_depart['id'];
                    break;
                }
            }

            foreach ($designation_body['data'] as $row_designation) {
                if ($row_designation['name'] == $role_users) {
                    $designation_id = $row_designation['id'];
                    break;
                }
            }

            $designation_id = $row_designation['id'];

            foreach ($users as $user) {
                $random_number = '';
                for ($i = 0; $i < 15; $i++) {
                    $random_number .= rand(0, 9);
                }

                if (!in_array($user->user_email, $employeeArr)) {
                    $data_employees = [
                        'name' => sanitize_text_field($user->user_nicename),
                        'email' => sanitize_email($user->user_email),
                        'password' => sanitize_text_field($user->user_pass),
                        'email_notifications' => true,
                        'login' => 'enable',
                        'gender' => 'others',
                        'employee_id' => sanitize_text_field('emp-'.$random_number),
                        'joining_date' => date($company_date_format, strtotime($user->user_registered)),
                        'department_id' => sanitize_text_field($marketing_depart_id),
                        'designation_id' => sanitize_text_field($designation_id)
                    ];

                    ccf7_call_api_post(CCF7_API_POST_EMPLOYEES_URL,$data_employees);
                }
            }
        }

        // sync when have save settings
        if (isset($ccf7_setting_crm4_options['setting_crm4_field_sync_employees_checkbox']) 
            && $ccf7_setting_crm4_options['setting_crm4_field_sync_employees_checkbox'] == 1 
            && isset($ccf7_setting_crm4_options['setting_crm4_field_sync_employees_role_user'])) {

            $role_user_option_to_sync = $ccf7_setting_crm4_options['setting_crm4_field_sync_employees_role_user'];

            // get wp-user data by user role
            $args_user = array(
                'role' => $role_user_option_to_sync,
                'order' => 'ASC'
            );
            $users = get_users( $args_user );
            $role_users = $args_user['role'];

            // create designation by user role - example: author
            if (!is_wp_error($designation) && $designation['response']['code'] == 200) {
                $designation_body = ccf7_swap_json($designation['body']);

                $designationNameArr = [];
                foreach ($designation_body['data'] as $value) {
                    $key = $value['name'];
                    $designationNameArr[] = $key;
                }

                if (!in_array($role_users, $designationNameArr)) {
                    $data_designation = [
                        'name' => $role_users
                    ];
                    ccf7_call_api_post(CCF7_API_POST_DESIGNATION_URL,$data_designation);
                }
            }

            // synchronous processing of wp users into employees
            $arr = ccf7_swap_json($employees['body']);
    
            $employeeArr = [];
            foreach( $arr['data'] as $value) {
                $key = $value['email'];
                $employeeArr[] = $key;
            }
    
            foreach ($department_body['data'] as $row_depart) {
                if ($row_depart['name'] == "Marketing") {
                    $marketing_depart_id = $row_depart['id'];
                    break;
                }
            }
    
            foreach ($designation_body['data'] as $row_designation) {
                if ($row_designation['name'] == $role_users) {
                    $designation_id = $row_designation['id'];
                    break;
                }
            }
    
            foreach ($users as $user) {
                $random_number = '';
                for ($i = 0; $i < 15; $i++) {
                    $random_number .= rand(0, 9);
                }
    
                if (!in_array($user->user_email, $employeeArr)) {
                    $data_employees = [
                        'name' => sanitize_text_field($user->user_nicename),
                        'email' => sanitize_email($user->user_email),
                        'password' => sanitize_text_field($user->user_pass),
                        'email_notifications' => true,
                        'login' => 'enable',
                        'gender' => 'others',
                        'employee_id' => sanitize_text_field('emp-'.$random_number),
                        'joining_date' => date($company_date_format, strtotime($user->user_registered)),
                        'department_id' => sanitize_text_field($marketing_depart_id),
                        'designation_id' => sanitize_text_field($designation_id)
                    ];
    
                    ccf7_call_api_post(CCF7_API_POST_EMPLOYEES_URL,$data_employees);
                }
            }
        }
    }
}


