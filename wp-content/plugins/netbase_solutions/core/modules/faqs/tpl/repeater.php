<h3><?php echo $faq['heading'];?></h3>
<ul>
	<?php foreach ($faq['lists'] as $key => $list) {?>
	<li>
		<h4 class="nbt-faq-title"><?php echo $list['faq_title'];?><span></span></h4>
		<div class="nbt-faq-content">
			<?php echo $list['faq_content'];?>
		</div>
	</li>
	<?php }?>
</ul>