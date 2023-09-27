<?php
	function nbt_ajax_search(){
		$ajaxsearch_settings = get_option('ajax-search_settings');
		$get_settings = NBT_Ajax_Search_Settings::get_settings();



		$ajaxsearch_icon = $ajaxsearch_settings['wc_'.NBT_Solutions_Ajax_Search::$plugin_id.'_color_icon'];
		if(!$ajaxsearch_icon){
			if(isset($get_settings['icon_color']['default'])){
				$ajaxsearch_icon = $get_settings['icon_color']['default'];
			}
		}

		$ajaxsearch_primary_color = $ajaxsearch_settings['wc_'.NBT_Solutions_Ajax_Search::$plugin_id.'_primary_color'];
		if(!$ajaxsearch_primary_color){
			if(isset($get_settings['primary_color']['default'])){
				$ajaxsearch_primary_color = $get_settings['primary_color']['default'];
			}
		}
		?>
		<style type="text/css">
			.nbt-icon-search{
				color: <?php echo $ajaxsearch_icon;?> !important;
			}
			.nbt-search-wrapper{
				border-top-color: <?php echo $ajaxsearch_primary_color;?> !important;
			}
			.nbt-search-wrapper:before{
				border-bottom-color: <?php echo $ajaxsearch_primary_color;?> !important;
			}

		</style>


		<div class="nbt-icon-plugins">
			<i class="nbt-icon-search" aria-hidden="true"></i>
			<div class="nbt-search-wrapper">
				<form action="" autocomplete="off">
					<input type="text" class="nbt-input-search" name="phrase" value="" autocomplete="off" placeholder="<?php echo __('Search here...', 'nbt-solution');?>">
					<span class="nbt-icon-loading nbt-icon-spin4 animate-spin"></span>
				</form>
				<div class="nbt-search-results"></div>
			</div>
		</div>
		<?php
	}
?>