<?php global $post; ?>

<div class="row">
    <div class="col-md-2 col-dokan-menu">

    <?php

        /**
         *  dokan_dashboard_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
        ?>
    </div>

    <div class="col-md-10">
        <div class="dokan-dashboard-content dokan-product-listing">

            <?php

            /**
             *  dokan_dashboard_content_before hook
             *
             *  @hooked get_dashboard_side_navigation
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_inside_before' );
            do_action( 'dokan_before_listing_product' );
            ?>

            <article class="dokan-product-listing-area">

                <div class="product-listing-top dokan-clearfix">

                    <?php dokan_product_listing_status_filter();

                    $user_id = get_current_user_id();

                    if ( dokan_is_seller_enabled( $user_id ) ) {
                        echo '<span class="dokan-add-product-link">
                        <a href="'.dokan_get_navigation_url( 'new-product' ).'" class="dokan-btn dokan-btn-theme dokan-right dokan-add-new-product"><i class="fa fa-briefcase">&nbsp;</i>'.__( 'Add new product', 'printshop' ).'</a>
                        </span>';
                    }
                    ?>
                    
                </div>

                <?php dokan_product_dashboard_errors(); ?>

                <div class="dokan-w12">
                    <?php dokan_product_listing_filter(); ?>
                </div>

                <div class="dokan-dahsboard-product-listing-wrapper">
                    <table class="dokan-table dokan-table-striped product-listing-table">
                        <thead>
                            <tr>
                                <th><?php _e( 'Image', 'printshop' ); ?></th>
                                <th><?php _e( 'Name', 'printshop' ); ?></th>
                                <th><?php _e( 'Status', 'printshop' ); ?></th>
                                <th><?php _e( 'SKU', 'printshop' ); ?></th>
                                <th><?php _e( 'Stock', 'printshop' ); ?></th>
                                <th><?php _e( 'Price', 'printshop' ); ?></th>
                                <th><?php _e( 'Type', 'printshop' ); ?></th>
                                <th><?php _e( 'Views', 'printshop' ); ?></th>
                                <th><?php _e( 'Date', 'printshop' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

                            $post_statuses = array('publish', 'draft', 'pending');
                            $args = array(
                                'post_type'      => 'product',
                                'post_status'    => $post_statuses,
                                'posts_per_page' => 10,
                                'author'         => get_current_user_id(),
                                'orderby'        => 'post_date',
                                'order'          => 'DESC',
                                'paged'          => $pagenum,
                                'tax_query'      => array(
                                    array(
                                        'taxonomy' => 'product_type',
                                        'field'    => 'slug',
                                        'terms'    => apply_filters( 'dokan_product_listing_exclude_type', array() ),
                                        'operator' => 'NOT IN',
                                        ),
                                    ),
                                );

                            if ( isset( $_GET['post_status']) && in_array( $_GET['post_status'], $post_statuses ) ) {
                                $args['post_status'] = $_GET['post_status'];
                            }

                            if( isset( $_GET['date'] ) && $_GET['date'] != 0 ) {
                                $args['m'] = $_GET['date'];
                            }

                            if( isset( $_GET['product_cat'] ) && $_GET['product_cat'] != -1 ) {
                                $args['tax_query'][] = array(
                                    'taxonomy' => 'product_cat',
                                    'field' => 'id',
                                    'terms' => (int)  $_GET['product_cat'],
                                    'include_children' => false,
                                    );
                            }

                            if ( isset( $_GET['product_search_name']) && !empty( $_GET['product_search_name'] ) ) {
                                $args['s'] = $_GET['product_search_name'];
                            }


                            $original_post = $post;
                            $product_query = new WP_Query( apply_filters( 'dokan_product_listing_query', $args ) );

                            if ( $product_query->have_posts() ) {
                                while ($product_query->have_posts()) {
                                    $product_query->the_post();

                                    $tr_class = ($post->post_status == 'pending' ) ? ' class="danger"' : '';
                                    $product = wc_get_product( $post->ID );
                                    ?>
                                    <tr<?php echo $tr_class; ?>>
                                    <td data-title="<?php _e( 'Image', 'printshop' ); ?>">
                                        <a href="<?php echo dokan_edit_product_url( $post->ID ); ?>"><?php echo $product->get_image(); ?></a>
                                    </td>
                                    <td data-title="<?php _e( 'Name', 'printshop' ); ?>">
                                        <p><a href="<?php echo dokan_edit_product_url( $post->ID ); ?>"><?php echo $product->get_title(); ?></a></p>

                                        <div class="row-actions">
                                            <span class="edit"><a href="<?php echo dokan_edit_product_url( $post->ID ); ?>"><?php _e( 'Edit', 'printshop' ); ?></a> | </span>
                                            <span class="delete"><a onclick="return confirm('Are you sure?');" href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'dokan-delete-product', 'product_id' => $post->ID ), dokan_get_navigation_url('products') ), 'dokan-delete-product' ); ?>"><?php _e( 'Delete Permanently', 'printshop' ); ?></a> | </span>
                                            <span class="view"><a href="<?php echo get_permalink( $product->ID ); ?>" rel="permalink"><?php _e( 'View', 'printshop' ); ?></a></span>
                                        </div>
                                    </td>
                                    <td class="post-status" data-title="<?php _e( 'Status', 'printshop' ); ?>">
                                        <label class="dokan-label <?php echo dokan_get_post_status_label_class( $post->post_status ); ?>"><?php echo dokan_get_post_status( $post->post_status ); ?></label>
                                    </td>
                                    <td data-title="<?php _e( 'SKU', 'printshop' ); ?>">
                                        <?php
                                        if ( $product->get_sku() ) {
                                            echo $product->get_sku();
                                        } else {
                                            echo '<span class="na">&ndash;</span>';
                                        }
                                        ?>
                                    </td>
                                    <td data-title="<?php _e( 'Stock', 'printshop' ); ?>">
                                        <?php
                                        if ( $product->is_in_stock() ) {
                                            echo '<mark class="instock">' . __( 'In stock', 'printshop' ) . '</mark>';
                                        } else {
                                            echo '<mark class="outofstock">' . __( 'Out of stock', 'printshop' ) . '</mark>';
                                        }

                                        if ( $product->managing_stock() ) :
                                            echo ' &times; ' . $product->get_total_stock();
                                        endif;
                                        ?>
                                    </td>
                                    <td data-title="<?php _e( 'Price', 'printshop' ); ?>">
                                        <?php
                                        if ( $product->get_price_html() ) {
                                            echo $product->get_price_html();
                                        } else {
                                            echo '<span class="na">&ndash;</span>';
                                        }
                                        ?>
                                    </td>
                                    <td data-title="<?php _e( 'Type', 'printshop' ); ?>">
                                        <?php
                                        if( $product->get_type() == 'grouped' ):
                                            echo '<span class="product-type tips grouped" title="' . __( 'Grouped', 'printshop' ) . '"></span>';
                                        elseif ( $product->get_type() == 'external' ):
                                            echo '<span class="product-type tips external" title="' . __( 'External/Affiliate', 'printshop' ) . '"></span>';
                                        elseif ( $product->get_type() == 'simple' ):

                                            if ( $product->is_virtual() ) {
                                                echo '<span class="product-type tips virtual" title="' . __( 'Virtual', 'printshop' ) . '"></span>';
                                            } elseif ( $product->is_downloadable() ) {
                                                echo '<span class="product-type tips downloadable" title="' . __( 'Downloadable', 'printshop' ) . '"></span>';
                                            } else {
                                                echo '<span class="product-type tips simple" title="' . __( 'Simple', 'printshop' ) . '"></span>';
                                            }

                                            elseif ( $product->get_type() == 'variable' ):
                                                echo '<span class="product-type tips variable" title="' . __( 'Variable', 'printshop' ) . '"></span>';
                                            else:
                                                // Assuming that we have other types in future
                                                echo '<span class="product-type tips ' . $product->get_type() . '" title="' . ucfirst( $product->get_type() ) . '"></span>';
                                            endif;
                                            ?>
                                        </td>
                                        <td data-title="<?php _e( 'Views', 'printshop' ); ?>">
                                            <?php echo (int) get_post_meta( $post->ID, 'pageview', true ); ?>
                                        </td>
                                        <td class="post-date" data-title="<?php _e( 'Date', 'printshop' ); ?>">
                                            <?php
                                            if ( '0000-00-00 00:00:00' == $post->post_date ) {
                                                $t_time = $h_time = __( 'Unpublished', 'printshop' );
                                                $time_diff = 0;
                                            } else {
                                                $t_time = get_the_time( __( 'Y/m/d g:i:s A', 'printshop' ) );
                                                $m_time = $post->post_date;
                                                $time = get_post_time( 'G', true, $post );

                                                $time_diff = time() - $time;

                                                if ( $time_diff > 0 && $time_diff < 24 * 60 * 60 ) {
                                                    $h_time = sprintf( __( '%s ago', 'printshop' ), human_time_diff( $time ) );
                                                } else {
                                                    $h_time = mysql2date( __( 'Y/m/d', 'printshop' ), $m_time );
                                                }
                                            }

                                            echo '<abbr title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $post, 'date', 'all' ) . '</abbr>';
                                            echo '<div class="status">';
                                            if ( 'publish' == $post->post_status ) {
                                                _e( 'Published', 'printshop' );
                                            } elseif ( 'future' == $post->post_status ) {
                                                if ( $time_diff > 0 ) {
                                                    echo '<strong class="attention">' . __( 'Missed schedule', 'printshop' ) . '</strong>';
                                                } else {
                                                    _e( 'Scheduled', 'printshop' );
                                                }
                                            } else {
                                                _e( 'Last Modified', 'printshop' );
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td class="diviader"></td>
                                </tr>

                                <?php } ?>

                                <?php } else { ?>
                                <tr>
                                    <td colspan="7"><?php _e( 'No product found', 'printshop' ); ?></td>
                                </tr>
                                <?php } ?>

                            </tbody>

                        </table>
                    </div>
                    <?php
                    wp_reset_postdata();

                    $pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
                    $base_url = dokan_get_navigation_url('products');

                    if ( $product_query->max_num_pages > 1 ) {
                        echo '<div class="pagination-wrap">';
                        $page_links = paginate_links( array(
                            'current'   => $pagenum,
                            'total'     => $product_query->max_num_pages,
                            'base'      => $base_url. '%_%',
                            'format'    => '?pagenum=%#%',
                            'add_args'  => false,
                            'type'      => 'array',
                            'prev_text' => __( '&laquo; Previous', 'printshop' ),
                            'next_text' => __( 'Next &raquo;', 'printshop' )
                            ) );

                        echo '<ul class="pagination"><li>';
                        echo join("</li>\n\t<li>", $page_links);
                        echo "</li>\n</ul>\n";
                        echo '</div>';
                    }
                    ?>
                </article>

                <?php

            /**
             *  dokan_dashboard_content_before hook
             *
             *  @hooked get_dashboard_side_navigation
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_inside_after' );
            do_action( 'dokan_after_listing_product' );
            ?>

        </div><!-- #primary .content-area -->

        <?php

        /**
         *  dokan_dashboard_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        ?>

    </div><!-- .dokan-dashboard-wrap -->
