<div class="nbtsow-members clear">

	<?php foreach($members as $member): ?>
	<div class="nbtsow-member">

		<?php if($member['image']): ?>
		<div class="nbtsow-member-thumb">
			<?php echo wp_get_attachment_image($member['image'], 'full');?>
			<?php if($member['social']): ?>
			<div class="nbtsow-member-social">

				<?php if($member['social']['facebook']): ?>
				<a href="<?php echo esc_url($member['social']['facebook']); ?>">
					<i class="icon-facebook"></i>
				</a>
				<?php endif;?>

				<?php if($member['social']['twitter']): ?>
				<a href="<?php echo esc_url($member['social']['twitter']); ?>">
					<i class="icon-twitter"></i>
				</a>
				<?php endif;?>

				<?php if($member['social']['gplus']): ?>
				<a href="<?php echo esc_url($member['social']['gplus']); ?>">
					<i class="icon-gplus"></i>
				</a>
				<?php endif;?>

			</div>
			<?php endif;?>
		</div>
		<?php endif;?>

		<div class="nbtsow-member-details">
			<?php if($member['name']): ?>
			<h4 class="nbtsow-member-name"><?php echo wp_kses_post($member['name']); ?></h4>
			<?php endif;?>
			<?php if($member['profile']): ?>
			<p class="nbtsow-member-description"><?php echo wp_kses_post($member['profile']); ?></p>
			<?php endif;?>
		</div>

	</div>
	<?php endforeach;?>
</div>
