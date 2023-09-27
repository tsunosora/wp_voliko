<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists('NBT_Solutions_PDF_Template') ) {
	class NBT_Solutions_PDF_Template {

		static function get_template_temp1( $order, $settings, $download ) {
			extract($settings);
			$module_id = 'nbt_'.NBT_Pdf_Creator_Settings::$id;

			$fullname_order = get_post_meta($order->get_id(), '_billing_first_name', true). ' ' .get_post_meta($order->get_id(), '_billing_last_name', true);

			$logo_height = empty(${$module_id.'_logo_height'}) ? false : ' height="' . ${$module_id.'_logo_height'} . '"';

			$fix_break = $fix_break_one = $fix_padding = null;
			$fix_padding = ' padding: 15px 30px;';
			if( $download ) {
				$fix_break = '<br /><br />';
				$fix_break_one = '<br />';
				$fix_padding = ' padding: 30px 30px 20px 30px;';
			}

			$html = '<table style="width: 100%; margin-bottom: 5px;">
				<tbody>
					<tr>
						<td style="width: 50%; vertical-align: top;">
							<p class="strong" style="color: '. ${$module_id.'_primary_color'}.'; margin: 0 0 5px; font-size: 21px;">'. ${$module_id.'_brands'} .'</p>'.$fix_break.'
							<p style="margin: 0px; line-height: 23px; font-size: 16px; color: '. ${$module_id.'_text_color'}.';">'. ${$module_id.'_address'} .'</p>
						</td>
						<td align="right" style="width: 50%; vertical-align: top; text-align: right"><img class="logo" src="'. ${$module_id.'_logo'} .'"'.$logo_height.' style="max-width: none;" /></td>
					</tr>
				</tbody>
			</table><br />';

			$html .= '<table style="width: 100%; border-top: 2px solid '. ${$module_id.'_primary_color'}.'; margin-bottom: 20px;">
					<tr>
						<td style="width: 50%; padding-top: 10px;">
							<table style="width: 100%; color: '. ${$module_id.'_text_color'}.';">
								<tr>
									<td style="vertical-align: top;">
										<p class="strong" style="margin: 0 0 10px; color: '. ${$module_id.'_primary_color'}.'; font-size: 18px;">'. __( 'Bill to:', 'nbt-solution' ).'</p>'.$fix_break.'
										<div>';
										
										if ( $order->get_formatted_billing_address() ) {
											$order_address = str_replace($fullname_order, '', $order->get_formatted_billing_address());
											$html .= '<p style="margin: 0; line-height: 22px; font-size: 16px;">' . ltrim(wp_kses( $order_address, array( 'br' => array() ) ), '<br />') . '</p>';
										} else {
											$html .= '<p style="margin: 0; line-height: 22px; font-size: 16px;"><strong>' . __( 'Address:', 'woocommerce' ) . '</strong> ' . __( 'No billing address set.', 'woocommerce' ) . '</p>';
										}
									$html .= '</div>
									</td>
									<td style="vertical-align: top;">
										<p class="strong" style="margin: 0 0 10px; color: '. ${$module_id.'_primary_color'}.'; font-size: 18px;">'. __( 'Ship to:', 'nbt-solution' ).'</p>'.$fix_break.'
										<div>';
										if ( $order->get_formatted_shipping_address() ) {
											$html .= '<p style="margin: 0; line-height: 22px; font-size: 16px;">' . wp_kses( $order->get_formatted_shipping_address(), array( 'br' => array() ) ) . '</p>';
										} else {
											$html .= '<p style="margin: 0; line-height: 22px; font-size: 16px;">' . wp_kses( $order->get_formatted_billing_address(), array( 'br' => array() ) ) . '</p>';
										}
									$html .= '</div>
									</td>
								</tr>
							</table>
						</td>
						<td style="width: 50%; vertical-align: top; padding-top: 10px;">
							<table style="width: 100%; font-size: 16px; color: '. ${$module_id.'_text_color'}.';">
								<tr>
									<td class="strong" style="padding: 0 0 10px; color: '. ${$module_id.'_primary_color'}.';">'.$fix_break_one. __( 'Order Number:', 'nbt-solution' ) .$fix_break_one .'</td>
									<td style="padding: 0 0 10px;">'. $fix_break_one . $order->get_order_number() .$fix_break_one.'</td>
								</tr>

								<tr>
									<td class="strong" style="padding: 0 0 10px; color: '. ${$module_id.'_primary_color'}.';">'. __( 'Invoice Date:', 'nbt-solution' ) .$fix_break_one.'</td>
									<td style="padding: 0 0 10px;">'. wc_format_datetime( $order->get_date_created() ) .$fix_break_one.'</td>
								</tr>


								<tr>
									<td class="strong" style="padding: 0 0 10px; color: '. ${$module_id.'_primary_color'}.';">'. __( 'Payment', 'nbt-solution' ).$fix_break_one .'</td>
									<td style="padding: 0 0 10px;">'. wp_kses_post( $order->get_payment_method_title() ) .$fix_break_one.'</td>
								</tr>
							</table>
						</td>
					</tr>
			</table>';



			$td_header_style = 'vertical-align: top; '.$fix_padding.'background-color: '. ${$module_id.'_primary_color'}.'; color: #fff; border: 1px solid #ccc;';
			$td_body_style = 'vertical-align: top; '.$fix_padding.'background-color: #fff; color: '. ${$module_id.'_text_color'}.'; border: 1px solid #ccc;';



			$html .= '<table class="item-orders" style="width: 100%; border-spacing: 0; border-collapse: collapse; font-size: 16px; margin-bottom: 10px;">
				<tbody>
					<tr>
						<td align="center" style="width: 50%; '.$td_header_style.'">
							<div style="text-align: left; font-weight: 700;">'. __('Product Name', 'nbt-solution') .'</div>
						</td>
						<td align="center" style="width: 10%; '.$td_header_style.'">
							<div style="text-align: center; font-weight: 700;">'. __('Qty', 'nbt-solution') .'</div>
						</td>
						<td align="center" style="width: 20%; '.$td_header_style.'">
							<div style="text-align: left; font-weight: 700;">'. __('Price', 'nbt-solution') .'</div>
						</td>
						<td align="center" style="width: 20%; '.$td_header_style.'">
							<div style="text-align: left; font-weight: 700;">'. __('Total', 'nbt-solution') .'</div>
						</td>
					</tr>';
					foreach ( $order->get_items() as $item ) {
						$product_id = $item['product_id'];
						$_product = wc_get_product( $product_id );
					$html .= '
					<tr>
						<td align="center" style="width: 50%; '.$td_body_style.'">
							<div style="text-align: left; font-weight: 400;">'. $item->get_name() .'</div>
						</td>
						<td align="center" style="width: 10%; '.$td_body_style.'">
							<div style="text-align: center; font-weight: 400;">'.$item->get_quantity() .'</div>
						</td>
						<td align="center" style="width: 20%; '.$td_body_style.'">
							<div style="text-align: left; font-weight: 400;">'. strip_tags( wc_price($_product->get_price()) ) .'</div>
						</td>
						<td align="center" style="width: 20%; '.$td_body_style.'">
							<div style="text-align: left; font-weight: 400;">'.strip_tags( wc_price($item->get_total()) ) .'</div>
						</td>
					</tr>';
					}
				$html .= '</tbody>
			</table>';


			$footer_td_style = 'vertical-align: top; text-align: left; '.$fix_padding;
			$html .= '<table style="width: 100%; border-spacing: 0; border-collapse: collapse; font-size: 16px; color: '. ${$module_id.'_text_color'}.';">
				<tbody>';

					if( $subtotal = (float)$order->get_subtotal() ){	
					$html .= '<tr>
							<td align="center" colspan="2" style="width: 60%; '.$footer_td_style.'"></td>
							<td align="center" class="strong" style="width: 20%; '.$footer_td_style.'">'. esc_html__( 'Subtotal', 'woocommerce' ) .'</div></td>
							<td align="center" style="width: 20%; '.$footer_td_style.'">
								<div style="text-align: left;">'. strip_tags( wc_price($subtotal) ) .'</div>
							</td>
						</tr>';
					}

					if( $gettotals = (float)$order->get_total() ) {

					$html .= '<tr>
						<td align="center" colspan="2" style="width: 60%; '.$footer_td_style.'"> </td>
						<td align="center" class="strong" style="width: 20%; '.$footer_td_style.'">'. esc_html__( 'Total', 'woocommerce' ) .'</div></td>
						<td align="center" style="width: 20%; '.$footer_td_style.'">
							<div style="text-align: left;">'. strip_tags( wc_price($gettotals) ) .'</div>
						</td>
					</tr>';
					}
				$html .= '</tbody>
			</table>';

			return $html;
		}

		static function get_template_temp2( $order, $settings, $download ) {
			extract($settings);
			$module_id = 'nbt_'.NBT_Pdf_Creator_Settings::$id;

			$fullname_order = get_post_meta($order->get_id(), '_billing_first_name', true). ' ' .get_post_meta($order->get_id(), '_billing_last_name', true);

			$logo_height = empty(${$module_id.'_logo_height'}) ? false : ' height="' . ${$module_id.'_logo_height'} . '"';

			$fix_break = $fix_break_one = $fix_padding = null;
			$fix_padding = ' padding: 15px 30px;';
			if( $download ) {
				$fix_break = '<br /><br />';
				$fix_break_one = '<br />';
				$fix_padding = ' padding: 30px 30px 20px 30px;';
			}
			$html = $fix_break. '<table style="width: 100%; margin-bottom: 5px;">
				<tbody>
					<tr>
						<td style="width: 60%; vertical-align: top;">
							<p class="strong" style="text-transform: uppercase; margin: 0 0 5px; font-size: 18px; color: '. ${$module_id.'_text_color'}.';">'. __('Bill to', 'nbt-solution') .'</p>' . $fix_break;
							$html .= '<ul style="margin: 0 0 0 25px; padding: 0; list-style: none; color: '. ${$module_id.'_text_color'}.';">
								<li class="strong" style="color: '. ${$module_id.'_text_color'}.';text-transform: uppercase; margin-bottom: 5px;">'. $fullname_order .'<br /></li>';

								if ( $order->get_formatted_billing_address() ) {
									$order_address = str_replace($fullname_order, '', $order->get_formatted_billing_address());
									$html .= '<li style="margin-bottom: 5px;">' . ltrim(wp_kses( $order_address, array( 'br' => array() ) ), '<br />') . '<br /></li>';
								} else {
									$html .= '<li style="line-height: 22px;"><strong>' . __( 'Address:', 'woocommerce' ) . '</strong> ' . __( 'No billing address set.', 'woocommerce' ) . '<br /></li>';
								}

							$html .= '</ul>';
						$html .= '</td>

						<td style="width: 40%; vertical-align: top; text-align: left; border-left: 3px solid #d7d7d7; padding-left: 15px;">
							<img class="logo" src="'. ${$module_id.'_logo'} .'"'.$logo_height.' style="display: block; max-width: none;" />'.$fix_break .'
							<p style="display: block; margin-top: 10px; color: '. ${$module_id.'_text_color'}.'; margin: 0px; line-height: 23px; font-size: 16px;">'. ${$module_id.'_address'} .'</p>
						</td>
					</tr>
				</tbody>
			</table>' . $fix_break_one;


			$html .= '<table style="width: 100%; margin-bottom: 5px;">
				<tbody>
					<tr>
						<td style="width: 60%; vertical-align: top;">
							<p class="heading strong" style="color: '. ${$module_id.'_text_color'}.'; text-transform: uppercase; margin: 0 0 10px; font-size: 28px;">'. __('Invoice', 'nbt-solution') .'</p>'.$fix_break_one.'
							<p class="uppercase invoice" style="font-size: 16px; color: '. ${$module_id.'_text_color'}.'">'. __('Invoice', 'nbt-solution').' #'. $order->get_order_number().'</p>'.$fix_break_one.'
							<p class="uppercase date" style="font-size: 16px; color: '. ${$module_id.'_text_color'}.';">'. __('Date', 'nbt-solution').' '. wc_format_datetime( $order->get_date_created() ) .'</p>
						</td>
					</tr>
				</tbody>
			</table>' . $fix_break;


			$item_head_style = 'vertical-align: top; background-color: '. ${$module_id.'_primary_color'}.'; color: #fff;'.$fix_padding.' text-transform: uppercase; font-weight: 700; border: 1px solid transparent;';
			$item_body_style = 'vertical-align: top; color: '. ${$module_id.'_text_color'}.';'.$fix_padding.' border: 1px solid #ccc;';
			$item_subfooter_style = 'vertical-align: top;'.$fix_padding;
			$html .= '<table class="item-orders" style="width: 100%; border-spacing: 0; border-collapse: collapse; margin-top: 30px;">
				<tbody>
					<tr>
						<td style="width: 50%; '. $item_head_style .'">'. __('Product Name', 'nbt-solution') .'</td>
						<td align="center" style="width: 10%; text-align: center; '. $item_head_style .'">'. __('Qty', 'nbt-solution') .'</td>
						<td style="width: 20%; '. $item_head_style .'">'. __('Price', 'nbt-solution') .'</td>
						<td style="width: 20%; '. $item_head_style .'">'. __('Total', 'nbt-solution') .'</td>
					</tr>';
					foreach ( $order->get_items() as $item ) {
						$product_id = $item['product_id'];
						$_product = wc_get_product( $product_id );
					$html .= '
					<tr>
						<td style="width: 50%; '. $item_body_style .'">'. $item->get_name() .'</td>
						<td style="width: 10%; '. $item_body_style .'">'.$item->get_quantity() .'</td>
						<td style="width: 20%; '. $item_body_style .'">'. strip_tags( wc_price($_product->get_price())) .'</td>
						<td style="width: 20%; '. $item_body_style .'">'. strip_tags(wc_price($item->get_total())) .'</td>
					</tr>';
					}

					if( $subtotal = (float)$order->get_subtotal() ) {
					$html .= '
					<tr>
						<td colspan="2" style="width: 60%; '. $item_subfooter_style .' border-left: 1px solid #ccc;"></td>
						<td class="strong" style="width: 20%; '. $item_subfooter_style .' color: '. ${$module_id.'_text_color'}.'; border: 0;">'. esc_html__( 'Subtotal', 'woocommerce' ) .'</td>
						<td style="width: 20%; '. $item_subfooter_style .' color: '. ${$module_id.'_text_color'}.'; border-right: 1px solid #ccc;">'. strip_tags(wc_price($subtotal)) .'</td>
					</tr>';
					}

					if( $gettotals = (float)$order->get_total() ) {

					$html .= '<tr>
						<td colspan="2" style="width: 60%; background-color: '. ${$module_id.'_primary_color'}.'; color: #fff; '. $item_subfooter_style .' border-left: 1px solid #ccc;"></td>
						<td class="strong" style="width: 20%; background-color: '. ${$module_id.'_primary_color'}.'; color: #fff; '. $item_subfooter_style .' ">'. esc_html__( 'Total', 'woocommerce' ) .'</td>
						<td style="width: 20%; background-color: '. ${$module_id.'_primary_color'}.'; color: #fff; '. $item_subfooter_style .' border-right: 1px solid #ccc;">'. strip_tags(wc_price($gettotals)) .'</td>
					</tr>';
					}
				$html .= '</tbody>
			</table>';

			$html .= '<table style="margin-top: 15px; width: 100%;">
				<tbody>
					<tr>
						<td style="width: 100%;"><p style="color: '. ${$module_id.'_text_color'}.'; line-height: 22px;">'. sprintf(__('Thank you for your business<br />payment is due max 7 days after invoice without deduction.', 'nbt-solution')) .'</p></td>
					</tr>
				</tbody>
			</table>';
			return $html;
		}

		static function get_template_temp3 ( $order, $settings ) {
			extract($settings);
			$module_id = 'nbt_'.NBT_Pdf_Creator_Settings::$id;

			$fullname_order = get_post_meta($order->get_id(), '_billing_first_name', true). ' ' .get_post_meta($order->get_id(), '_billing_last_name', true);

			$html = '<table style="width: 100%;">
				<tbody>
					<tr>
						<td style="width: 70%; vertical-align: top;">
							<img class="logo" src="'. ${$module_id.'_logo'} .'" style="max-width: none;" />
							<p style="margin-bottom: 0; color: '. ${$module_id.'_text_color'}.'; margin-top: 5px; line-height: 23px; font-size: 16px;">'. ${$module_id.'_address'} .'</p>
						</td>
						<td align="right" style="vertical-align: top; width: 30%; text-align: right">
							<p class="strong" style="margin: 0; text-align: right; text-transform: uppercase; color: '. ${$module_id.'_text_color'}.'; font-size: 28px;">'. __('Invoice', 'nbt-solution') .'</p>
						</td>
					</tr>
				</tbody>
			</table><br />';


			$html .= '<table class="item-orders" style="width: 100%; border-spacing: 0; border-collapse: collapse;">
				<tbody>
					<tr>
						<td style="width: 50%;"></td>
						<td align="center" style="vertical-align: top; width: 50%; height: 45px; line-height: 30px; background-color: '. ${$module_id.'_secondary_color'}.'; color: #fff; padding: 0 30px; font-size: 16px;">'. __('Invoice', 'nbt-solution').' #'. $order->get_order_number() .' | '. __('Date', 'nbt-solution') . ' ' . wc_format_datetime( $order->get_date_created() ) .'</td>
					</tr>
				</tbody>
			</table>


			<table style="background-color: '. ${$module_id.'_primary_color'}.'; color: #fff; border-spacing: 0; border-collapse: collapse; width: 100%;">
				<tbody>
					<tr>
						<td align="center" valign="middle" style="width: 20%; background-color: '. ${$module_id.'_primary_color'}.'; color: #fff; padding: 0 30px;">
							<div class="strong" style="text-align: left; margin-top: 5px; font-size: 18px;">'. __('Bill to', 'nbt-solution') .'</div>
						</td>
						<td width="30%" align="left" valign="middle"><br />
							<p class="strong" style="margin: 0 0 0; font-size: 18px;">'. $fullname_order .'</p>
							<div>';
							
							if ( $order->get_formatted_billing_address() ) {
								$order_address = str_replace($fullname_order, '', $order->get_formatted_billing_address());
								$html .= '<p style="font-size: 16px;line-height: 22px;">' . ltrim(wp_kses( $order_address, array( 'br' => array() ) ), '<br />') . '</p>';
							} else {
								$html .= '<p style="font-size: 16px;line-height: 22px;">' . __( 'Address:', 'woocommerce' ) . ' ' . __( 'No billing address set.', 'woocommerce' ) . '</p>';
							}
						$html .= '</div>
						</td>
						<td align="center" valign="middle" style="width: 20%; background-color: '. ${$module_id.'_primary_color'}.'; color: #fff; padding: 0 30px;">
							<div class="strong" style="text-align: left; margin-top: 5px; font-size: 18px;">'. __('Ship to', 'nbt-solution') .'</div>
						</td>
						<td width="30%" align="left" valign="middle"><br />
								<p class="strong" style="margin: 0 0 0; font-size: 18px;">'. $fullname_order .'</p>
								<div>';
								if ( $order->get_formatted_shipping_address() ) {
									$ship_address = str_replace($fullname_order, '', $order->get_formatted_shipping_address()); 
									$html .= '<p style="font-size: 16px;line-height: 22px;">' . wp_kses( $ship_address , array( 'br' => array() ) ) . '</p>';
								} else {
									$html .= '<p style="font-size: 16px;line-height: 22px;">' . ltrim(wp_kses( $order_address, array( 'br' => array() ) ), '<br />') . '</p>';
								}
							$html .= '</div>
						</td>
					</tr>
				</tbody>
			</table><br /><br />';


			$td_header_style = 'vertical-align: top; height: 45px; line-height: 28px; background-color: '. ${$module_id.'_secondary_color'}.'; color: #fff; padding: 0 30px; font-size: 16px;';

			$td_body_style = 'vertical-align: top; height: 45px; line-height: 28px; background-color: #f2f2f2; color: '. ${$module_id.'_text_color'}.'; padding: 0 30px;  font-size: 16px;';
			$td_footer_style = 'vertical-align: top; height: 45px; line-height: 28px; background-color: '. ${$module_id.'_primary_color'}.'; color: #fff; padding: 0 30px; font-size: 16px;';

			$html .= '<table class="item-orders" style="width: 100%; border-spacing: 0; border-collapse: collapse;">
				<tbody>
					<tr>
						<td align="left" class="strong" style="width: 50%; '. $td_header_style .'">'. __('Product Name', 'nbt-solution') .'</td>
						<td align="left" class="strong" style="width: 10%; '. $td_header_style .'">'. __('Qty', 'nbt-solution') .'</td>
						<td align="left" class="strong" style="width: 20%; '. $td_header_style .'">'. __('Price', 'nbt-solution') .'</td>
						<td align="left" class="strong" style="width: 20%; '. $td_header_style .'">'. __('Total', 'nbt-solution') .'</td>
					</tr>';
					foreach ( $order->get_items() as $item ) {
						$product_id = $item['product_id'];
						$_product = wc_get_product( $product_id );
					$html .= '
					<tr>
						<td align="left" style="width: 50%; '.$td_body_style.'">'. $item->get_name() .'</td>
						<td align="left" style="width: 10%; '.$td_body_style.'">'.$item->get_quantity() .'</td>
						<td align="left" style="width: 20%; '.$td_body_style.'">'. strip_tags(wc_price($_product->get_price())) .'</td>
						<td align="left" style="width: 20%; '.$td_body_style.'">'. strip_tags(wc_price($item->get_total())) .'</td>
					</tr>';
					}

						if( $subtotal = (float)$order->get_subtotal() ){

						
					$html .= '
					<tr>
						<td align="left" colspan="2" style="width: 60%; '. $td_body_style .'"></td>
						<td align="left" class="strong" style="width: 20%; '. $td_body_style .'">'. esc_html__( 'Subtotal', 'woocommerce' ) .'</td>
						<td align="left" style="width: 20%; '. $td_body_style .'">'. strip_tags(wc_price($subtotal)) .'</td>
					</tr>';
					}

					if( $gettotals = (float)$order->get_total() ) {

					$html .= '<tr>
						<td align="left" colspan="2" style="width: 60%; '. $td_footer_style .'"></td>
						<td align="left" class="strong" style="width: 20%;'. $td_footer_style .'">'. esc_html__( 'Total', 'woocommerce' ) .'</td>
						<td align="left" style="width: 20%;'. $td_footer_style .'">'. strip_tags(wc_price($gettotals)) .'</td>
					</tr>';
					}
				$html .= '</tbody>
			</table>';

			$html .= '<table style="margin-top: 15px; width: 100%;">
				<tbody>
					<tr>
						<td style="width: 100%; "><p class="note-temp" style="color: '. ${$module_id.'_text_color'}.';">'. sprintf(__('Thank you for your business<br />payment is due max 7 days after invoice without deduction.', 'nbt-solution')) .'</p></td>
					</tr>
				</tbody>
			</table>';

			return $html;
		}

	}
}