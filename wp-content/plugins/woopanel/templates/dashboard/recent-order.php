<!--begin:: Widgets/Sale Reports-->
<div class="m-portlet m-portlet--full-height recent-order-wrapper">
	<div class="m-portlet__head">
		<div class="m-portlet__head-caption">
			<div class="m-portlet__head-title">
				<h3 class="m-portlet__head-text">
					<?php esc_html_e( 'Recent Orders', 'woopanel' );?>
				</h3>
				<span class="m-portlet__head-desc">Total invoices <?php echo empty($total_orders) ? 0 : $total_orders;?>, unpaid <?php echo empty($total_unpaid_order) ? 0 : $total_unpaid_order;?></span>
			</div>
		</div>
		<div class="m-portlet__head-tools">
			<ul class="nav nav-pills nav-pills--brand m-nav-pills--align-right m-nav-pills--btn-pill m-nav-pills--btn-sm" role="tablist">
				<li class="nav-item m-tabs__item">
					<a class="nav-link m-tabs__link active" href="<?php echo woopanel_dashboard_url('product-orders/');?>">
						<?php esc_html_e( 'View all', 'woopanel' );?>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="m-portlet__body">

		<!--Begin::Tab Content-->
		<div class="tab-content">

			<!--begin::tab 1 content-->
			<div class="tab-pane active" id="m_widget11_tab1_content">

				<!--begin::Widget 11-->
				<div class="m-widget11">
					<div class="table-responsive">
						<?php if( ! empty($recent_orders) ) {?>
						<!--begin::Table-->
						<table class="table">

							<!--begin::Thead-->
							<thead>
								<tr>
									<td class="m-widget11__label dashboard-col-order"><?php _e( 'Order', 'woocommerce' );?></td>
									<td class="m-widget11__app"><?php esc_html_e( 'Date', 'woopanel' );?></td>
									<td class="m-widget11__price"><?php esc_html_e( 'Status', 'woopanel' );?></td>
									<td class="m-widget11__total m--align-right"><?php esc_html_e( 'Amount', 'woopanel' );?></td>
								</tr>
							</thead>

							<!--end::Thead-->

							<!--begin::Tbody-->
							<tbody>
								<?php
								global $post;
								foreach ( $recent_orders as $k => $order ) {
									$order = wc_get_order($order['ID']);
									$order_data = $order->get_data();
									?>
								<tr>
									<td class="dashboard-col-order">
                                        <?php
                                        if( method_exists($order,'get_formatted_billing_full_name') ) {
                                            $buyer = $order->get_formatted_billing_full_name();
                                            if( strlen($buyer) <= 1 ) {
                                                $buyer = esc_html__( 'Guest', 'woopanel' );
                                            }
                                        }

                                        echo '<a href="' . esc_url( woopanel_post_edit_url( $order->get_id()) ) . '" class="order-view">';
                                        echo '<strong>#' . esc_attr( $order->get_order_number() ) . sprintf( ' ' . esc_html__( 'by %s', 'woopanel' ), esc_html( $buyer ) ) . '</strong>';
                                        echo '</a>';
                                        ?>
                                    </td>
									<td>
                                        <?php echo date_i18n( get_option( 'date_format' ), strtotime($order->get_date_created()));?>
                                    </td>
									<td>
                                        <span class="m-badge m-badge--brand m-badge--wide <?php echo esc_attr($woopanel_order_status["wc-{$order_data['status']}"]['color']);?>">
                                            <?php echo woopanel_get_order_status($order_data['status']);?>
                                        </span>
                                    </td>
									<td class="m--align-right m--font-brand"><?php print($order->get_formatted_order_total());?></td>
								</tr>
								<?php }?>
							</tbody>
							<!--end::Tbody-->
						</table>
						<!--end::Table-->
						<?php } else {?>
							<div class="dashboard-block-empty">
								<i class="fa flaticon-clipboard"></i>
								<h3><?php esc_html_e('Your Order List Is Empty', 'woopanel' );?></h3>
								<p><?php esc_html_e('No orders matching your search criteria.', 'woopanel' );?></p>
							</div>
							<?php
						}?>
					</div>
				</div>

				<!--end::Widget 11-->
			</div>

			<!--end::tab 1 content-->

			<!--end::tab 3 content-->
		</div>

		<!--End::Tab Content-->
	</div>
</div>

<!--end:: Widgets/Sale Reports-->