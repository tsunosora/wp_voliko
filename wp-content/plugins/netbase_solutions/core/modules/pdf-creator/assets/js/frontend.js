
jQuery(document).ready(function($){
	var x = false;

	var nbtpdf_js = {
		init: function(){
			$(document).on('click', '.btn-pdf-preview', this.save_id);
			$(document).on('click', '.btn-print-pdf', this.download_file);
		},
		save_id: function(){
			if($(this).hasClass('active')){
				$(this).next().slideUp();
				$(this).removeClass('active');
			}else{
				$(this).next().slideDown();
				$(this).addClass('active');
			}
		},
		
		download_file: function() {
			$('.preview').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});

			$.ajax({
				url: admin_ajax,
				data: {
					action:     'nbtpdf_download',
					order_id: order_id,
					_wpnonce: nonce
				},
				type: 'POST',
				datatype: 'json',
				success: function( response ) {
					$('.preview').unblock();
					if(response.complete != undefined ) {
						window.location.href = response.redirect;
					}
				},
				error:function(){
					$('.preview').unblock();
				}
			});

			return false;
		}
	}
	
	nbtpdf_js.init();
});