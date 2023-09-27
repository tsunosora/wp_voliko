<?php
class NBT_Notification_Frontend{
	function __construct() {
		add_action( 'wp_ajax_nopriv_nbtpn_notification', array($this, 'nbtpn_notification') );
		add_action( 'wp_ajax_nbtpn_notification', array($this, 'nbtpn_notification') );		
	}
	
	public function nbtpn_notification(){	
		$options_settings = get_option( NBT_NOTI_SETTINGS );	
		if ( isset($options_settings['nbt_product_notification_form_success']) && $options_settings['nbt_product_notification_form_success'] ) {
			$options_success = $options_settings['nbt_product_notification_form_success'];
		}else{
			$options_success = 'Thank you. We will notify you when the product is in stock.';
		}

		if ( isset($options_settings['nbt_product_notification_form_error']) && $options_settings['nbt_product_notification_form_error'] ) {
			$options_error = $options_settings['nbt_product_notification_form_error'];
		}else{
			$options_error = 'Invalid email address.';
		}
		$notice = array();
		$id = absint($_REQUEST['product_id'] );		
		$the_email    = $_REQUEST['email'];			
		if ( filter_var($the_email, FILTER_VALIDATE_EMAIL) && is_numeric($id) ) {
		        //self::instock_email_notification_save_email($the_email, $id);
		    global $wpdb;
		    $table_name = $wpdb->prefix . "instock_email_notification";
		    $date = date('d-m-Y h:i:s');
		    $wpdb->insert( $table_name, array( 'date' => $date, 'user_email' => $the_email, 'product_id' => $id, 'status' => 0), array( '%s', '%s', '%d', '%d' ) );
		        
		    $json['complete'] = true;
			$json['msg'] = $this->show_notice($options_success, 'message');
		        
		} else {		    	
		    	$json['msg'] = $this->show_notice($options_error, 'error');

		        
		}
		echo wp_json_encode($json, TRUE);
		wp_die();
		
	}	

	public function show_notice($msg, $class){
		$html = '<ul class="woocommerce-'.$class.'" style="display: block;">';
		if(is_array($msg)){
			foreach ($msg as $m) {
				$html .= '<li>'.$m.'</li>';
			}
		}else{
			$html .= '<li>'.$msg.'</li>';
		}
		$html .= '</ul>';
		return $html;
	}
}
new NBT_Notification_Frontend();