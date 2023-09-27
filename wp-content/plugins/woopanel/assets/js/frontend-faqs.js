(function($) {
'use strict';
	var nbtfaq_js = {
		init: function(){
			jQuery(document).on('click', '.nb-faq-title', this.faq_triggle);
		},
		faq_triggle: function(e){
		    e.preventDefault();

		    if(jQuery(this).hasClass('active')){
		    	jQuery(this).removeClass('active');
		    	jQuery(this).closest('li').find('.nb-faq-content').slideUp();

		    }else{
		    	jQuery(this).addClass('active');
		    	jQuery(this).closest('li').find('.nb-faq-content').slideDown();
		    }
		}
	}
	
	nbtfaq_js.init();
})(jQuery);


