<?php
global $woopanel_options;

$woopanel_options = array();

$woopanel_options = array(
    'general' => [
        'menu_title' => esc_html__( 'Generals', 'woopanel' ),
        'title'      => esc_html__( 'General Settings', 'woopanel' ),
        'desc'       => '',
        'parent'     => '',
        'icon'       => '',
        'type'       => 'personal',
        'fields'     => array(

        )
    ],
    'live_chat' => [
        'menu_title' => esc_html__( 'Live Chat', 'woopanel' ),
        'title'      => esc_html__( 'Live Chat Settings', 'woopanel' ),
        'desc'       => '',
        'parent'     => '',
        'icon'       => '',
        'type'       => 'personal',
        'fields'     => array(
            array(
                'id'       => 'live_chat_position',
                'type'     => 'select',
                'title'    => esc_html__( 'Position', 'woopanel'  ),
                'options'  => array(
                    'wp_head'   => esc_html__('Header (before 	&lt;/head&gt;)', 'woopanel' ),
                    'wp_footer' => esc_html__('Footer (before 	&lt;/body&gt;)', 'woopanel' )
                ),
                'default' => 'c'
            ),
            array(
                'id'       => 'live_chat_embed',
                'type'     => 'textarea',
                'title'    => esc_html__( 'Embed Code', 'woopanel'  ),
                'description' => esc_html__('Paste embed code here. Example: tawk.to Live Chat', 'woopanel' )
            )
        )
    ],
);

add_action('init', function() {
    if( ! defined("NB_DEMO") ) { 
        global $woopanel_options, $woopanel_admin_options, $current_user;
        $customize_layout = isset($woopanel_admin_options['customize_layout']) ? $woopanel_admin_options['customize_layout'] : 'fullwidth';
        $user_customize_layout = get_user_meta($current_user->ID, '_shop_layout', true);

        if( isset($_POST['shop_layout']) ) {
            $user_customize_layout = esc_attr($_POST['shop_layout']);
        }
        
        $woopanel_options['general']['fields'][] = array(
            'id'       => 'shop_layout',
            'type'     => 'select',
            'title'    => __( 'Shop Layout', 'woopanel'  ),
            'options'  => array(
                'fullwidth' => __('Fullwidth', 'woopanel' ),
                'fixed' => __('Fixed', 'woopanel' ),
            ),
            'value' => empty($user_customize_layout) ? $customize_layout : $user_customize_layout
        );
    }
});

if( WooPanel_Admin_Options::get_option('customize_dashboard') == 'yes' ){
    $woopanel_options['shop_customizer'] = array(
        'menu_title' => esc_html__( 'Customize' ),
        'title'      => esc_html__( 'Shop Customizer', 'woopanel' ),
        'desc'       => '',
        'parent'     => '',
        'icon'       => '',
        'type'       => 'personal',
        'fields'     => array(
            array(
                'id'       => 'shop_name',
                'type'     => 'text',
                'title'    => esc_html__( 'Shop name', 'woopanel'  ),
            ),
            array(
                'id'       => 'dashboard_header_logo',
                'type'     => 'image',
                'title'    => esc_html__( 'Dashboard Header Logo', 'woopanel'  ),
            ),
        )
    );
}

/**
 * Get all options by seller_options
 */
function woopanel_all_options(){
    global $woopanel_options;

    $shop_options = get_option( 'woopanel_options' );
    $seller_options = get_user_meta( get_current_user_id(), 'seller_options' );

    $options_return = array();

    foreach ( $woopanel_options as $section ) {
        if($section['type'] == 'personal') {
            foreach ($section['fields'] as $option) {
                $options_return[$option['id']] = isset($seller_options[0][$option['id']]) ? $seller_options[0][$option['id']] : null;
            }
        }
    }

    return $options_return;
}

/**
 * Get option value by option key
 */
function woopanel_get_option( $id ){
	if( isset(woopanel_all_options()[$id]) ) {
		return woopanel_all_options()[$id];
	}
	return false;
}

/**
 * Set option value
 */
function woopanel_set_options( $args = array() ){
    $options_name = 'woopanel_options';
    $options = get_option( $options_name );
    $args = wp_parse_args( $args, $options );
    update_option( $options_name, $args );
}

/**
 * Get field type by id
 */
function woopanel_get_option_type( $id ){
	global $woopanel_options;

	foreach ( $woopanel_options as $section ) {
		foreach ( $section['fields'] as $fields ) {
			if( $fields['id'] === $id ) return $fields['type'];
		}
	}
	return null;
}

/**
 * Save all options
 */
function woopanel_save_options() {
    global $woopanel_options;

    if ( is_woopanel_endpoint_url('settings') && ( isset($_POST['save']) || isset($_POST['save1']) ) ) {
        $user_ID = get_current_user_id();
        $options = $_POST;
        $seller_options = array();

        if ($user_ID !== absint($options['user_ID'])) return false;

        unset($options['user_ID']);
        unset($options['save']);
        unset($options['save1']);
        unset($options['_wpnonce']);
        unset($options['_wp_http_referer']);

        foreach ( $woopanel_options as $section ) {
            if($section['type'] == 'personal') {
                foreach ( $section['fields'] as $field ){
                    $seller_options[$field['id']] = $options[$field['id']];
                }
            }
        }

        if( isset($_POST['shop_layout']) ) {
            update_user_meta($user_ID, '_shop_layout', $seller_options['shop_layout']);
            unset($seller_options['shop_layout']);
        }

        update_user_meta( $user_ID, 'seller_options', $seller_options );

        /**
         * Save data user settings
         *
         * @since 1.0.0
         * @hook woopanel_save_settings
         * @param {int} $user_id
         */
        do_action( 'woopanel_save_settings', $user_ID );

        wpl_add_notice( "settings", esc_html__('Settings saved.', 'woopanel' ), 'success' );
    }
}

add_action('woopanel_init', 'woopanel_save_options');