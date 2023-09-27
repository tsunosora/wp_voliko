<?php

/**
* Replace dropdown agent WP-Admin
**/
add_filter('ticket_support_staff_dropdown', 'woopanels_ticket_support_staff_dropdown', 40, 4);
function woopanels_ticket_support_staff_dropdown($support_staff_dropdown, $post_id, $staff_id, $staff_name) {
	
	$sellers = woopanel_get_all_seller();

	$html = sprintf('<select name="%s" class="wpas-form-control" id="wpas-assignee">', 'wpas_assignee');
	foreach ($sellers as $user_id => $seller) {
		$selected_attr = ($user_id == $staff_id) ? ' selected' : '';
		$html .= "<option value='$user_id' $selected_attr>$seller</option>";
	}
	$html .= '</select>';


	return $html;
}

add_action('wpas_ticket_after_saved', 'woopanel_wpas_ticket_assigned', 20, 2);
function woopanel_wpas_ticket_assigned( $post_id ) {
	if( isset($_POST['wpas_assignee']) ) {
		update_post_meta( $post_id, '_wpas_assignee', absint($_POST['wpas_assignee']) );
	}
}

add_action("wp_ajax_woopanel_awesome_reply_ticket", "woopanel_awesome_reply_ticket");
function woopanel_awesome_reply_ticket() {
	global $current_user;

	$json = [];
	$allowed_html = woopanel_wses_allowed_menu_html();


	if( ! empty ($_POST['content']) && ! empty ($_POST['id']) ) {
		$post_content = trim( $_POST['content'] );

		if( empty($post_content) ) {
			$json['message'] = esc_html__('Please enter the reply text', 'woopanel');
		}else {
			$post_content = wpautop( wp_kses( $post_content, $allowed_html ) );

			$parent = absint($_POST['id']);
	        $my_post = array(
	            'post_title'    => sprintf('Reply to ticket #%d', $parent),
	            'post_content'  => $post_content,
	            'post_status'   => 'read',
	            'post_type'     => 'ticket_reply',
	            'post_author'   => $current_user->ID,
	            'post_parent'	=> $parent
	        );

	        $post_id = wp_insert_post( $my_post );

	        $json['complete'] = true;

            $email_reply_ticket = get_option('woopanel_email_reply_ticket');
            if( ! empty($email_reply_ticket) ) {
                woopanel_ticket_send_email(
                    $current_user->email,
                    sprintf(
                        esc_html__('Reply Ticket #%d at %s', 'woopanel'),
                        $post_id,
                        get_option('blogname')
                    ),
                    woopanel_ticket_email_content($email_reply_ticket, $post_id, $my_post)
                );
            }
		}
	}else {
		$json['message'] = esc_html__('Please enter the reply text', 'woopanel');
	}

	wp_send_json($json);

}