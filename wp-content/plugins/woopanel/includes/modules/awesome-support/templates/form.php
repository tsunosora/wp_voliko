<form class="m-form m-form--label-align-left- m-form--state-" name="post" method="post" id="post">
	<div class="row">
		<div class="col col-main">

			<!--Begin::Main Portlet-->
			<div class="m-portlet">

				<!--begin: Portlet Head-->
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								<?php echo esc_html( $title ); ?>
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">

						<?php if ( ($post_id > 0) && current_user_can( $permission ) ) { ?>
							<a href="<?php echo esc_html( woopanel_post_new_url($post_type) );?>" class="btn btn-secondary m-btn m-btn--icon m-btn--md m--margin-right-10">
								<span>
									<i class="flaticon-add"></i>
									<span><?php echo esc_html( $add_title); ?></span>
								</span>
							</a>
						<?php } ?>

						<?php if ( current_user_can( $permission ) ) { ?>
							<div class="btn-group">
								<button type="submit" class="btn btn-primary m-btn m-btn--icon m-btn--wide m-btn--md m-loader--light m-loader--right" onclick="if(!this.classList.contains('m-loader')) this.className+=' m-loader';">
									<span>
										<i class="la la-check"></i>
										<span><?php echo esc_attr($button_label);?></span>
									</span>
								</button>
							</div>
						<?php } ?>

					</div>
				</div>
				<!--end: Portlet Head-->

				<div class="m-portlet__body<?php echo ! empty($post_id) ? ' ticket-main' : '';?>">
					<div class="row">
						<div class="col-lg-12">
							<div class="m-form__section  m--margin-top-20 m--margin-bottom-20">
								<?php
								if( empty($post_id) ) {
									woopanel_form_field(
									'post_title', 
									[
										'type'			=> 'text',
										'placeholder'	=> apply_filters( 'woopanel_{$post_type}_enter_title_here', esc_html__( 'Enter subject here', 'woopanel' ) ),
										'id'			=> 'title',
										'input_class'	=> ['form-control-lg m-input--solid']
									], esc_html($post->post_title) ); ?>

									<!-- edit slug -->
									<div class="inside">
										<?php
										if( $permalink ) {
											print( $post->ID ? woopanel_get_permalink_html($post->ID) : '' );
										}
										?>
									</div>

									<?php
									woopanel_form_field( 'content',
									[
											'type'			=> 'wpeditor',
											'label'			=> '',
											'description'	=> '',
											'placeholder'	=> '',
											'id'		=> 'post_content',
											'class'		=> ['post_content'],
											'settings'	=> [
												'textarea_rows' => 15
											]
									], $post->post_content );
								}else {
									printf('<h1 class="ticket-title">%s</h1>', $post->post_title);

									printf('<div class="ticket-content">%s</div>', wpautop($post->post_content) );
								}?>							
							</div>
						</div>
					</div>
				</div>
				<!-- .m-portlet__body -->
			</div>

			<?php if ( $post_id ) { ?>
			<!--Begin::Main Portlet-->
			<div class="m-portlet">

				<!--begin: Portlet Head-->
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								<?php echo esc_html__( 'Ticket Replies', 'awesome-support' ); ?>
							</h3>
						</div>
					</div>
				</div>
				<!--end: Portlet Head-->

				<div class="m-portlet__body reply-ticket-body">

					<?php
					$status = get_post_meta( $post_id, '_wpas_status', true ); ?>

					<!-- Table of replies, notes and logs -->
					<table class="form-table wpas-table-replies">
						<col class="col1"/>
						<col class="col2"/>
						<tbody>

							<?php
							/* If the post hasn't been saved yet we do not display the metabox's content */
							if( '' == $status ): ?>

								<div class="updated below-h2" style="margin-top: 2em;">
									<h2 style="margin: 0.5em 0; padding: 0; line-height: 100%;"><?php _e( 'Create Ticket', 'awesome-support' ); ?></h2>
									<p><?php _e( 'Please save this ticket to reveal all options.', 'awesome-support' ); ?></p>
								</div>

							<?php
							/* Now let's display the real content */
							else:

								/* We're going to get all the posts part of the ticket history */
								$replies_args = array(
									'posts_per_page' => - 1,
									'orderby'        => 'post_date',
									'order'          => wpas_get_option( 'replies_order', 'ASC' ),
									'post_type'      => apply_filters( 'wpas_replies_post_type', array(
										'ticket_history',
										'ticket_reply'
									) ),
									'post_parent'    => $post->ID,
									'post_status'    => apply_filters( 'wpas_replies_post_status', array(
										'publish',
										'inherit',
										'private',
										'trash',
										'read',
										'unread'
									) )
								);

								$history = new WP_Query( $replies_args );

								if ( ! empty( $history->posts ) ):

									foreach ( $history->posts as $row ):

										// Set the author data (if author is known)
										if ( $row->post_author != 0 ) {
											$user_data = get_userdata( $row->post_author );
											$user_id   = $user_data->data->ID;
											$user_name = $user_data->data->display_name;
										}

										// In case the post author is unknown, we set this as an anonymous post
										else {
											$user_name = __( 'Anonymous', 'awesome-support' );
											$user_id   = 0;
										}

										$user_avatar     = get_avatar( $user_id, '64', get_option( 'avatar_default' ) );
										$date            = human_time_diff( get_the_time( 'U', $row->ID ), current_time( 'timestamp' ) );
										$date_full		 = get_the_time('F j, Y g:i a', $row->ID);
										$days_since_open = woopanel_get_date_diff_string( $post->post_date, $row->post_date) ;  // This is a string showing the number of dates/hours/mins that this reply arrived compared to the date the ticket was opened
										$post_type       = $row->post_type;
										$post_type_class = ( 'ticket_reply' === $row->post_type && 'trash' === $row->post_status ) ? 'ticket_history' : $row->post_type;

										/**
										 * This hook is fired just before we open the post row
										 *
										 * @param WP_Post $row Reply post object
										 */
										do_action( 'wpas_backend_replies_outside_row_before', $row );
										?>
										<tr valign="top" class="wpas-table-row wpas-<?php echo str_replace( '_', '-', $post_type_class ); ?> wpas-<?php echo str_replace( '_', '-', $row->post_status ); ?>" id="wpas-post-<?php echo $row->ID; ?>">

											<?php if( $row->post_status == 'read' ) {?>
											<td class="col1" style="width: 64px;">

												<?php
												/* Display avatar only for replies */
												if( 'ticket_reply' == $row->post_type ) {

													echo $user_avatar;

													/**
													 * Triggers an action right under the user avatar for ticket replies.
													 *
													 * @since 3.2.6
													 *
													 * @param int $row->ID The current reply ID
													 * @param int $user_id The reply author user ID
													 */
													do_action( 'wpas_mb_replies_under_avatar', $row->ID, $user_id );

												}
												?>

											</td>
											<?php }?>

											<td<?php echo ($row->post_status == 'publish') ? ' colspan="3"' : '';?>>
												<?php $show_extended_date_in_replies = boolval( wpas_get_option( 'show_extended_date_in_replies', false ) ); ?>
												<div class="wpas-reply-meta">
													<div class="wpas-reply-user">
														<strong class="wpas-profilename"><?php echo $user_name; ?></strong><?php if ( $user_data ): ?> <span class="wpas-profilerole">(<?php echo wpas_get_user_nice_role( $user_data->roles ); ?>)</span><?php endif; ?>
													</div>

													<div class="wpas-reply-time">
														<time class="wpas-timestamp" datetime="<?php echo get_the_date( 'Y-m-d\TH:i:s' ) . wpas_get_offset_html5(); ?>"><span class="wpas-human-date"><?php echo date( get_option( 'date_format' ), strtotime( $row->post_date ) ); ?> <?php if ( true === $show_extended_date_in_replies ) { printf( __( '(%s - %s since ticket was opened.)', 'awesome-support' ), $date_full, $days_since_open ); } ?>  | </span><?php printf( __( '%s ago', 'awesome-support' ), $date ); ?></time>
													</div>
												</div>

												<div class="wpas-content">
													<?php
													/* Filter the content before we display it */
													$content = apply_filters( 'the_content', $row->post_content );
													echo wp_kses( $content, wp_kses_allowed_html( 'post' ) );?>
												</div>
											</td>
										</tr>
									<?php endforeach;
								endif;
							endif; ?>
						</tbody>
					</table>











					
					<?php
					woopanel_form_field( 'content_reply',
					[
							'type'			=> 'wpeditor',
							'label'			=> '',
							'description'	=> '',
							'placeholder'	=> '',
							'id'		=> 'content_reply',
							'class'		=> ['content_reply'],
							'settings'	=> [
								'textarea_rows' => 10
							]
					], '' );?>	

					<button type="button" name="reply_submit" class="btn btn-primary m-btn m-loader--light m-loader--right reply_submit" onclick="if(!this.classList.contains('m-loader')) this.className+=' m-loader';">
								<?php echo esc_html__('Send Reply', 'woopanel');?>
					</button>
				</div>
			</div>
			<?php }?>
		</div>




		<div class="col-xs-12 col-sm-12 col-sidebar">
			<div class="m-portlet m-portlet-ticket">
				<!--begin: Portlet Head-->
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								<?php echo esc_html__('Ticket Details', 'awesome-support');?>
							</h3>
						</div>
					</div>
				</div>

				<div class="m-portlet__body">


					<?php
					/* Get post status */
					$post_status = isset( $post->post_status ) ? $post->post_status : 'queued';



					/* Get the date */
					$date_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
					$date        = get_the_date( $date_format );

					/* Get time */
					if ( isset( $post ) ) {
						$dateago = human_time_diff( get_the_time( 'U', $post_id ), current_time( 'timestamp' ) );
					}
					?>
					<div class="wpas-row<?php echo $checkRole ? ' no-border' : '';?>" id="wpas-statusdate">
						<div class="wpas-col">
							<strong><?php _e( 'Status', 'awesome-support' ); ?></strong>
							<?php if ( $post_id ):
								wpas_cf_display_status( '', $post_id );
							?>
							<?php else: ?>
								<span><?php esc_html_e( 'Creating...', 'awesome-support' ); ?></span>
							<?php endif; ?>
						</div>
						<div class="wpas-col">
							<?php if ( isset( $post ) ): ?>
								<strong><?php echo $date; ?></strong>
								<em><?php printf( __( '%s ago', 'awesome-support' ), $dateago ); ?></em>
							<?php endif; ?>
						</div>
						
					</div>

					<div style="padding: 0 30px;">
						<?php
						if( ! $checkRole ) {

							if( in_array('administrator', $current_user->roles) ) {
								woopanel_form_field(
									'seller',
									array(
										'id'          => 'seller',
										'type'		  => 'select',
										'label'       => esc_html__( 'Seller', 'woopanel' ),
										'options'     => woopanel_get_all_seller(),
										'desc_tip'    => 'true',
									),
									get_post_meta($post_id, '_wpas_assignee', true)
								);
							}


							if ( ! empty($post_id) ) {
								if( ! empty($_assignee) && woopanel_is_vendor() || woopanel_is_super_admin() ) {
									woopanel_form_field(
										'ticket_status',
										array(
											'id'          => 'ticket_status',
											'type'		  => 'select',
											'label'       => esc_html__( 'Current Status', 'awesome-support' ),
											'options'     => wpas_get_post_status(),
											'desc_tip'    => 'true',
										),
										$post_status
									);
								}

							}
						}else {
							if( ! empty($vendor_user) ) {
								echo sprintf( '<input type="hidden" name="seller" value="%d" />', $vendor_user );
							}
							
						}
						?>


					</div>
				</div>



				<div class="m-portlet__foot">					
					<div id="publishing-actions">
						<div id="publishing-action">
							<button type="submit" name="save" class="btn btn-primary m-btn m-loader--light m-loader--right" id="publish" onclick="if(!this.classList.contains('m-loader')) this.className+=' m-loader';">
								<?php echo esc_attr($button_label);?>
							</button>
						</div>
					</div>
					<!--end: Form Body -->
				</div>
			</div>

			<?php if( $post_id ) { ?>
			<div class="m-portlet m-portlet-user-profile">
				<!--begin: Portlet Head-->
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								<?php echo esc_html__('User Profile', 'awesome-support');?>
							</h3>
						</div>
					</div>
				</div>

				<div class="m-portlet__body">
					<?php
					// Get the user object
					$user = get_userdata( $post->post_author );

					// Get tickets
					$get_tickets = apply_filters( 'wpas_user_profile_show_tickets', true ) ;
					if ( true === $get_tickets ) { 
						$open   = wpas_get_tickets( 'open', array( 'posts_per_page' => apply_filters( 'wpas_user_profile_tickets_open_limit', 10 ), 'author' => $post->post_author ) );
						$closed = wpas_get_tickets( 'closed', array( 'posts_per_page' => apply_filters( 'wpas_user_profile_tickets_closed_limit', 5 ), 'author' => $post->post_author ) );
					} else {
						$open 	= array();
						$closed = array();
					}

					// Get tickets again without the wpas_user_profile_tickets_open_limit filter so that we can get a full and accurate count of tickets.  Gah - hate duplicating code.
					$open_for_count   = wpas_get_tickets( 'open', array( 'posts_per_page' => -1, 'author' => $post->post_author ) );
					$closed_for_count = wpas_get_tickets( 'closed', array( 'posts_per_page' => -1, 'author' => $post->post_author ) );

					// Sort open tickets
					$by_status  = array();
					$all_status = wpas_get_post_status();

					foreach ( $open as $t ) {

						if ( ! is_a( $t, 'WP_Post' ) ) {
							continue;
						}

						if ( ! array_key_exists( $t->post_status, $all_status ) ) {
							continue;
						}

						if ( ! array_key_exists( $t->post_status, $by_status ) ) {
							$by_status[ $t->post_status ] = array();
						}

						$by_status[ $t->post_status ][] = $t;

					}

					// Add the closed tickets in the list
					$by_status['closed'] = $closed;
					?>
					<div id="wpas-up">

						<?php
						/**
						 * Fires before anything is processed in the user profile metabox
						 *
						 * @since 3.3
						 * @var WP_User $user The user object
						 * @var WP_Post $post Post object of the current ticket
						 */
						do_action( 'wpas_user_profile_metabox_before', $user, $post ); ?>

						<div class="wpas-up-contact-details wpas-cf">
							<?php if ( $user ): ?>
							<a href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . $user->ID ) ); ?>">
								<?php echo get_avatar( $user->ID, '80', 'mm', $user->data->display_name, array( 'class' => 'wpas-up-contact-img' ) ); ?>
							</a>
							<?php endif; ?>

							<div class="wpas-detail-right">
								<?php
								$contact_fields = woopanel_user_profile_get_contact_info( $post->ID );

								foreach ( $contact_fields as $contact_field ) {
									printf( '<div class="wpas-up-contact-%1$s">', $contact_field );
									woopanel_user_profile_contact_info_contents( $contact_field, $user, $post->ID );
									echo '</div>';
								}
								?>
							</div>
						</div>

						<?php
						/**
						 * Fires after the contact information fields
						 *
						 * @since 3.3
						 * @var WP_User $user The user object
						 * @var WP_Post $post Post object of the current ticket
						 */
						do_action( 'wpas_user_profile_metabox_after_contact_info', $user, $post ); ?>
						
						<div class="wpas-row wpas-up-stats">
							<div class="wpas-col wpas-up-stats-all">
								<strong><?php echo count( $open_for_count ) + count( $closed_for_count ); ?></strong>
								<?php echo esc_html__( 'Total', 'awesome-support' ); ?>
							</div>
							<div class="wpas-col wpas-up-stats-open">
								<strong><?php echo count( $open_for_count ); ?></strong>
								<?php echo esc_html__( 'Open', 'awesome-support' ); ?>
							</div>
							<div class="wpas-col wpas-up-stats-closed">
								<strong><?php echo count( $closed_for_count ); ?></strong>
								<?php echo esc_html__( 'Closed', 'awesome-support' ); ?>
							</div>
						</div>
						
						<?php
						
						If ( ( count( $open_for_count) <> count ($open) ) or ( count( $closed_for_count ) <> count ($closed) ) ) {
							if ( true === $get_tickets ) {
								// add warning message that the totals shown will not match the list of open tickets
								echo esc_html__( 'Note: A filter is enabled that allows the totals shown above to be greater than the list of tickets below.', 'awesome-support' ); 
							}
						}
						
						/**
						 * Fires after the user stats
						 *
						 * @since 3.3
						 * @var WP_User $user The user object
						 * @var WP_Post $post Post object of the current ticket
						 */
						do_action( 'wpas_user_profile_metabox_after_stats', $user, $post ); ?>

						<div class="wpas-up-tickets">
							<?php
							if ( true === $get_tickets  ) {
								
								foreach ( $by_status as $status => $tickets ) {

									if ( empty( $tickets ) ) {
										continue;
									}

									$status_label = 'closed' === $status ? esc_html__( 'Closed', 'awesome-support' ) : $all_status[ $status ];
									$lis = sprintf( '<li><span class="wpas-label" style="background-color:%1$s;">%2$s ▾</span></li>', wpas_get_option( "color_$status", '#dd3333' ), $status_label );

									foreach ( $tickets as $t ) {
										$created = sprintf( esc_html_x( 'Created on %s', 'Ticket date creation', 'awesome-support' ), date( get_option( 'date_format' ), strtotime( $t->post_date ) ) );
										$title   = apply_filters( 'the_title', $t->post_title );
										$link    = esc_url( admin_url( "post.php?post=$t->ID&action=edit" ) );

										if ( $t->ID !== (int) $post->ID ) {
											$lis .= sprintf( '<li data-hint="%1$s" class="hint-left hint-anim"><a href="%3$s">%2$s</a></li>', $created, $title, $link );
										} else {
											$lis .= sprintf( '<li data-hint="%1$s" class="hint-left hint-anim">%2$s (%3$s)</li>', $created, $title, esc_html_x( 'current', 'Identifies the ticket in a list as being the ticket displayed on the current screen', 'awesome-support' ) );
										}
									}

									printf( '<ul>%s</ul>', $lis );

								}
								
							}
							?>

							<!-- @todo <a href="/wp-admin/edit.php?post_type=ticket" class="button">View all tickets</a> -->
						</div>

						<?php
						/**
						 * Fires after everything else is processed in the user profile metabox
						 *
						 * @since 3.3
						 * @var WP_User $user The user object
						 * @var WP_Post $post Post object of the current ticket
						 */
						do_action( 'wpas_user_profile_metabox_after', $user, $post ); ?>

					</div>
				</div>
			</div>
			<?php }?>
		</div>
	</div>

	<input type="hidden" name="ticket_ID" value="<?php echo $post->ID;?>" />
</form>