jQuery( function( $ ) {
	var wp = window.wp;
	/**
	 * Variations Price Matrix actions
	 */
	var nbt_solutions_admin = {

		/**
		 * Initialize variations actions
		 */
		init: function() {
			this.call();
			$(document).on('click', '.nb-metabox-image-upload', this.upload_image);
			$(document).on( 'click', '.nb-metabox-remove-image', this.remove_upload_image);
			$(document).on('submit', '#frm-solution-settings', this.save_settings)
		},

		call: function() {
			if( jQuery().wpColorPicker ) {
				$('.nbt-colorpicker' ).wpColorPicker();
			}
		},
		
		upload_image: function(e) {
			e.preventDefault();
			
			var $button = $( this ).closest('.nb-metabox-image-wrapper');
			
			// Create the media frame.
			var frame = wp.media.frames.downloadable_file = wp.media( {
				title   : nbt_solutions.i18n.mediaTitle,
				button  : {
					text: nbt_solutions.i18n.mediaButton
				},
				multiple: false
			} );
			
			// When an image is selected, run a callback.
			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();

					$button.addClass('class_name' + attachment.id);
					$button.find( 'input.nb-metabox-image-input' ).val( attachment.id );
					$button.find( '.nb-metabox-image-remove' ).css('opacity', '1');
					$button.find( 'img' ).attr( 'src', attachment.url );
			} );

			// Finally, open the modal.
			frame.open();
		},

		remove_upload_image: function(){
			var $button = $( this );

			$button.siblings( 'input.nb-metabox-term-image' ).val( '' );
			$button.siblings( '.nb-metabox-remove-image' ).show();
			$button.parent().prev( '.nb-metabox-term-image-thumbnail' ).find( 'img' ).attr( 'src', nbt_solutions.placeholder );

			return false;
		},
		
		save_settings: function(event) {
			event.preventDefault();
			
			$.ajax({
				url: nbt_solutions.save_settings,
				data: $(this).serialize(),
				type: 'POST',
				datatype: 'json',
				success: function( response ) {
					alert(response.message);
				},
				error:function(){
					alert('There was an error when processing data, please try again !');
				}
			});

		}
	}
	
	nbt_solutions_admin.init();

});