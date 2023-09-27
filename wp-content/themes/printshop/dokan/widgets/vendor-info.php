<?php
/**
 * Dokan Widget Content Product Template
 *
 * @since 2.4
 *
 * @package dokan
 */
$store_url  = dokan_get_store_url( $user_id );
$user_info = get_userdata($user_id);

if(isset($user_info->caps['seller'])){?>
<div class="single-vendor-info">
    <?php echo get_avatar( $user_id, 150 ); ?>
    <div class="wrapper-vendor-info">
        <h2><a href="<?php echo $store_url;?>"><?php echo $seller['store_name'];?></a></h2>
        <p><?php echo $user_info->display_name;?></p>
        <div class="rating-wrap" title="Rated 0 out of 5"><div class="rating-content"><div class="star-rating" title="" data-original-title="0"><span style="width:0%"><strong class="rating">0</strong> out of 5</span></div></div></div>
        <div class="dokan-store-footer">
            <?php
            /**
             * @hooked dokan_count_store_views
             * @hooked dokan_count_store_products
             */
            echo dokan_count_store_views($user_id);
            echo dokan_count_store_products($user_id);
            echo dokan_count_store_reviews($user_id);?>
        </div>
    </div>
</div>
<?php }?>