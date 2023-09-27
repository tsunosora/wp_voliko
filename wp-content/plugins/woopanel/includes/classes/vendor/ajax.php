<?php

class WooPanel_Seller_Ajax {

    /**
     * Total vendors found
     *
     * @var integer
     */
    private $total_users;

    function __construct()
    {
        add_action('wp_ajax_wplsl_load_stores', array($this, 'load_stores'));    
        add_action('wp_ajax_nopriv_wplsl_load_stores', array($this, 'load_stores'));

        
        add_action('wp_ajax_woopanel_loadmore_store', array($this, 'loadmore_stores'));    
        add_action('wp_ajax_woopanel_loadmore_store', array($this, 'loadmore_stores'));
    }

    public function load_stores( $per_page = 9 ) {
        global $admin_options;



        if( wp_doing_ajax() ) {
            $paged = ! empty($_GET['paged']) ? absint($_GET['paged']) : 1;
            $limit = isset($_GET['per_page']) ? absint($_GET['per_page']) : 9;
        }else {
            $paged  = (int) is_front_page() ? max( 1, get_query_var( 'page' ) ) : max( 1, get_query_var( 'paged' ) );
            $limit  = absint($per_page);   
        }


        $offset = ( $paged - 1 ) * $limit;

        $all_results = $this->get_results($offset, $limit);

        $days_in_words = array('sun'=>__( 'Sun','asl_locator'), 'mon'=>__('Mon','asl_locator'), 'tue'=>__( 'Tues','asl_locator'), 'wed'=>__( 'Wed','asl_locator' ), 'thu'=> __( 'Thur','asl_locator'), 'fri'=>__( 'Fri','asl_locator' ), 'sat'=> __( 'Sat','asl_locator')) ;
        $days          = array('mon','tue','wed','thu','fri','sat','sun');


        foreach($all_results as $aRow) {

            $aRow->url = esc_url( $this->get_url($aRow->name) );
            $aRow->banner = esc_url($this->get_banner_url($aRow->banner_id));
            $aRow->avatar = $this->get_html_logo($aRow->logo_id, $aRow->user_id);

            if($aRow->open_hours) {

                $days_are   = array();
                $open_hours = json_decode($aRow->open_hours);

                foreach($days as $day) {

                    if( ! empty($open_hours->$day) ) {
                        $days_are[] = $days_in_words[$day];
                    }
                }

                $aRow->days_str = implode(', ', $days_are);

                if( ! empty($admin_options->options) ) {
                    $aRow->website = home_url(sprintf(
                        '%s/%s',
                        $admin_options->options['profile_store_permalink'],
                        $aRow->name
                    ));
                }
                
            }
        }

        if( wp_doing_ajax() ) {
            wp_send_json($all_results);
        }else {
            return $all_results;
        }
    }

    public function loadmore_stores() {
        $paged = ! empty($_POST['paged']) ? absint($_POST['paged']) : 1;
        $limit = isset($_POST['per_page']) ? absint($_POST['per_page']) : 9;

        $offset = ( $paged - 1 ) * $limit;

        $all_results = $this->get_results($offset, $limit);

        $days_in_words = array('sun'=>__( 'Sun','asl_locator'), 'mon'=>__('Mon','asl_locator'), 'tue'=>__( 'Tues','asl_locator'), 'wed'=>__( 'Wed','asl_locator' ), 'thu'=> __( 'Thur','asl_locator'), 'fri'=>__( 'Fri','asl_locator' ), 'sat'=> __( 'Sat','asl_locator')) ;
        $days          = array('mon','tue','wed','thu','fri','sat','sun');


        foreach($all_results as $aRow) {

            $aRow->url = esc_url( $this->get_url($aRow->name) );
            $aRow->banner = esc_url($this->get_banner_url($aRow->banner_id));
            $aRow->avatar = $this->get_html_logo($aRow->logo_id, $aRow->user_id);

            if($aRow->open_hours) {

                $days_are   = array();
                $open_hours = json_decode($aRow->open_hours);

                foreach($days as $day) {

                    if( ! empty($open_hours->$day) ) {
                        $days_are[] = $days_in_words[$day];
                    }
                }

                $aRow->days_str = implode(', ', $days_are);
            }
        }

        $json = array();
        if( ! empty($all_results) ) {
            $json['complete'] = true;
            $json['results'] = $all_results;
        }

        wp_send_json($json);
    }

    public function get_results( $paged, $limit ) {
        global $wpdb;

        $query = $this->get_query(0, $paged, $limit);

        $sql = implode(' ', $query);

        return $wpdb->get_results($sql);
    }

    public function get_count() {
        global $wpdb;

        $query = $this->get_query(1);
        $sql = implode(' ', $query);


        return $wpdb->get_var($sql);
    }

    public function get_query( $count = 0, $paged = 0, $limit = 10) {
        global $wpdb;

        $prefix = WOOPANEL_STORE_LOCATOR_PREFIX;

        $query = array();
        if( empty($count) ) {
            $query['select'] = "SELECT s.*, u.display_name FROM " . $prefix . "stores as s";
        }else {
            $query['select'] = "SELECT COUNT(*) as total FROM " . $prefix . "stores as s";
        }
        
        $query['join'] = "LEFT JOIN {$prefix}stores_categories as sc ON s.id = sc.store_id ";
        $query['join'] .= "LEFT JOIN {$wpdb->prefix}users as u ON s.user_id = u.ID";
        $query['where'] = "WHERE (is_disabled is NULL || is_disabled = 0) ";

        if( ! empty($_GET['store_category']) ) {
            $store_category = absint($_GET['store_category']);
            $query['where'] .= "AND sc.category_id = {$store_category}";
        }
        //$query['where'] .= "AND s.user_id > 0";
        
        if( empty($count) ) {
            $query['group_by'] = "GROUP BY s.id";
            $query['limit'] = "LIMIT {$paged}, {$limit}";
        }

        return $query;
    }

    /**
     * Create a vendor
     *
     * @param array $data
     *
     * @return Dokan_Vendor|WP_Error on failure
     */
    public function get_url($name, $path = '') {
        global $admin_options;

        if( $path ) {
            $path = '/' . $path;
        }

        return home_url( sprintf('%s/%s%s', $admin_options->options['profile_store_permalink'], $name, $path ) );
    }

    /**
     * Create a vendor
     *
     * @param array $data
     *
     * @return Dokan_Vendor|WP_Error on failure
     */
    public function get_banner_url( $banner_id, $image_size = 'full' ) {
        $store_banner_url =  $banner_id ? wp_get_attachment_image_src( $banner_id, $image_size ) : WOODASHBOARD_URL . '/assets/images/default-store-banner.png';

        return is_array( $store_banner_url ) ? esc_attr( $store_banner_url[0] ) : esc_attr( $store_banner_url );
    }


    /**
     * Create a vendor
     *
     * @param array $data
     *
     * @return Dokan_Vendor|WP_Error on failure
     */
    public function get_html_logo( $logo_id, $user_id, $size = 'full' ) {
        $store_logo_url    = wp_get_attachment_image_src( $logo_id, $size );
        $store_logo_url    = is_array( $store_logo_url ) ? esc_attr( $store_logo_url[0] ) : esc_attr( $store_logo_url );

        return empty($store_logo_url) ? get_avatar( $user_id, $size ) : sprintf('<img src="%s" />', $store_logo_url);
    }
}

$GLOBALS['seller_ajax'] = new WooPanel_Seller_Ajax();