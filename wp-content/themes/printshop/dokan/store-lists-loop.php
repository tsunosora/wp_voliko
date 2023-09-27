<?php
if ( $sellers['users'] ) {
    ?>
    <div class="row dokan-seller-wrap">
        <?php
        foreach ( $sellers['users'] as $seller ) {
            $store_info = dokan_get_store_info( $seller->ID );
            $banner_id  = isset( $store_info['banner'] ) ? $store_info['banner'] : 0;
            $store_name = isset( $store_info['store_name'] ) ? esc_html( $store_info['store_name'] ) : __( 'N/A', 'printshop' );
            $store_url  = dokan_get_store_url( $seller->ID );


            ?>

            <div class="col-md-3 dokan-single-seller">
                <div class="dokan-store-thumbnail">

                    <div class="dokan-store-banner-wrap">
                        <a href="<?php echo $store_url; ?>">
                            <?php if ( $banner_id ) {
                                $banner_url = wp_get_attachment_image_src( $banner_id, $image_size );
                                ?>
                                <img class="dokan-store-img" src="<?php echo esc_url( $banner_url[0] ); ?>" alt="<?php echo esc_attr( $store_name ); ?>">
                            <?php } else { ?>
                                <img class="dokan-store-img" src="<?php echo dokan_get_no_seller_image(); ?>" alt="<?php _e( 'No Image', 'printshop' ); ?>">
                            <?php } ?>
                        </a>
                        <div class="dokan-profile-avatar">
                            <a href="#">
                                <?php echo get_avatar( $seller->ID, 150 ); ?>
                            </a>
                        </div>
                        <div class="dokan-overlay-profile">
                            <a class="dokan-btn dokan-btn-theme" href="<?php echo $store_url; ?>"><?php _e( 'Visit Store', 'printshop' ); ?></a>
                        </div>

                    </div>

                    <div class="dokan-store-caption">
                        <p class="dokan-user-store"><a href="#" title="<?php echo $seller->display_name;?>"><?php echo $seller->display_name;?></a></p>
                        <h3><a href="<?php echo $store_url; ?>"><?php echo $store_name; ?></a></h3>
                    </div> <!-- .caption -->
                    <div class="dokan-store-footer">
                        <?php
                        /**
                         * @hooked dokan_count_store_views
                         * @hooked dokan_count_store_products
                         */
                        echo dokan_count_store_views($seller->ID);
                        echo dokan_count_store_products($seller->ID);
                        echo dokan_count_store_reviews($seller->ID);?>
                    </div>
                </div> <!-- .thumbnail -->
            </div> <!-- .single-seller -->
        <?php } ?>
    </div> <!-- .dokan-seller-wrap -->

    <?php
    $user_count   = $sellers['count'];
    $num_of_pages = ceil( $user_count / $limit );

    if ( $num_of_pages > 1 ) {
        echo '<div class="pagination-container clearfix">';

        $pagination_args = array(
            'current'   => $paged,
            'total'     => $num_of_pages,
            'base'      => $pagination_base,
            'type'      => 'array',
            'prev_text' => __( '&larr; Previous', 'printshop' ),
            'next_text' => __( 'Next &rarr;', 'printshop' ),
        );

        if ( ! empty( $search_query ) ) {
            $pagination_args['add_args'] = array(
                'dokan_seller_search' => $search_query,
            );
        }

        $page_links = paginate_links( $pagination_args );

        if ( $page_links ) {
            $pagination_links  = '<div class="pagination-wrap">';
            $pagination_links .= '<ul class="pagination"><li>';
            $pagination_links .= join( "</li>\n\t<li>", $page_links );
            $pagination_links .= "</li>\n</ul>\n";
            $pagination_links .= '</div>';

            echo $pagination_links;
        }

        echo '</div>';
    }
    ?>

<?php } else { ?>
    <p class="dokan-error"><?php _e( 'No vendor found!', 'printshop' ); ?></p>
<?php } ?>