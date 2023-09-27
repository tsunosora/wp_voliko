<?php
/**
 * Order Delivery Date for WooCommerce Lite
 *
 * Processes performed on the frontend checkout page
 *
 * @author      Tyche Softwares
 * @package     Order-Delivery-Date-Lite-for-WooCommerce/Frontend/Checkout-Page-Processes
 * @since       1.5
 */

/**
 * Class for adding processes to be performed on the checkout page
 */

class nbtodd_process {



    /**
     * Adds hidden fields and delivery date field on the frontend checkout page
     * 
     * @hook woocommerce_after_checkout_billing_form
     * @hook woocommerce_after_checkout_shipping_form
     * @hook woocommerce_before_order_notes
     * @hook woocommerce_after_order_notes
     *
     * @globals array $nbtodd_weekdays Weekdays array
     *
     * @param resource $checkout WooCommerce checkout object
     * @since 1.5
     */

	public static function nbtodd_my_custom_checkout_field( $checkout = '' ) {
        global $nbtodd_weekdays; 
        $options = get_option( NBTODD_SETTINGS ); 
        
        
        if ( $options['nbt_order-delivery-date_enable']=='1' ) {
        	$var = '';

            $first_day_of_week = '1';
            if( $options['nbt_order-delivery-date_start_of_week'] != '' ) {
                $first_day_of_week = $options['nbt_order-delivery-date_start_of_week'];
            }
            $var .= '<input type="hidden" name="orddd_first_day_of_week" id="orddd_first_day_of_week" value="' . $first_day_of_week . '">';
            
            $var .= '<input type="hidden" name="nbtodd_delivery_date_format" id="nbtodd_delivery_date_format" value="'. $options['nbt_order-delivery-date_date_format'] . '">';

            $field_note_text = $options['nbt_order-delivery-date_field_note'];
	        $field_note_text = str_replace( array( "\r\n", "\r", "\n" ), "<br/>", $field_note_text );
	        if( strpos( $field_note_text, '"' ) !== false ) {
	            $var .= '<input type="hidden" name="nbtodd_field_note" id="nbtodd_field_note" value=\'' . $field_note_text . '\'>';
	        } else {
	            $var .= '<input type="hidden" name="nbtodd_field_note" id="nbtodd_field_note" value="' . $field_note_text . '">';
	        }

            
            for ($i=0; $i < 7 ; $i++) { 
                if($options['nbt_order-delivery-date_weekday_'.$i.''] =='1'){
                    $var .= '<input type="hidden" id="nbtodd_weekday_' . $i . '" value="checked">';
                }else{
                    $var .= '<input type="hidden" id="nbtodd_weekday_' . $i . '" value="">';
                }
            }
            
            $min_date = '';
            $current_time = current_time( 'timestamp' );
            
            /*if(isset($options['nbt_order-delivery-date_minimum_order_days']) && '' !=  $options['nbt_order-delivery-date_minimum_order_days'] ) {
                
                $minimum_delivery_time_nbtodd = $options['nbt_order-delivery-date_minimum_order_days'];
            } else {*/
                $minimum_delivery_time_nbtodd = 0;
            /*}*/

            $delivery_time_seconds = $minimum_delivery_time_nbtodd *60 *60;
            $cut_off_timestamp = $current_time + $delivery_time_seconds;
            $cut_off_date = date( "d-m-Y", $cut_off_timestamp );
            $min_date = date( "j-n-Y", strtotime( $cut_off_date ) );
            
            $var .= '<input type="hidden" name="nbtodd_minimumOrderDays" id="nbtodd_minimumOrderDays" value="' . $min_date . '">';
            $var .= '<input type="hidden" name="nbtodd_number_of_dates" id="nbtodd_number_of_dates" value="' . $options['nbt_order-delivery-date_numb_of_dates'] . '">';
        	$var .= '<input type="hidden" name="nbtodd_date_field_mandatory" id="nbtodd_date_field_mandatory" value="' . $options['nbt_order-delivery-date_field_mandatory'] . '">';
            
        	$var .= '<input type="hidden" name="nbtodd_number_of_months" id="nbtodd_number_of_months" value="' . $options['nbt_order-delivery-date_number_of_months'] . '">';
        	$var .= '<input type="hidden" name="h_deliverydate" id="h_deliverydate" value="">';
        	 
        	$lockout_days_str = '';
        	if ( $options['nbt_order-delivery-date_lockout_date_after_orders'] > 0 ) {
        	    $lockout_days_arr = array();
        	    $lockout_days = get_option( 'nbtodd_lockout_days' );
        	    if ( $lockout_days != '' && $lockout_days != '{}' && $lockout_days != '[]' ) {
        	        $lockout_days_arr = json_decode( get_option( 'nbtodd_lockout_days' ) );
        	    }
        	    foreach ( $lockout_days_arr as $k => $v ) {
        	        if ( $v->o >= $options['nbt_order-delivery-date_lockout_date_after_orders']) {
        	            $lockout_days_str .= '"' . $v->d . '",';
        	        }
        	    }
        	    $lockout_days_str = substr( $lockout_days_str, 0, strlen( $lockout_days_str ) -1 );
        	}
        	$var .= '<input type="hidden" name="nbtodd_lockout_days" id="nbtodd_lockout_days" value=\'' . $lockout_days_str . '\'>';

            //fetch holidays
            $holidays_arr = array();
            //$holidays = get_option( 'nbtodd_holidays' );
            /*hn*/
            $hcount = count($options['nbt_order-delivery-date_holiday']);
            $ik=1;
            $holidays .='[';
            foreach ($options['nbt_order-delivery-date_holiday'] as $key => $value) {
                $holidays .='{"n":"'.$value['nbt_order-delivery-date_holiday_name'].'","d":"'.$value['nbt_order-delivery-date_holiday_date'].'"}';
                if($ik < $hcount){
                    $holidays.=',';
                }
                $ik ++;
            }
            $holidays .=']';
            //var_dump($ks);
            /*e-hn*/
            /*echo '<pre>';
            var_dump($holidays);
            echo '</pre>';*/
            if ( $holidays != '' && $holidays != '{}' && $holidays != '[]' && $holidays != 'null' ) {
                $holidays_arr = json_decode( $holidays );
            }
            $holidays_str = "";
            foreach ( $holidays_arr as $k => $v ) {
                $name = str_replace( "'", "&apos;", $v->n );
                $name = str_replace( "'", "&quot;", $name );
                $holidays_str .= '"' . $name . ":" . $v->d . '",';
                
            }
            
            $holidays_str = substr( $holidays_str, 0, strlen( $holidays_str )-1 );
            $var .= '<input type="hidden" name="nbtodd_holidays" id="nbtodd_holidays" value=\'' . $holidays_str . '\'>';

            if($options['nbt_order-delivery-date_auto_first_available_date'] =='1'){
                $auto_available_date = 'on';
            }else{
                $auto_available_date = '';
            }

            $var .= '<input type="hidden" name="nbtodd_auto_populate_first_available_date" id="nbtodd_auto_populate_first_available_date" value="' . $auto_available_date . '">';

            if($options['nbt_order-delivery-date_calculate_min_time_disabled_days'] =='1'){
                $calculate_min_time = 'on';
            }else{
                $calculate_min_time = '';
            }

            $var .= '<input type="hidden" name="nbtodd_calculate_min_time_disabled_days" id="nbtodd_calculate_min_time_disabled_days" value="' . $calculate_min_time . '">';

            $current_time = current_time( 'timestamp' );
	    	$current_date = date( "j-n-Y", $current_time );
            $var .= '<input type="hidden" name="nbtodd_current_day" id="nbtodd_current_day" value="' . $current_date . '">';

            $admin_url = get_admin_url();
            $admin_url_arr = explode( "://", $admin_url );
            $home_url = get_home_url();
            $home_url_arr = explode( "://", $home_url );
            if( $admin_url_arr[ 0 ] != $home_url_arr[ 0 ] ) {
                $admin_url_arr[ 0 ] = $home_url_arr[ 0 ];
                $ajax_url = implode( "://", $admin_url_arr );
            } else {
                $ajax_url = $admin_url;
            }

            $var .= '<input type="hidden" name="orddd_admin_url" id="orddd_admin_url" value="' . $ajax_url . '">';

            //Session fields
            if( isset( $_SESSION[ 'e_deliverydate_lite' ] ) ) {
                $e_deliverydate_session = $_SESSION[ 'e_deliverydate_lite' ];
                $h_deliverydate_session =$_SESSION[ 'h_deliverydate_lite' ];
                $var .= '<input type="hidden" name="h_deliverydate_lite_session" id="h_deliverydate_lite_session" value="' . $h_deliverydate_session . '">';
                $var .= '<input type="hidden" name="e_deliverydate_lite_session" id="e_deliverydate_lite_session" value="' . $e_deliverydate_session . '">';
            }

			echo $var;

            $delivery_enabled = NBT_Solutions_Order_Delivery_Date::nbtodd_is_delivery_enabled();
            $is_delivery_enabled = 'yes';
            if ( $delivery_enabled == 'no' ) {
                $is_delivery_enabled = 'no';
            }
            
            if( $is_delivery_enabled == 'yes' ) {
                $validate_wpefield = false;
                if ( $options['nbt_order-delivery-date_field_mandatory'] == '1' ) {
                    $validate_wpefield = true;
                }
                if( '' == $checkout ) {
                    woocommerce_form_field( 'e_deliverydate', array(
                        'type'              => 'text',
                        'label'             => $options['nbt_order-delivery-date_field_label'],
                        'required'          => $validate_wpefield,
                        'placeholder'       => $options['nbt_order-delivery-date_field_placeholder'],
                        'custom_attributes' => array( 'style'=>'cursor:text !important;'),
                        'class' => array( 'form-row-wide' )
                    ) );
                } else {
                    woocommerce_form_field( 'e_deliverydate', array(
                        'type'              => 'text',
                        'label'             => $options['nbt_order-delivery-date_field_label'],
                        'required'          => $validate_wpefield,
                        'placeholder'       => $options['nbt_order-delivery-date_field_placeholder'],
                        'custom_attributes' => array( 'style'=>'cursor:text !important;'),
                        'class' => array( 'form-row-wide' ) 
                    ),
                    $checkout->get_value( 'e_deliverydate' ) );
                }
            }
        }
    }
    
    /**
     * Adds the selected delivery date into the php session variable
     * 
     * @since 1.5
     */
    public static function nbtodd_update_delivery_session() {
        var_dump($_POST[ 'e_deliverydate' ]);
        $_SESSION[ 'e_deliverydate_lite' ] = $_POST[ 'e_deliverydate' ];
        $_SESSION[ 'h_deliverydate_lite' ] = $_POST[ 'h_deliverydate' ];
        
        $_POST[ 'h_deliverydate' ] = "";
        $_POST[ 'e_deliverydate' ] = "";
    }

    /**
     * Saves the selected delivery date into the post meta table 
     *
     * @hook woocommerce_checkout_update_order_meta
     *
     * @param int $order_id Order ID
     * @since 1.5
     */
    public static function nbtodd_my_custom_checkout_field_update_order_meta( $order_id ) {
        $options = get_option( NBTODD_SETTINGS );
        if ( isset( $_POST['e_deliverydate'] ) && $_POST['e_deliverydate'] != '' ) {
            if( isset( $_POST[ 'h_deliverydate' ] ) ) {	    
                $delivery_date = $_POST['h_deliverydate'];
            } else {
                $delivery_date = '';
            }
            $date_format = 'dd-mm-y';
            
            update_post_meta( $order_id, $options['nbt_order-delivery-date_field_label'] , esc_attr( $_POST['e_deliverydate'] ) );
	    
            $timestamp = NBT_Solutions_Order_Delivery_Date::nbtodd_get_timestamp( $delivery_date, $date_format );
            update_post_meta( $order_id, '_nbtodd_timestamp', $timestamp );
		    nbtodd_process::nbtodd_update_lockout_days( $delivery_date );
        } else {
		    global $woocommerce;
		    $delivery_enabled = NBT_Solutions_Order_Delivery_Date::nbtodd_is_delivery_enabled();
		    $is_delivery_enabled = 'yes';
		    if ( $delivery_enabled == 'no' ) {
		        $is_delivery_enabled = 'no';
		    }
            
            if( $is_delivery_enabled == 'yes' ) {
                update_post_meta( $order_id, get_option( 'orddd_delivery_date_field_label' ), '' );
            }
        }

        if( isset( $_SESSION[ 'e_deliverydate_lite' ] ) ) {
            unset( $_SESSION[ 'e_deliverydate_lite' ] );
        }

        if( isset( $_SESSION[ 'h_deliverydate_lite' ] ) ) {
            unset( $_SESSION[ 'h_deliverydate_lite' ] );
        }
    }
    

    /**
     * Updates the lockout for the delivery date in the options table
     *
     * @param string $delivery_date Selected Delivery Date
     * @since 1.5
     */

    public static function nbtodd_update_lockout_days( $delivery_date ) {
        global $wpdb;
        
        $lockout_date = date( 'n-j-Y', strtotime( $delivery_date ) );
        $lockout_days = get_option( 'nbtodd_lockout_days' );
        if ( $lockout_days == '' || $lockout_days == '{}' || $lockout_days == '[]' ) {
            $lockout_days_arr = array();
        } else {
            $lockout_days_arr = json_decode( $lockout_days );
        }
        //existing lockout days
        $existing_days = array();
        foreach ( $lockout_days_arr as $k => $v ) {
            $orders = $v->o;
            if ( $lockout_date == $v->d ) {
                $orders = $v->o + 1;
            }
            $existing_days[] = $v->d;
            $lockout_days_new_arr[] = array( 'o' => $orders, 'd' => $v->d );
        }
        // add the currently selected date if it does not already exist
        if ( !in_array( $lockout_date, $existing_days ) ) {
            $lockout_days_new_arr[] = array( 'o' => 1,
                'd' => $lockout_date );
        }
        $lockout_days_jarr = json_encode( $lockout_days_new_arr );
        update_option( 'nbtodd_lockout_days', $lockout_days_jarr );
    }
        
    /**
     * Show delivery date in the email notification for the WooCommerce version below 2.3
     * 
     * @hook woocommerce_email_order_meta_keys
     * 
     * @param array $keys
     * @return array $keys
     * @since 1.3
     */
    public static function nbtodd_add_delivery_date_to_order_woo_deprecated( $keys ) {
        $options = get_option( NBTODD_SETTINGS );
        $label_name = __( $options['nbt_order-delivery-date_field_label'], "order-delivery-date" );
        $keys[] = $options['nbt_order-delivery-date_field_label'];
        return $keys;
    }
        
    /**
     * Display Delivery Date in Customer notification email for WooCOmmerce version 2.3 and above
     *
     * @hook woocommerce_email_order_meta_fields
     * @param array $fields
     * @param bool $sent_to_admin
     * @param resource $order
     * @return array fields
     * @since 1.3
     */
    
    public static function nbtodd_add_delivery_date_to_order_woo_new( $fields, $sent_to_admin, $order ) {
        $options = get_option( NBTODD_SETTINGS );
        if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }
        $fields[ $options['nbt_order-delivery-date_field_label'] ] = array(
           'label' => __( $options['nbt_order-delivery-date_field_label'], 'order-delivery-date' ),
           'value' => get_post_meta( $order_id, $options['nbt_order-delivery-date_field_label'], true ),
       );
       return $fields;
    }
        
    /**
     * Validate delivery date field
     *
     * @hook woocommerce_checkout_process
     * @globals resource $woocommerce WooCommerce Object
     * @since 1.4
     **/

    public static function nbtodd_validate_date_wpefield() {
        global $woocommerce;
        $delivery_enabled = NBT_Solutions_Order_Delivery_Date::nbtodd_is_delivery_enabled();
        $options = get_option( NBTODD_SETTINGS );
        $is_delivery_enabled = 'yes';
        if ( $delivery_enabled == 'no' ) {
            $is_delivery_enabled = 'no';
        }
        
        if( isset( $_POST[ 'e_deliverydate' ] ) ) {
            $delivery_date = $_POST[ 'e_deliverydate' ];
        } else {
            $delivery_date = '';
        }
         
        if( $is_delivery_enabled == 'yes' ) {
            //Check if set, if its not set add an error.
            if ( $delivery_date == '' ) {
                $message = '<strong>' . $options['nbt_order-delivery-date_field_label'] . '</strong>' . ' ' . __( 'is a required field.', 'order-delivery-date' );
                wc_add_notice( $message, $notice_type = 'error' );
            }
        }
    }
        
    /**
     * Display Delivery Date on Order Recieved Page
     *
     * @hook woocommerce_order_details_after_order_table
     *
     * @globals array nbtodd_date_formats Date Format array
     * 
     * @param resource $order
     * @since 1.0
     */
    public static function nbtodd_add_delivery_date_to_order_page_woo( $order ) {
        global $nbtodd_date_formats;
        $options = get_option( NBTODD_SETTINGS );
        if( version_compare( get_option( 'woocommerce_version' ), '3.0.0', ">=" ) ) {            
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }
        $delivery_date_formatted = NBT_Solutions_Order_Delivery_Date::nbtodd_get_order_delivery_date( $order_id );
        
        if( $delivery_date_formatted != '' ) {
            echo '<p><strong>'. $options['nbt_order-delivery-date_field_label'] . ':</strong> ' . $delivery_date_formatted . '</p>';
        }
    }
}
$nbtodd_process = new nbtodd_process();