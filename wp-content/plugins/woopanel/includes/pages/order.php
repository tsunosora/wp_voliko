<?php

/**
 * This class will load order
 *
 * @package WooPanel_Template_Order
 */
class WooPanel_Template_Order {
	private $get_order_statuses = array();
	private $classes;

	public function __construct() {
		$this->get_order_statuses = wc_get_order_statuses();
		$this->hooks_table();

 		$this->classes = new WooPanel_Post_List_Table(array(
			'post_type'     => 'shop_order',
			'screen'        => 'orders',
			'columns'       => array(
				'title'     => esc_html__( 'Order', 'woopanel' ),
				'date'      => esc_html__( 'Date', 'woopanel' ),
				'status'    => esc_html__( 'Status', 'woopanel' ),
				'total'     => esc_html__( 'Total', 'woopanel' ),
				'action'    => esc_html__( 'Actions', 'woopanel' )
			),
			'primary_columns' => 'title',
			'post_statuses' => $this->get_order_statuses,
			'custom_query' => array(
				'query_count' => $this->set_query(true),
				'query_results' => $this->set_query(false)
			)
		));
	}

	public function lists() {
		$this->classes->prepare_items();
		$this->classes->display();
	}

	public function form() {
		$id = absint($_GET['id']);

		$order = $line_items = array();
	
		if( $order = wc_get_order($id) ) {
			$line_items = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );

			if( isset($_POST['publish']) ) {
				// Update customer.
				$customer_id = isset( $_POST['customer_user'] ) ? absint( $_POST['customer_user'] ) : 0;
				if ( $customer_id !== $order->get_customer_id() ) {
					$props['customer_id'] = $customer_id;
				}
		
				// Update date.
				if ( empty( $_POST['order_date'] ) ) {
					$date = current_time( 'timestamp', true );
				} else {
					$date = gmdate( 'Y-m-d H:i:s', strtotime( $_POST['order_date'] . ' ' . (int) $_POST['order_date_hour'] . ':' . (int) $_POST['order_date_minute'] . ':' . (int) $_POST['order_date_second'] ) );
				}

				$props['date_created'] = $date;

				// Set created via prop if new post.
				if ( isset( $_POST['original_post_status'] ) && $_POST['original_post_status'] === 'auto-draft' ) {
					$props['created_via'] = 'admin';
				}

				// Save order data.
				$order->set_props( $props );
				$order->set_status( wc_clean( $_POST['order_status'] ), '', true );
				$order->save();

				$ptype = get_post_type_object('shop_order');

				/*
				* @todo Document the $messages array(s).
				*/
				$permalink = get_permalink( $order->get_id() );
				if ( ! $permalink ) {
					$permalink = '';
				}
	
				// View post link.
				$view_post_link_html = sprintf( ' <a href="%1$s">%2$s</a>',
					esc_url( $permalink ),
					$ptype->labels->view_item
				);
				
				if( empty($ptype->public) ) {
					$view_post_link_html = '';
				}
				
	
				wpl_add_notice( "{$ptype->name}", esc_html__( 'Order updated.', 'woopanel' ) . esc_attr( $view_post_link_html ), 'success' );
	
			}

			wpl_print_notices();

			woopanel_get_template_part('orders/edit', '', array(
				'order' => $order,
				'line_items' => $line_items
			));
		}


	

	}

	public function hooks_table() {
        add_filter( 'woopanel_shop_order_title_column', array($this, 'title_custom'), 99, 3);
        add_filter( 'woopanel_shop_order_date_column', array($this, 'date_custom'), 99, 3);
		add_filter( 'woopanel_shop_order_status_column', array($this, 'status_custom'), 99, 3);
		add_filter( 'woopanel_shop_order_total_column', array($this, 'total_custom'), 99, 3);
		add_filter( 'woopanel_shop_order_action_column', array($this, 'action_custom'), 99, 3);
		
		add_action( 'woopanel_shop_order_filter_display', array($this, 'filter_display'), 99, 2 );
		add_action( 'wp_footer', array($this, 'wp_footer_modal_preview') );
		add_filter( 'woopanel_shop_order_user_can_create', '__return_false');

        add_filter( 'bulk_actions-shop_order', array( $this, 'define_bulk_actions' ) );
        add_filter( 'handle_bulk_actions-shop_order', array( $this, 'handle_bulk_actions' ), 10, 3 );

		add_action( 'woopanel_shop_order_no_item_icon', array($this, 'no_item_icon') );

		add_filter( 'woopanel_shop_order_table_row_display', array($this, 'table_row_display'), 50, 3 );
	}

	public function table_row_display( $return, $post, $order ) {
		global $current_user;

        $shop_role = array( 'shop_manager', 'administrator' );
        if( !empty( array_intersect( $shop_role, (array) $current_user->roles ) ) ) {
			return true;
		}

		$items = $order->get_items();

		$accept = false;
		foreach ( $items as $item ) {
			$_post = get_post($item->get_product_id());

			if( $_post->post_author == $current_user->ID ) {
				$accept = true;
			}
		}

		return $accept;
	}


	public function no_item_icon() {
		echo '<i class="flaticon-notepad"></i>';
	}

	public function title_custom($html, $post, $order) {
		global $woopanel_post_types;

		$buyer = '';
	
		if ( $order->get_billing_first_name() || $order->get_billing_last_name() ) {
			/* translators: 1: first name 2: last name */
			$buyer = trim( sprintf( _x( '%1$s %2$s', 'full name', 'woopanel' ), $order->get_billing_first_name(), $order->get_billing_last_name() ) );
		} elseif ( $order->get_billing_company() ) {
			$buyer = trim( $order->get_billing_company() );
		} elseif ( $order->get_customer_id() ) {
			$user  = get_user_by( 'id', $order->get_customer_id() );
			$buyer = ucwords( $user->display_name );
		}
	
		if ( $order->get_status() === 'trash' ) {
			echo '<strong>#' . esc_attr( $order->get_order_number() ) . sprintf( ' ' . esc_html__( 'by %s', 'woopanel' ), esc_html( $buyer ) ) . '</strong>';
		} else {
			// echo '<button class="order-preview" data-order-id="' . absint( $order->get_id() ) . '" data-toggle="tooltip" data-placement="top" title="' . esc_attr( esc_html__( 'Preview', 'woopanel' ) ) . '"><i class="fa fa-eye icon-preview"></i><div class="m-loader m-loader--primary icon-loader" style="width: 16px; display: none;"></div></button>';
			echo '<a href="' . esc_url( woopanel_post_edit_url( $order->get_id()) ) . '" class="order-view"><strong>#' . esc_attr( $order->get_order_number() ) . sprintf( ' ' . esc_html__( 'by %s', 'woopanel' ), esc_html( $buyer ) ) . '</strong></a>';
		}
	}

    public function date_custom($html, $post, $order) {
        $order_timestamp = $order->get_date_created() ? $order->get_date_created()->getTimestamp() : '';

        if ( ! $order_timestamp ) {
            echo '&ndash;';
            return;
        }

        // Check if the order was created within the last 24 hours, and not in the future.
        if ( $order_timestamp > strtotime( '-1 day', current_time( 'timestamp', true ) ) && $order_timestamp <= current_time( 'timestamp', true ) ) {
            $show_date = sprintf(
            /* translators: %s: human-readable time difference */
                _x( '%s ago', '%s = human-readable time difference', 'woocommerce' ),
                human_time_diff( $order->get_date_created()->getTimestamp(), current_time( 'timestamp', true ) )
            );
        } else {
            $show_date = $order->get_date_created()->date_i18n( apply_filters( 'woocommerce_admin_order_date_format', 'M j, Y' ) );
        }
        printf(
            '<time datetime="%1$s" title="%2$s" aria-label="%2$s" data-toggle="tooltip" data-placement="top">%3$s</time>',
            esc_attr( $order->get_date_created()->date( 'c' ) ),
            esc_html( $order->get_date_created()->date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ),
            esc_html( $show_date )
        );
    }

	public function status_custom($html, $post, $order) {
        global $woopanel_order_status;
		$tooltip                 = '';
		$comment_count           = get_comment_count( $order->get_id() );
		$approved_comments_count = absint( $comment_count['approved'] );
	
		if ( $approved_comments_count ) {
			$latest_notes = wc_get_order_notes(
				array(
					'order_id' => $order->get_id(),
					'limit'    => 1,
					'orderby'  => 'date_created_gmt',
				)
			);
	
			$latest_note = current( $latest_notes );
	
			if ( isset( $latest_note->content ) && 1 === $approved_comments_count ) {
				$tooltip = wc_sanitize_tooltip( $latest_note->content );
			} elseif ( isset( $latest_note->content ) ) {
				/* translators: %d: notes count */
				$tooltip = wc_sanitize_tooltip( $latest_note->content . '<br/><small style="display:block">' . sprintf( _n( 'Plus %d other note', 'Plus %d other notes', ( $approved_comments_count - 1 ), 'woopanel' ), $approved_comments_count - 1 ) . '</small>' );
			} else {
				/* translators: %d: notes count */
				$tooltip = wc_sanitize_tooltip( sprintf( _n( '%d note', '%d notes', $approved_comments_count, 'woopanel' ), $approved_comments_count ) );
			}
		}
	
		if ( $tooltip ) {
			$order_status = '';
			if( isset($woopanel_order_status["wc-{$order->get_status()}"]['color']) ) {
				$order_status = esc_attr( sanitize_html_class( $woopanel_order_status["wc-{$order->get_status()}"]['color'] ) );
			}
			printf(
			        '<mark class="m-badge m-badge--brand m-badge--wide order-status %s tips" data-toggle="tooltip" title="%s"><span>%s</span></mark>', $order_status,
                    wp_kses_post( $tooltip ),
                    esc_html( wc_get_order_status_name( $order->get_status() ) )
            );
		} else {
			printf(
			        '<mark class="m-badge m-badge--brand m-badge--wide order-status %s"><span>%s</span></mark>',
                    esc_attr( sanitize_html_class( $woopanel_order_status["wc-{$order->get_status()}"]['color'] ) ),
                    esc_html( wc_get_order_status_name( $order->get_status() ) )
            );
		}
	}

	public function total_custom($html, $post, $order) {
		if ( $order->get_payment_method_title() ) {
			/* translators: %s: method */
			echo '<span class="tips" data-tip="' . esc_attr( sprintf( esc_html__( 'via %s', 'woopanel' ), $order->get_payment_method_title() ) ) . '">' . wp_kses_post( $order->get_formatted_order_total() ) . '</span>';
		} else {
			echo wp_kses_post( $order->get_formatted_order_total() );
		}
	}

	public function action_custom($html, $post, $order) {
		$actions = array();

		if ( $order->has_status( array( 'pending', 'on-hold' ) ) ) {
			$actions['processing'] = array(
				'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=processing&order_id=' . absint( $order->get_id() ) ), 'woocommerce-mark-order-status' ),
				'name'   => esc_html__( 'Processing', 'woopanel' ),
				'action' => 'processing',
			);
		}

		if ( $order->has_status( array( 'pending', 'on-hold', 'processing' ) ) ) {
			$actions['complete'] = array(
				'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id=' . absint( $order->get_id() ) ), 'woocommerce-mark-order-status' ),
				'name'   => esc_html__( 'Complete', 'woopanel' ),
				'action' => 'complete',
			);
		}

		print( $this->wc_render_action_buttons( $actions ) ); // WPCS: XSS ok.
	}

    public function define_bulk_actions( $actions ) {
        $actions['mark_processing']      = esc_html__( 'Change status to processing', 'woopanel' );
        $actions['mark_on-hold']         = esc_html__( 'Change status to on-hold', 'woopanel' );
        $actions['mark_completed']       = esc_html__( 'Change status to completed', 'woopanel' );
        $actions['remove_personal_data'] = esc_html__( 'Remove personal data', 'woopanel' );

        return $actions;
    }
    public function handle_bulk_actions( $redirect_to, $action, $ids ) {
        $ids     = apply_filters( 'woocommerce_bulk_action_ids', array_reverse( array_map( 'absint', $ids ) ), $action, 'order' );
        $changed = 0;

        if ( 'remove_personal_data' === $action ) {
            $report_action = 'removed_personal_data';

            foreach ( $ids as $id ) {
                $order = wc_get_order( $id );

                if ( $order ) {
                    do_action( 'woocommerce_remove_order_personal_data', $order );
                    $changed++;
                }
            }
        } elseif ( false !== strpos( $action, 'mark_' ) ) {
            $order_statuses = wc_get_order_statuses();
            $new_status     = substr( $action, 5 ); // Get the status name from action.
            $report_action  = 'marked_' . esc_attr($new_status);

            // Sanity check: bail out if this is actually not a status, or is not a registered status.
            if ( isset( $order_statuses[ 'wc-' . esc_attr($new_status) ] ) ) {
                // Initialize payment gateways in case order has hooked status transition actions.
                wc()->payment_gateways();

                foreach ( $ids as $id ) {
                    $order = wc_get_order( $id );
                    $order->update_status( $new_status, esc_html__( 'Order status changed by bulk edit:', 'woopanel' ), true );
                    do_action( 'woocommerce_order_edit_status', $id, $new_status );
                    $changed++;
                }
            }
        }

        if ( $changed ) {
            $redirect_to = add_query_arg(
                array(
                    'post_type'   => 'shop_order',
                    'bulk_action' => $report_action,
                    'changed'     => $changed,
                    'ids'         => join( ',', $ids ),
                ), $redirect_to
            );
        }

        return esc_url_raw( $redirect_to );
    }

	
	/**
	 * Get HTML for some action buttons. Used in list tables.
	 *
	 * @since 3.3.0
	 * @param array $actions Actions to output.
	 * @return string
	 */
	public function wc_render_action_buttons( $actions ) {
		$actions_html = '';

		foreach ( $actions as $action ) {
			if ( isset( $action['group'] ) ) {
				$actions_html .= '<div class="wc-action-button-group"><label>' . esc_attr($action['group']) . '</label> <span class="wc-action-button-group__items">' . wc_render_action_buttons( $action['actions'] ) . '</span></div>';
			} elseif ( isset( $action['action'], $action['url'], $action['name'] ) ) {
				$actions_html .= sprintf( '<a class="button m-btn--icon m-btn--icon-only wc-action-button wc-action-button-%1$s %1$s" href="%2$s" aria-label="%3$s" title="%3$s" data-toggle="tooltip" data-placement="top"><i class="la la-check"></i></a>', esc_attr( $action['action'] ), esc_url( $action['url'] ), esc_attr( isset( $action['title'] ) ? $action['title'] : $action['name'] ), esc_html( $action['name'] ) );
			}
		}

		return $actions_html;
	}


	public function filter_display($post_type, $post_type_object) {
		$status = isset($_GET['status']) ? strip_tags($_GET['status']) : '';

		if( isset($_GET['post_status']) ) {
			$status = strip_tags($_GET['post_status']);
		}
		?>
		<div class="col-md-4">
			<div class="m-form__group m-form__group--inline">
				<div class="m-form__label"><label for="filter-status"><?php esc_html_e('Status', 'woopanel' );?></label></div>
				<div class="m-form__control">
					<select name="status" id="filter-status" class="form-control m-bootstrap-select">
						<option selected='selected' value="0"><?php esc_html_e( 'All status', 'woopanel' );?></option>
						<?php foreach( $this->get_order_statuses as $k_status => $val_status) {
							printf('<option value="%s" %s>%s</option>', $k_status, selected( $k_status, $status, false ), $val_status);
						}?>
					</select>
				</div>
			</div>
			<div class="d-md-none m--margin-bottom-10"></div>
		</div>
		<?php
	}

	public function wp_footer_modal_preview() {
		global $woopanel_post_types;
		?>
		<script type="text/template" id="tmpl-wc-modal-view-order">
			<!-- Modal -->
			<div class="wc-backbone-modal modal fade" id="order-preview" role="dialog">
				<div class="modal-dialog wc-backbone-modal-content modal-lg">
					<!-- Modal content-->
					<div class="wc-backbone-modal-main modal-content">
						<div class="wc-backbone-modal-header modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<div class="modal-header-heading">
								<h1><?php echo esc_html( sprintf( esc_html_e( 'Order #%s', 'woopanel' ), '{{ data.order_number }}' ) ); ?></h1>
								<mark class="m-badge m-badge--brand m-badge--wide order-status status-{{ data.status }} {{ data.status_color }}"><span>{{ data.status_name }}</span></mark>
							</div>
						</div>
						<div class="modal-body">
						<article>
								<?php do_action( 'woocommerce_admin_order_preview_start' ); ?>

								<div class="wc-order-preview-addresses row">
									<div class="wc-order-preview-address col">
										<h2><?php esc_html_e( 'Billing details', 'woopanel' ); ?></h2>
										{{{ data.formatted_billing_address }}}

										<# if ( data.data.billing.email ) { #>
											<strong><?php esc_html_e( 'Email', 'woopanel' ); ?></strong>
											<a href="mailto:{{ data.data.billing.email }}">{{ data.data.billing.email }}</a>
										<# } #>

										<# if ( data.data.billing.phone ) { #>
											<strong><?php esc_html_e( 'Phone', 'woopanel' ); ?></strong>
											<a href="tel:{{ data.data.billing.phone }}">{{ data.data.billing.phone }}</a>
										<# } #>

										<# if ( data.payment_via ) { #>
											<strong><?php esc_html_e( 'Payment via', 'woopanel' ); ?></strong>
											{{{ data.payment_via }}}
										<# } #>
									</div>
									<# if ( data.needs_shipping ) { #>
										<div class="wc-order-preview-address col">
											<h2><?php esc_html_e( 'Shipping details', 'woopanel' ); ?></h2>
											<# if ( data.ship_to_billing ) { #>
												{{{ data.formatted_billing_address }}}
											<# } else { #>
												<a href="{{ data.shipping_address_map_url }}" target="_blank">{{{ data.formatted_shipping_address }}}</a>
											<# } #>

											<# if ( data.shipping_via ) { #>
												<strong><?php esc_html_e( 'Shipping method', 'woopanel' ); ?></strong>
												{{ data.shipping_via }}
											<# } #>
										</div>
									<# } #>

									<# if ( data.data.customer_note ) { #>
										<div class="wc-order-preview-note">
											<strong><?php esc_html_e( 'Note', 'woopanel' ); ?></strong>
											{{ data.data.customer_note }}
										</div>
									<# } #>
								</div>

								{{{ data.item_html }}}

								<?php do_action( 'woocommerce_admin_order_preview_end' ); ?>
							</article>
						</div>
						<div class="modal-footer">
							<div class="inner">
								{{{ data.actions_html }}}
								<a class="btn btn-primary btn-modal-editorder btn-sm m-btn--widee" aria-label="<?php esc_attr_e( 'Edit this order', 'woopanel' ); ?>" href="{{ data.order_url }}"><?php esc_html_e( 'Edit', 'woopanel' ); ?></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</script>
		<?php
	}

	public function set_query( $count = false ) {
		global $wpdb, $current_user;

		$query = array();
		$order_status = array( 'completed', 'processing', 'on-hold' );

		if( $count ) {
			$query['select'] = "SELECT COUNT( DISTINCT posts.ID ) as total FROM {$wpdb->posts} as posts";
		}else {
			$query['select'] = "SELECT DISTINCT posts.ID, posts.* FROM {$wpdb->posts} as posts";
		}

    
    $query['join']   = "INNER JOIN {$wpdb->postmeta} AS od_meta ON posts.ID = od_meta.post_id ";
    $query['join'] .= "INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON (posts.ID = order_items.order_id) ";

    $query[ "join" ] .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta ON (order_items.order_item_id = order_item_meta.order_item_id)  AND (order_item_meta.meta_key = '_product_id') ";

    $query['join']   .= "INNER JOIN {$wpdb->posts} AS product ON order_item_meta.meta_value = product.ID";

    $query['where'] = "WHERE posts.post_type = 'shop_order' ";

    if( ! empty($_GET['status']) ) {
    	$status = $_GET['status'];

    	$query['where'] .= "AND posts.post_status = '". $status ."' ";
    }else {
    	//$query['where'] .= "AND posts.post_status IN ( 'wc-" . implode( "','wc-", $order_status ) . "') ";
    }

    
    $query['where'] .= "AND od_meta.meta_key = '_customer_user' AND od_meta.meta_value > 0";

 

		
		// Permission
		if( ! is_shop_staff(false, true) ) {
			$query['where']  .= sprintf( " AND product.post_author = %d AND product.post_status = 'publish'", absint($current_user->ID) );
		}

		return $query;
	}
}
