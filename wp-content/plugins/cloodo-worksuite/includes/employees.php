<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
} 

require CLWS_PLUGIN_DIR . 'includes/clws-api.php';

// get token
$token = get_option('cloodo_token');

// check before sync
if ($token && $token != null && admin_url('users.php')) {
    // get setting_clws_options
    $setting_clws_options = get_option( 'setting_clws_options' );
            
    $employees = Clws_API::call_api_get(CLWS_API_GET_EMPLOYEES_URL);
    $department = Clws_API::call_api_get(CLWS_API_GET_DEPARTMENT_URL);
    $designation = Clws_API::call_api_get(CLWS_API_GET_DESIGNATION_URL);
    $company = Clws_API::call_api_get(CLWS_API_GET_COMPANY_URL);
    $company_body = Clws_API::swap_json(wp_remote_retrieve_body($company));
    $company_date_format = $company_body['data']['date_format'];

    if (is_wp_error($employees)) {
        $_SESSION['error'] = sanitize_text_field($employees->get_error_message());
    } elseif ($employees['response']['code'] != 200) {                   
        $_SESSION['error'] = 'Employees sync error!';                    
    } else {
        // create default department - example: Marketing
        if (!is_wp_error($department) && $department['response']['code'] == 200) {
            $department_body = Clws_API::swap_json($department['body']);

            $departNameArr = [];
            foreach ($department_body['data'] as $value) {
                $key = $value['team_name'];
                $departNameArr[] = $key;
            }

            if (!in_array("Marketing", $departNameArr)) {
                $data_depart = [
                    'team_name' => "Marketing"
                ];
                Clws_API::call_api_post(CLWS_API_POST_DEPARTMENT_URL,$data_depart);
            }
        } 

        // default sync when not settings with role: author
        if ($setting_clws_options == null) {
            // get wp-user data by user role
            $args_user = array(
                'role' => 'author',
                'order' => 'ASC'
            );
            $users = get_users( $args_user );
            $role_users = $args_user['role'];

            // create designation by user role - example: author
            if (!is_wp_error($designation) && $designation['response']['code'] == 200) {
                $designation_body = Clws_API::swap_json($designation['body']);

                $designationNameArr = [];
                foreach ($designation_body['data'] as $value) {
                    $key = $value['name'];
                    $designationNameArr[] = $key;
                }

                if (!in_array($role_users, $designationNameArr)) {
                    $data_designation = [
                        'name' => $role_users
                    ];
                    Clws_API::call_api_post(CLWS_API_POST_DESIGNATION_URL,$data_designation);
                }
            }

            // synchronous processing of wp users into employees
            $arr = Clws_API::swap_json($employees['body']);

            $employeeArr = [];
            foreach( $arr['data'] as $value) {
                $key = $value['email'];
                $employeeArr[] = $key;
            }

            foreach ($department_body['data'] as $row_depart) {
                if ($row_depart['team_name'] == "Marketing") {
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

                    Clws_API::call_api_post(CLWS_API_POST_EMPLOYEES_URL,$data_employees);
                }
            }
        }

        // sync when have save settings
        if (isset($setting_clws_options['setting_clws_field_sync_employees_checkbox']) 
            && $setting_clws_options['setting_clws_field_sync_employees_checkbox'] == 1 
            && isset($setting_clws_options['setting_clws_field_sync_employees_role_user'])) {

            $role_user_option_to_sync = $setting_clws_options['setting_clws_field_sync_employees_role_user'];

            // get wp-user data by user role
            $args_user = array(
                'role' => $role_user_option_to_sync,
                'order' => 'ASC'
            );
            $users = get_users( $args_user );
            $role_users = $args_user['role'];

            // create designation by user role - example: author
            if (!is_wp_error($designation) && $designation['response']['code'] == 200) {
                $designation_body = Clws_API::swap_json($designation['body']);

                $designationNameArr = [];
                foreach ($designation_body['data'] as $value) {
                    $key = $value['name'];
                    $designationNameArr[] = $key;
                }

                if (!in_array($role_users, $designationNameArr)) {
                    $data_designation = [
                        'name' => $role_users
                    ];
                    Clws_API::call_api_post(CLWS_API_POST_DESIGNATION_URL,$data_designation);
                }
            }

            // synchronous processing of wp users into employees
            $arr = Clws_API::swap_json($employees['body']);

            $employeeArr = [];
            foreach( $arr['data'] as $value) {
                $key = $value['email'];
                $employeeArr[] = $key;
            }

            foreach ($department_body['data'] as $row_depart) {
                if ($row_depart['team_name'] == "Marketing") {
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

                    Clws_API::call_api_post(CLWS_API_POST_EMPLOYEES_URL,$data_employees);
                }
            }
        }
    }
}
