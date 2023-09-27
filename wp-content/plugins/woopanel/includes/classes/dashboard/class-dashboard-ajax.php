<?php

/**
 * WooPanel Dashboard Ajax class
 *
 * @package WooPanel_Dashboard_Ajax
 */
class WooPanel_Dashboard_Ajax {
    /**
     * Set order status
     */
    private $order_status = array( 'completed', 'processing', 'on-hold' );
    
    /**
     * WooPanel_Dashboard_Ajax Constructor.
     */
	public function __construct() {
		$this->hooks();
	}

    /**
     * Call hooks
     */
	public function hooks() {
        /**
         * Fires authenticated Ajax actions for logged-in users, load chart orders.
         *
         * @since 1.0.0
         * @hook wp_ajax_woopanel_get_chart_orders
         * @param null
         */
        add_action( 'wp_ajax_woopanel_get_chart_orders', array($this, 'get_chart_orders') );

        /**
         * Fires authenticated Ajax actions for logged-in users, load chart amounts.
         *
         * @since 1.0.0
         * @hook wp_ajax_woopanel_get_chart_amounts
         * @param null
         */
        add_action( 'wp_ajax_woopanel_get_chart_amounts', array($this, 'get_chart_amounts') );
	}

    /**
     * Call ajax get chart order
     */
	public function get_chart_orders() {
		$json = array();

		$filter = sanitize_text_field( $_POST['filter'] );
		$status = sanitize_text_field( $_POST['status'] );
        $range  = sanitize_text_field( $_POST['range'] );

        $sales_by_date  = new WooPanel_Report_Order();

        if( ! empty($status) ) {
            if( $status == 'all' ) {
                $sales_by_date->order_status = $this->order_status;
            }else {
                $sales_by_date->order_status = array($status);
            }
        }

        if( !empty($range) ) {
            $range_day = explode('-', $range);

            if( count($range_day) == 2) {
                $sales_by_date->start_date = strtotime($range_day[0]);
                $sales_by_date->end_date = strtotime($range_day[1]);
            }

        }else {
            if( $filter == 'this-week' ) {
                $sales_by_date->start_date = strtotime( 'monday this week' );
                $sales_by_date->end_date = strtotime( 'sunday this week' );
            }
            
            if( $filter == 'last-week' ) {
                $sales_by_date->start_date = strtotime( 'monday last week' );
                $sales_by_date->end_date = strtotime( 'sunday last week' );
            }
            
            if( $filter == 'this-month' ) {
                $sales_by_date->start_date = strtotime("first day of this month");
                $sales_by_date->end_date = strtotime("last day of this month");
            }
            
            if( $filter == 'last-month' ) {
                $sales_by_date->start_date = strtotime("first day of last month");
                $sales_by_date->end_date = strtotime("last day of last month");
            }
        }

        $sales_by_date->chart_groupby  = 'day';
        $sales_by_date->group_by_query = 'YEAR(posts.post_date), MONTH(posts.post_date), DAY(posts.post_date)';

        wp_send_json( $sales_by_date->get_report_status_data() );
    }
    
    /**
     * Call ajax get chart amount
     */
    public function get_chart_amounts() {
        $json = array();

		$filter = sanitize_text_field( $_POST['filter'] );
		$status = sanitize_text_field( $_POST['status'] );
        $range  = sanitize_text_field( $_POST['range'] );

        $sales_by_date  = new WooPanel_Report_Order();

        if( ! empty($status) ) {
            if( $status == 'all' ) {
                $sales_by_date->order_status = $this->order_status;
            }else {
                $sales_by_date->order_status = array($status);
            }
        }

        if( !empty($range) ) {
            $range_day = explode('-', $range);

            if( count($range_day) == 2) {
                $sales_by_date->start_date = strtotime($range_day[0]);
                $sales_by_date->end_date = strtotime($range_day[1]);
            }

        }else {
            if( $filter == 'this-week' ) {
                $sales_by_date->start_date = strtotime( 'monday this week' );
                $sales_by_date->end_date = strtotime( 'sunday this week' );
            }
            
            if( $filter == 'last-week' ) {
                $sales_by_date->start_date = strtotime( 'monday last week' );
                $sales_by_date->end_date = strtotime( 'sunday last week' );
            }
            
            if( $filter == 'this-month' ) {
                $sales_by_date->start_date = strtotime("first day of this month");
                $sales_by_date->end_date = strtotime("last day of this month");
            }
            
            if( $filter == 'last-month' ) {
                $sales_by_date->start_date = strtotime("first day of last month");
                $sales_by_date->end_date = strtotime("last day of last month");
            }
        }

        $sales_by_date->chart_groupby  = 'day';
        $sales_by_date->group_by_query = 'YEAR(posts.post_date), MONTH(posts.post_date), DAY(posts.post_date)';

        wp_send_json($sales_by_date->get_report_filter_data());
    }
}

/**
 * Returns the main instance of WooPanel_Dashboard_Ajax.
 *
 * @since  1.0.0
 * @return WooPanel_Dashboard_Ajax
 */
new WooPanel_Dashboard_Ajax();