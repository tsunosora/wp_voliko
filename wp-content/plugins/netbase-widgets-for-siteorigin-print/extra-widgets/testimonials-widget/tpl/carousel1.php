<?php if( !empty( $instance['title'] ) ) 
echo $args['before_title'] . esc_html($instance['title']) . $args['after_title'] ?>

<div class="wpnetbase-testimonials-carousel owl-carousel" id="owl-demo">
	<?php foreach($instance['columns'] as $i => $column) : ?>
		<div class="ow-pt-column">
			<?php 	
				if($instance['image_top']){
					 if(!empty($column['testimonials_avatar'])) : ?>
					<div class="ow-pt-image">
						<?php $this->column_image($column) ?>
					</div>
				<?php endif;  
				}			
			?>
			<?php if(!empty($column['testimonials_content'])) : ?>
			<div class="testimonials-widget-content">
			<?php echo esc_html( $column['testimonials_content'] ) ?> 
			</div>
			<?php endif;?>
			
			<div class="nbt-row-info">
				<?php if(!empty($column['testimonials_name'])) : ?>
				<div class="ow-pt-title">
				
					<?php echo esc_html( $column['testimonials_name'] ) ?>
					
				</div>
				<?php endif;?>
				
				<?php 
				if(!$instance['image_top']){
			
					if(!empty($column['testimonials_avatar'])) : ?>
						<div class="ow-pt-image">
							<?php $this->column_image($column) ?>
						</div>
				<?php endif; 
				
				}
			
				?>		
				<?php if( !empty($column['testimonials_company']) ) : ?>
					<div class="ow-pt-button">
						<a href='<?php echo sow_esc_url($column['testimonials_company_url']) ?>' class="ow-pt-link" <?php //if( !empty( $instance['button_new_window'] ) ) echo 'target="_blank"' ?>><?php echo esc_html($column['testimonials_company']) ?></a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>

			
			
			
			
