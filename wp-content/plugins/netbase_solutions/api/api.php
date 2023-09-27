<?php
class NBT_Solutions_API {

    public function __construct()
    {
        require_once(PREFIX_NBT_SOL_PATH.'api/data.php');
        add_action('rest_api_init', array($this, 'dashboard_route'));
    }

    // (?P<period>[a-zA-Z])
    public function dashboard_route()
    {
        register_rest_route('solutions/v1', '/best-seller/(?P<period>\S+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'best_seller_callback'),
            'permissions_callback' => array($this, 'permissions'),
            'args' => array(
                'period' => array(
                    'default' => 'month',
                    'validate_callback' => function($param, $request, $key) {
                        if($param === 'week' || $param === 'month' || $param === 'year') {
                            return true;
                        } else {
                            return false;
                        }
                    }
                ),
            ),
        ));
        register_rest_route('solutions/v1', '/recent-income/(?P<period>\S+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'recent_income_callback'),
            'permissions_callback' => array($this, 'permissions'),
            'args' => array(
                'period' => array(
                    'default' => 'month',
                    'validate_callback' => function($param, $request, $key) {
                        if($param === 'week' || $param === 'month' || $param === 'year') {
                            return true;
                        } else {
                            return false;
                        }
                    }
                ),
            ),
        ));        
        register_rest_route('solutions/v1', '/dashboard', array(
            'methods' => 'GET',
            'callback' => array($this, 'dashboard_callback'),
            'permissions_callback' => array($this, 'permissions'),
        ));
        register_rest_route('solutions/v1', '/modules', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_modules'),
                'permissions_callback' => array($this, 'permissions'),
            ),
            array(
                'methods' => 'POST',
                'callback' => array($this, 'set_modules_list'),
                'permissions_callback' => array($this, 'permissions'),
                'args' => array(
                    'activated_modules' => array(
                        'type' => 'array',
                        'required' => false,
                        'sanitize_callback' => array($this, 'sanitize_array')
                    )
                ),
            )
        ));
        register_rest_route('solutions/v1', '/modules/(?P<module>\S+)', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_module_settings'),
                'permissions_callback' => array($this, 'permissions'),
            ),
            array(
                'methods' => 'POST',
                'callback' => array($this, 'update_module_settings_callback'),
                'permissions_callback' => array($this, 'permissions'),
                'args' => array(
                    // 'slug' => array(
                    //     'type' => 'string',
                    //     'required' => false,
                    //     'sanitize_callback' => 'sanitize_text_field'
                    // ),
                    'settings' => array(
                        'type' => 'array',
                        'required' => false,
                        'sanitize_callback' => ''
                    ),
                ),
            )
        ));
        // register_rest_route('solutions/v1', '/module-settings', array(
        //     'methods' => 'POST',
        //     'callback' => array($this, 'update_module_settings_callback'),
        //     'permissions_callback' => array($this, 'permissions'),
        //     'args' => array(
        //         'module' => array(
        //             'type' => 'string',
        //             'required' => false,
        //             'sanitize_callback' => 'sanitize_text_field'
        //         ),
        //         'settings' => array(
        //             'type' => 'array',
        //             'required' => false,
        //             'sanitize_callback' => ''
        //         ),
        //     ),
        // ));
        register_rest_route('solutions/v1', '/settings', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_settings_callback'),
            'permissions_callback' => array($this, 'permissions'),
        ));
        register_rest_route('solutions/v1', '/settings/(?P<module_name>[a-zA-Z0-9-]+)', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_module_settings_callback'),
            'permissions_callback' => array($this, 'permissions'),
        ));
        register_rest_route('solutions/v1', '/comments', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_on_hold_comments'),
                'permissions_callback' => array($this, 'permissions'),
            ),
            array(
                'methods' => 'POST',
                'callback' => array($this, 'update_comment_status'),
                'permissions_callback' => array($this, 'permissions'),
                'args' => array(
                    'id' => array(
                        'type' => 'integer',
                        'required' => false,
                        'sanitize_callback' => 'absint'
                    ),
                    'status' => array(
                        'type' => 'string',
                        'required' => false,
                        'sanitize_callback' => 'sanitize_text_field'
                    )
                ),
            )
        ));
    }

    public function sanitize_array($input) {
        $new_input = array();
        // Loop through the input and sanitize each of the values
        foreach ( $input as $key => $val ) {
            
            $new_input[ $key ] = ( isset( $input[ $key ] ) ) ?
                sanitize_text_field( $val ) :
                '';
        }
        return $new_input;
    }

    public function permissions()
    {
        return current_user_can( 'manage_options' );
    }

    public function best_seller_callback(WP_REST_Request $request)
    {
        $period = $request['period'];
        
        return rest_ensure_response(NBT_Solutions_Data::get_best_seller($period, 5));
       
    }

    public function recent_income_callback(WP_REST_Request $request) {
        $period = $request['period'];
        
        return rest_ensure_response(NBT_Solutions_Data::get_recent_income($period));
        
    }

    public function dashboard_callback(WP_REST_Request $request)
    {
        return rest_ensure_response(NBT_Solutions_Data::dashboard_reports());
    }

    public function get_modules(WP_REST_Request $request) {
        return rest_ensure_response(NBT_Solutions_Data::modules_list());
    }

    public function get_module_settings(WP_REST_Request $request) {
        $module = $request['module'];
        // return rest_ensure_response(get_option($module . '_settings'));

        return rest_ensure_response(NBT_Solutions_Data::get_module_settings($module));
    }

    //FIX THIS
    public function update_module_settings_callback(WP_REST_Request $request) {
        // $module = $request->get_url_params('slug');
        $module = $request['module'];
        // $module = $request->get_param('module');
        $settings = $request->get_param('settings');

        NBT_Solutions_Data::update_module_setting($module, $settings);

        return rest_ensure_response( NBT_Solutions_Data::get_module_settings($module) )->set_status(201);

        // return $settings;
    }

    public function set_modules_list(WP_REST_Request $request) {
        $params = $request->get_param('modules_list');

        if( ! is_array($params)) {
            $params = array(
                'smtp',
                'metabox',
                $params
            );
        }

        sort($params);

        $rs = NBT_Solutions_Data::activated_modules($params);
        NBT_Solutions_Data::modules_list();
        
        return rest_ensure_response($rs);
    }

    public function get_settings_callback() {
        return rest_ensure_response( NBT_Solutions_Data::get_settings() );
    }
    
    public function get_module_settings_callback($request){
        $nodule_setting = [];
        if(array_key_exists($request['module_name'], NBT_Solutions_Data::get_settings())){
            $nodule_setting = NBT_Solutions_Data::get_settings()[$request['module_name']];
        }
        return rest_ensure_response( $nodule_setting );
    }

    public function get_on_hold_comments() {
        return rest_ensure_response( NBT_Solutions_Data::get_recent_comments() );
    }

    public function update_comment_status(WP_REST_Request $request) {
        $comment_id_param = $request->get_param('id');
        $comment_status_param = $request->get_param('status');

        wp_set_comment_status($comment_id_param, $comment_status_param);
        
        return rest_ensure_response( NBT_Solutions_Data::get_recent_comments() )->set_status( 201 );
    }
}
new NBT_Solutions_API();