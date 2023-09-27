<?php

function woopanel_wses_allowed_menu_html() {
	return array (
		'a' => array(
			'href' => array(),
			'class' => array(),
		),
		'img' => array(
			'src' => array()
		),
		'ul' => array(),
		'blockquote' => array(),
		'ol' => array(),
		'li' => array(),
	    'br' => array(),
	    'em' => array(),
	    'strong' => array(),
	);
}

function woopanel_get_all_seller() {
	$args = array(
	    'role__in'    =>  NBWooCommerceDashboard::$permission,
	    'orderby' => 'user_nicename',
	    'order'   => 'ASC'
	);

	$data = [
		esc_html__('Select a seller', 'woopanel')
	];
	$users = get_users( $args );

	if( $users ) {
		foreach ($users as $key => $user) {
			$data[$user->ID] = $user->display_name;
		}
	}

	return $data;
}

/**
 * Returns difference between two dates in string format to help with debugging.
 * Formatted string will look like this sample : 0 day(s) 14 hour(s) 33 minute(s)
 * 
 * @since  4.0.5
 *
 * @param  date $firstdate 	First date in the format you get when using post->post_date to get a date from a post
 * @param  date $seconddate Second date in the format you get when using post->post_date to get a date from a post
 *
 * @return string  difference between two dates, an empty string otherwise
 */
function woopanel_get_date_diff_string( $firstdate, $seconddate ) {
	
		// Calculate difference object...
		$date1 = new DateTime( $firstdate );
		$date2 = new DateTime( $seconddate );
		$diff_dates = $date2->diff($date1) ;	
		
		$date_string = '' ;
		$date_string .= ' ' . $diff_dates->format('%d') .  __(' day(s)', 'awesome-support') ;
		$date_string .=  ' ' . $diff_dates->format('%h') .  __(' hour(s)', 'awesome-support') ;								
		$date_string .=  ' ' . $diff_dates->format('%i') .  __(' minute(s)', 'awesome-support') ;
		
		return $date_string ;
	
}

/**
 * Get the list of user profile data to display in the user profile metabox
 *
 * @since 3.3
 *
 * @param int $ticket_id Current ticket iD
 *
 * @return array
 */
function woopanel_user_profile_get_contact_info( $ticket_id ) {

	$data = array(
		'name',
		'role',
		'email',
	);

	return apply_filters( 'wpas_user_profile_contact_info', $data, $ticket_id );

}

/**
 * Get the content of a user profile data field
 *
 * User profile data fields are declared in wpas_user_profile_get_contact_info()
 *
 * @since 3.3
 *
 * @param string  $info      ID of the information field being displayed
 * @param WP_User $user      The current user object (the creator of the ticket)
 * @param int     $ticket_id ID of the current ticket
 *
 * @return void
 */
function woopanel_user_profile_contact_info_contents( $info, $user, $ticket_id ) {

	if ( !$user ) {
		return;
	}

	switch ( $info ) {

		case 'name':
			echo apply_filters( 'wpas_user_profile_contact_name', $user->data->display_name, $user, $ticket_id );
			break;

		case 'role':
			echo wp_kses_post( sprintf( __( 'Support User since %s', 'awesome-support' ), '<strong>' . date( get_option( 'date_format' ), strtotime( $user->data->user_registered ) ) . '</strong>' ) );
			break;

		case 'email':
			printf( '<a href="mailto:%1$s">%1$s</a>', $user->data->user_email );
			break;

		default:
			do_action( 'wpas_user_profile_info_' . $info, $user, $ticket_id );
			break;

	}

}

function woopanel_ticket_status() {
	$data = [
		'any' => esc_html__('All States', 'awesome-support' ),
		'open' => esc_html__('Open', 'awesome-support' ),
		'closed' => esc_html__('Closed', 'awesome-support' )
	];

	return $data;
}

function woopanel_ticket_send_email( $to, $subject, $body ) {
	$headers = array('Content-Type: text/html; charset=UTF-8');
	wp_mail( $to, $subject, $body, $headers );
}

function woopanel_ticket_email_content( $content, $ticket_id, $data ) {
	$content = str_replace('{ticket_id}', $ticket_id, $content);
	$content = str_replace('{content}', wpautop($data->post_content), $content);

	return $content;
}