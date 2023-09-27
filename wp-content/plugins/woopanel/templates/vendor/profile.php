<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header( 'shop' );

global $wp_query;

if( ! isset($wp_query->query_vars['store_user']) ) {
	return '404';
}

$store_user = $wp_query->query_vars['store_user'];
?>
	<div class="wpl-main">
		<?php do_action('woopanel_before_store_profile');?>

		<div class="wpl-row">
			<div class="wpl-col-4">
				<?php if ( is_active_sidebar( 'wpl-seller-sidebar' ) ) {
					dynamic_sidebar('wpl-seller-sidebar');
				}?>
			</div>

			<div class="wpl-col-8">
				<div class="wpl-content">
					<div class="wpl-profile-frame">

					    <div class="wpl-profile-info-box">
					        <?php if ( $store_user->get_banner_url() ) { ?>
					            <img src="<?php echo esc_url( $store_user->get_banner_url() ); ?>"
					                 alt="<?php echo esc_attr( $store_user->get_store_name() ); ?>"
					                 title="<?php echo esc_attr( $store_user->get_store_name() ); ?>"
					                 class="wpl-profile-info-img">
					        <?php } else { ?>
					            <div class="wpl-profile-info-img dummy-image" style="height: 367.68px;">&nbsp;</div>
					        <?php } ?>

					        <div class="wpl-profile-info-summary-wrapper profile-info-left-summery dokan-clearfix">
					            <div class="wpl-profile-info-summary">
					                <div class="wpl-profile-info-head">
					                    <div class="wpl-profile-img profile-img-circle">
					                        <?php echo $store_user->get_html_logo();?>
					                    </div>
					                    <?php if ( ! empty( $store_user->get_store_name() ) ) { ?>
					                        <h1 class="store-name"><?php echo esc_html( $store_user->get_store_name() ); ?></h1>
					                    <?php } ?>
					                </div>


					                <div class="wpl-profile-info">
					                    <ul class="wpl-store-info">
					                        <?php if ( ! empty( $store_user->get_address() ) ) { ?>
					                            <li class="dokan-store-address"><i class="fa fa-map-marker"></i>
					                                <?php echo wp_kses_post( $store_user->get_address() ); ?>
					                            </li>
					                        <?php } ?>

					                        <?php if ( !empty( $store_user->get_phone() ) ) { ?>
					                            <li class="wpl-store-phone">
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


					                        <?php if ( $show_store_open_close == 'on' && $dokan_store_time_enabled == 'yes') : ?>
					                            <li class="dokan-store-open-close">
					                                <i class="fa fa-shopping-cart"></i>
					                                <?php if ( dokan_is_store_open( $store_user->get_id() ) ) {
					                                    echo esc_attr( $store_open_notice );
					                                } else {
					                                    echo esc_attr( $store_closed_notice );
					                                } ?>
					                            </li>
					                        <?php endif ?>

					                        <?php do_action( 'woopanel_store_header_info_fields',  $store_user->get_id() ); ?>
					                    </ul>

					                    <?php if ( $social_fields ) { ?>
					                        <div class="store-social-wrapper">
					                            <ul class="store-social">
					                                <?php foreach( $social_fields as $key => $field ) { ?>
					                                    <?php if ( !empty( $social_info[ $key ] ) ) { ?>
					                                        <li>
					                                            <a href="<?php echo esc_url( $social_info[ $key ] ); ?>" target="_blank"><i class="fa fa-<?php echo esc_attr( $field['icon'] ); ?>"></i></a>
					                                        </li>
					                                    <?php } ?>
					                                <?php } ?>
					                            </ul>
					                        </div>
					                    <?php } ?>

					                </div> <!-- .profile-info -->
					            </div>
					            <!-- .profile-info-summery -->
					        </div>
					        <!-- .profile-info-summery-wrapper -->
					    </div>
					    <!-- .profile-info-box -->
					</div>

					<div class="wpl-profile-tab">
						<div class="wpl-profile-tab-nav">
							<ul>
								<li class="<?php echo trim(woopanel_profile_tab());?>"><a href="<?php echo esc_url($store_user->get_url()) ;?>"><?php echo esc_html__('Intro', 'woopanel');?></a></li>
								<li class="<?php echo trim(woopanel_profile_tab('tos'));?>"><a href="<?php echo esc_url($store_user->get_url('tos')) ;?>"><?php echo esc_html__('TOS', 'woopanel');?></a></li>
							</ul>
						</div>

						<div class="wpl-profile-tab-content">
							<div id="profile-intro" class="tab-panel<?php echo woopanel_profile_tab();?>">
								<?php echo nl2br( $store_user->get_intro() );?>
							</div>

							<div id="profile-tos" class="tab-panel<?php echo woopanel_profile_tab('tos');?>">
								<?php echo nl2br( $store_user->get_tos() );?>
							</div>
						</div>
					</div>
				</div>










			</div>
		</div>

		<?php do_action('woopanel_after_store_profile');?>
	</div>

<?php get_footer( 'shop' ); ?>
