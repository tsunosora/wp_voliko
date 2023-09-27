
<div class="wrap order-upload-library">
    <h1 class="wp-heading-inline"><?php echo esc_html('Order Upload Media', 'netbase-solutions'); ?></h1>

    <div class="media-frame wp-core-ui mode-grid mode-edit hide-menu">
        <div class="media-frame-content" data-columns="8">
            <div class="media-toolbar wp-filter">
                <form class="media-toolbar-left" action="<?php echo admin_url('admin.php');?>" method="get">
                    <input type="hidden" name="page" value="order-upload">
                    <input type="text" name="search_order" class="search-order" placeholder="Search order number..." value="<?php echo isset($_GET['search_order']) ? $_GET['search_order'] : '';?>">
                    <button type="submit" class="button media-button  select-mode-toggle-button">Filter</button>
                </form>
            </div>
        </div>
    </div>
    
    <?php
        global  $wpdb;

        $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
        $limit = 10; // number of rows in page
        $offset = ( $pagenum - 1 ) * $limit;
        $total = $wpdb->get_var( "SELECT COUNT(id) FROM {$wpdb->prefix}posts WHERE post_type = 'ou_attachment'" );
        $num_of_pages = ceil( $total / $limit );

        $sql = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'ou_attachment' LIMIT $offset, $limit";
        $ou_images = $wpdb->get_results($sql);
        $all_orders_meta = $wpdb->get_results(" SELECT ID, meta_value FROM {$wpdb->prefix}posts INNER JOIN {$wpdb->prefix}postmeta ON ID = post_id WHERE post_type = 'shop_order' AND meta_key = 'order_upload' ");

        $meta_values                = array();
        $attached_images            = array();
        $attached_images_by_order   = array();

        foreach( $all_orders_meta as $order_meta ) {

            $meta_values = @unserialize($order_meta->meta_value);
            $order_id = $order_meta->ID;

            foreach($meta_values as $meta_value) {

                foreach($meta_value as $meta) {

                    $attached_images[$meta] = $order_id;
                    $attached_images_by_order[$order_id][] = $meta;
                }
            }
        }

        $upload_dir         = wp_upload_dir();
        $ou_base_url        = $upload_dir['baseurl'].'/nbt-order-uploads/';
        $ou_base_dir        = $upload_dir['basedir'].'/nbt-order-uploads/';

        if( isset( $_GET['action'] ) && $_GET['action'] == 'delete' && isset( $_GET['post'] ) && $_GET['post'] != '' ) {
            if(is_numeric($_GET['post'])) {
                $attachment = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'ou_attachment' AND ID = " . $_GET['post']);

                if(is_object($attachment[0]) && wp_verify_nonce($_GET['_wpnonce'], $attachment[0]->post_name)) {

                    $post_mime_type     = explode('/', $attachment[0]->post_mime_type);
                    $ou_image_extension = isset($post_mime_type[1]) ? $post_mime_type[1]: 'jpg';
                    $image_name         = $attachment[0]->post_name . '.' . $ou_image_extension;
                    
                    //delete from db
                    $wpdb->delete( $wpdb->prefix . 'posts', array( 'ID' => $attachment[0]->ID ), array( '%d' ) );
                    $wpdb->delete( $wpdb->prefix . 'postmeta', array( 'post_id' => $attachment[0]->ID ), array( '%d' ) );

                    //delete main image from uploads folder
                    @unlink($ou_base_dir . $image_name);

                    //delete thumb image from uploads folder
                    @unlink($ou_base_dir . 'thumb_' . $image_name);

                    if ( wp_redirect( admin_url('admin.php?page=order-upload' ) ) ) {
                        exit;
                    }

                }
            }
        }

        if( isset( $_GET['search_order'] ) && is_numeric( $_GET['search_order'] ) && $_GET['search_order'] != '' ) {

            if( isset( $attached_images_by_order[ $_GET['search_order'] ] ) ) {

                $image_ids_string = implode($attached_images_by_order[ $_GET['search_order'] ], ',');

                $sql = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'ou_attachment' AND ID in ({$image_ids_string})";
                $ou_images = $wpdb->get_results($sql);
            }
            else {
                $ou_images = array();
            }
        }
    ?>
    <?php if(is_object($ou_images[0])):?>
        <table class="wp-list-table widefat fixed striped media">
            <thead>
                <tr>
                    <th><?php echo esc_html('File', 'netbase-solutions')?></th>
                    <th><?php echo esc_html('Order attachment', 'netbase-solutions')?></th>
                    <th><?php echo esc_html('Uploaded date', 'netbase-solutions')?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($ou_images as $ou_image):?>
                    <?php
                        $post_mime_type     = explode('/', $ou_image->post_mime_type);
                        $ou_image_extension = isset($post_mime_type[1]) ? $post_mime_type[1]: '';
                        $image_name         = $ou_image->post_name . '.' . $ou_image_extension;
                        $image_path         = $ou_image->post_mime_type != '' ? $ou_base_url . $ou_image->post_name . '.' . $ou_image_extension : $ou_image->post_content;
                        $order_url          = isset($attached_images[$ou_image->ID]) ? admin_url() . 'post.php?post=' . $attached_images[$ou_image->ID] . '&action=edit' : '#';
                        $delete_url         = wp_nonce_url( admin_url('admin.php?page=order-upload&amp;action=delete&amp;post=' . $ou_image->ID), $ou_image->post_name  );

                    ?>
                    <tr id="post-101" class="author-other status-inherit">
                        <td class="title column-title has-row-actions column-primary" data-colname="File">     
                            <strong class="has-media-icon">
                                <a>               
                                    <?php if($ou_image->post_mime_type != ''):?>
                                        <span class="media-icon image-icon"><img width="60" height="60" src="<?php echo $image_path;?>" alt="">
                                        </span>
                                    <?php else:?>
                                        <span class="media-icon image-icon dashicons dashicons-media-default" style="font-size: 45px;">
                                        </span>
                                    <?php endif;?>
                                <?php echo $ou_image->post_title;?>
                                </a>       
                            </strong>
                            <p class="filename">
                                <span class="screen-reader-text"><?php echo esc_html('File name:', 'netbase-solutions')?></span>
                                <?php echo $image_name;?>
                            </p>

                            <div class="row-actions">
                                <span class="delete"><a href="<?php echo esc_url($delete_url);?>" class="submitdelete aria-button-if-js" onclick="return showNotice.warn();" role="button"><?php echo esc_html('Delete Permanently', 'netbase-solutions')?></a> | </span>

                                <span class="view"><a target="_blank" href="<?php echo $image_path;?>"><?php echo esc_html('View', 'netbase-solutions')?></a></span>
                            </div>
                        </td>

                        <td class="parent column-parent" data-colname="Uploaded to">
                            <?php if(isset($attached_images[$ou_image->ID])):?>           
                                <strong><a target="_blank" href="<?php echo esc_url($order_url);?>"><?php echo esc_html('Show  Order', 'netbase-solutions')?></a></strong>
                            <?php else:?>
                                <?php echo esc_html('Unattached', 'netbase-solutions');?>
                            <?php endif;?>
                        </td>
                        <td class="date column-date" data-colname="Date"><?php echo $ou_image->post_date;?></td>          
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <?php

        $page_links = paginate_links( array(
            'base' => add_query_arg( 'pagenum', '%#%' ),
            'format' => '',
            'prev_text' => __( '&laquo;', 'text-domain' ),
            'next_text' => __( '&raquo;', 'text-domain' ),
            'total' => $num_of_pages,
            'current' => $pagenum
        ) );

        if ( $page_links ) {
            echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
        }

        ?>
    <?php else:?>
        <p>Nothing found here !</p>
    <?php endif;?>
</div>