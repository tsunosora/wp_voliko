<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once NBT_PDF_PATH . 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class NBT_PDF_Admin {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts') );
		add_action( 'woocommerce_admin_order_actions_end', array( $this, 'add_pdf_button' ) );

		add_action( 'wp_ajax_nopriv_nbtpdf_download', array( $this, 'dowwnload_pdf') );
		add_action( 'wp_ajax_nbtpdf_download', array( $this, 'dowwnload_pdf') );

	}

	public function enqueue_scripts($hook) {
		if( $hook != 'nbt-solutions_page_solutions-settings' ) {
			return;
		}

		wp_enqueue_style( 'pdf_admin', NBT_PDF_URL . 'assets/css/admin.css'  );
	}

	public function add_pdf_button( $order ){
		if ( $order->get_status() == 'trash' ) {
			return;
		}

		$data = array(
			'url'		=> wp_nonce_url( admin_url( "admin-ajax.php?action=nbtpdf_download&order_id=" . $order->get_id() ), 'netbase_pdf_download' ),
			'img'		=> NBT_PDF_URL . "assets/img/invoice.png",
			'alt'		=> __('PDF Invoice', 'nbt-solution'),
		);

		printf('<a href="%1$s" class="button tips nbt_pdf-creator" target="_blank" alt="%2$s" data-tip="%2$s" style="display: inline-flex; justify-content: center; align-items: center;"><img src="%3$s" alt="%2$s" width="16" style="margin: 0 0 0 -2px; height: 14px;"></a>',
			esc_url($data['url']),
			esc_attr($data['alt']),
			esc_url($data['img'])
		);
	}

	public function dowwnload_pdf() {
		global $current_user;

		$json = array();
		$error = false;

		if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'netbase_pdf_download' ) ) {
			$error = true;
			$login_error = esc_html__('You are not authorized to access this page', 'nbt-solution');

			if( $this->is_ajax() ) {
				$json['message'] = $login_error;
			}else {
				wp_die( $login_error );
			}
		}

		$order_id = isset($_REQUEST['order_id']) ? absint($_REQUEST['order_id'] ) : 0;

		if( is_admin() && ! $current_user->exists() ) {
			$error = true;
			$login_error = esc_html__('Please login to download this PDF Invoice!', 'nbt-solution');

			if( $this->is_ajax() ) {
				$json['message'] = $login_error;
			}else {
				wp_die( $login_error );
			}

			
		}

		if( empty($order_id) ) {
			$error = true;
			$json['message'] = esc_html__('This order not found, please check again.', 'nbt-solution');
		}


		if( empty($error) ) {
			$json = $this->render_template($order_id, $json);
		}

		wp_send_json($json);
	}

	public function render_template($order_id, $json = array() ) {
		$order = wc_get_order($order_id);
		$settings = NB_Solution::get_setting('pdf-creator');
		$module_id = NBT_Pdf_Creator_Settings::$id;

		$callback = 'get_template_' . $settings['nbt_'.$module_id.'_template'];


		if( ! method_exists('NBT_Solutions_PDF_Template', $callback) ) {
			$error = true;
			$callback_error = sprintf( __('Callback function %s not exists!', 'nbt-solution'), '<strong>'. $callback .'</strong>' );

			if( $this->is_ajax() ) {
				$json['message'] = $callback_error;
			}else {
				wp_die( $callback_error );
			}
		}

		if( empty($error) ) {
			$GLOBALS['order'] = $order;

			$loadHtml = $this->display_header($settings, $module_id, true); 
			$loadHtml .= NBT_Solutions_PDF_Template::$callback( $order, $settings, true );
			$loadHtml .= $this->display_footer($settings, $module_id); 

			// echo $loadHtml;
			// die();

			$fileName = 'invoice-'.$order->get_id().'-'.time().'.pdf';

			try {
				$options = new Options();
				$options->setIsRemoteEnabled( true );

				$dompdf = new Dompdf($options);
				$dompdf->loadHtml($loadHtml);
				$dompdf->setPaper('A4', $settings['nbt_'.$module_id.'_page_orientation']);
				$dompdf->render();

				$upload_dir = wp_upload_dir();
				$file_to_save = $upload_dir['path'] . '/' . $fileName;

				// save the pdf file on the server
				file_put_contents($file_to_save, $dompdf->output()); 

				//NBT_Solutions_Pdf_Creator::downloadFile($upload_dir['url'] . '/' . $filename, $filename);


				if( $this->is_ajax() ) {
					$json['complete'] = true;
					$json['redirect'] = $upload_dir['url'] . '/' . $fileName;
				}else {
					wp_redirect($upload_dir['url'] . '/' . $fileName);
					die();
				}

				
				//
			} catch (Exception $e) {
			    $json['message'] = $e->getMessage();
			}
		}


		return $json;
	}

	public function display_header($settings, $module_id, $download = false) {
		global $order;
		$globalHTML = '
		<!DOCTYPE html>
		<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
			if( empty( $this->is_ajax() ) ) {
			$globalHTML .= '<link rel="stylesheet" href="'. NBT_PDF_URL .'assets/css/pdf_' . $settings['nbt_'. $module_id.'_template'] .'.css" type="text/css" media="all" />
				<script type="text/javascript" src="' . home_url() .'/wp-includes/js/jquery/jquery.js?ver=1.12.4"></script>
				<script type="text/javascript" src="' . home_url() .'/wp-includes/js/jquery/jquery-migrate.js?ver=1.4.1" defer onload=""></script>
				<script type="text/javascript" src="' . NBT_PDF_URL .'assets/js/jquery.blockUI.js" defer onload=""></script>
				<script type="text/javascript">
					var admin_ajax = "'. admin_url("/admin-ajax.php") .'";
					var order_id = '. $order->get_id() .';
					var nonce = "'. wp_create_nonce( 'netbase_pdf_download' ) .'";
				</script>
				<script type="text/javascript" src="' . NBT_PDF_URL .'assets/js/frontend.js" defer onload=""></script>';
			}

			$dataFont = $settings['nbt_'. $module_id.'_fonts'];


			$styleFont = $linkFont = '';
			switch ($dataFont) {
				case 'default':
					$linkFont .= '<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&amp;subset=vietnamese" rel="stylesheet">';
					$styleFont .= 'body {
						max-width: 100%;
   						height: auto;
						font-family: Roboto, sans-serif;
					}

					.strong {
						font-weight: 700;
					}';
					break;
				case 'chinese':
					$styleFont .= '@font-face {
						font-family: "Firefly Sung";
						font-style: normal;
						font-weight: 400;
						src: url('. NBT_PDF_URL . 'assets/fonts/fireflysung.ttf) format("truetype");
					}
					body {
						max-width: 100%;
   						height: auto;
						font-family: "Firefly Sung", sans-serif;
					}';
					break;
				case 'korean':
					$linkFont .= '<link href="https://fonts.googleapis.com/css?family=Sunflower:400,500,700&amp;subset=korean" rel="stylesheet">';
					$styleFont .= 'body {
						max-width: 100%;
   						height: auto;
						font-family: Sunflower, sans-serif;
					}

					.strong {
						font-weight: 700;
					}';
					break;
				default:
					# code...
					break;
			}

			$globalHTML .= $linkFont . '<style>' . $styleFont;
				if( ! $download ) {
					$globalHTML .= '
					body.preview table.item-orders td {
						vertical-align: middle !important;
					}';
				}
			$globalHTML .= '</style>';

		$globalHTML .= '</head>';
		$globalHTML .= sprintf('<body class="%s">', empty( $this->is_ajax() ) ? 'preview' : 'download' );

		return $globalHTML;
	}

	public function display_footer($settings, $module_id) {
		$globalHTML = '</body></html>';

		return $globalHTML;
	}

	public function is_ajax() {
		if( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
			return true;
		}

		return false;
	}
}

$GLOBALS['PDF_Admin'] = new NBT_PDF_Admin();