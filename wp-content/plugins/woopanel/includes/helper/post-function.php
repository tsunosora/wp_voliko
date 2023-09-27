<?php

/**
 * Attachment Image HTML
 * @param  string  $imageID
 * @param  boolean $container
 * @param  string  $input_name
 */
function woopanel_attachment_image( $imageID = null, $container = true, $featured_image = true, $input_name = '_thumbnail_id', $size = '', $args = array() ) {
	$size = empty($size) ? 'post-thumbnail' : $size;
    if(isset($_POST['image_ids']) && !empty($_POST['image_ids']))
        $imageID = $_POST['image_ids'];
    if(isset($_POST['input_name']) && !empty($_POST['input_name']))
        $input_name = $_POST['input_name'];


    $extra_class = isset($args['fullwidth']) ? ' fullwidth' : '';

    $html = '';
    if( $container ) $html .= '<div '. ($featured_image ? 'id="featured_image_container"' : '' ) .' class="media-uploader'.$extra_class.'">';

    if( empty($imageID) || $imageID == -1 ) {
        $html .= '<p><a href="javascript:void(0);" id="'. ($featured_image ? 'set-post-thumbnail' : 'set-image' ) .'" class="add_image">';
        $html .= '<span class="thumbnail-placeholder">';
        $html .= $featured_image ? esc_html__( 'Set featured image', 'woopanel' ) : esc_html__( 'Set Image', 'woopanel' );
        $html .= '</span>';
        $html .= '</a></p>';
    } else {
        $html .= '<p><a href="javascript:void(0);" id="'. ($featured_image ? 'set-post-thumbnail' : 'set-image' ) .'" class="add_image">';
        $html .=  wp_get_attachment_image( $imageID, $size, "", array( "class"=> "image_preview") );
        
        $html .= '</a>';
        $html .= '<span class="howto" id="set-post-thumbnail-desc"><i class="la la-edit"></i> '. esc_html__( 'Click the image to edit or update', 'woopanel' ) .'</span>';
        $html .= '</p>';
        if($featured_image) {
            $html .= '<p><a href="javascript:void(0);" id="remove-post-thumbnail" class="remove_image">' . esc_html__('Remove featured image', 'woopanel' ) . '</a></p>';
        } else {
            $html .= '<p><a href="javascript:void(0);" id="remove-image" class="remove_image">' . esc_html__('Remove Image', 'woopanel' ) . '</a></p>';
        }
    }

    $html .= '<input type="hidden" name="'. esc_attr($input_name) .'" id="'. esc_attr($input_name) .'" value="'. esc_attr( empty($imageID) ?  -1 : $imageID ) .'">';

    if( $container ) $html .= '</div>';

    print($html);
}


/**
 * Gallery Images HTML
 * @param  string  $imageIDs
 * @param  boolean $container
 * @param  string  $input_name
 */
function woopanel_gallery_images($imageIDs = null, $container = true, $input_name = '_image_gallery' ){

    if(isset($_POST['image_ids']) && !empty($_POST['image_ids']))
        $imageIDs = $_POST['image_ids'];
    if(isset($_POST['input_name']) && !empty($_POST['input_name']))
        $input_name = $_POST['input_name'];

    $html = '';
    if( ! is_array($imageIDs) ) {
        $images = array_filter( explode(',', $imageIDs) );
    }else {
        $images = $imageIDs;
        $imageIDs = implode(',', $imageIDs);
    }


    if( $container ) $html .= '<div id="gallery_images_container" class="media-uploader">';
    if (!empty($imageIDs) && count( $images ) > 0 ) {
        $html .= '<ul class="images ui-sortable">';
        foreach ($images as $image_id) {
            $image = wp_get_attachment_image( $image_id, 'thumbnail' );

            $html .= '<li class="image" data-attachment_id="' . esc_attr( $image_id ) . '">';
            $html .= $image;
            $html .= '<ul class="actions">';
            $html .= '<li><a href="javascript:void(0);" class="delete tips" data-tip="' . esc_attr__( 'Delete image' ) . '">' . esc_html__( 'Delete', 'woopanel' ) . '</a></li>';
            $html .= '</ul>';
            $html .= '</li>';
        }
        $html .= '</ul>';
        $html .= '<p class="howto">'. esc_html__( 'Drag and drop to reorder media files.', 'woopanel' ) .'</p>';
    }
    $html .= '<p class="add_images"><a href="javascript:void(0);" data-choose="' . esc_html__('Add gallery images', 'woopanel' ) .'" data-update="Add to gallery" data-delete="' . esc_html__('Delete', 'woopanel' ) .'" data-text="' . esc_html__('Delete', 'woopanel' ) .'" id="add_gallery_images">' . esc_html__('Add gallery images', 'woopanel' ) .'</a></p>';
    $html .= '<input type="hidden" id="'. esc_attr($input_name) .'" name="'. esc_attr($input_name) .'" value="'. esc_attr( $imageIDs ) .'" />';
    $html .= '</div>';

    print($html);
}

/**
 * Proccess create a new post
 *
 * @since 1.0.0
 *
 * @param array $args
 * @return int $post_id Post ID after create
 */
function woopanel_write_post($args) {
    global $current_user;

    if( apply_filters('woopanel_create_product_permission', false) ) {
        return;
    }
    
    $taxonomy = $args['taxonomy'];
    $tags = $args['tags'];

    if( isset($args['data']) ) {
        $data = $args['data'];
    }else {
        $data = $_POST;
    }


    if ( isset($data['nb_post_type']) )
        $ptype = get_post_type_object($data['nb_post_type']);
    else
        $ptype = get_post_type_object('post');


    
    if ( !current_user_can( $ptype->cap->edit_posts ) ) {
        if ( 'page' == $ptype->name )
            return new WP_Error( 'edit_pages', esc_html__( 'Sorry, you are not allowed to create pages on this site.', 'woopanel' ) );
        else
            return new WP_Error( 'edit_posts', esc_html__( 'Sorry, you are not allowed to create posts or drafts on this site.', 'woopanel' ) );
    }
    

    $data['post_mime_type'] = '';

    // Clear out any data in internal vars.
    unset( $data['filter'] );

    if ( isset($data['visibility']) ) {
        switch ( $data['visibility'] ) {
            case 'public' :
                $data['post_password'] = '';
                break;
            case 'password' :
                unset( $data['sticky'] );
                break;
            case 'private' :
                $data['post_status'] = 'private';
                $data['post_password'] = '';
                unset( $data['sticky'] );
                break;
        }
    }

    $translated = _woopanel_translate_postdata( false, $data );
    if ( is_wp_error($translated) )
        return $translated;

    // Create the post.
    $post_ID = wp_insert_post( $translated );

    

    /**
     * Save data post type
     *
     * @since 1.0.0
     * @hook woopanel_{$post_type}_save_post
     * @param {int} $post_id Post ID
     * @param {array} $data Data form
     */
    do_action( "woopanel_{$ptype->name}_save_post", $post_ID, $translated );

    // Set Article Featured Image
    if(isset($_POST['_thumbnail_id']) && !empty($_POST['_thumbnail_id'])) {
        $featured_img_id = $_POST['_thumbnail_id'];
        set_post_thumbnail( $post_ID, $featured_img_id );
        wp_update_post( array( 'ID' => $featured_img_id, 'post_parent' => $post_ID ) );
    } elseif(isset($_POST['_thumbnail_id']) && empty($_POST['_thumbnail_id'])) {
        delete_post_thumbnail( $post_ID );
    }

    // Convert taxonomy input to term IDs, to avoid ambiguity.
    if( isset($_POST[$tags]) ) {
        wp_set_object_terms($post_ID , explode(',', $_POST[$tags]), $args['tags'], false);
    }

    if( isset($_POST[$taxonomy]) && is_array($_POST[$taxonomy]) ) {
        $cat_ids = array_map( 'intval', $_POST[$taxonomy] );
        $cat_ids = array_unique( $cat_ids );

        wp_set_object_terms( $post_ID, $cat_ids, $taxonomy );
    }

    if ( is_wp_error( $post_ID ) )
        return $post_ID;

    if ( empty($post_ID) )
        return 0;


    /**
     * Save data post meta
     *
     * @since 1.0.0
     * @hook woopanel_save_{$post_type}_post_meta
     * @param {int} $post_id Post ID
     * @param {array} $data Data form
     */
    do_action("woopanel_save_{$ptype->name}_post_meta", $post_ID, $translated);
    add_post_meta( $post_ID, '_edit_last', $GLOBALS['current_user']->ID );

    /*
     * @todo Document the $messages array(s).
     */
    $permalink = get_permalink( $post_ID );
    if ( ! $permalink ) {
        $permalink = '';
    }

    // View post link.
    $view_post_link_html = sprintf( ' <a href="%1$s">%2$s</a>',
        esc_url( $permalink ),
        $ptype->labels->view_item
    );
    
    if( empty($ptype->public) ) {
        $view_post_link_html = '';
    }

    wpl_add_notice( "{$ptype->name}", $ptype->labels->singular_name . ' ' . esc_html__( 'updated.', 'woopanel' ) . wp_kses( $view_post_link_html, array(
                            'a' => array(
                                'href' => array()
                            ),
                        ) ), 'success' );

    return $post_ID;
}

/**
 * Proccess edit post
 * @since 1.0.0
 *
 * @param array $args
 * @param array $post_data
 * @return int $post_id Post ID after edit
 */
function woopanel_edit_post( $args, $post_data = null ) {
    global $wpdb, $current_user, $woopanel_post_types;

    if( apply_filters('woopanel_update_product_permission', false) ) {
        return;
    }

    $taxonomy = $args['taxonomy'];
    $tags = $args['tags'];

    if ( empty($post_data) )
        $post_data = &$_POST;

    // Clear out any data in internal vars.
    unset( $post_data['filter'] );

    $post_ID = (int) $post_data['post_ID'];
    $post = get_post( $post_ID );
    $post_data['post_type'] = $post->post_type;
    $post_data['post_mime_type'] = $post->post_mime_type;

    // Redirect if not have permission
    if ( ! is_shop_staff(false, true) && $post->post_author != $current_user->ID ) {
        $endpoint = $woopanel_post_types[$post->post_type]['plural_slug'];
        wpl_add_notice( "edit_permission", esc_html__('You can not access this post.', 'woopanel' ), 'error' );
        wp_redirect( woopanel_dashboard_url($endpoint) );
        exit;
    }

    if ( isset($post_data['post_type']) )
        $ptype = get_post_type_object($post_data['post_type']);
    else
        $ptype = get_post_type_object('post');

    if ( ! empty( $post_data['post_status'] ) ) {
        $post_data['post_status'] = sanitize_key( $post_data['post_status'] );

        if ( 'inherit' == $post_data['post_status'] ) {
            unset( $post_data['post_status'] );
        }
    }

    
    $post_data = _woopanel_translate_postdata( true, $post_data );
    if ( is_wp_error($post_data) )
        wp_die( $post_data->get_error_message() );

    // Post Formats
    if ( isset( $post_data['post_format'] ) )
        set_post_format( $post_ID, $post_data['post_format'] );

    $format_meta_urls = array( 'url', 'link_url', 'quote_source_url' );
    foreach ( $format_meta_urls as $format_meta_url ) {
        $keyed = '_format_' . esc_attr($format_meta_url);
        if ( isset( $post_data[ $keyed ] ) )
            update_post_meta( $post_ID, $keyed, wp_slash( esc_url_raw( wp_unslash( $post_data[ $keyed ] ) ) ) );
    }

    $format_keys = array( 'quote', 'quote_source_name', 'image', 'gallery', 'audio_embed', 'video_embed' );

    foreach ( $format_keys as $key ) {
        $keyed = '_format_' . esc_attr($key);
        if ( isset( $post_data[ $keyed ] ) ) {
            if ( current_user_can( 'unfiltered_html' ) )
                update_post_meta( $post_ID, $keyed, $post_data[ $keyed ] );
            else
                update_post_meta( $post_ID, $keyed, wp_filter_post_kses( $post_data[ $keyed ] ) );
        }
    }

    if ( 'attachment' === $post_data['post_type'] && preg_match( '#^(audio|video)/#', $post_data['post_mime_type'] ) ) {
        $id3data = wp_get_attachment_metadata( $post_ID );
        if ( ! is_array( $id3data ) ) {
            $id3data = array();
        }

        foreach ( wp_get_attachment_id3_keys( $post, 'edit' ) as $key => $label ) {
            if ( isset( $post_data[ 'id3_' . esc_attr($key) ] ) ) {
                $id3data[ $key ] = sanitize_text_field( wp_unslash( $post_data[ 'id3_' . esc_attr($key) ] ) );
            }
        }
        wp_update_attachment_metadata( $post_ID, $id3data );
    }

    // Meta Stuff
    if ( isset($post_data['meta']) && $post_data['meta'] ) {
        foreach ( $post_data['meta'] as $key => $value ) {
            if ( !$meta = get_post_meta_by_id( $key ) )
                continue;
            if ( $meta->post_id != $post_ID )
                continue;
            if ( is_protected_meta( $meta->meta_key, 'post' ) || ! current_user_can( 'edit_post_meta', $post_ID, $meta->meta_key ) )
                continue;
            if ( is_protected_meta( $value['key'], 'post' ) || ! current_user_can( 'edit_post_meta', $post_ID, $value['key'] ) )
                continue;
            update_meta( $key, $value['key'], $value['value'] );
        }
    }

    if ( isset($post_data['deletemeta']) && $post_data['deletemeta'] ) {
        foreach ( $post_data['deletemeta'] as $key => $value ) {
            if ( !$meta = get_post_meta_by_id( $key ) )
                continue;
            if ( $meta->post_id != $post_ID )
                continue;
            if ( is_protected_meta( $meta->meta_key, 'post' ) || ! current_user_can( 'delete_post_meta', $post_ID, $meta->meta_key ) )
                continue;
            delete_meta( $key );
        }
    }

    // Attachment stuff
    if ( 'attachment' == $post_data['post_type'] ) {
        if ( isset( $post_data[ '_wp_attachment_image_alt' ] ) ) {
            $image_alt = wp_unslash( $post_data['_wp_attachment_image_alt'] );
            if ( $image_alt != get_post_meta( $post_ID, '_wp_attachment_image_alt', true ) ) {
                $image_alt = wp_strip_all_tags( $image_alt, true );
                // update_meta expects slashed.
                update_post_meta( $post_ID, '_wp_attachment_image_alt', wp_slash( $image_alt ) );
            }
        }

        $attachment_data = isset( $post_data['attachments'][ $post_ID ] ) ? $post_data['attachments'][ $post_ID ] : array();

        /** This filter is documented in wp-admin/includes/media.php */
        $post_data = apply_filters( 'attachment_fields_to_save', $post_data, $attachment_data );
    }

    // Convert taxonomy input to term IDs, to avoid ambiguity.
    if( isset($_POST[$tags]) ) {
        wp_set_object_terms($post_ID , explode(',', $_POST[$tags]), $args['tags'], false);
    }

    if( isset($_POST[$taxonomy]) && is_array($_POST[$taxonomy]) ) {
        $cat_ids = array_map( 'intval', $_POST[$taxonomy] );
        $cat_ids = array_unique( $cat_ids );

        wp_set_object_terms( $post_ID, $cat_ids, $taxonomy );
    }

    update_post_meta( $post_ID, '_edit_last', get_current_user_id() );

    $success = wp_update_post( $post_data );

    // If the save failed, see if we can sanity check the main fields and try again
    if ( ! $success && is_callable( array( $wpdb, 'strip_invalid_text_for_column' ) ) ) {
        $fields = array( 'post_title', 'post_content', 'post_excerpt' );

        foreach ( $fields as $field ) {
            if ( isset( $post_data[ $field ] ) ) {
                $post_data[ $field ] = $wpdb->strip_invalid_text_for_column( $wpdb->posts, $field, $post_data[ $field ] );
            }
        }

        wp_update_post( $post_data );
    }

    do_action( "woopanel_{$ptype->name}_save_post", $post_ID, $post_data );
    
    // Now that we have an ID we can fix any attachment anchor hrefs
    if ( current_user_can( $ptype->cap->edit_others_posts ) && current_user_can( $ptype->cap->publish_posts ) ) {
        if ( ! empty( $post_data['sticky'] ) )
            stick_post( $post_ID );
        else
            unstick_post( $post_ID );
    }


    do_action("woopanel_save_{$ptype->name}_post_meta", $post_ID, $post_data);

    /*
     * @todo Document the $messages array(s).
     */
    $permalink = get_permalink( $post_ID );
    if ( ! $permalink ) {
        $permalink = '';
    }

    // View post link.
    $view_post_link_html = sprintf( ' <a href="%1$s">%2$s</a>',
        esc_url( $permalink ),
        $ptype->labels->view_item
    );
    
    if( empty($ptype->public) ) {
        $view_post_link_html = '';
    }

    wpl_add_notice( "{$ptype->name}", esc_attr($ptype->labels->singular_name) . ' ' . esc_html__(  'updated.', 'woopanel' ) . wp_kses( $view_post_link_html, array(
                            'a' => array(
                                'href' => array()
                            ),
                        ) ), 'success' );

    return $post_ID;
}


/**
 * Check permission, secuity data before proccess
 * @since 1.0.0
 *
 * @param boolean $update Default is false
 * @param array $post_data
 * @return array $post_data
 */
function _woopanel_translate_postdata( $update = false, $post_data = null ) {

    if ( empty($post_data) )
        $post_data = &$_POST;

    if ( $update )
        $post_data['ID'] = (int) $post_data['post_ID'];

    $ptype = get_post_type_object( $post_data['nb_post_type'] );

    if ( $update && ! current_user_can( 'edit_post', $post_data['ID'] ) ) {
        if ( 'page' == $post_data['nb_post_type'] )
            return new WP_Error( 'edit_others_pages', esc_html__( 'Sorry, you are not allowed to edit pages as this user.', 'woopanel' ) );
        else
            return new WP_Error( 'edit_others_posts', esc_html__( 'Sorry, you are not allowed to edit posts as this user.', 'woopanel' ) );
    } elseif ( ! $update && ! current_user_can( $ptype->cap->create_posts ) ) {
        if ( 'page' == $post_data['nb_post_type'] )
            return new WP_Error( 'edit_others_pages', esc_html__( 'Sorry, you are not allowed to create pages as this user.', 'woopanel' ) );
        else
            return new WP_Error( 'edit_others_posts', esc_html__( 'Sorry, you are not allowed to create posts as this user.', 'woopanel' ) );
    }

    if ( isset( $post_data['content'] ) )
        $post_data['post_content'] = $post_data['content'];

    if ( isset( $post_data['excerpt'] ) )
        $post_data['post_excerpt'] = $post_data['excerpt'];

        if ( isset( $post_data['post_permalink'] ) )
        $post_data['post_name'] = $post_data['post_permalink'];

    if ( isset( $post_data['parent_id'] ) )
        $post_data['post_parent'] = (int) $post_data['parent_id'];

    if ( isset($post_data['trackback_url']) )
        $post_data['to_ping'] = $post_data['trackback_url'];

    $post_data['user_ID'] = get_current_user_id();

    if (!empty ( $post_data['post_author_override'] ) ) {
        $post_data['post_author'] = (int) $post_data['post_author_override'];
    } else {
        if (!empty ( $post_data['post_author'] ) ) {
            $post_data['post_author'] = (int) $post_data['post_author'];
        } else {
            $post_data['post_author'] = (int) $post_data['user_ID'];
        }
    }

    if ( isset( $post_data['user_ID'] ) && ( $post_data['post_author'] != $post_data['user_ID'] )
        && ! current_user_can( $ptype->cap->edit_others_posts ) ) {
        if ( $update ) {
            if ( 'page' == $post_data['nb_post_type'] )
                return new WP_Error( 'edit_others_pages', esc_html__( 'Sorry, you are not allowed to edit pages as this user.', 'woopanel' ) );
            else
                return new WP_Error( 'edit_others_posts', esc_html__( 'Sorry, you are not allowed to edit posts as this user.', 'woopanel' ) );
        } else {
            if ( 'page' == $post_data['nb_post_type'] )
                return new WP_Error( 'edit_others_pages', esc_html__( 'Sorry, you are not allowed to create pages as this user.', 'woopanel' ) );
            else
                return new WP_Error( 'edit_others_posts', esc_html__( 'Sorry, you are not allowed to create posts as this user.', 'woopanel' ) );
        }
    }

    if ( ! empty( $post_data['post_status'] ) ) {
        $post_data['post_status'] = sanitize_key( $post_data['post_status'] );

        // No longer an auto-draft
        if ( 'auto-draft' === $post_data['post_status'] ) {
            $post_data['post_status'] = 'draft';
        }

        if ( ! get_post_status_object( $post_data['post_status'] ) ) {
            unset( $post_data['post_status'] );
        }
    }

    // What to do based on which button they pressed
    if ( isset($post_data['saveasdraft']) && '' != $post_data['saveasdraft'] )
        $post_data['post_status'] = 'draft';
    if ( isset($post_data['saveasprivate']) && '' != $post_data['saveasprivate'] )
        $post_data['post_status'] = 'private';
    if ( isset($post_data['publish']) && ( '' != $post_data['publish'] ) && ( !isset($post_data['post_status']) || $post_data['post_status'] != 'private' ) )
        $post_data['post_status'] = 'publish';
    if ( isset($post_data['advanced']) && '' != $post_data['advanced'] )
        $post_data['post_status'] = 'draft';
    if ( isset($post_data['pending']) && '' != $post_data['pending'] )
        $post_data['post_status'] = 'pending';

    if ( isset( $post_data['ID'] ) )
        $post_id = $post_data['ID'];
    else
        $post_id = false;
    $previous_status = $post_id ? get_post_field( 'post_status', $post_id ) : false;

    if ( isset( $post_data['post_status'] ) && 'private' == $post_data['post_status'] && ! current_user_can( $ptype->cap->publish_posts ) ) {
        $post_data['post_status'] = $previous_status ? $previous_status : 'pending';
    }

    $published_statuses = array( 'publish', 'future' );

    if ( isset($post_data['post_status']) && (in_array( $post_data['post_status'], $published_statuses ) && !current_user_can( $ptype->cap->publish_posts )) )
        if ( ! in_array( $previous_status, $published_statuses ) || !current_user_can( 'edit_post', $post_id ) )
            $post_data['post_status'] = 'pending';

    if ( ! isset( $post_data['post_status'] ) ) {
        $post_data['post_status'] = 'auto-draft' === $previous_status ? 'draft' : $previous_status;
    }

    if ( isset( $post_data['post_password'] ) && ! current_user_can( $ptype->cap->publish_posts ) ) {
        unset( $post_data['post_password'] );
    }

    if (!isset( $post_data['comment_status'] ))
        $post_data['comment_status'] = 'closed';

    if (!isset( $post_data['ping_status'] ))
        $post_data['ping_status'] = 'closed';

    foreach ( array('aa', 'mm', 'jj', 'hh', 'mn') as $timeunit ) {
        if ( !empty( $post_data['hidden_' . esc_attr($timeunit)] ) && $post_data['hidden_' . esc_attr($timeunit)] != $post_data[$timeunit] ) {
            $post_data['edit_date'] = '1';
            break;
        }
    }

    if ( !empty( $post_data['edit_date'] ) ) {
        $aa = $post_data['aa'];
        $mm = $post_data['mm'];
        $jj = $post_data['jj'];
        $hh = $post_data['hh'];
        $mn = $post_data['mn'];
        $ss = $post_data['ss'];
        $aa = ($aa <= 0 ) ? date('Y') : $aa;
        $mm = ($mm <= 0 ) ? date('n') : $mm;
        $jj = ($jj > 31 ) ? 31 : $jj;
        $jj = ($jj <= 0 ) ? date('j') : $jj;
        $hh = ($hh > 23 ) ? $hh -24 : $hh;
        $mn = ($mn > 59 ) ? $mn -60 : $mn;
        $ss = ($ss > 59 ) ? $ss -60 : $ss;
        $post_data['post_date'] = sprintf( "%04d-%02d-%02d %02d:%02d:%02d", $aa, $mm, $jj, $hh, $mn, $ss );
        $valid_date = wp_checkdate( $mm, $jj, $aa, $post_data['post_date'] );
        if ( !$valid_date ) {
            return new WP_Error( 'invalid_date', esc_html__( 'Invalid date.', 'woopanel' ) );
        }
        $post_data['post_date_gmt'] = get_gmt_from_date( $post_data['post_date'] );
    }

    if ( isset( $post_data['post_category'] ) ) {
        $category_object = get_taxonomy( 'category' );
        if ( ! current_user_can( $category_object->cap->assign_terms ) ) {
            unset( $post_data['post_category'] );
        }
    }

    $post_data['post_type'] = $post_data['nb_post_type'];


    return $post_data;
}

/**
 * Format the price with a currency symbol.
 * @since 1.0.0
 *
 * @param float $price Raw price.
 * @param array $args  Arguments to format a price
 * @return string
 */
function woopanel_price( $price, $args = array() ) { 
    extract( apply_filters( 'wc_price_args', wp_parse_args( $args, array( 
        'ex_tax_label' => false,  
        'currency' => '',  
        'decimal_separator' => wc_get_price_decimal_separator(),  
        'thousand_separator' => wc_get_price_thousand_separator(),  
        'decimals' => wc_get_price_decimals(),  
        'price_format' => get_woocommerce_price_format(),  
 ) ) ) ); 
 
    $negative = $price < 0; 
    $price = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) ); 
    $price = apply_filters( 'formatted_woocommerce_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator ); 
 
    if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) { 
        $price = wc_trim_zeros( $price ); 
    } 
 
    $formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, '', $price ); 
    $return = $formatted_price; 

 
    return $return; 
} 