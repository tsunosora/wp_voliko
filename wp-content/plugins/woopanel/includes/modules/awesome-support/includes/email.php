<?php
class NBT_Solutions_Awesome_Email {
	function __construct() {
		add_action( 'woopanel_dashboard_awesome-support-email_endpoint', array( $this, 'email_settings' ) );
	}

	public function email_settings() {
		$email_create_ticket = get_option('woopanel_email_create_ticket');
		$email_reply_ticket = get_option('woopanel_email_reply_ticket');


		if( isset($_POST['save']) ) {
			$nonce = $_REQUEST['_wpnonce'];
			if ( ! wp_verify_nonce( $nonce, 'update_options' ) ) {
				die( __( 'Security check', 'textdomain' ) ); 
			} else {
			    // Do stuff here.
			    $data = [];
			    $allowed_html = woopanel_wses_allowed_menu_html();

			    if( isset($_POST['email_create_ticket']) && ! empty($_POST['email_content_create_ticket']) ) {
			    	$email_create_ticket = $data['email_create_ticket'] = wp_kses( $_POST['email_content_create_ticket'], $allowed_html );
			    }else {
			    	$email_create_ticket = 0;
			    	delete_option('woopanel_email_create_ticket');
			    }

			    if( isset($_POST['email_reply_ticket']) && ! empty($_POST['email_content_reply_ticket']) ) {
			    	$email_reply_ticket = $data['email_reply_ticket'] = wp_kses( $_POST['email_content_reply_ticket'], $allowed_html );
			    }else {
			    	$email_reply_ticket = 0;
			    	delete_option('woopanel_email_reply_ticket');
			    }

			    if( ! empty($data) ) {
				    foreach ($data as $key => $value) {
				    	update_option('woopanel_' .  $key, $value);
				    }
			    }
			}
		}

		$fields = $this->fields();

		$fields['email_create_ticket']['value'] = empty($email_create_ticket) ? 0 : 1;
		$fields['email_content_create_ticket']['value'] = empty($email_create_ticket) ? '' : $email_create_ticket;

		$fields['email_reply_ticket']['value'] = empty($email_reply_ticket) ? 0 : 1;
		$fields['email_content_reply_ticket']['value'] = empty($email_reply_ticket) ? '' : $email_reply_ticket;

	    include NBT_AWESOME_SUPPORT_PATH . 'templates/email-template.php';
	}

	public function fields() {
		return [
            'email_create_ticket' => array(
                'id'       => 'email_create_ticket',
                'type'     => 'checkbox',
                'label'    => '&nbsp;',
                'description' => esc_html__( 'Enable send email template when create new ticket', 'woopanel' ),
                'default'	  => 1
            ),
            'email_reply_ticket' => array(
                'id'       => 'email_reply_ticket',
                'type'     => 'checkbox',
                'label'    => '&nbsp;',
                'description' => esc_html__( 'Enable send email template when reply ticket', 'woopanel' ),
                'default'	  => 1
            ),
            'email_content_create_ticket' => array(
                'id'       => 'email_content_create_ticket',
                'type'     => 'textarea',
                'label'    => esc_html__( 'Email Create New Ticket', 'woopanel'  )
            ),
            'email_content_reply_ticket' => array(
                'id'       => 'email_content_reply_ticket',
                'type'     => 'textarea',
                'label'    => esc_html__( 'Email Reply Ticket', 'woopanel'  )
            )
		];
	}
}

new NBT_Solutions_Awesome_Email();