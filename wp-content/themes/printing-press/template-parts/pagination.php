<?php
	the_posts_pagination( array(
		'prev_text' => esc_html__( 'Previous page', 'printing-press' ),
		'next_text' => esc_html__( 'Next page', 'printing-press' ),
	) );