jQuery( function( $ ) {


	var nbt_ajax_search_load = {
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
	var nbt_ajax_search = {

		/**
		 * Initialize variations actions
		 */
		init: function() {
			$(document).on('click', '.nas-icon-click', this.openSearchPopup);
			$(document).on('click', '.nas-overlay-close', this.closeSearchPopup);
			
			
        	$(window).load(function(){
				//setup before functions
				var typingTimer;                //timer identifier
				var doneTypingInterval = 1000;  //time in ms, 5 second for example
				var $input = $('.nas-field');

				//on keyup, start the countdown
				$input.on('keyup', function () {
					var $this = $(this).closest('.nas-wrapper'),
						$val = $(this).val();

					$this.addClass('nas-searching');
					$this.removeClass('nas-active');
					$this.find('.nas-results').empty();
					if( $val.length > 2 ) {
						clearTimeout(typingTimer);
						typingTimer = setTimeout(doneTyping.bind(null, $this), doneTypingInterval);
					}else {
						$this.removeClass('nas-searching');
					}

				});

				//on keydown, clear the countdown 
				$input.on('keydown', function () {
					clearTimeout(typingTimer);
				});

				//user is "finished typing," do something
				function doneTyping (el) {
					nbt_ajax_search.searchNow(typingTimer, el);
				}
        	});
 

			$(document).mouseup(function(e) 
			{
				if( $(".nas-layout-input .nas-active").length ){
				    var container = $('.nas-search-form');
				    if ( ! container.is(e.target) && container.has(e.target).length === 0 ) 
				    {
						$('.nas-search-form.nas-active .nas-results').empty();
						container.removeClass('nas-active');
				    }
				}
			});
		},
		searchNow: function(typingTimer, el) {
			var data = el.find('.nas-field').val();

			$.ajax({
				url: nbt_solutions.ajax_url,
				data: {
					action:     'nbt_search_now',
					search: data
				},
				type: 'POST',
				datatype: 'json',
				success: function( response ) {
					if( response.complete != undefined ) {
						el.find('.nas-results').html(response.result);
						el.find('.nas-results').mCustomScrollbar({
						    theme:"dark"
						});
					}else{
						
					}
					el.removeClass('nas-searching');
					el.addClass('nas-active');

					clearTimeout(typingTimer);
				},
				error:function(){
					clearTimeout(typingTimer);
					alert('There was an error when processing data, please try again !');
				}
			});
		},
		openSearchPopup: function(e) {
			e.preventDefault();
			var $this = $(this).closest('.nas-wrapper');
			$this.addClass('nas-overlay-active');
		},
		closeSearchPopup: function(e) {
			e.preventDefault();
			var $this = $(this).closest('.nas-wrapper');

			$this.removeClass('nas-overlay-active nas-active');
		}
	}


	nbt_ajax_search.init();
	
});


