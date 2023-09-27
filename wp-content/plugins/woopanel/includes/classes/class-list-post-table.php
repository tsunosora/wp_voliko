<?php
class WooPanel_Post_List_Table extends WooPanel_List_Table {

	public $post_type;
	public $taxonomy;
	public $tags;
	public $post_statuses;
	public $editor;
	public $thumbnail;
	public $preview;
	public $gallery;
	public $permalink;
	public $custom_query;

    private $is_trash;


	/**
	 * Constructor.
	 *
	 * The child class should call this constructor from its own constructor to override
	 * the default $args.
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $args {
	 *     Array or string of arguments.
	 *
	 *     @type string $plural   Plural value used for labels and the objects being listed.
	 *                            This affects things such as CSS class-names and nonces used
	 *                            in the list table, e.g. 'posts'. Default empty.
	 *     @type string $singular Singular label for an object being listed, e.g. 'post'.
	 *                            Default empty
	 *     @type bool   $ajax     Whether the list table supports Ajax. This includes loading
	 *                            and sorting data, for example. If true, the class will call
	 *                            the _js_vars() method in the footer to provide variables
	 *                            to any scripts handling Ajax events. Default false.
	 *     @type string $screen   String containing the hook name used to determine the current
	 *                            screen. If left null, the current screen will be automatically set.
	 *                            Default null.
	 * }
	 */
	public function __construct( $args = array() ) {
		$this->post_statuses = isset( $args['post_statuses'] ) ? $args['post_statuses'] : get_post_statuses();

		$this->taxonomy = isset( $args['taxonomy'] ) ? $args['taxonomy'] : false;
        $this->is_trash = isset( $_GET['post_status'] ) && $_GET['post_status'] === 'trash';

		parent::__construct( array(
			'type'   => 'WP_Query',
			'post_type' => isset( $args['post_type'] ) ? $args['post_type'] : null,
			'taxonomy'	=> $this->taxonomy,
			'screen' => isset( $args['screen'] ) ? $args['screen'] : null,
			'columns' => isset( $args['columns'] ) ? $args['columns'] : array(),
			'template' => isset( $args['template'] ) ? $args['template'] : false,
			'primary_columns' => isset( $args['primary_columns'] ) ? $args['primary_columns'] : false,
			'post_statuses' => $this->post_statuses,
			'custom_query' => isset( $args['custom_query'] ) ? $args['custom_query'] : false
		) );

		$this->post_type = $args['post_type'];
		
		$this->tags = isset( $args['tags'] ) ? $args['tags'] : false;
		$this->editor = isset( $args['editor'] ) ? $args['editor'] : true;
		$this->thumbnail = isset( $args['thumbnail'] ) ? $args['thumbnail'] : true;
		$this->preview = isset( $args['preview'] ) ? $args['preview'] : true;
		$this->gallery = isset( $args['gallery'] ) ? $args['gallery'] : false;
		$this->permalink = isset( $args['permalink'] ) ? $args['permalink'] : true;
		$this->hooks();
	}

    protected function get_bulk_actions() {
        $actions = array();
        $post_type_obj = get_post_type_object( $this->post_type );

        if ( current_user_can( $post_type_obj->cap->delete_posts ) ) {
            if ( $this->is_trash || ! EMPTY_TRASH_DAYS ) {
                $actions['untrash'] = esc_html__( 'Restore', 'woopanel' );
                $actions['delete'] = esc_html__( 'Delete Permanently', 'woopanel' );
            } else {
                $actions['trash'] = esc_html__( 'Move to Trash', 'woopanel' );
            }
        }

        return $actions;
    }

	public function form( $args = array() ) {
		global $current_user, $wp, $woopanel_post_types;
		
		if( ! empty($args) ) {
			extract($args);
		}

		$form_action = '';
		$user_ID = $current_user->ID;
		$post_type_object = get_post_type_object( $this->post_type );

		if ( isset( $wp->query_vars[$woopanel_post_types[$this->post_type]['slug']]) && isset($_GET['id']) ) {
			$post_id = $post_ID = $_GET['id'];

		} else {
			$post_id = $post_ID = 0;
		}
		
		if ($post_id > 0) {
			$title        =  $post_type_object->labels->edit_item;
			$post         = get_post($post_id);
			$submit_text  = esc_html__( 'Update', 'woopanel' );
			$form_action  = isset($_GET['action']) ? $_GET['action'] : 'edit';
			$nonce_action = sprintf( 'update-post_%d', $post_id);
			
		
			// Redirect if not_found post
			if (!isset($post->post_type)) {
				wp_redirect( woopanel_post_new_url($this->post_type) );
				exit;
			}
		
			// Redirect if wrong post_type
			if ($post->post_type != $this->post_type) {
				
				wp_redirect( woopanel_post_edit_url($post_id) );
				exit;
			}

			// Redirect if not author
			if ( ! is_shop_staff(false, true) && $post->post_author != $current_user->ID ) {
				$endpoint = $woopanel_post_types[$post->post_type]['plural_slug'];
				wpl_add_notice( "edit_permission", esc_html__('You can not access this post.', 'woopanel' ), 'error' );
				wp_redirect( woopanel_dashboard_url($endpoint) );
				exit;
			}	
		
			$thumbnail_id = get_post_thumbnail_id( $post->ID );
		} else {
			$title = $post_type_object->labels->add_new_item;
		
			// Set object $post NULL
			$post_null = array();
			foreach ((array) get_post() as $key => $value) {
				switch ($key) {
					case 'ID':
						$post_null[$key] = 0;
						break;
					case 'post_author':
						$post_null[$key] = (int) $user_ID;
						break;
					
					case 'post_type':
						$post_null[$key] = esc_attr( $this->post_type );
						break;
					
					case 'post_status':
						$post_null[$key] = esc_attr( 'publish' );
						break;
					
					default:
						$post_null[$key] = null;
						break;
				}
			}
			$post = (object) $post_null;
			$submit_text  = esc_html__( 'Publish', 'woopanel' );
			if( ! is_super_admin() ) {
				$post->post_status = 'pending';
				$submit_text  = sprintf( esc_html__( 'Create %s', 'woopanel' ), $post_type_object->labels->name_admin_bar );
			}
			$form_action  = 'new';
			$nonce_action = 'new-post';
			$thumbnail_id = 0;
		}
		
		$sendback  = wp_get_referer();
		
		$action_form = isset($_POST['action']) ? $_POST['action'] : '';
		if(isset($_GET['action']) 
			&& in_array( $_GET['action'], array('trash', 'untrash', 'delete') )) {
			$action_form = $_GET['action'];
		}

		
		switch( $action_form ) {
			case 'new':		
				$post_id = woopanel_write_post( array(
					'taxonomy' => $this->taxonomy,
					'tags'	   => $this->tags
				) );
				
				if( $post_id > 0 ) {
				    woopanel_redirect_post( $post_id );
                    exit();
                }
                break;
		
			case 'edit':
				$post_id = woopanel_edit_post( array(
					'taxonomy' => $this->taxonomy,
					'tags'	   => $this->tags
				) );
				woopanel_redirect_post( $post_id );
				exit();
		
			case 'trash':
				check_admin_referer( sprintf( 'trash-post_%d', $post_id) );
		
				if ( ! $post )
					wp_die( esc_html__( 'The item you are trying to move to the Trash no longer exists.', 'woopanel' ) );
		
				if ( ! $post_type_object )
					wp_die( esc_html__( 'Invalid post type.', 'woopanel' ) );
		
				if ( ! current_user_can( 'delete_post', $post_id ) )
					wp_die( esc_html__( 'Sorry, you are not allowed to move this item to the Trash.', 'woopanel' ) );
		
				if ( $user_id = woopanel_check_post_lock( $post_id ) ) {
					$user = get_userdata( $user_id );
					wp_die( sprintf( esc_html__( 'You cannot move this item to the Trash. %s is currently editing.', 'woopanel' ), $user->display_name ) );
				}
		
				if ( ! wp_trash_post( $post_id ) )
					wp_die( esc_html__( 'Error in moving to Trash.', 'woopanel' ) );
		
				wp_redirect( add_query_arg( array('ids' => $post_id, 'trashed' => 1), $sendback ) );
				exit();
		
			case 'untrash':
				check_admin_referer( sprintf( 'untrash-post_%d', $post_id) );
		
				if ( ! $post )
					wp_die( esc_html__( 'The item you are trying to restore from the Trash no longer exists.', 'woopanel' ) );
		
				if ( ! $post_type_object )
					wp_die( esc_html__( 'Invalid post type.', 'woopanel' ) );
		
				if ( ! current_user_can( 'delete_post', $post_id ) )
					wp_die( esc_html__( 'Sorry, you are not allowed to restore this item from the Trash.', 'woopanel' ) );
		
				if ( ! wp_untrash_post( $post_id ) )
					wp_die( esc_html__( 'Error in restoring from Trash.', 'woopanel' ) );
		
				wp_redirect( add_query_arg('untrashed', 1, $sendback) );
				exit();
		
			case 'delete':
				check_admin_referer( sprintf( 'delete-post_%d', $post_id) );
		
				if ( ! $post )
					wp_die( esc_html__( 'This item has already been deleted.', 'woopanel' ) );
		
				if ( ! $post_type_object )
					wp_die( esc_html__( 'Invalid post type.', 'woopanel' ) );
		
				if ( ! current_user_can( 'delete_post', $post_id ) )
					wp_die( esc_html__( 'Sorry, you are not allowed to delete this item.', 'woopanel' ) );
		
				if ( $post->post_type == 'attachment' ) {
					$force = ( ! MEDIA_TRASH );
					if ( ! wp_delete_attachment( $post_id, $force ) )
						wp_die( esc_html__( 'Error in deleting.', 'woopanel' ) );
				} else {
					if ( ! wp_delete_post( $post_id, true ) )
						wp_die( esc_html__( 'Error in deleting.', 'woopanel' ) );
				}
		
				wp_redirect( add_query_arg('deleted', 1, $sendback) );
				exit();
		}

		if( ! is_super_admin() ) {
			if( empty($post_ID) ) {
				unset($this->post_statuses['publish']);
				unset($this->post_statuses['private']);
			}else {

				if( $post->post_status == 'publish' ) {
					unset($this->post_statuses['pending']);
					unset($this->post_statuses['private']);
				}else {
					unset($this->post_statuses['publish']);
					unset($this->post_statuses['private']);
				}

			}

		}
		
		$post_type = $this->post_type;
		$post_type_cat = $this->taxonomy;
		$post_statuses = $this->post_statuses;
		$post_tags = $this->tags;
		$editor = $this->editor;
		$thumbnail = $this->thumbnail;
		$preview = $this->preview;
		$gallery = $this->gallery;
		$permalink = $this->permalink;



		include_once WOODASHBOARD_VIEWS_DIR . 'edit.php';
	}

	public function hooks() {
		
        /**
         * Filters title columns displayed in the Posts list table for a specific post type.
         *
         * @since 1.0.0
         * @hook woopanel_{$post_type}_title_column
         * @param {string} $html Output html
         * @param {object} $post Return post
         * @return string
         */
		add_filter("woopanel_{$this->post_type}_title_column", array($this, 'default_title_column'), 10, 2 );

        /**
         * Filters date columns displayed in the Posts list table for a specific post type.
         *
         * @since 1.0.0
         * @hook woopanel_{$post_type}_date_column
         * @param {string} $html Output html
         * @param {object} $post Return post
         * @return string
         */
		add_filter("woopanel_{$this->post_type}_date_column", array($this, 'default_date_column'), 10, 2 );
	}

	public function default_title_column($return, $post) {

		ob_start();
		$actions = woopanel_post_actions($post);
		woopanel_column_title($post, $actions);
		return ob_get_clean();
	}

	public function default_date_column($return, $post) {
	    ob_start();
		woopanel_column_date($post);
        return ob_get_clean();
	}

	/**
	 *
	 * @global array    $avail_post_stati
	 * @global WP_Query $wp_query
	 * @global int      $per_page
	 * @global string   $mode
	 */
	public function prepare_items() {
		global $wp_query;

        $this->handle_bulk_action();

		$this->wp_edit_posts_query();
	}

	private function handle_bulk_action(){
        $doaction = $this->current_action();
        $post_ids = isset($_GET['ids']) ? $_GET['ids'] : false;

        if ( $doaction && $post_ids ) {
            wp_verify_nonce("bulk-{$this->post_type}");

            $sendback = remove_query_arg( array('trashed', 'untrashed', 'deleted', 'locked', 'ids'), wp_get_referer() );

            switch ( $doaction ) {
                case 'trash':
                    $trashed = $locked = 0;

                    foreach ( (array) $post_ids as $post_id ) {
                        if ( !current_user_can( 'delete_post', $post_id) )
                            wp_die( esc_html__('Sorry, you are not allowed to move this item to the Trash.', 'woopanel' ) );

                        if ( woopanel_check_post_lock( $post_id ) ) {
                            $locked++;
                            continue;
                        }

                        if ( !wp_trash_post($post_id) )
                            wp_die( esc_html__('Error in moving to Trash.', 'woopanel' ) );

                        $trashed++;
                    }

                    $sendback = add_query_arg( array('trashed' => $trashed, 'ids' => join(',', $post_ids), 'locked' => $locked ), $sendback );
                    break;
                case 'untrash':
                    $untrashed = 0;
                    foreach ( (array) $post_ids as $post_id ) {
                        if ( !current_user_can( 'delete_post', $post_id) )
                            wp_die( esc_html__('Sorry, you are not allowed to restore this item from the Trash.', 'woopanel' ) );

                        if ( !wp_untrash_post($post_id) )
                            wp_die( esc_html__('Error in restoring from Trash.', 'woopanel' ) );

                        $untrashed++;
                    }
                    $sendback = add_query_arg('untrashed', $untrashed, $sendback);
                    break;
                case 'delete':
                    $deleted = 0;
                    foreach ( (array) $post_ids as $post_id ) {
                        $post_del = get_post($post_id);

                        if ( !current_user_can( 'delete_post', $post_id ) )
                            wp_die( esc_html__('Sorry, you are not allowed to delete this item.', 'woopanel' ) );

                        if ( $post_del->post_type == 'attachment' ) {
                            if ( ! wp_delete_attachment($post_id) )
                                wp_die( esc_html__('Error in deleting.', 'woopanel' ) );
                        } else {
                            if ( !wp_delete_post($post_id) )
                                wp_die( esc_html__('Error in deleting.', 'woopanel' ) );
                        }
                        $deleted++;
                    }
                    $sendback = add_query_arg('deleted', $deleted, $sendback);
                    break;

                default:
                    $sendback = apply_filters( sprintf( 'handle_bulk_actions-%s', $this->post_type), $sendback, $doaction, $post_ids );
                    break;
            }

            $sendback = remove_query_arg( array('action', 'action2', 'tags_input', 'post_author', 'comment_status', 'ping_status', '_status', 'post', 'bulk_edit', 'post_view', 'locked', 'ids'), $sendback );

            wp_redirect($sendback);
            exit();
        } elseif ( ! empty($_REQUEST['_wp_http_referer']) ) {
            wp_redirect( remove_query_arg( array('_wp_http_referer', '_wpnonce'), wp_unslash($_SERVER['REQUEST_URI']) ) );
            exit;
        }
    }
}