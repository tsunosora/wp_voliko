<?php

/**
 * This class display setting page WP-Admin
 */
class WooPanel_Admin_Options {

    /**
     * Set option name
     * @var string
     */
    public static $options_name = 'woopanel_admin_options';

    /**
     * Set option field
     * @var array
     */
    public static $options_fields;

    public $options = array();

    /**
     * WooPanel_Admin_Options Constructor.
     */
    public function __construct() {
        self::$options_fields = array(
            array(
                'title'    => esc_html__( 'Dashboard page', 'woopanel' ),
                'desc'     => '',
                'id'       => 'dashboard_page_id',
                'type'     => 'single_select_page',
                'default'  => '',
                'class'    => 'wc-enhanced-select-nostd',
                'css'      => 'min-width:300px;',
                'desc_tip' => true,
            ),
            array(
                'title'    => esc_html__( 'Store List Page', 'woopanel' ),
                'desc'     => '',
                'id'       => 'woopanel_page_stores',
                'type'     => 'single_select_page',
                'default'  => '',
                'value_field' => 'post_name',
                'class'    => 'wc-enhanced-select-nostd',
                'css'      => 'min-width:300px;',
                'desc_tip' => true,
            ),
            array(
                'title'    => esc_html__( 'Dashboard Logo', 'woopanel' ),
                'desc'     => '',
                'id'       => 'dashboard_header_logo',
                'type'     => 'image',
                'default'  => '',
            ),
            array(
                'title'    => esc_html__( 'Profile Store Permalink', 'woopanel' ),
                'desc'     => '',
                'id'       => 'profile_store_permalink',
                'type'     => 'text',
                'options'  => array(),
                'value'  => 'store-profile',
            ),
            array(
                'title'    => esc_html__( 'Background Image Login', 'woopanel' ),
                'desc'     => '',
                'id'       => 'bg_images',
                'type'     => 'image',
                'default'  => '',
            ),
            array(
                'title'    => esc_html__( 'Background Color Login', 'woopanel' ), 
                'id'       => 'color_pages',
                'type'     => 'color',    
            ),
            array(
                'title'    => esc_html__( 'Allow User to Customize Dashboard', 'woopanel' ),
                'desc'     => '',
                'id'       => 'customize_dashboard',
                'type'     => 'checkbox',
                'default'  => '',
            ),
            array(
                'title'    => esc_html__( 'Allow any user access to WooPanel', 'woopanel' ),
                'desc'     => '',
                'id'       => 'any_access',
                'type'     => 'checkbox',
                'default'  => '',
            ),
            array(
                'title'    => sprintf( esc_html__( 'Block %s Access for Non-Admins', 'woopanel' ), '<code>wp-admin</code>' ),
                'desc'     => '',
                'id'       => 'block_wp_admin',
                'type'     => 'checkbox',
                'default'  => '',
            ),
            'enable_modules' => array(
                'title'    => esc_html__( 'Enable Modules', 'woopanel' ),
                'desc'     => '',
                'id'       => 'enable_modules',
                'type'     => 'multi_checkbox',
                'options'  => array(),
                'default'  => '',
            ),
            array(
                'title'    => esc_html__( 'Show Loading Effect', 'woopanel' ),
                'desc'     => '',
                'id'       => 'show_loading_effect',
                'type'     => 'checkbox',
                'default'  => '',
            ),
            array(
                'title'    => esc_html__( 'Show Loading Closed', 'woopanel' ),
                'desc'     => '',
                'id'       => 'shop_loading_closed',
                'type'     => 'checkbox',
                'default'  => '',
            ),
            array(
                'title'    => esc_html__( 'Show Loading Icon', 'woopanel' ),
                'desc'     => '',
                'id'       => 'shop_loading_icon',
                'type'     => 'icon_list',
                'options'   => array(
                  'style1' => '<div class="loader__wrap" role="alertdialog" aria-busy="true" aria-live="polite" aria-label="Loading…"><div class="loader" aria-hidden="true"><div class="loader__sq"></div><div class="loader__sq"></div></div></div>',
                  'style2' => '<div class="loader loader-1"><div class="loader-outter"></div><div class="loader-inner"></div></div>',
                  'style3' => '<div class="loader loader-3"><div class="dot dot1"></div><div class="dot dot2"></div><div class="dot dot3"></div></div>',
                  'style4' => '<div class="loader loader-7"><div class="line line1"></div><div class="line line2"></div><div class="line line3"></div></div>',
                  'style5' => '<div class="loader loader-17"><div class="css-square square1"></div><div class="css-square square2"></div><div class="css-square square3"></div><div class="css-square square4"></div><div class="css-square square5"></div><div class="css-square square6"></div><div class="css-square square7"></div><div class="css-square square8"></div></div>',
                  'style6' => '<div class="loader loader-6"><div class="loader-inner"></div></div>',
                  'style7' => '<div class="loader">'.esc_html__('Loading', 'woopanel' ).'...</div>',
                  'style8' => '<div class="loader loader-2"><svg class="loader-star" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1"><polygon points="29.8 0.3 22.8 21.8 0 21.8 18.5 35.2 11.5 56.7 29.8 43.4 48.2 56.7 41.2 35.1 59.6 21.8 36.8 21.8 " fill="#fff" /></svg><div class="loader-circles"></div></div>',
                ),
            ),
            array(
                'title'    => esc_html__( 'Store Listing Layout', 'woopanel' ),
                'desc'     => '',
                'id'       => 'store_listing_layout',
                'type'     => 'select',
                'options'  => array(
                    'style1' => esc_html__('Style 1', 'woopanel'),
                    'style2' => esc_html__('Style 2', 'woopanel')
                ),
                'default'  => '',
            ),
            array(
                'title'    => esc_html__( 'Import Vendor', 'woopanel' ),
                'desc'     => '',
                'id'       => 'shop_loading_icon',
                'type'     => 'import_vendor',
                'button_class'    => 'import-vendor',
                'button_label' => esc_html__('Start Import', 'woopanel'),
                'description'  => esc_html__('Add role vendor for all users.', 'woopanel')
            )
        );
        
        $this->options = get_option( self::$options_name );


        /**
         * Fires before the administration menu loads in the admin.
         *
         * @since 1.0.0
         * @hook admin_menu
         * @param {string} $context Empty context.
         */
        add_action( 'admin_menu', array( $this, 'woopanel_register_ref_page'), 99 );

        /**
         * Enqueue scripts for all admin pages.
         *
         * @since 1.0.0
         * @hook admin_enqueue_scripts
         * @param {string} $hook_suffix The current admin page.
         */
        add_action('admin_enqueue_scripts', array($this, 'woopanel_admin_scripts'));

        add_action( 'wp_ajax_woopanel_import_vendor',  array($this, 'woopanel_import_vendor') );
    }

    /**
     * Get all options
     */
    static function all_options(){
        return get_option( self::$options_name );
    }

    /**
     * Get option by id
     */
    static function get_option( $id ){
        $admin_options = self::all_options();
        return isset($admin_options[$id]) ? $admin_options[$id] : null;
    }

    /**
     * Save option with id, value
     */
    static function set_option( $id, $value ){
        $options = self::all_options();
        $options[$id] = $value;
        update_option( self::$options_name, $options );
    }

    /**
     * Save all option with args
     */
    static function set_options( $args = array() ){
        $options = self::all_options();
        $args = wp_parse_args( $args, $options );

        update_option( self::$options_name, $args );
    }

    /**
     * Add menu page in WP-Admin
     */
    public function woopanel_register_ref_page() {
        add_submenu_page(
            'options-general.php',
            esc_html__( 'WooPanel Settings', 'woopanel' ),
            esc_html__( 'WooPanel', 'woopanel' ),
            'manage_options',
            'woopanel-settings',
            array( $this, 'woopanel_page_callback' )
        );
    }

    /**
     * Render field HTML
     */
    static function get_field_html( $value ){
        $html = '';
        $html .= "<tr valign='top' class='{$value['type']}-type {$value['id']}-wrap'>";
        $html .= "<th scope='row' class='titledesc'><label>{$value['title']}</label></th>";
        $html .= '<td>';
        switch ($value['type']){
            case 'text':
            case 'password':
            case 'datetime':
            case 'month':
            case 'week':
            case 'time':
            case 'number':
            case 'file':
            case 'email':
            case 'url':

            case 'tel':
                $default = self::get_option($value['id']);
                $_value = empty($default) ? $value['value'] : $default;
                $html .= '<input type="'. esc_attr($value['type']) .'" name="'. esc_attr($value['id']) .'" id="'. esc_attr($value['id']) .'" value="'. $_value .'" class="regular-text">';
                break;

            case 'textarea' :
                $html .= '<textarea name="'. esc_attr($value['id']) .'" id="'. esc_attr($value['id']) .'" rows="5" cols="50">'. stripslashes(self::get_option($value['id'])) .'</textarea>';
                break;

            case 'checkbox' :
                $checked = (self::get_option($value['id']) == 'yes') ? 'checked' : '';
                $html .= '<label for="'. esc_attr($value['id']) .'">';
                $html .= '<input type="hidden" name="'. esc_attr($value['id']) .'" value="no">';
                $html .= '<input name="'. esc_attr($value['id']) .'" type="checkbox" id="'. esc_attr($value['id']) .'" value="yes" '. esc_attr($checked) .'>';
                if( isset( $value['label'] ) ) $html .= esc_attr($value['label']);
                $html .= '</label>';
                break;
            case 'multi_checkbox':
                if( isset($value['options']) ) {
                    foreach ($value['options'] as $option_key => $option_label) {
                        $checked = '';
                        if( is_array(self::get_option($value['id'])) && in_array($option_key, self::get_option($value['id']) ) ) {
                            $checked = 'checked';
                        }

                        $html .= '<p><label for="'. esc_attr($option_key) .'">';
                        //$html .= '<input type="hidden" name="'. esc_attr($value['id']) .'[]" value="no">';
                        $html .= '<input name="'. esc_attr($value['id']) .'[]" type="checkbox" id="'. esc_attr($option_key) .'" value="'. esc_attr($option_key) .'" '. esc_attr($checked) .'>';
                        if( isset( $option_label ) ) $html .= esc_attr($option_label);
                        $html .= '</label></p>';
                    }
                }
                break;
            case 'image':
                $default = array(
                    'id'      => esc_attr($value['id']),
                    'echo'    => false,
                    'value' => absint( self::get_option($value['id']) ),
                );
                $args = wp_parse_args( $value, $default );
                $html .= self::woopanel_image_uploader( $args );
                break;
            case 'color' : 
                $html .= '<input type="color" class="my-color-field" name="'. esc_attr($value['id']) .'"  data-default-color="#000" value="'. esc_attr(self::get_option($value['id'])) .'">'; 
                
                break;
            case 'select':
                $html .= '<select name="'. esc_attr($value['id']) .'">';
                    foreach ($value['options'] as $key => $name) {
                        $selected = '';
                        if( $key == self::get_option($value['id']) ) {
                            $selected = ' selected';
                        }
                        $html .= '<option value="' . $key .'"'.$selected.'>' . $name .'</option>';
                    }
                $html .= '</select>';
                break;
            case 'import_vendor':
                global $wp_roles;

                unset($wp_roles->roles['wpl_seller']);
                foreach ($wp_roles->roles as $role_name => $role) {

                    $html .= sprintf('<p><label for="role-%s">', $role_name );

                    $html .= sprintf(
                        '<input name="import_role[]" type="checkbox" id="role-%s" class="role-input" value="%s">%s',
                        $role_name,
                        $role_name,
                        $role['name']
                    );

                    $html .= '</label></p>';
                }

                $loaderImg = '<img src="' . WOODASHBOARD_URL .'assets/images/sloader.svg">';

                $html .= '<button type="button" name="'. esc_attr($value['id']) .'" id="'. esc_attr($value['id']) .'" class="button-primary '. $value['button_class'].'" style="margin-top: 15px;">' . $value['button_label'] . $loaderImg . '</button>';
                $html .= empty($value['description']) ? '' : '<p class="description">' . esc_html__($value['description']) .'</p>';


                break;
            case 'single_select_page':

                $args = array(
                    'name'             => esc_attr($value['id']),
                    'id'               => esc_attr($value['id']),
                    'sort_column'      => 'menu_order',
                    'value_field'       => isset($value['value_field']) ? $value['value_field'] : 'ID',
                    'sort_order'       => 'ASC',
                    'show_option_none' => ' ',
                    'class'            => esc_attr($value['class']),
                    'echo'             => false,
                    'selected'         => self::get_option($value['id']),
                    'post_status'      => 'publish,private,draft',
                );

                if ( isset( $value['args'] ) ) {
                    $args = wp_parse_args( $value['args'], $args );
                }

                $pages  = get_pages( $args );

                if ( ! empty( $pages ) ) {

                    $class = '';
                    if ( ! empty( $args['class'] ) ) {
                        $class = " class='" . esc_attr( $args['class'] ) . "'";
                    }

                    $html .= "<select name='" . esc_attr( $args['name'] ) . "'" . $class . " id='" . esc_attr( $args['id'] ) . "' data-placeholder='". esc_attr__( 'Select a page&hellip;', 'woopanel' ) . "'>\n";

                    $html .= "\t<option value=\"-1\">" . esc_attr__( 'Select a page&hellip;', 'woopanel' ) . "</option>\n";
                    foreach ($pages as $key => $page) {
                        $value = $page->{$args['value_field']};

                        $html .= "\t<option value=\"" . esc_attr( $value ) . '"'. selected( $args['selected'], $value, false ) .'>' . $page->post_title . "</option>\n";
                    }

                    $html .= '</select>';

                }


                // $html .= str_replace( ' id=', " data-placeholder='". esc_attr__( 'Select a page&hellip;', 'woopanel' ) . "' style='" . esc_attr($value['css']) . "' class='" . esc_attr($value['class']) . "' id=", wp_dropdown_pages( $args ) );
                break;
            case 'icon_list':
                $html .= '<div class="wpl-icon_lists">';
                    $index = 0;
                    $total = count($value['options']);
                    foreach( $value['options'] as $k => $icon_html ) {
                        if( $index % 4 == 0) {
                            $html .= '<div class="wpl-icon_item_row">';
                        }

                        $checked = false;
                        if( self::get_option($value['id']) == $k ) {
                            $checked = ' checked';
                        }

   
                        $html .=  '<input type="radio" name="'. esc_attr($value['id']) .'" value="'. esc_attr($k) .'"'. esc_attr($checked) .' /><div class="wpl-icon_item loading-'. esc_attr($k) .'">'. wp_kses( $icon_html, array(
                                'div' => array(
                                    'class' => array()
                                ),
                            ) ) .'</div>';

                        if( $index % 4 == 3 || $index == ($total-1) ) {
                            $html .= '</div>';
                        }

                        $index++;
                    }
                $html .= '</div>';
                break;
            default:
                $html .= '<input type="text" name="'. esc_attr($value['id']) .'" id="'. esc_attr($value['id']) .'" value="'. esc_attr( self::get_option($value['id']) ) .'" class="regular-text">';
                break;

        }
        if( isset($value['desc']) ) $html .= '<p class="description">'. esc_attr( $value['desc'] ) .'</p>';
        $html .= '</td>';
        $html .= '</tr>';

        print($html);
    }

    /**
     * Display content page settings
     */
    function woopanel_page_callback() {
        global $woopanel_modules;

        if( empty($woopanel_modules) ) {
            unset(self::$options_fields['enable_modules']);
        }else {
            $module_option = array();
            foreach ($woopanel_modules as $module_key => $module) {
                $module_option[$module_key] = isset($module['label']) ? $module['label'] : '';
            }

            self::$options_fields['enable_modules']['options'] = $module_option;
        }

        if(isset($_POST['save'])){ $this->save_options(); } ?>
        <div class="wrap">
            <h1 id="woopanel-title"><?php esc_html_e('WooPanel Settings', 'woopanel' ); ?></h1>

            <div id="woopanel-main">
                <div id="woopanel-tabs">
                    <form method="post" id="mainform" action="" enctype="multipart/form-data">
                        <table class="form-table">
                            <?php foreach (self::$options_fields as $field) {
                                self::get_field_html($field);
                            } ?>
                        </table>

                        <p class="submit">
                            <button name="save" class="button-primary" type="submit"
                                    value="Save changes"><?php esc_html_e('Save changes', 'woopanel' ); ?></button>
                            <?php wp_nonce_field('woopanel-settings'); ?>
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            jQuery(document).ready(function() {
                jQuery(document).on( 'click', '.wpl-icon_item', function(e) {
                    e.preventDefault();

                    jQuery(this).prev().trigger('click');
                });


                if( jQuery().wpColorPicker ) {
                    jQuery('.my-color-field').wpColorPicker(); 
                }
                
            });
        </script>
        <?php
    }

    /**
     * Save data when press Save
     */
    function save_options() {
        if( isset($_POST) && ! empty($_POST) ) {
            $options = $_POST;

            unset($options['save']);
            unset($options['_wpnonce']);
            unset($options['_wp_http_referer']);

            update_option( self::$options_name, $options );
        }

    }

    function woopanel_import_vendor() {
        $json = array();
        $roles = wp_unslash( $_POST['roles'] );

        $data = array();
        foreach ($roles as $role) {
            $users = get_users( array(
                'role'    => $role,
                'orderby' => 'user_nicename',
                'order'   => 'ASC'
            ) );

            if( $users ) {
                foreach ($users as $user) {

                    if( ! in_array('wpl_seller', $user->roles) ) {
                        $data[$user->ID] = $user;
                    }
                    
                }
            }
        }


        foreach ($data as $user_id => $u) {
            $wp_capabilities = get_user_meta( $user_id, 'wp_capabilities', true );
            $wp_capabilities['wpl_seller'] = 1;
            update_user_meta( $user_id, 'wp_capabilities', $wp_capabilities );
        }

        $json['complete'] = true;
        $json['message'] = esc_html__('Set role vendor successfuly!', 'woopanel');

        wp_send_json($json);
    }

    /**
     * Enqueue styles.
     */
    function woopanel_admin_scripts( $hook ) {
        if( $hook == 'settings_page_woopanel-settings' ) {
            wp_enqueue_media();
            if( is_woo_installed() ) {
                wp_enqueue_script('wp-color-picker');
                wp_enqueue_style('wp-color-picker');

                wp_register_script('wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select.min.js', array('jquery', 'selectWoo'), WC_VERSION);
                wp_localize_script(
                    'wc-enhanced-select',
                    'wc_enhanced_select_params',
                    array(
                        'i18n_no_matches' => _x('No matches found', 'enhanced select', 'woopanel' ),
                        'i18n_ajax_error' => _x('Loading failed', 'enhanced select', 'woopanel' ),
                        'i18n_input_too_short_1' => _x('Please enter 1 or more characters', 'enhanced select', 'woopanel' ),
                        'i18n_input_too_short_n' => _x('Please enter %qty% or more characters', 'enhanced select', 'woopanel' ),
                        'i18n_input_too_long_1' => _x('Please delete 1 character', 'enhanced select', 'woopanel' ),
                        'i18n_input_too_long_n' => _x('Please delete %qty% characters', 'enhanced select', 'woopanel' ),
                        'i18n_selection_too_long_1' => _x('You can only select 1 item', 'enhanced select', 'woopanel' ),
                        'i18n_selection_too_long_n' => _x('You can only select %qty% items', 'enhanced select', 'woopanel' ),
                        'i18n_load_more' => _x('Loading more results&hellip;', 'enhanced select', 'woopanel' ),
                        'i18n_searching' => _x('Searching&hellip;', 'enhanced select', 'woopanel' ),
                        'ajax_url' => admin_url('admin-ajax.php'),
                        'search_products_nonce' => wp_create_nonce('search-products'),
                        'search_customers_nonce' => wp_create_nonce('search-customers'),
                    )
                );
                wp_enqueue_script('wc-enhanced-select');

                wp_enqueue_style('woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION);
            }
            wp_enqueue_style('woopanel_settings_styles', WOODASHBOARD_URL . 'assets/css/admin-settings.css', array());
            wp_enqueue_style('woopanel_admin_styles', WOODASHBOARD_URL . 'admin/assets/css/admin.css', array());
            wp_enqueue_script('woopanel_admin_scripts', WOODASHBOARD_URL . 'admin/assets/js/scripts.js', array('jquery'), WooDashboard()->version );
        }
    }

    /**
     * Field for image type
     */
    static function woopanel_image_uploader( $args = array() ) {
        $default = array(
            'id'             => 'image_id',
            'width'          => 115,
            'height'         => 115,
            'value'          => '',
            'echo'           => false,
        );
        $r = wp_parse_args( $args, $default );

        $placeholder = 'assets/images/no-image.svg';
        if( isset($r['rectangle']) ) {
            $placeholder = 'assets/images/no-image-rectangle.svg';
        }

        $field_name = isset($r['name']) ? $r['name'] : $r['id'];

        // Set variables
        $default_image = WooDashboard()->plugin_url( $placeholder );

        if ( absint( $r['value'] ) > 0 ) {
            $image_attributes = wp_get_attachment_image_src( $r['value'], array( $r['width'], $r['height'] ) );
            $src = $image_attributes[0];
            $value = $r['value'];
        } else {
            $src = $default_image;
            $value = '';
        }

        $text = esc_html__( 'Upload', 'woopanel' );
        $html = '';

        // Print HTML field
        $html .= '<div class="upload">
            <img data-src="' . esc_url($default_image) . '" src="' . esc_url($src) . '" style="max-width: ' . absint($r['width']) . 'px; max-height: ' . absint($r['height']) . 'px" />
            <div>
                <input type="hidden" name="' . esc_attr($field_name) . '" id="' . esc_attr($r['id']) . '" value="' . esc_attr($value) . '" />
                <button type="button" class="upload_image_button button">' . esc_attr($text) . '</button>
                <button type="button" class="remove_image_button button">&times;</button>
            </div>
        </div>';

        if( $r['echo'] ) print($html);
        return $html;
    }
}

/**
 * Returns the main instance of WooPanel_Admin_Options.
 *
 * @since  1.0.0
 * @return WooPanel_Admin_Options
 */
$GLOBALS['admin_options'] = new WooPanel_Admin_Options();