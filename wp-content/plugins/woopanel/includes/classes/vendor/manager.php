<?php

/**
 * Vendor Manager Class
 *
 * @since 2.6.10
 */
class WooPanel_Store {

    /**
     * Total vendors found
     *
     * @var integer
     */
    private $total_users;

    private $store_name;

    public $data;

    /**
     * Get all vendors
     *
     * @since 2.8.0
     *
     * @param  array  $args
     *
     * @return array
     */
    public function __construct( $store_name = '' ) {
        global $wpdb;

        $prefix = WOOPANEL_STORE_LOCATOR_PREFIX;

        if( is_numeric($store_name) ) {
            $sql = $wpdb->prepare( "SELECT * FROM {$prefix}stores WHERE id = %d", $store_name );
        }else {
            $sql = $wpdb->prepare( "SELECT * FROM {$prefix}stores WHERE name = %s", $store_name );
        }

        $result = $wpdb->get_row( $sql );

        if( $result ) {
            $this->data = $result;
        }
    }

    /**
     * Get total user according to query
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_total() {
        return $this->total_users;
    }

    /**
     * Get single vendor data
     *
     * @param object|integer $vendor
     *
     * @return object|vednor instance
     */
    public function get( $store_name ) {
        return new self( $store_name );
    }

    /**
     * Create a vendor
     *
     * @param array $data
     *
     * @return Dokan_Vendor|WP_Error on failure
     */
    public function get_url($path = '') {
        global $admin_options;

        if( $path ) {
            $path = '/' . $path;
        }

        return home_url( sprintf('%s/%s%s', $admin_options->options['profile_store_permalink'], $this->data->name, $path ) );
    }

    /**
     * Create a vendor
     *
     * @param array $data
     *
     * @return Dokan_Vendor|WP_Error on failure
     */
    public function get_html_logo( $size = 'full' ) {
        $store_logo_url    = wp_get_attachment_image_src( $this->data->logo_id, $size );
        $store_logo_url    = is_array( $store_logo_url ) ? esc_attr( $store_logo_url[0] ) : esc_attr( $store_logo_url );

        return empty($store_logo_url) ? get_avatar( $store->user_id, $size ) : sprintf('<img src="%s" />', $store_logo_url);
    }

    /**
     * Create a vendor
     *
     * @param array $data
     *
     * @return Dokan_Vendor|WP_Error on failure
     */
    public function get_banner_url( $image_size = 'full' ) {
        $store_banner_url =  $this->data->banner_id ? wp_get_attachment_image_src( $this->data->banner_id, $image_size ) : WOODASHBOARD_URL . '/assets/images/default-store-banner.png';

        return is_array( $store_banner_url ) ? esc_attr( $store_banner_url[0] ) : esc_attr( $store_banner_url );
    }

    public function get_store_name() {
        return $this->data->title;
    }

    public function get_phone() {
        return $this->data->phone;
    }
    
    public function show_email() {
        return $this->data->phone;
    }

    public function get_id() {
        return $this->data->id;
    }

    public function get_intro() {
        return $this->data->intro;
    }

    public function get_tos() {
        return $this->data->tos;
    }

    public function get_address() {
        return sprintf(
            '%s, %s, %s',
            $this->data->street,
            $this->data->city,
            $this->data->state
        );
    } 
}
