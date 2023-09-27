<?php
add_action('woopanel_header_avatar_after', 'woopanel_membership_avatar');
function woopanel_membership_avatar($user) {
	if( ! function_exists('is_plugin_active') ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

    if ( is_plugin_active( 'indeed-membership-pro/indeed-membership-pro.php' ) ) {
		$levels = array();
		$level_list_data = get_user_meta($user->ID, 'ihc_user_levels', true);
		$level_list_data = apply_filters( 'ihc_public_get_user_levels', $level_list_data, $user->ID );

		if (isset($level_list_data) && $level_list_data!=''){
			$level_list_data = explode(',', $level_list_data);
			if ($level_list_data){
				foreach ($level_list_data as $id){
					$level = ihc_get_level_by_id($id);

					echo sprintf(
						'<div class="membership-info"><span class="badge-membership-plan %s">%s</span>',
						$level['name'],
						$level['label']
					);

					echo sprintf('<div class="membership-desc">%s</div></div>', $level['description']);
				}
			}
		}
    }
}

add_action('woopanel_header_menu_item', 'woopanel_header_menu_item');
function woopanel_header_menu_item( $user ) {
	if( ! function_exists('is_plugin_active') ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	}

    if ( is_plugin_active( 'indeed-membership-pro/indeed-membership-pro.php' ) ) {
    	$subscription_plan_page = get_option('ihc_subscription_plan_page');
    	$permalink = get_permalink($subscription_plan_page);
		?>
		<li class="m-nav__item">
	        <a href="<?php echo $permalink;?>" class="m-nav__link">
	            <i class="m-nav__link-icon flaticon-profile-1"></i>
	            <span class="m-nav__link-title">
	                <span class="m-nav__link-wrap">
	                    <span class="m-nav__link-text"><?php esc_html_e('Subscription Plan', 'ihc');?></span>
	                </span>
	            </span>
	        </a>
	    </li>
	    <?php
	}
}