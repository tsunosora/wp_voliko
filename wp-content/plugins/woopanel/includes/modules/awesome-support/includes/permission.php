<?php
/**
 * Set add cap
 */
add_action('init', 'woopanel_awsome_add_cap');
function woopanel_awsome_add_cap() {
	global $current_user;

	$user = new WP_User( $current_user->ID );
	foreach (woopanel_awsome_get_capabilities() as $cat) {
		# code...
		$user->add_cap( $cat );
	}
}

add_filter('wpas_agent_submit_front_end', function() {
	return true;
});

//add_filter( 'wpas_can_view_ticket', 'woopanel_awsome')
function woopanel_awsome_get_capabilities() {
	return [
		'create_ticket',
		'edit_ticket',
		'view_ticket'
	];
}