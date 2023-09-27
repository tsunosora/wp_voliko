<div class="nbt-faqs-container">
	<?php if($data){
		foreach ($data as $key => $faq) {
			$heading = $faq['heading'];
			if(!isset($faq['lists'])){
				$faqs = get_post_meta($heading['faq'], '_nbt_faq', true);
				$faq = $faqs[$heading['id']];
			}
			include NBT_FAQS_PATH .'tpl/repeater.php';
		}
	}?>
</div>