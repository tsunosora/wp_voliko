<?php
global $wpdb;
?>
<div class="order_download_permissions wc-metaboxes-wrapper">

    <div class="panel-group" id="accordion">
        <?php
            $download_permissions = $wpdb->get_results( $wpdb->prepare( "
                SELECT * FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions
                WHERE order_id = %d ORDER BY product_id
            ", $order->get_id() ) );

            $product = null;
            $loop    = 0;

            if ( $download_permissions && sizeof( $download_permissions ) > 0 ) foreach ( $download_permissions as $download ) {

                if ( ! $product || $product->id != $download->product_id ) {
                    $product = wc_get_product( absint( $download->product_id ) );
                    $file_count = 0;
                }

                // don't show permissions to files that have since been removed
                if ( ! $product || ! $product->exists() || ! $product->has_file( $download->download_id ) )
                    continue;

                include( 'order-download-permission-html.php' );

                $loop++;
                $file_count++;
            }
        ?>
    </div>

    <div class="toolbar row dokan-clearfix">

        <div class="dokan-w8">

            <select name="grant_access_id" class="grant_access_id dokan-form-control" data-placeholder="<?php _e( 'Choose a downloadable product&hellip;', 'printshop' ) ?>" multiple="multiple">
                <?php
                    echo '<option value=""></option>';

                    $args = array(
                        'post_type'         => array( 'product', 'product_variation' ),
                        'posts_per_page'    => -1,
                        'post_status'       => 'publish',
                        'author'            => get_current_user_id(),
                        'order'             => 'ASC',
                        'orderby'           => 'parent title',
                        'meta_query'        => array(
                            array(
                                'key'   => '_downloadable',
                                'value' => 'yes'
                            )
                        )
                    );
                    $products = get_posts( $args );

                    if ( $products ) foreach ( $products as $product ) {

                        $product_object = wc_get_product( $product->ID );
                        $product_name   = woocommerce_get_formatted_product_name( $product_object );

                        echo '<option value="' . esc_attr( $product->ID ) . '">' . esc_html( $product_name ) . '</option>';

                    }
                ?>
            </select>
        </div>

        <div class="dokan-w4">
            <button type="button" class="dokan-btn dokan-btn-success grant_access" data-order-id="<?php echo $order->get_id(); ?>" data-nonce="<?php echo wp_create_nonce( 'grant-access' ); ?>"><?php _e( 'Grant Access', 'printshop' ); ?></button>
        </div>

    </div> <!-- .toolbar -->
</div> <!-- .order_download_permissions -->