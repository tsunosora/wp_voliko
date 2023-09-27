<?php
class NBT_Brands_Thumbnail_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'nbt-brands-thumbnail',
			__( 'NBT Brands Thumbnail', 'nbt-solution' ),
			array(
				'classname' => 'nbt-brands-thumbnail',
				'description' => __( 'Enter a custom description for your new widget', 'nbt-solution' )
			)
		);
	}

	public function form( $instance ) {
		$title = esc_attr( $instance['title'] );
		$show_title = esc_attr( $instance['show_title'] );
		$limit = esc_attr( $instance['limit'] );?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>: 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>">
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('show_title'); ?>">
				 <input type="checkbox" id="<?php echo $this->get_field_id('show_title'); ?>" name="<?php echo $this->get_field_name('show_title'); ?>" value="1" <?php checked( $show_title, 1 ); ?>> <?php _e('Show title', 'nbt-solution'); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Limit of items:'); ?>: 
				<input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo $limit; ?>">
			</label>
		</p>
	<?php
	}
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';

        $instance['limit'] = strip_tags($new_instance['limit']);
        $instance['show_title'] = strip_tags($new_instance['show_title']);
        return $instance;
    }
	public function widget( $args, $instance ) {
		global $woocommerce;
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
			if ( $title ) :
				echo $before_title . $title . $after_title;
			endif;

			?>
			<div class="nbtsow-products-wrap">
			<?php
			$terms = get_terms( array(
			    'taxonomy' => 'product_brand',
			    'hide_empty' => false,
			) );
			if($terms){?>
				<ul class="nbt-brands-thumbnail clearfix">
				<?php foreach ($terms as $key => $term) {
					$brands_thumbnail = get_term_meta( $term->term_id, 'brands_thumbnail', true );
				    $brands_thumbnail = wp_get_attachment_image_src($brands_thumbnail);

				    $brands_target = get_term_meta( $term->term_id, 'brands_target', true );
				    $brands_type = get_term_meta( $term->term_id, 'brands_type', true );
				    $brands_link  = get_term_link($term, 'product_brand');
				    if($brands_type == 'url'){
				    	$brands_link = get_term_meta( $term->term_id, 'brands_url', true );
				    }
					?>
					<li>
						<div class="nbt-bc-thumb">
							<a href="<?php echo $brands_link;?>" target="<?php echo $brands_target;?>"><img src="<?php echo $brands_thumbnail[0];?>" class="img1" alt="Evolis"></a>
						</div>
					</li>
				    <?php
				}?>
				</ul>
				<?php
			}
			?>

			</div>
			<?php

		echo $after_widget;

	}

}


class NBT_Brands_Slider_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'nbt-brands-slider',
			__( 'NBT Brands Slider', 'nbt-solution' ),
			array(
				'classname' => 'nbt-brands-slider',
				'description' => __( 'Enter a custom description for your new widget', 'nbt-solution' )
			)
		);
	}

	public function form( $instance ) {
		$title = esc_attr( $instance['title'] );
		$limit = esc_attr( $instance['limit'] );?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>: 
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>">
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Limit of items', 'nbt-solution'); ?>: 
				<input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo $limit; ?>">
			</label>
		</p>

	<?php
	}
    public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
        $instance['limit'] = strip_tags($new_instance['limit']);
        return $instance;
    }
	public function widget( $args, $instance ) {
		global $woocommerce;
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );
		$limit = $instance['limit'];
		if(!$limit){
			$limit = 6;
		}

		echo $before_widget;

		$terms = get_terms( array(
		    'taxonomy' => 'product_brand',
		    'hide_empty' => false,
		) );
		if($terms){
			$did = rand(0,1000);
			?>

			<div class="nbtsow-products-wrap">
			<?php
			if ( $title ) :
				echo $before_title . $title . $after_title;
			endif;?>
				<ul class="owl-carousel nbt-brands-carousel" id="slider_<?php echo $did;?>">
					<?php foreach ($terms as $term) {
						$brands_thumbnail = get_term_meta( $term->term_id, 'brands_thumbnail', true );
					    $brands_thumbnail = wp_get_attachment_image_src($brands_thumbnail);

					    $brands_target = get_term_meta( $term->term_id, 'brands_target', true );
					    $brands_type = get_term_meta( $term->term_id, 'brands_type', true );
					    $brands_link  = get_term_link($term, 'product_brand');
					    if($brands_type == 'url'){
					    	$brands_link = get_term_meta( $term->term_id, 'brands_url', true );
					    }
						?>
					<li>
						<div class="nbt-bc-thumb">
							<a href="<?php echo $brands_link;?>" target="<?php echo $brands_target;?>"><img src="<?php echo $brands_thumbnail[0];?>" class="img1" alt="Evolis"></a>
						</div>
					</li>
					<?php }?>
				</ul>
				<script type="text/javascript">
				jQuery(document).ready(function() {
					var nb_rtl = false;
					if(jQuery('body.rtl').length){
						nb_rtl = true;
					}
					jQuery('#slider_<?php echo $did;?>').owlCarousel({ 
						items: <?php echo $limit;?>,
						autoplay: true,
						margin: 15,
						autoplayTimeout: 3000, 
						autoplayHoverPause: true,
						autoplaySpeed:350,
						nav : false,
						rtl: nb_rtl,
						loop: true,
						navText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
						responsive:{
							0:{
								items:1,            
							},
							480:{
								items:2,            
							},
							600:{
								items:3, margin: 18            
							},
							768:{
								items: <?php echo $limit;?>,    
							}

						},

					});
				});
				</script>
			</div>
			<?php
		}

		echo $after_widget;

	}

}
