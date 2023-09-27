<?php
class wpnetbase_woo_products_same_category extends WP_Widget {

	function __construct() {
		parent::__construct(
		/*Base ID of your widget*/ 
		'wpnetbase_woo_products_same_category', 

		/*Widget name will appear in UI*/ 
		__('NBT products same category', 'wpb_widget_domain'), 

		/*Widget description*/ 
		array( 'description' => __( 'Woocommerce products in same category.', 'wpb_widget_domain' ),  'panels_groups' => array('netbaseteam') ) 
		);
	}
    /**
     * How to display the widget on the screen.
     */
    function widget($args, $instance) {
        echo $args['before_widget']; // start

        $this->_products($instance);
        echo $args['after_widget']; //end

        
    }

    /**
     * Update the widget settings.
     */
    function update($new_instance, $old_instance) {
        
        $instance = $old_instance;
        $instance['title'] =  empty($new_instance['title']) ? '' : $new_instance['title'];
        $instance['product_count'] =  empty($new_instance['product_count'])
                                      ? 0
                                      : (int) $new_instance['product_count'];
        return $instance;
    }

    /**
     * Displays the widget settings controls on the widget panel.
     * Make use of the get_field_id() and get_field_name() function
     * when creating your form elements. This handles the confusing stuff.
     */
    function form($instance) {
      $product_count =  isset($instance['product_count']) ? $instance['product_count'] : 0;
      $title =  isset($instance['title']) ? $instance['title'] : '';
    ?>
            <!-- Widget Title: Text Input -->
              <p>
                    <label for="<?= $this->get_field_id( 'product_count' ); ?>">
                      Title
                    </label>
                    <input type="text" name="<?= $this->get_field_name( 'title' ); ?>"
                          value="<?= $title; ?>" class="widefat"
                          id="<?= $this->get_field_id( 'title' ); ?>" />
                </p>
         
            <!-- Widget text: Text Input -->
                <p>
                    <label for="<?= $this->get_field_id( 'product_count' ); ?>">
                      Number of Products To Show
                    </label>
                    <input type="text" name="<?= $this->get_field_name( 'product_count' ); ?>"
                          value="<?= $product_count; ?>" class="widefat"
                          id="<?= $this->get_field_id( 'product_count' ); ?>" />
                </p>
    <?php
       
    }

    private function _products($instance){
         if ( is_singular('product') ) {
          ?>
          <div class="nbtsow-products-wrap">
            <?php
            if(isset($instance['title'])){ $title =  $instance['title'];
            ?>
                <h2 class="widget-heading"><?php echo $title ?></h2>
            <?php
            }
            if(isset($instance['product_count'])){
                 $product_count = $instance['product_count'];
            }else{ $product_count = 5; }
           
            global $post;
              $post_ID = $post->ID;
              // get categories
              $terms = wp_get_post_terms( $post->ID, 'product_cat' );
              foreach ( $terms as $term ) $cats_array[] = $term->term_id;
              $query_args = array(
                  // 'post__not_in' => array( $post->ID ),
                  'posts_per_page' => $product_count,
                  'no_found_rows' => 1,
                  'post_status' => 'publish',
                  'orderby' => 'title',
                  'order' => 'ASC',
                  'post_type' => 'product',
                  'tax_query' => array(
                    array(
                      'taxonomy' => 'product_cat',
                      'field' => 'id',
                      'terms' => $cats_array
                )));
              $r = new WP_Query($query_args);
              if ($r->have_posts()) {
            ?>
          
            <ul class="product_list_widget">
                  <?php while ($r->have_posts()) : $r->the_post(); global $product; ?>
                    <li class="product">
                    <?php
                    $cur_product = new WC_Product(get_the_ID());
                    ?>
                    <div class="product-thumb">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()){
                                the_post_thumbnail();
                            } ?>
                            
                        </a>
                    </div>
                    <div class="product-details">
                        <div class="product-meta">
                            <h4 class="product-title">
                                <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php the_title(); ?></a>
                            </h4>
                            <span class="product-price"><?php echo $cur_product->get_price_html(); ?></span>
                        </div>
                          <p class="product-description">
                          <?php echo wp_trim_words( get_the_excerpt(), 6, '...' ); ?>
                          </p>
                    </div>
                      
                    </li>
                  <?php endwhile; ?>
              </ul>
            </div>
            <?php
              // Reset the global $the_post as this query will have stomped on it
              wp_reset_query();
              }
         }
    }

}
function wpnetbase_woo_products_same_category_widget() 
{
	register_widget( 'wpnetbase_woo_products_same_category' );
}
add_action( 'widgets_init', 'wpnetbase_woo_products_same_category_widget' );
?>