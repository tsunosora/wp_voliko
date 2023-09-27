<?php
/* Child category widget */
class wpnetbase_child_category_widget extends WP_Widget
{
	function __construct()
	{
		parent::__construct(
		// Base ID of your widget
		'wpnetbase_child_category_widget', 

		// Widget name will appear in UI
		__('NBT Woo Child Category', 'wpb_widget_domain'), 

		// Widget description
		array( 'description' => __( 'Show child Category by parent For Woocommerce', 'wpb_widget_domain' ), 'panels_groups' => array('netbaseteam')) 
		);
		$this->cateshow_limit = array( '1', '2','3','4','6','12');
	}
	public function widget( $args, $instance ) 
	{

		if($instance['title']){
			$title =  $instance['title'];
		}
		
		if($instance['parent_category']){
			$parent_category =  $instance['parent_category'];
		}
		else{
			$parent_category = "Category Products";
		}
		if($instance['cateshow_limit']){
			$cateshow_limit =  $instance['cateshow_limit'];
		}
		else { $cateshow_limit = 10; }

		?>
		<div id="nbt-child-cat-<?php echo $this->id; ?>" class="child-category-widget row">
					<h3 class="title"><?php echo $title; ?></h3>
					<?php
						if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) 
						{
								 
								$IDbyNAME = get_term_by('name', $parent_category, 'product_cat');
								 
								$product_cat_ID = $IDbyNAME->term_id;
								 
								   $args = array(
								 
									   'hierarchical' => 1,
								 
									   'show_option_none' => '',
								 
									   'hide_empty' => 0,
								 
									   'parent' => $product_cat_ID,
								 
									   'taxonomy' => 'product_cat'
								 
								   );
								 
								$subcats = get_categories($args);
								$columnlimit = 12 / $cateshow_limit;
								foreach ($subcats as $sc) {
								 
									$link = get_term_link( $sc->slug, $sc->taxonomy );
									$thumbnail_id = get_woocommerce_term_meta( $sc->term_id, 'thumbnail_id', true );
									$image = wp_get_attachment_url( $thumbnail_id );									
									
									echo '<div class="items-child-cate col-md-'.$columnlimit.' col-xs-4">';
									if ( $image ) {
										echo '<div class="box-img-child-cate"><img src="' . $image . '"  /></div>';
									}
									echo '<a href="'. $link .'">'.$sc->name.'</a></div>';							 
								}								 								
						}
						else
						{
							?>
								<div class="js-empty-section">
										
										<p>Woocommerce plugin does not exist</p>
											
									</div>
							<?php
						}
					?>
					</div>
			
			<?php
		
	}			
	// Widget Backend 
	public function form( $instance ) 
	{
		
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		
		if ( isset( $instance[ 'parent_category' ] ) ) {
			$parent_category = $instance[ 'parent_category' ];
		
		}
		else 
		{
			$parent_category ='Clothing';
		}
		if ( isset( $instance[ 'cateshow_limit' ] ) ) 
		{
			$cateshow_limit = $instance[ 'cateshow_limit' ];
		}
		else { $cateshow_limit =6; }
			
		$prod_cat_args = array(
		  'taxonomy'     => 'product_cat', 
		  'orderby'      => 'name'
		  
		);

		$woo_categories = get_categories( $prod_cat_args );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p class="default-form">
			<label for="<?php echo $this->get_field_id( 'parent_category' ); ?>"><?php _e( 'Product Category:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'parent_category' ); ?>" name="<?php echo $this->get_field_name( 'parent_category' ); ?>" type="text" value="<?php echo esc_attr( $parent_category ); ?>" />			
		</p>
		<p>

			<label for="<?php echo $this->get_field_id( 'cateshow_limit' ); ?>"><?php _e( 'Number columns:' ); ?></label> 
			<select id="<?php echo $this->get_field_id( 'cateshow_limit' ); ?>" name="<?php echo $this->get_field_name( 'cateshow_limit' ); ?>">
	        <?php
	        foreach ( (array) $this->cateshow_limit as $cateshow_limit ) {
	        printf( '<option value="%s" %s>%s</option>', $cateshow_limit, selected( $cateshow_limit, $instance['cateshow_limit'], 0 ), $cateshow_limit );
	        } ?>
	        </select>
		</p>
		
		<?php 
	}
		
	public function nbt_category_pro_update( $new_instance, $old_instance ) 
	{
		$instance = array();
		
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['parent_category'] = ( ! empty( $new_instance['parent_category'] ) ) ? strip_tags( $new_instance['parent_category'] ) : '';
		$instance['cateshow_limit'] = ( ! empty( $new_instance['cateshow_limit'] ) ) ? strip_tags( $new_instance['cateshow_limit'] ) : '';
		
		return $instance;
	}
}

function wpnetbase_child_category_load_widget() {	
	register_widget( 'wpnetbase_child_category_widget' );
	
}
add_action( 'widgets_init', 'wpnetbase_child_category_load_widget' );
