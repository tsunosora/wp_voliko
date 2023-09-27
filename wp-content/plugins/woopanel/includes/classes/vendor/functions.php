<?php
/**
 * WooPanel Social Profile fields
 *
 * @since 2.2
 *
 * @return array
 */
function woopanel_get_social_profile_fields() {
    $fields = array(
        'fb' => array(
            'icon'  => 'facebook-square',
            'title' => __( 'Facebook', 'woopanel' ),
        ),
        'gplus' => array(
            'icon'  => 'google-plus-square',
            'title' => __( 'Google Plus', 'woopanel' ),
        ),
        'twitter' => array(
            'icon'  => 'twitter-square',
            'title' => __( 'Twitter', 'woopanel' ),
        ),
        'pinterest' => array(
            'icon'  => 'pinterest-square',
            'title' => __( 'Pinterest', 'woopanel' ),
        ),
        'linkedin' => array(
            'icon'  => 'linkedin-square',
            'title' => __( 'LinkedIn', 'woopanel' ),
        ),
        'youtube' => array(
            'icon'  => 'youtube-square',
            'title' => __( 'Youtube', 'woopanel' ),
        ),
        'instagram' => array(
            'icon'  => 'instagram',
            'title' => __( 'Instagram', 'woopanel' ),
        ),
        'flickr' => array(
            'icon'  => 'flickr',
            'title' => __( 'Flickr', 'woopanel' ),
        ),
    );

    return apply_filters( 'woopanel_profile_social_fields', $fields );
}


/**
 * Generate country dropdwon
 *
 * @param array $options
 * @param string $selected
 * @param bool $everywhere
 */
function woopanel_country_dropdown( $options, $selected = '', $everywhere = false ) {
    printf( '<option value="">%s</option>', esc_html__( '- Select a location -', 'woopanel' ) );

    if ( $everywhere ) {
        echo '<optgroup label="--------------------------">';
        printf( '<option value="everywhere"%s>%s</option>', selected( $selected, 'everywhere', true ), esc_html__( 'Everywhere Else', 'woopanel' ) );
        echo '</optgroup>';
    }

    echo '<optgroup label="------------------------------">';
    foreach ( $options as $key => $value ) {
        printf( '<option value="%s"%s>%s</option>', esc_attr( $key ), selected( $selected, $key, true ), esc_html( $value ) );
    }
    echo '</optgroup>';
}

/**
 * Get seller rating in a readable rating format
 *
 * @param int $seller_id
 *
 * @return void
 */
function woopanel_get_readable_seller_rating( $seller_id ) {
    $vendor = WooDashboard()->vendor->get( $seller_id );

    return $vendor->get_readable_rating( false );
}

function woopanel_store_config( $meta_key, $return_value = true ) {
    global $wpdb;

    $prefix = WOOPANEL_STORE_LOCATOR_PREFIX;

    $reuslt = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$prefix}configs WHERE `key` = %s", $meta_key ) );


    if( ! empty($reuslt) && $return_value ) {
        return $reuslt->value;
    }

    return $reuslt;
}

function woopanel_profile_tab( $current = '' ) {
    global $wp_query;

    if( isset($wp_query->query['section']) && $wp_query->query['section'] == $current ) {
        return ' active';
    }

    if( empty($current) &&  empty($wp_query->query['section']) ) {
        return ' active';
    }
}


function woopanel_store_category_checkboxtree_metabox($store_id) {
    global $wpdb, $current_user;
    $prefix = WOOPANEL_STORE_LOCATOR_PREFIX;

    $selected = array();
    if( is_numeric($store_id)) {
        $results = $wpdb->get_results( $wpdb->prepare("SELECT * FROM {$prefix}stores_categories WHERE store_id = %d", $store_id) );

        if( $results ) {
            $selected = array();
            foreach ($results as $key => $value) {
                $selected[] = $value->category_id;
            }
        }
    }

    $fullPermission = woopanel_full_permission();




    $query = array();
    $query['select'] = "SELECT * FROM {$prefix}categories";
    if( empty($fullPermission) ) {
        $query['where'] = "WHERE ( user_id = 0 OR user_id = {$current_user->ID} )";
    }
    $query['orderby'] = "ORDER BY category_name ASC";
    $sql = implode( ' ', $query);



    $categories = $wpdb->get_results($sql);
    ?>
    <div id="<?php printf( 'taxonomy%s', $taxonomy);?>" class="categorydiv">
        <div class="list-panel">
            <?php
            echo '<ul id="story_categories-checklist" class="categorychecklist form-no-clear m-checkbox-list">';
            woopanel_list_store_categories( $categories, $selected);
            echo '</ul>'; ?>
        </div>
    </div>
    <?php
}


function woopanel_list_store_categories( $categories, $defaults = array() ) {
    if( ! empty($categories) ) {
        foreach ( $categories as $key => $category ) {
            $selected = '';
            if( in_array($category->id, $defaults) ) {
                $selected = ' checked';
            }

            echo sprintf( "<li id='story_categories-%d' class='term-item term-item-has-children'>", $category->id );

            echo sprintf( "<label class='m-checkbox m-checkbox--solid m-checkbox--brand'><input type='checkbox' name='story_category[]' id='story_categories-%d' value='%d'%s />", $category->id, $category->id, $selected );
            echo sprintf("&nbsp;%s<span></span></label></li>", $category->category_name );
        }
    }
}

function woopanel_full_permission() {
    global $current_user;

    $prerogative_role = array( 'administrator', 'shop_manager' );
    return array_intersect( $prerogative_role, $current_user->roles );
}

function woopanel_dropdown_store_categories() {
    global $wpdb;

    $prefix = WOOPANEL_STORE_LOCATOR_PREFIX;
    $store_category = isset($_GET['store_category']) ? absint($_GET['store_category']) : '';

    $query = array();
    $query['select'] = "SELECT * FROM {$prefix}categories";
    $query['orderby'] = "ORDER BY category_name ASC";
    $sql = implode( ' ', $query);

    $categories = $wpdb->get_results($sql);

    $html = '';
    foreach ($categories as $key => $cat) {
        $selected = '';
        if( $store_category == $cat->id ) {
            $selected = ' selected';
        }
        $html .= sprintf('<option value="%d"%s>%s</option>', $cat->id, $selected, $cat->category_name);
    }

    return $html;
}

function woopanel_store_list_template1( $store = '' ) {
    ob_start();?>
    <div class="store-item" data-id="{{:id}}">
        <div class="store-item-wrapper">
            <div class="store-item-detail addr-sec" style="background-image: url(<?php echo WOODASHBOARD_URL;?>assets/images/store/banner.png);">
                <h3 class="p-title">{{:title}}</h3>

                <ul class="store-item-meta">
                    <!--<li><i class="wpl-store-icon-clock store-item-icon"></i> xxx</li>-->
                    <li><i class="wpl-store-icon-pin store-item-icon"></i>
                        <?php if( empty($store) ) {?>
                            {{:address}}
                        <?php }else {
                            echo $store->street .'<br />' . $store->city .', '. $store->state;
                        }?>
                    </li>

                    <?php if( empty($store) ) {?>
                        {{if days_str}}
                        <li><i class="wpl-store-icon-calendar store-item-icon"></i> {{:days_str}}</li>
                        {{/if}}
                    <?php }else {
                        if( $store->days_str ) {?>
                        <li><i class="wpl-store-icon-calendar store-item-icon"></i> <?php echo $store->days_str;?></li>
                    <?php }
                    }?>


                    <?php if( empty($store) ) {?>
                        {{if email}}
                        <li><a href="mailto:{{:email}}" style="text-transform: lowercase"><i class="wpl-store-icon-at store-item-icon"></i> {{:email}}</a></li>
                        {{/if}}
                    <?php }else {
                        if( $store->email ) {?>
                        <li><a href="mailto:<?php echo $store->email;?>" style="text-transform: lowercase"><i class="wpl-store-icon-at store-item-icon"></i> <?php echo $store->email;?></a></li>
                    <?php }
                    }?>
                </ul>
            </div>

            <div class="store-item-footer">
                <div class="store-item-vendor">
                    <a href="{{:url}}">{{:avatar}}</a>

                    <?php if( empty($store) ) {?>
                        {{if display_name}}
                        <h4 class="store-item-user">{{:display_name}}</h4>
                        {{/if}}
                    <?php }else {
                        if( $store->display_name ) {?>
                        <h4 class="store-item-user"><?php echo $store->display_name;?></h4>
                    <?php }
                    }?>
                </div>

                <div class="distance">
                    <p class="p-direction"><span class="s-direction"><i class="wpl-store-icon-send store-item-icon"></i> <?php echo esc_html__('Directions', 'asl_locator') ?></span></p>
                </div>
            </div>
        </div>
    </div>
    <?php
    $html = ob_get_clean();

    if( ! empty($store) ) {

        if( preg_match_all('/{{:([a-zA-Z]+)}}/', $html, $syntaxs) ) {
            foreach ($syntaxs[1] as $key => $value) {
                $html = str_replace($syntaxs[0][$key], $store->{$value}, $html);
            }
        }
    }




    return $html;
}

function woopanel_modal_directions() {
    ?>
    <!-- agile-modal -->
    <div id="wpl-store-modal-direction" class="wpl-store-modal fade">
      <div class="wpl-store-modal-backdrop-in"></div>
      <div class="wpl-store-modal-dialog in">
        <div class="wpl-store-modal-content">
          <div class="wpl-store-modal-header">
            <button type="button" class="close-directions close" data-dismiss="agile-modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4>Get Your Directions</h4>
          </div>
          <div class="form-group">
            <label class="form-label" for="frm-lbl">From:</label>
            <input type="text" class="form-control frm-place" id="frm-lbl" placeholder="Enter a Location">
          </div>
          <div class="form-group">
            <label class="form-label" for="frm-lbl">To:</label>
            <input readonly="true" type="text"  class="directions-to form-control" id="to-lbl" placeholder="Prepopulated Destination Address">
          </div>
          <div class="form-group">
            <span class="form-label" for="frm-lbl">Show Distance In</span>
            <label class="checkbox-inline">
              <input type="radio" name="dist-type" checked id="rbtn-km" value="0"> KM
            </label>
            <label class="checkbox-inline">
              <input type="radio" name="dist-type" id="rbtn-mile" value="1"> Mile
            </label>
          </div>
          <div class="form-group form-submit">
            <button type="submit" class="button btn btn-default btn-submit">GET DIRECTIONS</button>
          </div>
        </div>
      </div>
    </div>
    <?php
}