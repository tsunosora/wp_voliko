<?php
get_header();
do_action('woopanel_init');?>
	<div id="woopanel_main" class="m-grid m-grid--hor m-grid--root m-page">
		<?php

		do_action('woopanel_start');
		global $post;
		echo do_shortcode('[woopanel]');
		do_action('woopanel_end');?>
	</div>
<?php
do_action('woopanel_footer');
get_footer();?>