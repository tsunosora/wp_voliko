<?php
global $woopanel_dokan_store, $wp_query;
$store_user               = dokan()->vendor->get( get_query_var( 'author' ) );
$store_info               = $store_user->get_shop_info();
$social_info              = $store_user->get_social_profiles();
$store_tabs               = dokan_get_store_tabs( $store_user->get_id() );
$social_fields            = dokan_get_social_profile_fields();

$dokan_appearance         = get_option( 'dokan_appearance' );
$profile_layout           = empty( $dokan_appearance['store_header_template'] ) ? 'default' : $dokan_appearance['store_header_template'];
$store_address            = dokan_get_seller_short_address( $store_user->get_id(), false );

$dokan_store_time_enabled = isset( $store_info['dokan_store_time_enabled'] ) ? $store_info['dokan_store_time_enabled'] : '';
$store_open_notice        = isset( $store_info['dokan_store_open_notice'] ) && ! empty( $store_info['dokan_store_open_notice'] ) ? $store_info['dokan_store_open_notice'] : esc_html__( 'Store Open', 'woopanel' );
$store_closed_notice      = isset( $store_info['dokan_store_close_notice'] ) && ! empty( $store_info['dokan_store_close_notice'] ) ? $store_info['dokan_store_close_notice'] : esc_html__( 'Store Closed', 'woopanel' );
$show_store_open_close    = dokan_get_option( 'store_open_close', 'dokan_general', 'on' );


$class = '';
if( ! isset($woopanel_dokan_store->data['woocommerce_enable_layout']) || isset($woopanel_dokan_store->data['woocommerce_enable_filter']) ) {
	$class = ' style="margin-bottom: 25px;"';
}

$query = 'products';
if( $store_tabs ) {
	foreach ($wp_query->query as $query_key => $tab) {
		switch ($query_key) {
			case 'store_review':
				$query = 'reviews';
				break;
			default:
				# code...
				break;
		}
	}
}
?>


<div id="woopanel-facebook-header"<?php echo esc_attr($class);?>>
	<div class="cover">

		<a href="#" class="woopanel-facebook-cover-wrap">
	        <?php if ( $store_user->get_banner() ) { ?>
	            <img src="<?php echo esc_url( $store_user->get_banner() ); ?>"
	                 alt="<?php echo esc_attr( $store_user->get_shop_name() ); ?>"
	                 title="<?php echo esc_attr( $store_user->get_shop_name() ); ?>"
	                 class="woopanel-facebook-cover profile-info-img">
	        <?php } else { ?>
	            <div class="profile-info-img dummy-image">&nbsp;</div>
	        <?php } ?>
			<div class="coverBorder"></div>
		</a>
        <?php if ( ! empty( $store_user->get_shop_name() ) && 'default' === $profile_layout ) { ?>
            <div class="fbCoverName">
            	<h2 class="wpl-store-name"><?php echo esc_html( $store_user->get_shop_name() ); ?></h2>
		            		
		        <ul class="dokan-store-info">
		            <?php if ( isset( $store_address ) && !empty( $store_address ) ) { ?>
		                <li class="dokan-store-address"><i class="fa fa-map-marker"></i>
		                    <?php echo esc_attr($store_address); ?>
		                </li>
		            <?php } ?>

		            <?php if ( !empty( $store_user->get_phone() ) ) { ?>
		                <li class="dokan-store-phone">
		                    <i class="fa fa-mobile"></i>
		                    <a href="tel:<?php echo esc_html( $store_user->get_phone() ); ?>"><?php echo esc_html( $store_user->get_phone() ); ?></a>
		                </li>
		            <?php } ?>

		            <?php if ( $store_user->show_email() == 'yes' ) { ?>
		                <li class="dokan-store-email">
		                    <i class="fa fa-envelope-o"></i>
		                    <a href="mailto:<?php echo esc_attr( antispambot( $store_user->get_email() ) ); ?>"><?php echo esc_attr( antispambot( $store_user->get_email() ) ); ?></a>
		                </li>
		            <?php } ?>

		            <li class="dokan-store-rating">
		                <i class="fa fa-star"></i>
		                <?php echo dokan_get_readable_seller_rating( $store_user->get_id() ); ?>
		            </li>

		            <?php do_action( 'dokan_store_header_info_fields',  $store_user->get_id() ); ?>
		        </ul>
            </div>
        <?php } ?>


	</div>

	<div class="fbTimelineHeadline">
		<div class="photoContainer">
			<a class="_1nv3 _11kg _1nv5 profilePicThumb" href="https://www.facebook.com/photo.php?fbid=372808933176302&amp;set=a.118372925286572&amp;type=3&amp;source=11&amp;referrer_profile_id=100013415950946" rel="theater" id="u_0_10">                        <img src="<?php echo esc_url( $store_user->get_avatar() ) ?>"
                            alt="<?php echo esc_attr( $store_user->get_shop_name() ) ?>"
                            size="150"></a>
		</div>
		<?php if ( $store_tabs ) { ?>
		<div class="fbNav">
			<ul>
            <?php foreach( $store_tabs as $key => $tab ) { ?>
                <?php if ( $tab['url'] ): ?>
                    <li<?php echo ($key == $query) ? ' class="active"' : '';?>><a href="<?php echo esc_url( $tab['url'] ); ?>"><?php echo esc_html( $tab['title'] ); ?></a></li>
                <?php endif; ?>
            <?php } ?>
            <?php do_action( 'dokan_after_store_tabs', $store_user->get_id() ); ?>
			</ul>
		</div>
		<?php }?>
	</div>
</div>

<?php do_action( 'woopanel_header_after', $woopanel_dokan_store, $store_tabs, $query );?>