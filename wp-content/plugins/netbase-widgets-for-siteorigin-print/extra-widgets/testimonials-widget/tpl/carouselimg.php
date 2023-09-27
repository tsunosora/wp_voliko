<?php if( !empty( $instance['title'] ) ) 
echo $args['before_title'] . esc_html($instance['title']) . $args['after_title'] ?>

<div class="noo_testimonial_wrap">
<div class="noo-testimonial-sync2 testimonial-three owl-carousel owl-theme">
	<?php foreach($instance['columns'] as $i => $column) : ?>
	<div class="item">
		<div class="testimonial-content">
			<?php if(!empty($column['testimonials_name'])) : ?>
				<h3 class="testi-title"><?php echo esc_html( $column['testimonials_name'] ) ?></h3>
			<?php endif;?>
			
			<div>
				<i class="fa fa-quote-left"></i>
				<?php if(!empty($column['testimonials_content'])) : ?>
				<p>
				<?php echo esc_html( $column['testimonials_content'] ) ?> 
				</p>
			<?php endif;?>
				
				<i class="fa fa-quote-right"></i>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
</div>
<div class="noo-testimonial-sync1 testimonial-three owl-carousel owl-theme">
<?php foreach($instance['columns'] as $i => $columnssync1) : ?>
	<div class="item">
		<div class="background_image">
			
			<?php if(!empty($columnssync1['testimonials_avatar'])) : ?>
					
						<?php $this->column_image($columnssync1) ?>
					
				<?php endif; ?>
			
		</div>
		<div class="testimonial-name">
			<?php if(!empty($columnssync1['testimonials_name'])) : ?>
				
				<h4 class="noo_testimonial_name">
				<?php 
					$strname = explode(' ', $columnssync1['testimonials_name']);
					echo $strname[0];
				?></h4>
				
			<?php endif;?>
			<?php if( !empty($columnssync1['testimonials_company']) ) : ?>					
						
					<span class="noo_testimonial_position">
					<a href='<?php 
					if(!empty($columnssync1['testimonials_company_url'])){
						echo sow_esc_url($columnssync1['testimonials_company_url']);
						}else {echo "#";}  ?>' class="ow-pt-link">
					<?php echo esc_html($columnssync1['testimonials_company']) ?>
					</a> 
					</span>
			<?php endif; ?>
			
		</div>
	</div>	
<?php endforeach; ?>	
</div>
</div>
<script>
jQuery(document).ready(function ($) {
 
	var $sync1 = jQuery(".noo-testimonial-sync2"),
		$sync2 = jQuery(".noo-testimonial-sync1"),
		flag = false,
		duration = 300;

	$sync1.owlCarousel({
		startPosition:1, 
		//autoplay:true,
		//autoplayTimeout:5000,
			items: 1
			
			//margin: 10
			//nav: true,
			//dots: true
		})
		.on('changed.owl.carousel', function (e) {
			if (!flag) {
				flag = true;
				$sync2.trigger('to.owl.carousel', [e.item.index, duration, true]);
				flag = false;
			}
		});

	$sync2.owlCarousel({
			//margin: 60,
			items: 3, 
			//autoplay:true, 
			//autoplayTimeout:5000,
			//nav: true,
			startPosition:1,
			center: true,
			responsive : {
			// breakpoint from 480 up
				0 : {
					items: 1
				},768 : {
					items: 3
				}
			}
			//dots: true
			
		})
		.on('click', '.owl-item', function () {
			$sync1.trigger('to.owl.carousel', [$(this).index(), duration, true]);			

		})
		.on('changed.owl.carousel', function (e) {
			if (!flag) {
				flag = true;		
				$sync1.trigger('to.owl.carousel', [e.item.index, duration, true]);
				flag = false;
			}
		});
});
</script>
			
			
			
			
