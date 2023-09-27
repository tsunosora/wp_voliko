<?php
function woopanel_tera_wallet_fields() {
	$users = [];
	$_users = get_users( array( 'fields' => array( 'display_name', 'user_email', 'ID' ) ) );

	if( ! empty($_users) ) {
		foreach ($_users as $key => $u) {
			$users[$u->ID] = $u->display_name .' ('. $u->user_email .')';
		}
	}


	return [
	    'wallet_topup' => [
	        'menu_title' => esc_html__( 'Wallet Topup', 'woopanel' ),
	        'title'      => esc_html__( 'General Settings', 'woopanel' ),
	        'desc'       => '',
	        'parent'     => '',
	        'icon'       => '',
	        'type'       => 'personal',
	        'fields'     => array(
	            array(
	                'id'       => 'wallet_topup_amount',
	                'type'     => 'text',
	                'title'    => esc_html__( 'Amount', 'woopanel'  ),
	                'placeholder' => esc_html__('Enter amount...', 'woopanel' )
	            )
	        )
	    ],
	    'wallet_transfer' => [
	        'menu_title' => esc_html__( 'Wallet Transfer', 'woopanel' ),
	        'title'      => esc_html__( 'Wallet Transfer', 'woopanel' ),
	        'desc'       => '',
	        'parent'     => '',
	        'icon'       => '',
	        'type'       => 'personal',
	        'fields'     => array(
				array(
					'id'                => 'woo_wallet_transfer_user_id',
					'type'				=> 'select',
					'title'             => esc_html__( 'Select whom to transfer (Email)', 'woopanel' ),
					'wrapper_class'		=> 'hide_if_grouped hide_if_external',
					'input_class' => array('select2-tags-ajax'),
					'options'     => $users
				),
	            array(
	                'id'       => 'woo_wallet_transfer_amount',
	                'type'     => 'text',
	                'title'    => esc_html__( 'Amount', 'woopanel'  ),
	                'placeholder' => esc_html__('Enter amount...', 'woopanel' )
	            ),
	            array(
	                'id'       => 'woo_wallet_transfer_note',
	                'type'     => 'textarea',
	                'title'    => esc_html__( 'What\'s this for', 'woopanel'  )
	            )
	        )
	    ],
	];
}