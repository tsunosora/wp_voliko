<?php
/**
 * This class for Settings Page in WooPanel
 *
 * @package WooPanel_Modules
 */
class NBT_Smart_Invoice_Settings {

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
            'menu_title' => esc_html__( 'Info Contact', 'woopanel' ),
            'title'      => esc_html__( 'Info Contact Settings', 'woopanel' ),
            'desc'       => '',
            'parent'     => '',
            'icon'       => '',
            'type'       => 'personal',
            'fields'     => array(
                array(
                    'id'       => 'contact_name',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Company Name', 'woopanel'  ),
                    'value' => get_user_meta( $current_user->ID, 'contact_name', true)
                ),
                array(
                    'id'       => 'contact_address_line1',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Address Line 1', 'woopanel'  ),
                    'value' => get_user_meta( $current_user->ID, 'contact_address_line1', true)
                ),
                array(
                    'id'       => 'contact_address_line2',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Address Line 2', 'woopanel'  ),
                    'value' => get_user_meta( $current_user->ID, 'contact_address_line2', true)
                ),
                array(
                    'id'       => 'contact_phone',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Phone', 'woopanel'  ),
                    'value' => get_user_meta( $current_user->ID, 'contact_phone', true)
                ),
                array(
                    'id'       => 'contact_email',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Email', 'woopanel'  ),
                    'value' => get_user_meta( $current_user->ID, 'contact_email', true)
                ),
                array(
                    'id'       => 'contact_website',
                    'type'     => 'text',
                    'title'    => esc_html__( 'Website', 'woopanel'  ),
                    'value' => get_user_meta( $current_user->ID, 'contact_website', true)
                )
            )
        );



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
        $fields['info_contact'] = self::options();

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

            update_user_meta( $current_user->ID, 'contact_name', woopanel_clean($_POST['contact_name']) );
            update_user_meta( $current_user->ID, 'contact_address_line1', woopanel_clean($_POST['contact_address_line1']) );
            update_user_meta( $current_user->ID, 'contact_address_line2', woopanel_clean($_POST['contact_address_line2']) );
            update_user_meta( $current_user->ID, 'contact_phone', woopanel_clean($_POST['contact_phone']) );
            update_user_meta( $current_user->ID, 'contact_email', woopanel_clean($_POST['contact_email']) );
            update_user_meta( $current_user->ID, 'contact_website', woopanel_clean($_POST['contact_website']) );

        }
    }
}

add_filter('woopanel_options', array( 'NBT_Smart_Invoice_Settings', 'get_settings'), 99, 1);
add_action( 'woopanel_init', array( 'NBT_Smart_Invoice_Settings', 'save_settings') );