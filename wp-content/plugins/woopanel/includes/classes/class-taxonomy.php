<?php

/**
 * WooPanel Taxonomy class
 *
 * @package WooPanel_Taxonomy
 */
class WooPanel_Taxonomy {

  /**
   * The name of the taxonomy.
   *
   * @var string
   */
	public $taxonomy;

  /**
   * Set column of taxonomy
   *
   * @var array
   */
	protected $columns;
	private $tax;
	public $endpoint;
	private $_actions;
	private $total_items = 0;
	public $limits = array( 10, 20, 30, 50, 100 );
	public $per_page;
	public $paged = 1;
	public $type;

	public function __construct() {
		$this->tax = get_taxonomy( $this->taxonomy );

		$this->columns = array_merge($this->columns, array(
			'title' => array(
				'label' => esc_html__('Name', 'woopanel' ),
				'order' => 1
			),
			'description' => array(
				'label' => esc_html__('Description', 'woopanel' ),
				'order' => 2
			),
			'slug'	=> array(
				'label' => esc_html__('Slug', 'woopanel' ),
				'order' => 3
			),
			'count'	=> array(
				'label' => esc_html__('Count', 'woopanel' ),
				'order' => 4
			)
		));

		$this->_actions = array(
			'delete' => esc_html__('Delete', 'woopanel' )
		);
		/* Paginate */
		$this->per_page = $this->limits[0];
		
		$this->paged       = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : $this->paged;
		if( isset($_GET['limit']) && in_array($_GET['limit'], $this->limits) ) {
			$this->per_page = $_GET['limit'];
		}
		$this->total_items = $this->get_count();

		$this->hooks_index();
	}

	public function hooks_index() {
        /**
         * Filters custom title column post list table
         *
         * @since 1.0.0
         * @hook woopanel_{$post_type}_title_column
         * @param {string} $html Output HTML
         * @param {object} $post Object post
         * @return void
         */
		add_filter( "woopanel_{$this->taxonomy}_title_column", array($this, 'title_column'), 99, 2);

        /**
         * Filters custom description column post list table
         *
         * @since 1.0.0
         * @hook woopanel_{$post_type}_description_column
         * @param {string} $html Output HTML
         * @param {object} $post Object post
         * @return void
         */
		add_filter("woopanel_{$this->taxonomy}_description_column", array($this, 'description_column'), 99, 2);

        /**
         * Filters custom slug column post list table
         *
         * @since 1.0.0
         * @hook woopanel_{$post_type}_slug_column
         * @param {string} $html Output HTML
         * @param {object} $post Object post
         * @return void
         */
		add_filter( "woopanel_{$this->taxonomy}_slug_column", array($this, 'slug_column'), 99, 2);

        /**
         * Filters custom count column post list table
         *
         * @since 1.0.0
         * @hook woopanel_{$post_type}_count_column
         * @param {string} $html Output HTML
         * @param {object} $post Object post
         * @return void
         */
		add_filter( "woopanel_{$this->taxonomy}_count_column", array($this, 'count_column'), 99, 2);
		
	}
	
	public function title_column($return, $tax) {
		echo '<strong><a class="row-title" href="'. esc_url(woopanel_dashboard_url($this->endpoint) . '?id=' . absint($tax->term_id) ) .'" aria-label="'. esc_attr($tax->name) .' (' . esc_html__('Edit', 'woopanel' ) .')">'. esc_attr($tax->name) .'</a></strong>';
		echo '<div class="row-actions">';
			echo '<span class="edit"><a href="'. esc_url(woopanel_dashboard_url($this->endpoint) . '?id=' . absint($tax->term_id) ) .'" aria-label="' . esc_html__('Edit', 'woopanel' ) .' “'. esc_attr($tax->name) .'”">' . esc_html__('Edit', 'woopanel' ) .'</a> | </span>';

	        /**
	         * Filters permission delete taxonomy
	         *
	         * @since 1.0.0
	         * @hook woopanel_taxonomy_delete_{$taxonomy}
	         * @param {int} $term_id
	         * @return boolean
	         */
			if( apply_filters("woopanel_taxonomy_delete_{$this->taxonomy}", true, absint($tax->term_id) ) ) {
				echo  '<span class="delete"><a href="'. esc_url(woopanel_dashboard_url($this->endpoint) . '?id=' . absint( $tax->term_id ) ) .'&action=delete&_wpnonce='. wp_create_nonce('woopanel-category-trash') .'" class="submitdelete" aria-label="'. esc_html__('Delete', 'woopanel' ) .' “'. esc_attr($tax->name) .'”">' . esc_html__('Delete', 'woopanel' ) .'</a> | </span>';
			}
			
			if( $this->tax->public ) {
				$term_id = (int)$tax->term_id;
				echo '<span class="view"><a href="'. get_term_link($term_id, $this->taxonomy) .'" rel="bookmark" aria-label="View “'. esc_attr($tax->name) .'”" target="_blank">' . esc_html__('View', 'woopanel' ) .'</a></span>';
			}
		echo '</div>';
	}

	public function description_column($return, $tax) {
		if( empty($tax->description) ) {
			echo '<span aria-hidden="true">—</span>';
		}else {
			echo trim($tax->description);
		}
	}

	public function slug_column($return, $tax) {
		echo esc_attr($tax->slug);
	}

	public function count_column($return, $tax) {
		echo esc_attr($tax->count);
	}

	/**
	 * Form create/edit for displaying.
	 *
	 * @since 1.0.0
	 * @abstract
	 */
	public function index() {

		if( isset($_GET['id']) ) {
			$term = get_term( $_GET['id'], $this->taxonomy, OBJECT, 'edit' );
			$this->save_taxonomy($_GET['id']);

			if( isset($_GET['action']) ) {
				if ( wp_verify_nonce( $_GET['_wpnonce'], 'woopanel-category-trash' ) ) {
					wp_delete_term( $term->term_id, $this->taxonomy );
				}
			}

			if( isset($_POST[ sprintf( 'tax_%s', $this->taxonomy ) ]) ) {
				$term = new stdClass;
				$term->term_id = (int)$_GET['id'];
				$term->name = sanitize_text_field($_POST['tax_name']);
				$term->slug = sanitize_title($_POST['tax_slug']);
				$term->description = sanitize_text_field($_POST['tax_description']);
				$term->parent = (int)$_POST['tax_parent'];
			}
		}else {
			$this->save_taxonomy();
		}

		include_once WOODASHBOARD_VIEWS_DIR . 'taxonomy.php';
		
	}
	
	/**
	 * Prepares the list of items for displaying.
	 *
	 * @since 1.0.0
	 * @abstract
	 */
	public function dropdown_categories($parent_id = 0) { ?>
		<div class="form-group m-form__group type-select " id="tax_parent_field" data-priority="">
			<label for="tax_parent" class=""><?php echo esc_attr( $this->tax->labels->parent_item ); ?></label>
			<?php
			$dropdown_args = array(
				'hide_empty'       => 0,
				'hide_if_empty'    => false,
				'taxonomy'         => $this->taxonomy,
				'name'             => 'tax_parent',
				'orderby'          => 'name',
				'hierarchical'     => true,
				'show_option_none' => esc_html__( 'None', 'woopanel' ),
				'selected'		   => $parent_id,
				'class'            => 'form-control m-input',
			);

			/**
			 * Filters the taxonomy parent drop-down on the Edit Term page.
			 *
			 * @since 1.0.0
			 * @hook taxonomy_parent_dropdown_args
			 * @param {array} $dropdown_args An array of taxonomy parent drop-down arguments.
			 * @param {string} $taxonomy The taxonomy slug.
			 * @param {string} $context  Filter context. Accepts 'new' or 'edit'.
			 * @return {array}
			 */
			$dropdown_args = apply_filters( 'taxonomy_parent_dropdown_args', $dropdown_args, $this->taxonomy, 'new' );

			wp_dropdown_categories( $dropdown_args );?>
			<span class="m-form__help" id="tax_parent-description" aria-hidden="true">
				<?php if ( 'category' == $this->taxonomy ) : ?>
					<p><?php esc_html_e( 'Categories, unlike tags, can have a hierarchy. You might have a Jazz category, and under that have children categories for Bebop and Big Band. Totally optional.', 'woopanel' ); ?></p>
				<?php else : ?>
					<p><?php esc_html_e( 'Assign a parent term to create a hierarchy. The term Jazz, for example, would be the parent of Bebop and Big Band.', 'woopanel' ); ?></p>
				<?php endif; ?>
			</span>
		</div>
		<?php
	}

	/**
	 * Print column headers, accounting for hidden and sortable columns.
	 *
	 * @since 1.0.0
	 *
	 * @staticvar int $cb_counter
	 *
	 * @param bool $with_id Whether to set the id attribute or not
	 */
	public function print_column_headers( $with_id = true ) {
        ?>
        <th id="cb" class="wpl-datatable__cell--center wpl-datatable__cell wpl-datatable__cell--check check-column">
            <span>
                <label class="m-checkbox m-checkbox--single m-checkbox--all m-checkbox--solid m-checkbox--brand" for="cb-select-all-1">
                    <input id="cb-select-all-1" type="checkbox" />
                    <span></span>
                </label>
            </span>
        </th>
        <?php
        if( is_array($this->columns) && ! empty($this->columns) ) {
            foreach( $this->columns as $column_name => $column ) {
				if( isset($column['label']) ) { ?>
                <th scope="col" class="wpl-datatable__cell column-<?php echo esc_attr($column_name);?>">
                    <span><?php echo esc_attr($column['label']);?></span>
                    <span class="sorting-indicator"></span>
                </th>
                <?php
				}
            }
        }
	}

	/**
	 *
	 * @return bool
	 */
	public function has_items() {
		return $this->total_items;
	}

	/**
	 */
	public function no_items() {
		?>
		<tr class="no-items">
			<td class="colspanchange" colspan="<?php echo (count($this->columns) + 1);?>" style="text-align:center;">
				<div class="dashboard-block-empty">
					<?php
					if( ! has_action("woopanel_{$this->type}_no_item_icon") ) {
						echo '<i class="la la-shopping-cart"></i>';
					}else {

						/**
						 * Filters show icon if no data in taxonomy table
						 *
						 * @since 1.0.0
						 * @hook woopanel_{$taxonomy}_no_item_icon
						 * @param null
						 */
						do_action("woopanel_{$this->type}_no_item_icon");
					}?>
                    <?php if( isset($this->type_settings['singular_name']) ){ ?>
					    <h3><?php echo sprintf( esc_html__( 'No %s found.', 'woopanel' ), $this->type_settings['singular_name'] );?></h3>
                    <?php } ?>
					<p><?php esc_html_e('Not found', 'woopanel' );?></p>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Display the table
	 *
	 * @since 1.0.0
	 */
	public function display_rows_or_placeholder() {
		foreach( $this->get_results() as $index => $post) {
			$this->get_html_display_rows($post, $index);
		}
	}

	public function get_html_display_rows($term, $index) {
		$product = false;
		$data = '';
		?>
		<tr id="post-<?php echo absint($term->term_id);?>" class="wpl-datatable__row iedit author-self<?php if( $index % 2 == 1 ) { echo ' m-datatable__row--even';}?> level-0 post-<?php echo absint($term->term_id);?> post type-post status-publish format-standard has-post-thumbnail hentry category-comfort category-luxury category-market-updates category-sales category-uncategorized">
			<td class="wpl-datatable__cell--center wpl-datatable__cell wpl-datatable__cell--check check-column">
				<?php
				/**
				 * Filters permission show checkbox taxonomy list table
				 *
				 * @since 1.0.0
				 * @hook woopanel_bulk_action_{$taxonomy}
				 * @param {boolean} true Default set is true
				 * @param {object} $term Object term
				 * @return boolean
				 */
				if( apply_filters("woopanel_bulk_action_{$this->taxonomy}", true, $term) ) { ?>
				<span>
					<label class="m-checkbox m-checkbox--single m-checkbox--solid m-checkbox--brand" for="cb-select-<?php echo absint($term->term_id);?>">
						<input id="cb-select-<?php echo absint($term->term_id);?>" type="checkbox" name="ids[]" value="<?php echo absint($term->term_id);?>">
						<span></span>
					</label>
				</span>
				<?php }?>
			</td>
			<?php
			if( empty($this->template) ) {
				foreach( $this->columns as $key_column => $column ) {
					if( isset($column['label']) ) {?>
						<td class="wpl-datatable__cell column-<?php echo esc_attr($key_column);?>" data-colname="<?php echo strip_tags($column['label']);?>">
							<?php

							/**
							 * Filters custom column taxonomy list table
							 *
							 * @since 1.0.0
							 * @hook woopanel_{$taxonomy}_{$column_name}_column
							 * @param {string} $html Output display HTML
							 * @param {object} $term Object term
							 * @return void
							 */
							$action_name = "woopanel_{$this->taxonomy}_{$key_column}_column";
							if( ! has_filter($action_name) ) {
								echo esc_attr($action_name);
							}else {
								echo apply_filters($action_name, '', $term, $data);
							}
							
							?>
						</td>
					<?php
					}
				}
			}else {
				woopanel_get_template_part( $this->template, '', array(
					'data' => $term,
					'columns' => $this->columns
				));
			}?>
		</tr>
		<?php
	}

	public function display_paginate($total_items, $max_num_pages) {
		global $wp_query;
		?>
		<div class="m-datatable__pager m-datatable--paging-loaded clearfix">
			<?php

			woopanel_paginate_links( array(
				'current'   => $this->paged,
				'total'     => $max_num_pages,
				'format'    => '?pagenum=%#%',
				'end_size'  => 1,
				'mid_size'  => 2,
				'type'      => 'array',
				'prev_text' => '<i class="la la-angle-double-left"></i>',
				'next_text' => '<i class="la la-angle-double-right"></i>',
			) ); ?>
			
			<div class="m-datatable__pager-info">
				<?php if( $total_items > $this->limits[0] ) {?>
				<select name="limit" class="selectpicker m-bootstrap-select m-datatable__pager-size" title="Select page size" data-width="70px" tabindex="-98" onchange="this.form.submit()">
					<?php foreach ($this->limits as $value) {
						echo '<option value="'. esc_attr($value) .'" '. (($value == $this->per_page) ? 'selected' : '') .'>'. esc_attr($value) .'</option>';
					} ?>
				</select>
				<?php }?>
				<?php woopanel_paginate_text($this->paged, $this->per_page, $total_items); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Display the table
	 *
	 * @since 1.0.0
	 */
	public function display() {
		$max_num_pages = ceil($this->total_items / $this->per_page);
		?>
		<form id="posts-filter" method="GET">
			<div id="list-<?php echo esc_attr($this->type);?>-table" class="woopanel-list-post-table m-portlet m-portlet--mobile">
				<!--begin: Head -->
				<div class="m-portlet__head">
					<div class="m-portlet__head-caption">
						<div class="m-portlet__head-title">
							<h3 class="m-portlet__head-text">
								<?php echo esc_attr($this->tax->labels->name);?>
								<small><span class="displaying-num"><?php echo sprintf( _n( '%s item', '%s items', $this->total_items ), number_format_i18n( $this->total_items ) );?></span></small>
							</h3>
						</div>
					</div>
				</div>
				<!--end: Head -->
				<div class="m-portlet__body">
					<?php if( $this->has_items() || isset($_GET['date']) ) {?>
					<!--begin: Search Form -->
					<div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
						<div class="row align-items-center">
							<div class="col-xl-9 order-2 order-xl-1">
								<div class="form-group m-form__group align-items-center">
									<div class="m-input-icon m-input-icon--left">
										<input type="text" class="form-control m-input" placeholder="<?php echo empty($this->tax->hierarchical) ? esc_html__( 'Search Tags', 'woopanel' ) : esc_html__( 'Search Categories', 'woopanel' );?>" name="search_name" placeholder="<?php echo esc_html( $this->type_settings['search_items']); ?>" id="generalSearch" value="<?php echo isset($_GET['search_name']) ? strip_tags($_GET['search_name']) : '';?>">
										<span class="m-input-icon__icon m-input-icon__icon--left">
											<span><i class="la la-search"></i></span>
										</span>
									</div>
								</div>
							</div>
							<div class="col-xl-3 order-1 order-xl-2 m--align-right">
								<button type="submit" name="filter_action" id="post-query-submit" class="btn btn-accent m-btn m-btn--custom m-btn--icon">
									<span>
										<i class="la la-filter"></i>
										<span><?php esc_html_e( 'Filter', 'woopanel' ); ?></span>
									</span>
								</button>
								<div class="m-separator m-separator--dashed d-xl-none"></div>
							</div>
						</div>
					</div>
					<!--end: Search Form -->
					<?php }?>

                    <?php $this->display_tablenav( 'top' ); ?>

					<div id="m-listtable-<?php echo esc_attr($this->post_type);?>" class="m-datatable m-datatable--default wpl_datatable <?php if( ! $this->has_items() ) { echo ' m-datatable-empty';}?>">
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

							$this->display_paginate($this->total_items, $max_num_pages);
						}?>
					</div>
					
				</div><!-- .m-portlet__body -->
			</div>
		</form>
        <?php
	}
	
	public function query() {
		global $wpdb;

		$offset = ($this->paged - 1) * $this->per_page;

		

		$query = array();
		$query['select'] = "SELECT * FROM {$wpdb->terms} as t";
		$query['join']   = "INNER JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id";
		$query['where']  = "WHERE tt.taxonomy = '" . esc_attr($this->taxonomy) . "'";

		if( isset($_GET['search_name']) ) {
			$query['where_like']  = " AND (t.name LIKE %s OR t.slug LIKE %s)";
		}

		$query['orderby'] = "ORDER BY t.name ASC";

		$query['limit']   = "LIMIT {$offset}, {$this->per_page}";

		return $query;

	}

	public function get_count() {
		global $wpdb;

		$query = $this->query();
		$query['select'] = "SELECT COUNT( DISTINCT t.term_id ) as total FROM {$wpdb->terms} as t";
		unset($query['limit']);

		
		if( isset($_GET['search_name']) && ! empty($_GET['search_name']) ) {
			$sql = implode(' ', $query);
			$search_name = $wpdb->esc_like( $_GET['search_name'] );
			$search_name = '%' . esc_attr($search_name) . '%';
			$sql = $wpdb->prepare($sql, $search_name, $search_name);
		}else {
			unset($query['where_like']);
			$sql = implode(' ', $query);
		}

		return $wpdb->get_var($sql);
	}

	public function get_results() {
		global $wpdb;

		$query = $this->query();
		
		if( isset($_GET['search_name']) && ! empty($_GET['search_name']) ) {
			$sql = implode(' ', $query);
			$search_name = $wpdb->esc_like( $_GET['search_name'] );
			$search_name = '%' . esc_attr($search_name) . '%';
			$sql = $wpdb->prepare($sql, $search_name, $search_name);
		}else {
			unset($query['where_like']);
			$sql = implode(' ', $query);
		}

		return $wpdb->get_results($sql);
	}

    /**
     * Bulk actions
     * @since 1.0.0
     */
    protected function get_bulk_actions() {
        return array();
    }

    protected function bulk_actions( $which = '' ) {
       echo '<select name="action' . esc_attr($two) . '" id="bulk-action-selector-' . esc_attr( $which ) . "\" class=\"m-bootstrap-select m-bootstrap-select--solid m_selectpicker\" data-style=\"btn-sm\" data-width=\"\">\n";
        echo '<option value="-1">' . esc_html__( 'Bulk Actions', 'woopanel' ) . "</option>\n";

        foreach ( $this->_actions as $name => $title ) {
            $class = 'edit' === $name ? ' class="hide-if-no-js"' : '';

            echo "\t" . '<option value="' . esc_attr($name) . '"' . esc_attr($class) . '>' . esc_attr($title) . "</option>\n";
        }

        echo "</select>\n";

        echo '<button data-taxonomy="'. esc_attr($this->taxonomy) .'" data-security="'. wp_create_nonce( 'tax_verify_security' ) .'" type="button" id="doaction'. esc_attr($two) .'" class="btn btn-accent btn-sm m-btn m-loader--light m-loader--right btn-tax-actions" onclick="if(!this.classList.contains(\'m-loader\')) this.className+=\' m-loader\';">'. esc_html__( 'Apply', 'woopanel' ) .'</button>';
        echo "\n";
    }

    protected function display_tablenav( $which ) {
        if ( 'top' === $which ) {
            wp_nonce_field( sprintf( 'bulk-%s', $this->type ) );
        }
        ?>
        <?php if ( $this->has_items() ): ?>
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30 tablenav hidden <?php echo esc_attr( $which ); ?>">
                <div class="m-form__group m-form__group--inline">
                    <div class="m-form__label m-form__label-no-wrap">
                        <label class="m--font-bold m--font-danger-">
                            <?php printf( esc_html__( 'Selected %s:', 'woopanel' ), '<span id="m_datatable_selected_number" class="selected_number">0</span> records' ); ?>
                        </label>
                    </div>
                    <div class="m-form__control actions bulkactions">
                        <?php $this->bulk_actions( $which ); ?>
                    </div>
                </div>
            </div>
        <?php endif;
    }

    public function current_action() {
        if ( isset( $_GET['action'] ) && -1 != $_GET['action'] )
            return $_GET['action'];

        if ( isset( $_GET['action2'] ) && -1 != $_GET['action2'] )
            return $_GET['action2'];

        return false;
	}
	
	public function save_taxonomy( $term_id = false) {
		
		if( isset($_POST[ sprintf( 'tax_%s', $this->taxonomy ) ]) ) {
			$tax_slug = $_POST['tax_slug'];
			if( empty($_POST['tax_slug']) ) {
				$tax_slug = sanitize_title($_POST['tax_name']);
			}


			if( ! $term_id ) {
				$term = wp_insert_term(
					sanitize_text_field($_POST['tax_name']),
					$this->taxonomy,
					array(
					  'description'=> sanitize_text_field($_POST['tax_description']),
					  'slug' => $tax_slug,
					  'parent'=> isset($_POST['tax_parent']) ? (int)$_POST['tax_parent'] : 0
					)
				);

				if( ! is_wp_error($term) ) {
					do_action("woopanel_save_taxonomy_{$this->taxonomy}", $term['term_id'], $_POST);
				}
			}else {
				wp_update_term($term_id, $this->taxonomy, array(
					'name' => sanitize_text_field($_POST['tax_name']),
					'slug' => sanitize_title($_POST['tax_slug']),
					'parent' => (int)$_POST['tax_parent'],
					'description' => sanitize_text_field($_POST['tax_description'])
				));

		        /**
		         * Save data taxonomy
		         *
		         * @since 1.0.0
		         * @hook woopanel_save_taxonomy_{$taxonomy}
		         * @param {int} $term_id Term id
		         * @param {array} $data Data form $_POST
		         */
				do_action("woopanel_save_taxonomy_{$this->taxonomy}", $term_id, $_POST);
			}
		}

	}
}