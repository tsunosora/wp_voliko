<?php 
/**
 * Plugin Name:       Worksuite WP
 * Plugin URI:        https://worksuite.cloodo.com/
 * Description:       CRM, Live Chat, Clients, Leads, Project Manager & Notice Board
 * Version:           2.2.1
 * Requires at least: 5.2
 * Requires PHP:      7.3
 * Author:            Cloodo
 * Author URI:        https://cloodo.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cloodo-worksuite
 * Domain Path:       /languages
 */
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'CLWS_IFRAME_URL', 'https://worksuite.cloodo.com/' );

// API user
define( 'CLWS_API_LOGIN_URL', 'https://erp.cloodo.com/api/v1/auth/login' );
define( 'CLWS_API_CREATE_URL', 'https://erp.cloodo.com/api/v1/create-user' );

// API client
define( 'CLWS_API_POST_CLIENT_URL', 'https://erp.cloodo.com/api/v1/client' );
define( 'CLWS_API_GET_CLIENT_URL', 'https://erp.cloodo.com/api/v1/client/?fields=id,name,email,mobile,status,created_at,client_details{company_name,website,address,office_phone,city,state,country_id,postal_code,skype,linkedin,twitter,facebook,gst_number,shipping_address,note,email_notifications,category_id,sub_category_id,image}&offset=0' );
define( 'CLWS_API_GET_ALL_CLIENT_URL', 'https://erp.cloodo.com/api/v1/client/?fields=id,name,email,mobile,status,created_at,client_details{company_name,website,address,office_phone,city,state,country_id,postal_code,skype,linkedin,twitter,facebook,gst_number,shipping_address,note,email_notifications,category_id,sub_category_id,image}&offset=0&limit=' );

// API product
define( 'CLWS_API_POST_PRODUCT_URL', 'https://erp.cloodo.com/api/v1/product' );
define( 'CLWS_API_GET_PRODUCT_URL', 'https://erp.cloodo.com/api/v1/product/?fields=id,name,price,description,taxes,allow_purchase,category,hsn_sac_code&offset=0' );
define( 'CLWS_API_GET_ALL_PRODUCT_URL', 'https://erp.cloodo.com/api/v1/product/?fields=id,name,price,description,taxes,allow_purchase,category,hsn_sac_code&offset=0&limit=' );

// API employees
define( 'CLWS_API_POST_EMPLOYEES_URL', 'https://erp.cloodo.com/api/v2/employees' );
define( 'CLWS_API_GET_EMPLOYEES_URL', 'https://erp.cloodo.com/api/v2/employees?fields=id,name,email,password,email_notifications,login,gender,employee_id,joining_date,department_id,designation_id&offset=0' );

// API departments
define('CLWS_API_POST_DEPARTMENT_URL', 'https://erp.cloodo.com/api/v1/department');
define('CLWS_API_GET_DEPARTMENT_URL', 'https://erp.cloodo.com/api/v1/department?fields=id,team_name');

// API designation
define('CLWS_API_POST_DESIGNATION_URL', 'https://erp.cloodo.com/api/v1/designation');
define('CLWS_API_GET_DESIGNATION_URL', 'https://erp.cloodo.com/api/v1/designation?fields=id,name');

// API company
define('CLWS_API_GET_COMPANY_URL', 'https://erp.cloodo.com/api/v3/companies');

// plugin path
define( 'CLWS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CLWS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );




add_action( 'admin_init', 'setting_clws_settings_init' );
function setting_clws_settings_init() {
	// register setting
    register_setting( 
        'setting_clws', //string $option_group
        'setting_clws_options' //string $option_name
    );

	// sync section
    add_settings_section(
        'setting_clws_section_integrated_synchronized_developers', //string $id
        __( 'Sync setting', 'setting_clws' ),  //string $title
        'setting_clws_section_integrated_synchronized_developers_callback', //callable $callback
        'setting_clws' //string $page -  slug-name
    );


	// sync employees
    add_settings_field(
        'setting_clws_field_sync_employees_checkbox', //string $id
        __( '', 'setting_clws' ), //string $title
        'setting_clws_field_sync_employees_checkbox_cb', //callable $callback
        'setting_clws', //string $page
        'setting_clws_section_integrated_synchronized_developers', //string $section  =  'default'
        array( //array $args  =  array()
            'label_for'         => 'setting_clws_field_sync_employees_checkbox',
        )
    );

    add_settings_field(
      'setting_clws_field_sync_employees_role_user', //string $id
      __( '', 'setting_clws' ), //string $title
      'setting_clws_field_sync_employees_role_user_cb', //callable $callback
      'setting_clws', //string $page
      'setting_clws_section_integrated_synchronized_developers', //string $section  =  'default'
      array( //array $args  =  array()
          'label_for'         => 'setting_clws_field_sync_employees_role_user',
		  'default_role' => 'author'
      )
    );

	// sync clients
    add_settings_field(
        'setting_clws_field_sync_clients_checkbox', //string $id
        __( '', 'setting_clws' ), //string $title
        'setting_clws_field_sync_clients_checkbox_cb', //callable $callback
        'setting_clws', //string $page
        'setting_clws_section_integrated_synchronized_developers', //string $section  =  'default'
        array( //array $args  =  array()
            'label_for'         => 'setting_clws_field_sync_clients_checkbox',
        )
    );
}

/* add core cloodo active */
require_once __DIR__ . '/includes/cloodo_core.php';

$token = get_option('cloodo_token', '');

$admin_menus = [
    'worksuite' => [
        'parent_slug' => null,
        'target_url' => null,
        'token_require' => false,
        'page_title' => 'WorkSuite',
        'menu_title' => 'WorkSuite',
        'capability' => 'manage_options',
        'include' => __DIR__ . "/includes/dashboard.php",
        'icon_url' => 'dashicons-businessman',
        'position' => 7,
        'enqueue_style' => null,
    ],
    'worksuite-work' => [
        'parent_slug' => 'worksuite',
        'target_url' => 'https://worksuite.cloodo.com/apps/work/project?tokenws=' . $token,
        'token_require' => true,
        'page_title' => 'Work',
        'menu_title' => 'Work',
        'capability' => 'manage_options',
        'include' => null,
        'icon_url' => null,
        'position' => null,
        'enqueue_style' => null,
    ],
    'worksuite-leads' => [
        'parent_slug' => 'worksuite',
        'target_url' => 'https://worksuite.cloodo.com/apps/leads?tokenws=' . $token,
        'token_require' => true,
        'page_title' => 'Leads',
        'menu_title' => 'Leads',
        'capability' => 'manage_options',
        'include' => null,
        'icon_url' => null,
        'position' => null,
        'enqueue_style' => null,
    ],
    'worksuite-clients' => [
        'parent_slug' => 'worksuite',
        'target_url' => 'https://worksuite.cloodo.com/apps/clients?tokenws=' . $token,
        'token_require' => true,
        'page_title' => 'Clients',
        'menu_title' => 'Clients',
        'capability' => 'manage_options',
        'include' => CLWS_PLUGIN_DIR . 'includes/client.php',
        'icon_url' => null,
        'position' => null,
        'enqueue_style' => null,
    ],
    'worksuite-notice' => [
        'parent_slug' => 'worksuite',
        'target_url' => 'https://worksuite.cloodo.com/apps/notice-board?tokenws=' . $token,
        'token_require' => true,
        'page_title' => 'Notice',
        'menu_title' => 'Notice',
        'capability' => 'manage_options',
        'include' => null,
        'icon_url' => null,
        'position' => null,
        'enqueue_style' => null,
    ],
    'worksuite-messages' => [
        'parent_slug' => 'worksuite',
        'target_url' => 'https://worksuite.cloodo.com/apps/message?tokenws=' . $token,
        'token_require' => true,
        'page_title' => 'Messages',
        'menu_title' => 'Messages',
        'capability' => 'manage_options',
        'include' => null,
        'icon_url' => null,
        'position' => null,
        'enqueue_style' => null,
    ],
    'worksuite-products' => [
        'parent_slug' => 'worksuite',
        'target_url' => 'https://worksuite.cloodo.com/apps/settings/products?tokenws=' . $token,
        'token_require' => true,
        'page_title' => 'Products',
        'menu_title' => 'Products',
        'capability' => 'manage_options',
        'include' => __DIR__ . "/includes/products.php",
        'icon_url' => null,
        'position' => null,
        'enqueue_style' => null,
    ],
    'worksuite-employees' => [
        'parent_slug' => 'worksuite',
        'target_url' => 'https://worksuite.cloodo.com/apps/hr/employees?tokenws=' . $token,
        'token_require' => true,
        'page_title' => 'Employees',
        'menu_title' => 'Employees',
        'capability' => 'manage_options',
        'include' => __DIR__ . "/includes/employees.php",
        'icon_url' => null,
        'position' => null,
        'enqueue_style' => null,
    ],
    'worksuite-support' => [
        'parent_slug' => 'worksuite',
        'target_url' => null,
        'token_require' => true,
        'page_title' => 'Support',
        'menu_title' => 'Support',
        'capability' => 'manage_options',
        'include' => __DIR__ . "/includes/support.php",
        'icon_url' => null,
        'position' => null,
        'enqueue_style' => null,
    ],
    'worksuite-settings' => [
        'parent_slug' => 'worksuite',
        'target_url' => null,
        'token_require' => true,
        'page_title' => 'Settings',
        'menu_title' => 'Settings',
        'capability' => 'manage_options',
        'include' => __DIR__ . "/includes/settings.php",
        'icon_url' => null,
        'position' => null,
        'enqueue_style' => null,
    ],
];

add_action('init',function() use($admin_menus){
    /* add admin menu */
    cloodo_admin_page($admin_menus);

    add_action('cloodo_page_content',function(){
        require __DIR__ . "/includes/dashboard.php";
    });
});
