<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$options = get_option( NBTSL_SETTINGS );
if ( is_user_logged_in() ) {
    global $current_user;
    $user_info = "<span class='display-name'>{$current_user->data->display_name}</span>&nbsp;";
    $user_info .= get_avatar( $current_user->ID, 20 );

    if ( !empty( $_GET['redirect'] ) )
        $current_url = $_GET['redirect'];
    else
        $current_url = NBTSL_Lite_Login_Check_Class::curPageURL();

    /*if ( isset( $options['nbt_social-login_custom_logout_redirect'] ) && $options['nbt_social-login_custom_logout_redirect'] != '' ) {
        if ( $options['nbt_social-login_custom_logout_redirect'] == 'home' ) {
            $user_logout_url = wp_logout_url( home_url() );
        } else if ( $options['nbt_social-login_custom_logout_redirect'] == 'current_page' ) {
            $user_logout_url = wp_logout_url( $current_url );
        } else if ( $options['nbt_social-login_custom_logout_redirect'] == 'custom_page' ) {
            if ( $options['nbt_social-login_custom_logout_redirect_link'] != '' ) {
                $logout_page = $options['nbt_social-login_custom_logout_redirect_link'];
                $user_logout_url = wp_logout_url( $logout_page );
            } else {
                $user_logout_url = wp_logout_url( $current_url );
            }
        }
    } else {*/
        $user_logout_url = wp_logout_url( $current_url );
    /*}*/
    ?><div class="user-login"><?php _e('Welcome', 'nbt-solution'); ?> <b><?php echo $user_info; ?></b>&nbsp;|&nbsp;<a href="<?php echo $user_logout_url; ?>" title="<?php _e('Logout', 'nbt-solution'); ?>"><?php _e('Logout', 'nbt-solution'); ?></a></div>
    <?php
} else {
    if( isset( $options['nbt_social-login_custom_login_redirect'] ) && $options['nbt_social-login_custom_login_redirect'] != '' ) {
        if( $options['nbt_social-login_custom_login_redirect'] == 'home' ) {
            $user_login_url = home_url();
        }
        else if( $options['nbt_social-login_custom_login_redirect'] == 'current_page' ) {
            if ( !empty( $_GET['redirect'] ) )
                $current_url = $_GET['redirect'];
            else
                $current_url = NBTSL_Lite_Login_Check_Class::curPageURL();
            
            $user_login_url = $current_url;
        }
        else if( $options['nbt_social-login_custom_login_redirect'] == 'custom_page' ) {
            if( $options['nbt_social-login_custom_login_redirect_link'] != '' ) {
                $login_page = $options['nbt_social-login_custom_login_redirect_link'];
                $user_login_url = $login_page;
            }
            else {
                $user_login_url = home_url();
            }
        }
    }else {
    $user_login_url = home_url();
    }

// $redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';

$encoded_url = urlencode( $user_login_url );    
    ?>
    <div class='nbtsl-login-networks theme-<?php echo $options['nbt_social-login_icon_temp']; ?> clearfix'>
        <?php if ( isset( $attr['login_text'] ) && $attr['login_text'] != '' ) { ?>
            <span class='nbtsl-login-new-text'><?php echo $attr['login_text']; ?></span>
        <?php } ?>
        <?php
        if(isset($_SESSION['nbtsl_login_error_flag']) && $_SESSION['nbtsl_login_error_flag'] == '1'){ ?>
            <div class='nbtsl-error'><?php _e('You have Access Denied. Please authorize the app to login.', 'nbt-solution' ); ?></div>
            <?php
            unset($_SESSION['nbtsl_login_error_flag']);
        } ?>
        <?php if ( isset( $_REQUEST['error'] ) || isset( $_REQUEST['denied'] ) ) { ?>
            <div class='nbtsl-error'><?php _e( 'You have Access Denied. Please authorize the app to login.', 'nbt-solution' ); ?></div>
        <?php } ?>
        <div class='social-networks'>
            <?php
            if($options['nbt_social-login_facebook_enable']=='1'){?>
                <a href="<?php echo wp_login_url(); ?>?nbtsl_login_id=facebook_login<?php
                    if ( $encoded_url ) {
                        echo "&state=" . base64_encode( "redirect_to=$encoded_url" );
                    }
                    ?>" title='<?php
                       _e( 'Login with facebook', 'nbt-solution' );
                       
                       ?>' >
                        <div class="nbtsl-icon-block icon-facebook clearfix">
                            <span class="iconsl iconsl-facebook"></span>
                            <span class="nbtsl-login-text"><?php _e( 'Login', 'nbt-solution' ); ?></span>
                            <span class="nbtsl-long-login-text"><?php _e( 'Login with facebook', 'nbt-solution' ); ?></span>
                        </div>
                    </a>
                    <?php

            }
            if($options['nbt_social-login_twitter_enable']=='1'){?>
                <a href="<?php echo wp_login_url(); ?>?nbtsl_login_id=twitter_login<?php
                    if ( $encoded_url ) {
                        echo "&state=" . base64_encode( "redirect_to=$encoded_url" );
                    }
                    ?>" title='<?php
                       _e( 'Login with twitter', 'nbt-solution' );
                       
                       ?>' >
                        <div class="nbtsl-icon-block icon-twitter clearfix">
                            <span class="iconsl iconsl-twitter"></span>
                            <span class="nbtsl-login-text"><?php _e( 'Login', 'nbt-solution' ); ?></span>
                            <span class="nbtsl-long-login-text"><?php _e( 'Login with twitter', 'nbt-solution' ); ?></span>
                        </div>
                    </a>
                    <?php

            }
            if($options['nbt_social-login_google_enable']=='1'){?>
                <a href="<?php echo wp_login_url(); ?>?nbtsl_login_id=google_login<?php
                    if ( $encoded_url ) {
                        echo "&state=" . base64_encode( "redirect_to=$encoded_url" );
                    }
                    ?>" title='<?php
                       _e( 'Login with google', 'nbt-solution' );
                       
                       ?>' >
                        <div class="nbtsl-icon-block icon-google clearfix">                            
                            <span class="iconsl iconsl-google-plus"></span>                            
                            <span class="nbtsl-login-text"><?php _e( 'Login', 'nbt-solution' ); ?></span>
                            <span class="nbtsl-long-login-text"><?php _e( 'Login with google', 'nbt-solution' ); ?></span>
                        </div>
                    </a>
                    <?php

            }?>
        </div>
    </div>
<?php
}