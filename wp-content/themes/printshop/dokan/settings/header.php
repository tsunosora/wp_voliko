<?php
/**
 * Dokan Settings Header Template
 *
 * @since 2.4
 *
 * @package dokan
 */
global $wp;
?>
<header class="dokan-dashboard-header dokan-<?php echo $wp->query_vars['settings'];?>">
    <h1 class="entry-title">
        <?php echo $heading; ?>
        <small>&rarr; <a href="<?php echo dokan_get_store_url( get_current_user_id() ); ?>"><?php _e( 'Visit Store', 'printshop' ); ?></a></small>
    </h1>
</header><!-- .dokan-dashboard-header -->
