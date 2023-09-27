<div class="dokan-seller-listing">



    <div id="dokan-seller-listing-wrap">
        <?php
            if(!isset($search_query)){
                $search_query = '';
            }
            if(!isset($pagination_base)){
                $pagination_base = '';
            }
            $template_args = array(
                'sellers'         => $sellers,
                'limit'           => $limit,
                'offset'          => $offset,
                'paged'           => $paged,
                'search_query'    => $search_query,
                'pagination_base' => $pagination_base,
                'per_row'         => $per_row,
                'search_enabled'  => $search,
                'image_size'      => $image_size,
            );

            echo dokan_get_template_part( 'store-lists-loop', false, $template_args );
        ?>
    </div>
</div>