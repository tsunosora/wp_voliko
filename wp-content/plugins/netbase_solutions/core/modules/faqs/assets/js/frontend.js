jQuery(document).ready(function($){
	var x = false;
	var nbtou_load = {
		/**
		 * Init jQuery.BlockUI
		 */
		block: function($el) {
			$el.block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		/**
		 * Remove jQuery.BlockUI
		 */
		unblock: function($el) {
			$el.unblock();
		}
	}
	var nbtfaq_js = {
		init: function(){
			$(document).on('click', '.nbt-faq-title', this.faq_triggle);
		},
		faq_triggle: function(e){
		    e.preventDefault();

		    if($(this).hasClass('active')){
		    	$(this).removeClass('active');
		    	$(this).closest('li').find('.nbt-faq-content').slideUp();

		    }else{
		    	$(this).addClass('active');
		    	$(this).closest('li').find('.nbt-faq-content').slideDown();
		    }
		}
	}
	
	nbtfaq_js.init();
});	



