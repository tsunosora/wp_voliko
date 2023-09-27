<?php
global $post, $admin_options;

$pagination_base = str_replace( $post->ID, '%#%', esc_url( get_pagenum_link( $post->ID ) ) );
?>


<div id="wpl-store-list" data-store_category="<?php echo isset($_GET['store_category']) ? absint($_GET['store_category']) : '';?>">
	<?php do_action('woopanel_before_store_lists');?>

    <?php

    if( isset($admin_options->options['store_listing_layout']) ) {
      switch ($admin_options->options['store_listing_layout']) {
        case 'style2':
            $template_args = array(
              'limit'           => $limit,
              'paged'           => $paged,
              'count'       => $count,
              'pagination_base' => $pagination_base,
              'per_row'         => $per_row,
          );

          woopanel_get_template_part( 'vendor/store-listing-style-2', false, $template_args );
          break;
        
        default:
          $template_args = array(
              'limit'           => $limit,
              'paged'           => $paged,
              'count'       => $count,
              'pagination_base' => $pagination_base,
              'per_row'         => $per_row,
          );

          woopanel_get_template_part( 'vendor/store-listing-style-1', false, $template_args );
          break;
      }
    }else {
      $template_args = array(
          'limit'           => $limit,
          'paged'           => $paged,
          'count'       => $count,
          'pagination_base' => $pagination_base,
          'per_row'         => $per_row,
      );

      woopanel_get_template_part( 'vendor/store-listing-style-1', false, $template_args );
    }?>
	
	<?php do_action('woopanel_after_store_lists');?>
</div>

<?php echo do_shortcode('[woopanel_store_locator zoom="15"]');?>

