(function($) {
'use strict';
	var woopanel_dokan_review = {
		xhr_view: null,
		/**
		 * Initialize variations actions
		 */
		init: function() {
        	$(document).on('click', '.dokan-review-btn', this.writeReview);
			$(document).on('submit', '#frmDokanReview', this.submitReview);
			
			
		},
		
		dokanRating: function() {
			if( jQuery().starRating ) {
				$(".dokan-star-rating").starRating({
					initialRating: 0,
					strokeColor: '#894A00',
					strokeWidth: 10,
					starSize: 20,
					callback: function(currentRating, $el){
						$el.next().val(currentRating);
					}
				});
			}
		},
		
		writeReview: function(e) {
			e.preventDefault();
			
			var template = $('#tmpl-woopanel-popup-dokanreview').html();

			$('body').append(template);
			$.magnificPopup.open({
				items: {
					src: '#dokan-review-popup'
				},
				type: 'inline',
				midClick: true,
				mainClass: 'mfp-fade',
				callbacks: {
					open: function(){
						var $current_window = $(window).width() - 50;
						var $width_table = $('.price-matrix-table').width() + 60;

						if($width_table > 500 && $current_window > $width_table){
							$('#dokan-review-popup').css({
								"maxWidth": $width_table
							});
						}
						
						woopanel_dokan_review.dokanRating();
					}
				}
			});
		},
		
		submitReview: function(e) {
			e.preventDefault();
			
			var $this = $(this);
			
            if( this.xhr_view && this.xhr_view.readyState != 4 ){ this.xhr_view.abort(); }
            
			$this.find('.spinner').css("visibility", "visible");
			$this.find('button[type="submit"]').prop('disabled', true);
			
			$('#frmDokanReview .wpl-notice').remove();	
			this.xhr_view = jQuery.ajax({
				url:     wplModules.ajax_url,
				data:    {
					action  : 'woopanel_dokan_review',
					data	: $this.serialize(),
					security: wplModules.dokan_review_nonce
				},
				type:    'POST',
				success: function( response ) {
					$this.find('button[type="submit"]').prop('disabled', false);
					$this.find('.spinner').css("visibility", "hidden");
					
					if ( response.complete != undefined ) {
						$('#frmDokanReview').append(response.message);
						$('#frmDokanReview input, #frmDokanReview textarea').val('');
						setTimeout(function() {
							$.magnificPopup.close();
						}, 3000);
					}else {
						$('#frmDokanReview').append(response.error);
					}
				},
				error: function() {
					$this.find('.spinner').css("visibility", "hidden");
				}
			});
			
		}
	}

	woopanel_dokan_review.init();
})(jQuery);