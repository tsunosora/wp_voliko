<?php
/**
 * This class for Settings Page in WooPanel
 *
 * @package WooPanel_Modules
 */
class NBT_Geo_Location_Settings {

    /**
     * Storage field setting
     *
     * @return array
     */
    static $options;
    
    /**
     * Set field setting
     *
     * @return array
     */
    public static function options() {
        global $current_user;

        self::$options = array(
            'menu_title' => esc_html__( 'Geo Location', 'woopanel' ),
            'title'      => esc_html__( 'Geo Location Settings', 'woopanel' ),
            'desc'       => '',
            'parent'     => '',
            'icon'       => '',
            'type'       => 'personal',
            'fields'     => array(
                array(
                    'id'       => 'geo_application_id',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Application ID', 'woopanel'  ),
                    'placeholder' => esc_html__('Enter your Application ID of Here.com', 'woopanel' ),
                    'default'   => 'uPpJlH7GwJ5VFivyyrjn',
                    'value' => get_user_meta( $current_user->ID, 'geo_application_id', true),
                    'description' => sprintf( esc_html__( '%s to get Application ID of HereMaps.', 'woopanel' ), sprintf( '<a href="https://youtu.be/MPUHN6gBzMw" target="_blank">%s</a>', esc_html__('Click here', 'woopanel' ) ) )
                ),
                array(
                    'id'       => 'geo_application_code',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Application Code', 'woopanel'  ),
                    'placeholder' => esc_html__('Enter your Application Code of Here.com', 'woopanel' ),
                    'default' => 'jt713YZorNYpkHhGCkelOQ',
                    'value' => get_user_meta( $current_user->ID, 'geo_application_code', true),
                    'description' => sprintf( esc_html__( '%s to get Application ID of HereMaps.', 'woopanel' ), sprintf( '<a href="https://youtu.be/MPUHN6gBzMw" target="_blank">%s</a>', esc_html__('Click here', 'woopanel' ) ) )
                ),
                array(
                    'id'       => 'user_geo_location',
                    'type'     => 'map',
                    'title'    => esc_html__( 'Store Address', 'woopanel'  ),
                    'placeholder' => esc_html__('Enter your store address here.', 'woopanel' ),
                    'value' => get_user_meta( $current_user->ID, 'user_geo_location', true)
                ),
                array(
                    'id'       => 'woopanel_map_lat',
                    'type'     => 'hidden',
                    'title'    => esc_html__( 'Lat', 'woopanel'  ),
                    'value' => get_user_meta( $current_user->ID, 'woopanel_map_lat', true)
                ),
                array(
                    'id'       => 'woopanel_map_lng',
                    'type'     => 'hidden',
                    'title'    => esc_html__( 'Lng', 'woopanel'  ),
                    'value' => get_user_meta( $current_user->ID, 'woopanel_map_lng', true)
                ),
                'user_location_tab' => array(
                    'title'    => esc_html__( 'Product Location Tab', 'woopanel' ),
                    'desc'     => '',
                    'id'       => 'user_location_tab',
                    'type'     => 'checkbox',
                    'default'  => 'on',
                    'value' => get_user_meta( $current_user->ID, 'user_location_tab', true)
                )
            )
        );

        // Only for admin
        if( current_user_can('administrator') ) {
            self::$options['fields']['user_location_tab']['value'] = get_option('user_location_tab');

            self::$options['fields'][] = array(
                'title'    => esc_html__( 'Show Location in Shop page', 'woopanel' ),
                'desc'     => '',
                'id'       => 'show_location_shop',
                'type'     => 'checkbox',
                'default'  => 'on',
                'value' => get_option('show_location_shop')
            );

            self::$options['fields'][] = array(
                'title'    => esc_html__( 'Show Location in Store List page', 'woopanel' ),
                'desc'     => '',
                'id'       => 'show_location_storelist',
                'type'     => 'checkbox',
                'default'  => 'on',
                'value' => get_option('show_location_storelist')
            );
        }

        return self::$options;
    }

    /**
     * Set field setting
     *
     * @return array
     */
    public static function settings() {
        return isset(self::$options['fields']) ? self::$options['fields'] : array();
    }

    /**
     * Return setting fields
     *
     * @return array
     */
    public static function get_settings( $fields = array() ) {
        $fields['geo_location'] = self::options();

        return $fields;
    }

    /**
     * Save setting fields
     *
     * @return array
     */
    public static function save_settings() {
        global $current_user;

        if ( is_woopanel_endpoint_url('settings') && ( isset($_POST['save']) || isset($_POST['save1']) ) ) {

            update_user_meta( $current_user->ID, 'geo_application_id', woopanel_clean($_POST['geo_application_id']) );
            update_user_meta( $current_user->ID, 'geo_application_code', woopanel_clean($_POST['geo_application_code']) );

            update_user_meta( $current_user->ID, 'user_geo_location', woopanel_clean($_POST['user_geo_location']) );
            update_user_meta( $current_user->ID, 'woopanel_map_lat', woopanel_clean($_POST['woopanel_map_lat']) );
            update_user_meta( $current_user->ID, 'woopanel_map_lng', woopanel_clean($_POST['woopanel_map_lng']) );
            
            $user_location_tab = isset($_POST['user_location_tab']) ? woopanel_clean($_POST['user_location_tab']) : '';
            $show_location_shop = isset($_POST['show_location_shop']) ? woopanel_clean($_POST['show_location_shop']) : '';
            $show_location_storelist = isset($_POST['show_location_storelist']) ? woopanel_clean($_POST['show_location_storelist']) : '';

            if( current_user_can('administrator') ) {
                update_option( 'user_location_tab', $user_location_tab );
                update_option( 'show_location_shop', $show_location_shop );
                update_option( 'show_location_storelist', $show_location_storelist );
            }else {
                update_user_meta( $current_user->ID, 'user_location_tab', $user_location_tab );
            }
        }
    }
}

add_filter('woopanel_options', array( 'NBT_Geo_Location_Settings', 'get_settings'), 99, 1);
add_action( 'woopanel_init', array( 'NBT_Geo_Location_Settings', 'save_settings') );