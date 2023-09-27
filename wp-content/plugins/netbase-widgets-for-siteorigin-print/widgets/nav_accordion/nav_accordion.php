<?php 
function wpnetbase_jqverticalmegamenu_load_widget() 
{
	register_widget( 'wpnetbase_navaccordion_widget' );
}
add_action( 'widgets_init', 'wpnetbase_jqverticalmegamenu_load_widget' );

class wpnetbase_navaccordion_widget extends WP_Widget {
function __construct() 
	{
		
		parent::__construct(
			'wpnetbase_navaccordion_widget', 
			__( 'NBT Menu', 'text_domain' ),
			array( 'description' => __( 'Create Vertical, Accordion From Any Wordpress Custom Menu', 'text_domain' ),
			'panels_groups' => array('netbaseteam') ) 
		);
		
		$this->defaults = array(
			'title' => '',
			'rowItems' => 3,
			'subMenuWidth' => '260px',
			'speed' => 'slow',
			'effect' => 'fade',
			'direction' => 'right',
			'skin' => 'white.css'
		);
	}
    
	function widget($args, $instance) {
		extract( $args );
		
		/*Get menu*/	
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );
		
		$nav_menu = wp_get_nav_menu_object( $instance['nav_menu'] );

		if ( !$nav_menu )
			return;

		$instance['title'] = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		
		echo $args['before_widget'];
			
		?>
		
		<?php if($instance['skin'] =='skin-accordion'){

			if ( !empty($instance['title']) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
			
			?>
			<div class="nbt-accordion-menu" id="<?php echo $this->id.'-item'; ?>">			
			<?php	
				wp_nav_menu( array( 'fallback_cb' => '', 'menu' => $nav_menu, 'container' => false ) );
			
			?>
			<script>
			jQuery(document).ready(function(){		
				jQuery('.nbt-accordion-menu').navAccordion({
					expandButtonText: '<i class="fa fa-plus"></i>',  
					collapseButtonText: '<i class="fa fa-minus"></i>'
				}, 
				function(){
					console.log('Callback')
				});			
			});
			</script>
			</div>
			<?php 
		}
		else{?>
			<div class="dcjq-vertical-mega-menu" id="<?php echo $this->id.'-item'; ?>">
				<div id="dcjq-vertical-mega-menu-title">
					<span class="fa fa-bars"></span>
					<p><?php if ( !empty($instance['title']) )
					echo  $instance['title'];?></p>
				</div>
			<?php wp_nav_menu( array( 'fallback_cb' => '', 'menu' => $nav_menu, 'container' => false ) );?>	
			</div>
		<?php }?>		
		<?php		
		echo $args['after_widget'];
	}
    function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( stripslashes($new_instance['title']) );
		$instance['nav_menu'] = (int) $new_instance['nav_menu'];
		$instance['rowItems'] = $new_instance['rowItems'];
		$instance['subMenuWidth'] = $new_instance['subMenuWidth'];
		$instance['skin'] = $new_instance['skin'];
		$instance['speed'] = $new_instance['speed'];
		$instance['effect'] = $new_instance['effect'];
		$instance['direction'] = $new_instance['direction'];
		
		return $instance;
	}

    function form($instance) {
		$title = isset( $instance['title'] ) ? $instance['title'] : '';
		$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';
		$rowItems = isset( $instance['rowItems'] ) ? $instance['rowItems'] : '';
		$subMenuWidth = isset( $instance['subMenuWidth'] ) ? $instance['subMenuWidth'] : '';
		$skin = isset( $instance['skin'] ) ? $instance['skin'] : '';
		$speed = isset( $instance['speed'] ) ? $instance['speed'] : '';
		$effect = isset( $instance['effect'] ) ? $instance['effect'] : '';
		$direction = isset( $instance['direction'] ) ? $instance['direction'] : '';
		
		$widget_options = wp_parse_args( $instance, $this->defaults );
		extract( $widget_options, EXTR_SKIP );

		/*Get menus*/ 
		$menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
		
		if ( !$menus ) {
			echo '<p>'. sprintf( __('No menus have been created yet. <a href="%s">Create some</a>.'), admin_url('nav-menus.php') ) .'</p>';
			return;
		}
		?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('nav_menu'); ?>"><?php _e('Select Menu:'); ?></label>
		<select id="<?php echo $this->get_field_id('nav_menu'); ?>" name="<?php echo $this->get_field_name('nav_menu'); ?>">
		<?php
			foreach ( $menus as $menu ) {
				$selected = $nav_menu == $menu->term_id ? ' selected="selected"' : '';
				echo '<option'. $selected .' value="'. $menu->term_id .'">'. $menu->name .'</option>';
			}
		?>
		</select>
	</p>
	<p>
	  <label for="<?php echo $this->get_field_id('rowItems'); ?>"><?php _e( 'Number Items Per Row' , 'dcjq-vertical-mega-menu' ); ?></label>
		<select name="<?php echo $this->get_field_name('rowItems'); ?>" id="<?php echo $this->get_field_id('rowItems'); ?>" >
			<option value='1' <?php selected( $rowItems, '1'); ?> >1</option>
			<option value='2' <?php selected( $rowItems, '2'); ?> >2</option>
			<option value='3' <?php selected( $rowItems, '3'); ?> >3</option>
			<option value='4' <?php selected( $rowItems, '4'); ?> >4</option>
			<option value='5' <?php selected( $rowItems, '5'); ?> >5</option>
			<option value='6' <?php selected( $rowItems, '6'); ?> >6</option>
			<option value='7' <?php selected( $rowItems, '7'); ?> >7</option>
			<option value='8' <?php selected( $rowItems, '8'); ?> >8</option>
			<option value='9' <?php selected( $rowItems, '9'); ?> >9</option>
			<option value='10' <?php selected( $rowItems, '10'); ?> >10</option>
		</select>
		</p>
	<input type="hidden" value="<?php echo $subMenuWidth; ?>" class="widefat" id="<?php echo $this->get_field_id('subMenuWidth'); ?>" name="<?php echo $this->get_field_name('subMenuWidth'); ?>" />
	<p><label for="<?php echo $this->get_field_id('effect'); ?>"><?php _e('Animation Effect:', 'dcjq-vertical-mega-menu'); ?>
		<select name="<?php echo $this->get_field_name('effect'); ?>" id="<?php echo $this->get_field_id('effect'); ?>" >
			<option value='show' <?php selected( $effect, 'show'); ?> >No Animation</option>
			<option value='fade' <?php selected( $effect, 'fade'); ?> >Fade In</option>
			<option value='slide' <?php selected( $effect, 'slide'); ?> >Slide Out</option>
		</select>
		</label>
	</p>
	<p><label for="<?php echo $this->get_field_id('direction'); ?>"><?php _e('Animation Direction:', 'dcjq-vertical-mega-menu'); ?>
		<select name="<?php echo $this->get_field_name('direction'); ?>" id="<?php echo $this->get_field_id('direction'); ?>" >
			<option value='right' <?php selected( $direction, 'right'); ?> >Right</option>
			<option value='left' <?php selected( $direction, 'left'); ?> >Left</option>
		</select>
		</label>
	</p>
	<p><label for="<?php echo $this->get_field_id('speed'); ?>"><?php _e('Animation Speed:', 'dcjq-vertical-mega-menu'); ?>
		<select name="<?php echo $this->get_field_name('speed'); ?>" id="<?php echo $this->get_field_id('speed'); ?>" >
			<option value='fast' <?php selected( $speed, 'fast'); ?> >Fast</option>
			<option value='normal' <?php selected( $speed, 'normal'); ?> >Normal</option>
			<option value='slow' <?php selected( $speed, 'slow'); ?> >Slow</option>
		</select>
		</label>
	</p>
<p><label for="<?php echo $this->get_field_id('skin'); ?>">
<?php _e('Skin:', 'dcjq-vertical-mega-menu'); ?>  <?php	

echo "<select name='".$this->get_field_name('skin')."' id='".$this->get_field_id('skin')."'>";
		echo "<option value='skin-vertical' ".selected( $skin, 'skin-vertical', false).">Vertical</option>";
		echo "<option value='skin-accordion' ".selected( $skin, 'skin-accordion', false).">Accordion</option>";
		echo "</select>"; ?> 
		</label><br />
	</p>
	<?php 
	}
} 