jQuery( function( $ ) {
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

	

			this.check_enable_order_upload();
		},
		close_dialog: function(){
			$('.overlay-gallery').hide();
		},
		check_enable_order_upload: function(){
			var $cs = $('#_order_upload');
			if($cs.closest('label').hasClass('on')){
				$cs.prop('checked', true);
				$('.order_upload_options').removeClass('hide');
			}else{
				$cs.prop('checked', false);
				$('.order_upload_options').addClass('hide');

			}
		},
		submit_files: function(){
			var inside = $(this).closest('.inside');
			nbtou_load.block(inside);

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
						location.reload();
					}

				},
				error:function(){
					alert('There was an error when processing data, please try again !');
					nbtou_load.unblock(inside);
				}
			});

			return false;
		},
		remove_files: function(){
			var inside = $(this).closest('.inside');
			nbtou_load.block(inside);

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

					nbtou_load.unblock(inside);
				},
				error:function(){
					alert('There was an error when processing data, please try again !');
					nbtou_load.unblock(inside);
				}
			});

			return false;
		}
	}

	
	nbtou_admin.init();

});