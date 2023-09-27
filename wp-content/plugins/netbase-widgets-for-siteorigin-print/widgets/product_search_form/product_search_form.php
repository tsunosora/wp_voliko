<?php 
class wpnetbase_product_search_form_widget extends WP_Widget {

	function __construct() {
		parent::__construct(
		/*Base ID of your widget*/ 
		'wpnetbase_product_search_form_widget', 

		/*Widget name will appear in UI*/ 
		__('NBT Product Search Form', 'wpb_widget_domain'), 

		/*Widget description*/ 
		array( 'description' => __( 'Product Search Form For Woocommerce', 'wpb_widget_domain' ),  'panels_groups' => array('netbaseteam') ) 
		);
	}
	/*Creating widget front-end*/ 
	public function widget( $args, $instance )
	{
		$args = array(
				'number'     => '',
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => true,
				'include'    => array()
			);
			$product_categories = get_terms( 'product_cat', $args );
			
			$categories_show = '<option value="">'.__('All Categories','wpdance').'</option>';
			$check = '';
			if(is_search()){
				if(isset($_GET['term']) && $_GET['term']!=''){
					$check = $_GET['term'];
				}
			}
			$checked = '';
			foreach($product_categories as $category){
				if(isset($category->slug)){
					if(trim($category->slug) == trim($check)){
						$checked = 'selected="selected"';
					}
					$categories_show  .= '<option '.$checked.' value="'.$category->slug.'">'.$category->name.'</option>';
					$checked = '';
				}
			}
			$form = '<form role="search" method="get" id="searchform" action="' . esc_url( home_url( '/'  ) ) . '">
			 <select class="wd_search_product" name="term">'.$categories_show.'</select>
			 <div class="wd_search_form">
				 <label class="screen-reader-text" for="s">' . __( 'Search for:', 'wpdance' ) . '</label>
				 <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . __( 'Search for products', 'wpdance' ) . '" />
				 <input type="submit" title="Search" id="searchsubmit" value="'. esc_attr__( 'Search', 'wpdance' ) .'" />
				 <input type="hidden" name="post_type" value="product" />
				 <input type="hidden" name="taxonomy" value="product_cat" />
			 </div>
			</form>';
			//$form .='<script type="text/javascript">
			//		jQuery("select.wd_search_product").select2();
			//</script>';
			echo $form;
			if($instance['itemcart'] == 'yes' && is_object( WC()->cart ))
			{
				echo '<div class="printshop-minicart">';
				echo "".sprintf(_n('%d item', '%d ITEM(S)', WC()->cart->get_cart_contents_count(), 'woothemes'), WC()->cart->get_cart_contents_count()); echo "|";echo "".WC()->cart->get_cart_total();
				echo '</div>';
			}
	}
	/*Widget Backend */ 
	public function form( $instance ) 
	{
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		if ( isset( $instance[ 'itemcart' ] ) ){
			$itemcart = $instance[ 'itemcart'];
		}
		else {
			$itemcart ='yes';
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'itemcart' ); ?>"><?php _e( 'Show Items Cart:' ); ?></label> 
			
			<input type="radio" name="<?php echo $this->get_field_name( 'itemcart' ); ?>" id="<?php echo $this->get_field_id( 'itemcart_yes' ); ?>" value="yes" <?php if($itemcart=='yes'){ echo "checked";}?>>Yes
			
			<input type="radio" name="<?php echo $this->get_field_name( 'itemcart' ); ?>" id="<?php echo $this->get_field_id( 'itemcart_no' ); ?>" value="no" <?php if($itemcart=='no'){ echo "checked";}?>>NO
		</p>

	<?php
	}
	public function update( $new_instance, $old_instance ) 
	{
		$instance = array();
		
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['itemcart'] = ( ! empty( $new_instance['itemcart'] ) ) ? strip_tags( $new_instance['itemcart'] ) : '';
		return $instance;
	}

}

function wpnetbase_load_product_search_widget() 
{
	register_widget( 'wpnetbase_product_search_form_widget' );
}
add_action( 'widgets_init', 'wpnetbase_load_product_search_widget' );