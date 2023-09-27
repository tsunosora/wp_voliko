<?php

class WooPanel_Template_Profile_Listing extends WooPanel_List_Table {
	public $type;
	public $type_edit;
	public $type_settings;
	public $post_type;

	private $comment_status;
	private $user_can;
	private $get_statuses;
	private $current_user;

	protected $store_user;

	public function __construct( $args = array() ) {
		global $query_vars;
		$this->type = 'store';
		$this->type_edit = isset($args['type_edit']) ? $args['type_edit'] : 'comment';
		
		$this->post_type = isset($args['post_type']) ? $args['post_type'] : 'post';
		$this->type_settings = array(
            'name' => esc_html__('Profile Listing', 'woopanel' ),
            'singular_name' => esc_html__('Profile Listing', 'woopanel' ),
			'not_found' => false,
			'add_new' => false,
			'create_permission' => false,
			'search_items' => false
		);
		$this->get_statuses = array(
			'hold'	    => esc_html__('Pending', 'woopanel' ),
            'approve'	=> esc_html__('Approved', 'woopanel' ),
            'spam'	    => esc_html__('Spam', 'woopanel' ),
            'trash'     => esc_html__('Trash', 'woopanel' ),
        );
		
		parent::__construct( array(
			'type'   => 'WP_User_Query',
			'screen'         => 'customers',
			'columns'        => array(
				'avatar'      => '&nbsp;',
				'title'      => esc_html__( 'Store', 'woopanel' ),
				'street'    => esc_html__( 'Street', 'woopanel' ),
				'city'	=> esc_html__( 'City', 'woopanel' ),
				'state'	=> esc_html__( 'State', 'woopanel' ),
				'submitted_on'  => esc_html__( 'Created On', 'woopanel' )
			),
			'primary_columns' => 'title'
		) );
	
		$this->comments = $this->get_query(array(
			'per_page' => $this->per_page,
			'offset' => ($this->paged - 1) * $this->per_page,
			'orderby'      => 'ID',
			'order'        => 'ASC'
		));

		$this->comment_status = isset( $_REQUEST['comment_status'] ) ? wp_unslash( $_REQUEST['comment_status'] ) : null;
		$this->current_user = woopanel_current_user();
		$this->hooks_table();
	}

	public function lists() {
		$this->display();
	}
	
	public function form() {
		global $wp_query, $wpdb;

		$prefix = WOOPANEL_STORE_LOCATOR_PREFIX;

		$store_id = 0;
		$label = esc_html__('Add Store', 'woopanel');
		if( isset($wp_query->query_vars['store_user']) ) {
			$store = $wp_query->query_vars['store_user']->data;

			$store_id = $store->id;
 			if( $this->current_user['roles'] != 'administrator' && $store->user_id != $this->current_user['ID'] ) {
				woopanel_redirect($this->get_view_url());
			}


			$label = esc_html__('Edit Store', 'woopanel');

			if( ! empty($_POST['_type']) ) {
				$form_data = stripslashes_deep($_POST);

				$store_id = absint($_POST['storeID']);
				$wpdb->update($prefix."stores",
					apply_filters( 'woopanel_stores_field_update', array(
						'title'			=> $form_data['title'],
						'name'			=> sanitize_title($form_data['title']),
						'phone'			=> $form_data['phone'],
						'street'		=> $form_data['street'],
						'postal_code'	=> $form_data['postal_code'],
						'city'			=> $form_data['city'],
						'state'			=> $form_data['state'],
						'lat'			=> $form_data['lat'],
						'lng'			=> $form_data['lng'],
						'country'		=> $form_data['country'],
						'logo_id'		=> $form_data['logo_id'],
						'banner_id'		=> $form_data['banner_id'],
						'user_id'		=> $form_data['user_id'],
						'intro'			=> $form_data['intro'],
						'tos'			=> $form_data['tos'],
						'updated_on' 	=> date('Y-m-d H:i:s')
					), $form_data ),
					array('id' => $store_id)
				);

				wpl_add_notice(
					'store-edit',
					esc_html__('Store Saved.', 'woopanel')
				);

				$store = (object)$form_data;
			}
		}else {

			if( ! empty($_POST['_type']) ) {
				$error = false;
				$form_data = stripslashes_deep($_POST);
		
				unset($form_data['storeID']);
				unset($form_data['_type']);

				$form_data['name'] = sanitize_title($form_data['title']);

				if( empty($form_data['title']) ) {

					wpl_add_notice(
						'store-add',
						esc_html__('Please enter store name!', 'woopanel'),
						'error'
					);
					$error = true;
				}

				if( empty($error) && $store_id = $wpdb->insert( $prefix.'stores', $form_data))
				{
					$form['storeID'] = $form_data;
					$store = (object)$form_data;

					wpl_add_notice(
						'store-add',
						esc_html__('Create store successful!', 'woopanel')
					);
				}
			}
		}

		$new_countries = array();
		$countries = $wpdb->get_results( "SELECT * FROM {$prefix}countries ORDER BY country ASC" );
		if( $countries ) {
			foreach ($countries as $key => $country) {
				$new_countries[$country->id] = $country->country;
			}
		}


		/* CATEGORIES */
		if( ! empty($_POST['story_category']) && ! empty($store_id) ) {
			$story_categories = wp_unslash($_POST['story_category']);
			$results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$prefix}stores_categories WHERE store_id = %d", $store_id) );

			if( ! empty($results) ) {
				foreach ($results as $key => $value) {
					if( ! in_array($value->category_id, $story_categories)) {
						$wpdb->delete( $prefix . 'stores_categories', array( 'id' => $value->id ) );
					}else {
						$key = array_search($value->category_id, $story_categories); 
						unset($story_categories[$key]);
					}
				}
			}

			
			foreach ($story_categories as $key => $scat ) {
				$wpdb->insert(
					$prefix . 'stores_categories',
					array(
						'category_id' => $scat,
						'store_id' => $store_id,
						'created_on' 	=> date('Y-m-d H:i:s')
					),
					array('%d', '%d', '%s')
				);
			}
		}


		woopanel_get_template_part('vendor/edit', '', array(
			'label' 	 => $label,
			'type'	  	 => $this->type,
			'store_id'	 => $store_id,
			'store'	 => $store,
			'countries' => $new_countries
		));
	}

	public function hooks_table() {
		add_filter("woopanel_{$this->type}_avatar_column", array($this, 'avatar_column'), 99, 2);
		add_filter("woopanel_{$this->type}_title_column", array($this, 'title_column'), 99, 2);
		add_filter("woopanel_{$this->type}_street_column", array($this, 'street_column'), 99, 2);
		add_filter("woopanel_{$this->type}_city_column", array($this, 'city_column'), 99, 2);
		add_filter("woopanel_{$this->type}_state_column", array($this, 'state_column'), 99, 2);
		add_filter("woopanel_{$this->type}_submitted_on_column", array($this, 'submitted_on_column'), 99, 2);

		add_action("woopanel_{$this->type}_filter_display", array($this, 'filter_display'), 99, 2 );
        add_action("woopanel_comments_no_item_icon", array($this, 'no_item_icon'));
	}


	public function set_status($status) {
		switch( $status ) {
			case '1':
				$status = 'hold';
				break;
			case '0':
				$status = 'approve';
				break;
		}

		return $status;
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
						<option selected='selected' value="all"><?php esc_html_e( 'All status', 'woopanel' );?></option>
						<?php foreach( $this->get_statuses as $k_status => $val_status) {
							printf('<option value="%s" %s>%s</option>', $k_status, selected( $k_status, $status, false ), $val_status);
						}?>
					</select>
				</div>
			</div>
			<div class="d-md-none m--margin-bottom-10"></div>
		</div>
		<?php
	}

    public function no_item_icon() {
        echo '<i class="flaticon-chat-1"></i>';
    }

	public function avatar_column($return, $store ) {
		$this->store_user = WooDashboard()->store->get( $store->name );
		echo $this->store_user->get_html_logo(40);
	}

	public function title_column($return, $store) {
		$cm_status = array();
		if( isset($_GET['status']) ) {
			$cm_status['status'] = $_GET['status'];
		}
		echo '<strong><a class="row-title" href="' . esc_url( $this->get_view_url() ) . '?id=' . absint( $store->id ) . '" title="'. esc_attr($store->title) .'">'. esc_attr($store->title) .'</a></strong><br />';
		if ( $this->user_can ) {
			if ( ! empty( $comment->comment_author_email ) ) {
				/** This filter is documented in wp-includes/comment-template.php */
				$email = apply_filters( 'comment_email', $comment->comment_author_email, $comment );

				if ( ! empty( $email ) && '@' !== $email ) {
					printf( '<a href="%1$s">%2$s</a><br />', esc_url( 'mailto:' . sanitize_email($email) ), esc_html( $email ) );
				}
			}

			$author_ip = get_comment_author_IP( $comment );
			if ( $author_ip ) {
				$cm_status['search_name'] = $author_ip;
				$author_ip_url = add_query_arg(
					$cm_status,
					$this->get_view_url()
				);
				if ( 'spam' === $this->comment_status ) {
					$author_ip_url = add_query_arg( 'comment_status', 'spam', $author_ip_url );
				}
				printf( '<a href="%1$s">%2$s</a>', esc_url( $author_ip_url ), esc_html( $author_ip ) );
			}
		}
	}

	public function street_column($return, $store) {
		echo esc_attr($store->street);
	}

	public function city_column($return, $store) {
		echo esc_attr($store->city);
	}
	
	public function state_column($return, $store) {
		echo esc_attr($store->state);
	}

	public function submitted_on_column($return, $store) {
		echo '<div class="submitted-on">';
		echo esc_attr($store->created_on);
		echo '</div>';
	}

	public function get_view_url( $user_id  = null, $edit = false ) {
		$slug = isset($user_id) ? '?id='. absint($user_id) : '';
		$type = $this->type;
		if( $edit ) {
			$type = $this->type_edit;
		}
		return esc_url( woopanel_get_endpoint_url($type) . esc_attr( $slug ) );
	}


	/**
	 *
	 * @global array    $avail_post_stati
	 * @global WP_Query $wp_query
	 * @global int      $per_page
	 * @global string   $mode
	 */
	public function get_query($args = null) {
		global $wpdb, $current_user;

		$defaults = array(
			'post_type'   => $this->post_type,
			'post_status' => 'publish'
		);

		$defaults['status'] = 'all';
		if( isset($_GET['status']) && isset($this->get_statuses[$_GET['status']]) ) {
			$defaults['status'] = $_GET['status'];
		}

		if( isset($_GET['search_name']) ) {
			$defaults['search'] = $_GET['search_name'];
		}

		if( isset($_GET['post']) ) {
			$defaults['post_id'] = $_GET['post'];
		}



		$user = wp_get_current_user();
		$query = array();

		$prefix = WOOPANEL_STORE_LOCATOR_PREFIX;


		if( ! empty($args['count']) ) {
			$query['fields']  = "SELECT COUNT(DISTINCT s.id) as total_stores FROM {$prefix}stores as s";
		}else {
			// Custom select field here
			$query['fields']  = "SELECT DISTINCT s.id, s.* FROM {$prefix}stores as s";
		}

		//$query['join']   = "INNER JOIN {$wpdb->posts} AS posts ON comments.comment_post_ID = posts.ID";
		$query['where']	   = "WHERE 1=1 ";
		$query['where']	  .= "AND s.user_id = '". absint($current_user->ID) ."' ";


		// if( in_array($user->roles[0], NBWooCommerceDashboard::$permission)) {
		// 	$query['where']	  .= "AND posts.post_author = '". absint($user->ID) ."' ";
		// }

		// $query['where'] .= "AND posts.post_status != 'trash' ";

		// if( isset($_GET['post']) && is_numeric($_GET['post']) ) {
		// 	$query['where']	  .= "AND comments.comment_post_ID = '". absint($_GET['post']) ."' ";
		// }
		
		// if( isset($_GET['status']) && isset($this->get_statuses[$_GET['status']]) ) {
		// 	$query['where'] .=  "AND comments.comment_approved = '" . esc_attr( $this->exchange_status($_GET['status']) ) . "' ";
		// } else if(!isset($_GET['status']) || (isset($_GET['status']) && ($_GET['status']=='all' || $_GET['status']=='') ) ) {
  //           $query['where'] .=  "AND (comments.comment_approved = '0' OR comments.comment_approved = '1') ";
  //       }

		// if( isset($_GET['search_name']) ) {
		// 	$search_name = strip_tags($_GET['search_name']);
		// 	$search_like = '%'.esc_attr( $wpdb->esc_like( $search_name ) ).'%';
		// 	$query['where'] .=  $wpdb->prepare("AND (((comments.comment_content LIKE %s) OR (comments.comment_author_email LIKE %s) OR (comments.comment_author LIKE %s)))", $search_like, $search_like, $search_like);
		// }

		$query['orderby'] = "ORDER BY s.created_on DESC";

		if( empty($args['count']) ) {
			$query['limit']   = "LIMIT {$args["offset"]}, {$args["per_page"]}";
		}

		$sql = implode(' ', $query);

  		if( ! empty($args['count']) ) {
			$results = $wpdb->get_row( $sql );

			return $results->total_stores;
		}else {
			return $wpdb->get_results($sql);
		}

	}

	public function exchange_status($status) {
		if( $status == 'approve' ) {
			$status = 1;
		}

		if( $status == 'hold' ) {
			$status = 0;
		}

		return $status;
	}

	public function has_items() {
		$query = $this->get_query(array(
			'count' => true
		));

		if( ! empty( $query ) ) {
			return true;
		}
	}

	/**
	 * Display the table
	 *
	 * @since 1.0.0
	 */
	public function display_rows_or_placeholder() {

		if ( $this->has_items() ) {
			foreach ( $this->comments as $index => $user ) {
				?>
				<tr id="cm-hide-<?php echo esc_attr($user->comment_ID);?>" class="wpl-datatable__row wpl-datatable__row_cmhide" style="display: none">
					<td class="wpl-datatable__cell--center wpl-datatable__cell wpl-datatable__cell--check">
						<span><label class="m-checkbox m-checkbox--single m-checkbox--solid m-checkbox--brand" for="cb-select-">
								<input id="cb-select-" type="checkbox" name="user[]" value="">
								<span></span>
							</label>
						</span>
					</td>
					<td class="wpl-datatable__cell column-avatar" data-colname="&nbsp;">
						<?php echo get_avatar( $user, 32 );?>
					</td>
					<td colspan="4" class="wpl-datatable__cell">
						<div class="spam-undo-inside" style="display: none"><?php printf( esc_html__( 'Comment by %s marked as spam.', 'woopanel' ), '<strong>' . esc_attr($user->comment_author) .'</strong>' );?> <span class="undo unspam"><a href="" class="cm-destructive" data-id="<?php echo esc_attr($user->comment_ID);?>"><?php esc_html_e( 'Undo', 'woopanel' );?></a></span></div>
						<div class="trash-undo-inside" style="display: none"><?php printf( esc_html__( 'Comment by %s moved to the trash.', 'woopanel' ), '<strong>' . esc_attr($user->comment_author) .'</strong>' );?> <span class="undo untrash"><a href="" class="cm-destructive" data-id="<?php echo esc_attr($user->comment_ID);?>"><?php esc_html_e( 'Undo', 'woopanel' );?></a></span></div>
					</td>
				</tr>
				<tr id="user-<?php echo absint($user->comment_ID);?>" class="wpl-datatable__row wpl-datatable_edit author-self level-0 user-<?php echo absint($user->comment_ID);?> format-standard has-post-thumbnail tr-status-<?php echo wp_get_comment_status($user);?> x<?php echo esc_attr($user->comment_approved);?>">
					<td class="wpl-datatable__cell--center wpl-datatable__cell wpl-datatable__cell--check">
						<span>
							<label class="m-checkbox m-checkbox--single m-checkbox--solid m-checkbox--brand" for="cb-select-<?php echo absint($user->comment_ID);?>">
								<input id="cb-select-<?php echo absint($user->comment_ID);?>" type="checkbox" name="user[]" value="<?php echo absint($user->comment_ID);?>">
								<span></span>
							</label>
						</span>
					</td>

					<?php
					if( empty($this->template) ) {
						foreach( $this->columns as $key_column => $column ) {?>
							<td class="wpl-datatable__cell column-<?php echo esc_attr($key_column);?>" data-colname="<?php echo esc_attr($column);?>">
								<?php
								$action_name = "woopanel_{$this->type}_{$key_column}_column";
								if( ! has_filter($action_name) ) {
									echo '-';
								}else {
									echo apply_filters($action_name, '', $user, false);
								}
								?>
							</td>
						<?php }
					}?>
				</tr>
				<?php
			}
		}
	}

	public function comment_status($comment) {
		if( $comment == '0' ) {
			$comment = 'approve';
		}else if( $comment == '1' ) {
			$comment = 'unapprove';
		}

		return $comment;
	}

	public function display() {
		$total_items = $this->get_query(
			array('count' => true)
		);
		?>
		<form id="posts-filter" method="get">
			<div id="list-<?php echo esc_attr($this->type);?>-table" class="woopanel-list-post-table m-portlet m-portlet--mobile">
				<!--begin: Head -->
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								<?php echo esc_attr($this->type_settings['name']);?>
								<small><span class="displaying-num"><?php echo sprintf( _n( '%s item', '%s items', $total_items ), number_format_i18n( $total_items ) );?></span></small>
							</h3>
						</div>
					</div>
					<div class="m-portlet__head-tools">
						<?php if ( apply_filters("woopanel_{$this->type}_user_can_create", true) && current_user_can( $this->type_settings['create_permission'] ) ) { ?>
							<a href="<?php echo esc_html( woopanel_post_new_url($this->type) );?>" class="btn btn-secondary m-btn m-btn--icon m-btn--md">
								<span>
									<i class="flaticon-add"></i>
									<span><?php echo esc_html( $this->type_settings['add_new']); ?></span>
								</span>
							</a>
						<?php } ?>
					</div>
				</div>
				<!--end: Head -->

				<div class="m-portlet__body">


					<div class="m-datatable m-datatable--default wpl_datatable<?php if( ! $this->has_items() ) { echo ' m-datatable-empty';}?>">
						<div class="table-responsive">
							<table class="wpl-datatable__table table m-table">
								<thead class="wpl-datatable__head">
									<tr class="wpl-datatable__row">
										<?php $this->print_column_headers(); ?>
									</tr>
								</thead>

								<tbody class="wpl-datatable__body">
									<?php
									if ( $this->has_items() ) {
										$this->display_rows_or_placeholder();
									}else {
										$this->no_items();
									}?>
								</tbody>
							</table>
						</div>

						<?php
						if ( $this->has_items() ) {
							$max_num_pages = ceil($total_items / $this->per_page);
							$this->display_paginate($total_items, $max_num_pages);
						}?>
	
					</div>
				</div>
			</div>
		</form>
		<?php
	}
}