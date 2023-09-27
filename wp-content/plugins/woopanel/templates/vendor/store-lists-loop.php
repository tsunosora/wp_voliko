
<div id="wpl-seller-listing-wrap" class="grid-view">
    <div class="seller-listing-content">
        <?php if ( $results ) : ?>






            <div id="wpl-seller-panel" class="wpl-seller-wrap column-<?php echo esc_attr( $per_row ); ?>">
                <?php
                foreach ( $results as $store ) {
                    echo woopanel_store_list_template1( $store );
                } ?>
                <div class="dokan-clearfix"></div>
            </div> <!-- .dokan-seller-wrap -->

            <?php
            if( ! empty($pagination_base) ) {
                $num_of_pages = ceil( $count / $limit );

                if ( $num_of_pages > 1 ) {
                    echo '<div class="pagination-container clearfix">';

                    $pagination_args = array(
                        'current'   => $paged,
                        'total'     => $num_of_pages,
                        'base'      => $pagination_base,
                        'type'      => 'array',
                        'prev_text' => __( '&larr; Previous', 'woopanel' ),
                        'next_text' => __( 'Next &rarr;', 'woopanel' ),
                    );

                    if ( ! empty( $search_query ) ) {
                        $pagination_args['add_args'] = array(
                            'woopanel_seller_search' => $search_query,
                        );
                    }

                    $page_links = paginate_links( $pagination_args );

                    if ( $page_links ) {
                        $pagination_links  = '<div class="pagination-wrap">';
                        $pagination_links .= '<ul class="pagination"><li>';
                        $pagination_links .= join( "</li>\n\t<li>", $page_links );
                        $pagination_links .= "</li>\n</ul>\n";
                        $pagination_links .= '</div>';

                        echo $pagination_links; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
                    }

                    echo '</div>';
                }
            }
            ?>

        <?php else:  ?>
            <p class="wpl-error"><?php esc_html_e( 'No vendor found!', 'woopanel' ); ?></p>
        <?php endif; ?>
    </div>
</div>
