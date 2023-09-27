<?php
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
} 
require CLWS_PLUGIN_DIR . 'includes/clws-api.php';
// get token
$token = get_option('cloodo_token');

// get setting_clws_options
$setting_clws_options = get_option( 'setting_clws_options' );

// check before sync
if ($token 
    && $token != null 
    && class_exists( 'WooCommerce' )) {

    if ($setting_clws_options == null || 
        (isset($setting_clws_options['setting_clws_field_sync_clients_checkbox']) 
        && $setting_clws_options['setting_clws_field_sync_clients_checkbox'] == 1) ){

        
        $res = Clws_API::call_api_get(CLWS_API_GET_CLIENT_URL);
        if (is_wp_error($res)) {
            $_SESSION['error'] = sanitize_text_field($res->get_error_message());
        } elseif ($res['response']['code'] != 200) {                   
            $_SESSION['error'] = 'Client sync error!';                    
        } else {
            $arr = Clws_API::swap_json($res['body']);
            $totalSum = $arr['meta']['paging']['total'];
            $res_all = Clws_API::call_api_get(CLWS_API_GET_ALL_CLIENT_URL.$totalSum);
            $all_data = Clws_API::swap_json($res_all['body']);
            $orders = wc_get_orders([
                'limit'=> -1
            ]);
            $customArr = [];
            foreach( $all_data['data'] as $value) {
                $key = $value['email'];
                $customArr[] = $key;
            }
            foreach ($orders as $key => $clwsvalue) {
                $data = ($clwsvalue->get_data());
                if (!in_array($data['billing']['email'], $customArr)) {
                    $randPass = substr(md5(rand(0, 99999)), 0, 6);
                    $data = [
                        'name' => sanitize_text_field($data['billing']['first_name'].' '.$data['billing']['last_name']),
                        'email' => sanitize_email($data['billing']['email']),
                        'password' => sanitize_text_field($randPass),
                        'mobile' => sanitize_text_field($data['billing']['phone']),
                        'client_detail' => [
                            'company_name'=> sanitize_text_field($data['billing']['company']),
                            'address'=> sanitize_text_field($data['billing']['address_1']),
                            'city'=> sanitize_text_field($data['billing']['city']),
                            'postal_code'=> sanitize_text_field($data['billing']['postcode']),
                            'shipping_address'=> sanitize_text_field($data['billing']['address_2']),
                        ] 
                    ];
                    Clws_API::call_api_post(CLWS_API_POST_CLIENT_URL,$data);
                }
            }
        }
    }
}

