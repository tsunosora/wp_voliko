jQuery( function( $ ) {

	/**
	 * Variations Price Matrix actions
	 */
	var nbtou_admin = {

		/**
		 * Initialize variations actions
		 */
		init: function() {
			$(document).on('click', '.nbtou-uploaded-delete', this.remove_files);
			$(document).on('click', '#wpf_umf_uploaded_file_submit', this.submit_files);
			$(document).on('click', '.media-modal-close', this.close_dialog);



			
		},
		close_dialog: function(){
			$('.overlay-gallery').hide();
		},
		submit_files: function(){
			var ans = $('[name="wpf_umf_uploaded_file_approve"]').val();

			$.ajax({
				url: nbtou.ajax_url,
				data: {
					action:     'submit_files',
					order_id: $('[name="wpf_umf_uploaded_file_approve"]').attr('data-id'),
					ans: ans
				},
				type: 'POST',
				datatype: 'json',
				success: function( response ) {
					var rs = JSON.parse(response);
					
					if(rs.complete != undefined){
						
					}
					pm_load.unblock();

				},
				error:function(){
					alert('There was an error when processing data, please try again !');
				}
			});

			return false;
		},
		remove_files: function(){
			var order_id = $(this).attr('data-orderid');
			var file_number = $(this).attr('data-filenumber');
			var product_id = $(this).attr('data-product-id');

			$.ajax({
				url: nbtou.ajax_url,
				data: {
					action:     'nbtou_remove_files',
					order_id:   order_id,
					file_number: file_number,
					product_id: product_id
				},
				type: 'POST',
				datatype: 'json',
				success: function( response ) {
					var rs = JSON.parse(response);
					if(rs.complete != undefined){
						$('#file-' + rs.order_id).remove();
					}

					nbtcs_ajax.unblock();
				},
				error:function(){
					alert('There was an error when processing data, please try again !');
					nbtcs_ajax.unblock();
				}
			});

			return false;
		}
	}

	
	nbtou_admin.init();

});