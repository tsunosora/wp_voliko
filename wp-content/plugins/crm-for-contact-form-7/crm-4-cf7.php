<?php
/**
 * Plugin Name:       CRM for Contact Form 7
 * Plugin URI:        https://worksuite.cloodo.com/
 * Description:       Lead management for Contact Form 7
 * Version:           1.1.1
 * Requires at least: 5.2
 * Requires PHP:      7.3
 * Author:            Cloodo
 * Author URI:        https://cloodo.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       crm-4-cf7
 * Domain Path:       /languages
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('CCF7_API_URL', 'https://erp.cloodo.com/api/v3');
define('CCF7_IFRAME_URL', 'https://worksuite.cloodo.com');
define('CCF7_API_TIMEOUT', 20);

// API employees
define( 'CCF7_API_POST_EMPLOYEES_URL', CCF7_API_URL . '/employees' );
define( 'CCF7_API_GET_EMPLOYEES_URL', CCF7_API_URL . '/employees?fields=id,name,email,password,email_notifications,login,gender,employee_id,joining_date,department_id,designation_id&offset=0' );

// API departments
define('CCF7_API_POST_DEPARTMENT_URL', CCF7_API_URL . '/departments');
define('CCF7_API_GET_DEPARTMENT_URL', CCF7_API_URL . '/departments?fields=id,name');

// API designation
define('CCF7_API_POST_DESIGNATION_URL', CCF7_API_URL . '/designations');
define('CCF7_API_GET_DESIGNATION_URL', CCF7_API_URL . '/designations?fields=id,name');

// API company
define('CCF7_API_GET_COMPANY_URL', CCF7_API_URL . '/companies');

// add_filter('wpcf7_before_send_mail', 'ccf7_sync_contact', 10, 3);

/* -------------------------------- register settings  -----------------------------*/
add_action( 'admin_init', 'ccf7_setting_crm4_settings_init' );
function ccf7_setting_crm4_settings_init() {
    register_setting( 
        'setting_crm4', //string $option_group
        'ccf7_setting_crm4_options' //string $option_name
    );

    add_settings_section(
        'setting_crm4_section_integrated_synchronized_developers', //string $id
        __( 'Sync setting', 'setting_crm4' ),  //string $title
        'setting_crm4_section_integrated_synchronized_developers_callback', //callable $callback
        'setting_crm4' //string $page -  slug-name
    );

    add_settings_field(
        'setting_crm4_field_sync_employees_checkbox', //string $id
        __( '', 'setting_crm4' ), //string $title
        'ccf7_setting_crm4_field_sync_employees_checkbox_cb', //callable $callback
        'setting_crm4', //string $page
        'setting_crm4_section_integrated_synchronized_developers', //string $section  =  'default'
        array( //array $args  =  array()
            'label_for'         => 'setting_crm4_field_sync_employees_checkbox',
        )
    );

    add_settings_field(
      'setting_crm4_field_sync_employees_role_user', //string $id
      __( '', 'setting_crm4' ), //string $title
      'ccf7_setting_crm4_field_sync_employees_role_user_cb', //callable $callback
      'setting_crm4', //string $page
      'setting_crm4_section_integrated_synchronized_developers', //string $section  =  'default'
      array( //array $args  =  array()
          'label_for'         => 'setting_crm4_field_sync_employees_role_user',
          'default_role' => 'author'
      )
    );
}

 function ccf7_setting_crm4_field_sync_employees_checkbox_cb($args) { ?>

      <div class="card-title header-elements">
          <h5 class="m-0 me-2">
              Sync employees
          </h5>
          <div class="card-title-elements">
              <label class="switch switch-primary switch-sm me-0">
                  <?php
                      $ccf7_setting_crm4_options = get_option( 'ccf7_setting_crm4_options' );
                      if ($ccf7_setting_crm4_options == null) {
                          $ccf7_setting_crm4_options['setting_crm4_field_sync_employees_checkbox'] = '1';
                      }
                  ?>
                  <input type="checkbox"
                      class="switch-input" 
                      id="<?php echo esc_attr( $args['label_for'] ); ?>" 
                      name="ccf7_setting_crm4_options[<?php echo esc_attr( $args['label_for'] ); ?>]" 
                      value="1"
                      <?php 
                          isset( $ccf7_setting_crm4_options[ $args['label_for'] ] ) 
                          ? checked( '1', $ccf7_setting_crm4_options[ $args['label_for'] ] ) 
                          : (''); 
                      ?> 
                  />
                  <span class="switch-toggle-slider">
                      <span class="switch-on"></span>
                      <span class="switch-off"></span>
                  </span>
              </label>
          </div>
      </div>
  <?php } 

function ccf7_setting_crm4_field_sync_employees_role_user_cb($arg_role_users) {
  $ccf7_setting_crm4_options = get_option('ccf7_setting_crm4_options');
  $selected_role = 
      isset($ccf7_setting_crm4_options[$arg_role_users['label_for']]) 
      ? $ccf7_setting_crm4_options[$arg_role_users['label_for']] 
      : $arg_role_users['default_role'];
  ?>
  <div style="padding-left: 50px;">
      <h6 class="card-text">
            Choose a user role
      </h6>
      <div class="card-title-elements ms-auto">
          <select 
              class="form-select form-select-sm w-auto"
              id="<?php echo esc_attr( $arg_role_users['label_for'] ); ?>"
              name="ccf7_setting_crm4_options[<?php echo esc_attr( $arg_role_users['label_for'] ); ?>]"
          >
              <?php wp_dropdown_roles( $selected_role ); ?>
          </select>
      </div>
      <p class="card-text mt-3">
          Synchronize wp users with CRM employees by single user role option. The initial default automatically syncs according to the author role. You can change the role and turn this feature on and off according to your needs to experience the utility.
      </p>
  </div>
<?php }

function ccf7_sync_contact($contact_form, $about, $object){
  $cloodo_token = get_option('cloodo_token');
  if (!$cloodo_token){
    return;
  }
  $posted_data = $object->get_posted_data();
  $form_tags = $object->get_contact_form()->scan_form_tags();
  
  $name = $email = $note = '';
  foreach($posted_data as $pd_key => $pd_value){
    $type = '';
    foreach($form_tags as $ft){
      if ($pd_key == $ft->name){
        $type = $ft->basetype;
        break;
      }
    }
    switch ($pd_key){
      case 'your-name':
        $name = $pd_value;
        break;
      case 'your-email':
        $email = $pd_value;
        break;
      default:
        if ($type == 'email'){
          $email = $pd_value;
        }else{
          $note .= $pd_key . ": ". $pd_value. '<br />';
        }
    }
  }
  // create lead
  $response = wp_remote_post(CCF7_API_URL . '/lead', array(
    'timeout' => CCF7_API_TIMEOUT,
    'headers' => array(
      'Authorization' => 'Bearer '. $cloodo_token,
    ),
    'body' => array(
      'client_name' => $name,
      'client_email' => $email,
      'next_follow_up' => 'yes',
      'note' => $note
    )
  ));
  if (isset($response['body'])){
    $body_res =  json_decode($response['body']);
    if ($body_res->message == "Resource created successfully"){
      //client created
    }
  }
}

// add_action('init','ccf7_get_contact_form' );
function ccf7_get_contact_form(){
  $posts = get_posts(array(
    'post_type'     => 'wpcf7_contact_form',
    'numberposts'   => -1
  ));
  foreach($posts as $p){
    $form_ID     = $p->ID;
    $ContactForm = WPCF7_ContactForm::get_instance( $form_ID );
    $form_fields = $ContactForm->scan_form_tags();
    var_dump( $form_fields );
  }
}

/* add core cloodo active */
require_once __DIR__ . '/includes/cloodo_core.php';

$token = get_option('cloodo_token', '');

$admin_menus = [
    'crm-4-cf7' => [
        'parent_slug' => null,
        'target_url' => null,
        'token_require' => false,
        'page_title' => 'CRM 4 CF7',
        'menu_title' => 'CRM 4 CF7',
        'capability' => 'manage_options',
        'include' => __DIR__ . "/includes/crm4_dashboard.php",
        'icon_url' => 'dashicons-groups',
        'position' => 7,
        'enqueue_style' => null,
    ],
    'crm-4-cf7-leads' => [
        'parent_slug' => 'crm-4-cf7',
        'target_url' => 'https://worksuite.cloodo.com/apps/leads?tokenws=' . $token,
        'token_require' => true,
        'page_title' => 'CRM Leads',
        'menu_title' => 'CRM Leads',
        'capability' => 'manage_options',
        'include' => null,
        'icon_url' => null,
        'position' => null,
        'enqueue_style' => null,
    ],
    'crm-4-cf7-agents' => [
        'parent_slug' => 'crm-4-cf7',
        'target_url' => 'https://worksuite.cloodo.com/apps/tickets/setting-tickets?tokenws=' . $token,
        'token_require' => true,
        'page_title' => 'CRM Agents',
        'menu_title' => 'CRM Agents',
        'capability' => 'manage_options',
        'include' => null,
        'icon_url' => null,
        'position' => null,
        'enqueue_style' => null,
    ],
    'crm-4-cf7-proposal' => [
        'parent_slug' => 'crm-4-cf7',
        'target_url' => 'https://worksuite.cloodo.com/apps/proposals?tokenws=' . $token,
        'token_require' => true,
        'page_title' => 'CRM Proposal',
        'menu_title' => 'CRM Proposal',
        'capability' => 'manage_options',
        'include' => null,
        'icon_url' => null,
        'position' => null,
        'enqueue_style' => null,
    ],
    'crm-4-cf7-clients' => [
        'parent_slug' => 'crm-4-cf7',
        'target_url' => 'https://worksuite.cloodo.com/apps/clients?tokenws=' . $token,
        'token_require' => true,
        'page_title' => 'CRM Clients',
        'menu_title' => 'CRM Clients',
        'capability' => 'manage_options',
        'include' => null,
        'icon_url' => null,
        'position' => null,
        'enqueue_style' => null,
    ],
    'crm-4-cf7-employees' => [
        'parent_slug' => 'crm-4-cf7',
        'target_url' => 'https://worksuite.cloodo.com/apps/hr/employees?tokenws=' . $token,
        'token_require' => true,
        'page_title' => 'CRM Employees',
        'menu_title' => 'CRM Employees',
        'capability' => 'manage_options',
        'include' => __DIR__ . "/includes/crm4_sync_user_wp_into_employees.php",
        'icon_url' => null,
        'position' => null,
        'enqueue_style' => null,
    ],
    'crm-4-cf7-settings' => [
        'parent_slug' => 'crm-4-cf7',
        'target_url' => null,
        'token_require' => true,
        'page_title' => 'CRM Settings',
        'menu_title' => 'CRM Settings',
        'capability' => 'manage_options',
        'include' => __DIR__ . "/includes/crm4_settings.php",
        'icon_url' => null,
        'position' => null,
        'enqueue_style' => null,
    ],
];

add_action('init',function() use($admin_menus){
    /* add admin menu */
    cloodo_admin_page($admin_menus);

    add_action('cloodo_page_content',function(){
      require __DIR__ . "/includes/crm4_dashboard.php";
    });
});

function ccf7_show_message_box($message, $type = 'success'){
  ?>
  <div class="notice notice-<?php echo esc_attr($type) ?>">
    <p>
      <?php echo wp_kses_post($message) ?>
    </p>
  </div>
  <?php
}
