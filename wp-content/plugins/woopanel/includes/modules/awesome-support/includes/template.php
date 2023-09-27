<?php
class NBT_Solutions_Awesome_Template {
	function __construct() {
		add_action( 'woopanel_dashboard_awesome-supports_endpoint', array( $this, 'endpoint_list_table' ) );
        add_action( 'woopanel_dashboard_awesome-support_endpoint', array( $this, 'endpoint_form' ) );
        add_action( 'woopanel_dashboard_wallet-transaction_endpoint', array( $this, 'woopanel_wallet_transaction_endpoint_content' ) );

		add_action( 'woopanel_enqueue_scripts', array($this, 'woopanel_scripts'));


	}

	public function endpoint_list_table() {
        $ticket = new WooPanel_Template_Ticket();
        $ticket->lists();
	}

    public function endpoint_form() {
        global $current_user;

        $url = woopanel_get_dashboard_endpoint_url('awesome-support');


        $message = [];
        $error = false;
        $post_id = $permalink = 0;
        $title = esc_html__('Add New Ticket', 'awesome-support');
        $permission = 'create_ticket';
        $button_label = esc_html__('Submit Ticket', 'awesome-support' );
        $allowed_html = woopanel_wses_allowed_menu_html();

        $post = new stdClass();
        $post->post_title = $post->post_content = '';

        if( isset($_POST['save']) ) {
            if( ! empty($_POST['ticket_ID']) && is_numeric($_POST['ticket_ID']) ) {
                $post_id = absint($_POST['ticket_ID']);
                $my_post = array(
                    'ID'           => $post_id,
                    'post_status'   => sanitize_text_field($_POST['ticket_status']),
                );


                wp_update_post($my_post);
            }else {
                if( empty($_POST['post_title']) ) {
                    $error = true;
                    $message[] = esc_html__('Please enter subject ticket', 'woopanel');
                }

                if( empty($_POST['content']) ) {
                    $error = true;
                    $message[] = esc_html__('Please enter content ticket', 'woopanel');
                }

                 if( ! $error ) {
                    $my_post = array(
                        'post_title'    => wp_strip_all_tags( $_POST['post_title'] ),
                        'post_content'  => wpautop( wp_kses( $_POST['content'], $allowed_html ) ),
                        'post_status'   => 'queued',
                        'post_type'     => 'ticket',
                        'post_author'   => $current_user->ID
                    );



                    $post_id = wp_insert_post( $my_post );

                    update_post_meta($post_id, '_wpas_status', 'open');
                    $this->update_assignee_vendor($post_id);

                    $email_create_ticket = get_option('woopanel_email_create_ticket');
                    if( ! empty($email_create_ticket) ) {
                        woopanel_ticket_send_email(
                            $current_user->email,
                            sprintf(
                                esc_html__('Create New Ticket %s', 'woopanel'),
                                get_option('blogname')
                            ),
                            woopanel_ticket_email_content($email_create_ticket, $post_id, $my_post)
                        );
                    }

                    wp_redirect( $url .'?id=' . $post_id );
                    die();
                }


            }
        }

        if( isset($_POST['seller']) ) {
            update_post_meta($post_id, '_wpas_assignee', $_POST['seller']);
        }
                

        if( isset($_GET['id']) && ! empty($_GET['id']) ) {
            $post_id = absint($_GET['id']);
            $post = get_post($post_id);
            $add_title = esc_html__('Add New Ticket', 'awesome-support');
            $title = esc_html__('View Ticket', 'awesome-support');

            $permalink = true;
        }

        $roles = array_merge( array('administrator'), NBWooCommerceDashboard::$permission );
        $current_roles = $current_user->roles;

        $checkRole = array_diff($current_roles, $roles);


        $_assignee = absint( get_post_meta($post_id, '_wpas_assignee', true) );




        include_once NBT_AWESOME_SUPPORT_PATH . 'templates/form.php';
    }

    public function woopanel_scripts() {
    		wp_enqueue_style('select2', WOODASHBOARD_URL . 'vendors/select2/select2.min.css', array());
    		wp_enqueue_script('select2', WOODASHBOARD_URL . 'vendors/select2/select2.full.min.js', array(), WC_VERSION);	
    }

    private function update_assignee_vendor($post_id) {
        $vendor_user = 0;
        if( isset($_GET['wc_order']) && ! empty($_GET['wc_order']) ) {
            $order = wc_get_order($_GET['wc_order']);

            $order_items = $order->get_items();

            foreach( $order_items as $item ) {
                $product = get_post( $item->get_product_id()) ;

                $vendor_user = $product->post_author;
            }

            echo 'update nè' . $vendor_user;
            update_post_meta($post_id, '_wpas_assignee', $vendor_user);
        }
    }
}

new NBT_Solutions_Awesome_Template();