<h3><?php echo esc_attr($faq['heading']);?></h3>
<ul>
	<?php foreach ($faq['lists'] as $key => $list) {?>
	<li>
		<h4 class="nb-faq-title"><?php echo esc_attr($list['faq_title']);?><span></span></h4>
		<div class="nb-faq-content">
			<?php echo esc_attr($list['faq_content']);?>
		</div>
	</li>
	<?php }?>
</ul>