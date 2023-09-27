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
	var nbtou_js = {
		init: function(){


			$(document).on('dragover', '.nbt-upload-zone', this.dragover_files);
			$(document).on('dragleave', '.nbt-upload-zone', this.dragleave_files);
			$(document).on('drop', '.nbt-upload-zone', this.drop_files);
			$(document).on('click', '.nbt-oupload-target', this.click_files);
			$(document).on('change', '.nbt-upload-input', this.change_files);
			$(document).on('click', '.nbt-icon-cancel', this.remove_files);
			$(document).on('click', '.toggle-order-upload', this.toggle_order_upload);
			$(document).on('click', '.nbt-ou-fast button', this.show_order_upload);

			if( $('#nbt-order-upload').length > 0 ) {
				var useAjax = false === $('form.variations_form').data( 'product_variations' );

				if( useAjax ) {
					$(document).ajaxComplete(function(event, xhr, options) {
						if( typeof(options.url) == "string" && options.url.includes("get_variation")) { 
							if( xhr.status == 200) {
								nbtou_js.change_variations_ajax();
							}
						}
					});
				}else {
					$(document).on('change', '.variations select', this.change_variations);
				}
			}

			if( $('body').hasClass('single-product') && $('form.variations_form').length > 0 ) {
				nbtou_js.change_variations();
			}

			

			if( $('.nbt-ou-fast').length ) {
				$('#nbt-orderupload-popup').remove();
				$('body').append('<div id="nbt-orderupload-popup" class="white-popup mfp-hide"><h2>' + nbt_solutions.nbt_ou_label + '</h2><div class="nbt-orderupload-popup-wrapper"></div></div>');
			}
			

			this.require_upload();
		},


		file_extension: function( file ) {
			return file.split('.').pop();
		},

		change_variations_ajax: function() {
			var currentVariations = '';
			/* Get current variations selected */
			$( 'form.variations_form .variations select' ).each( function() {
				var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
				var value          = $( this ).val() || '';

				currentVariations += attribute_name + value;
			});

			$('#nb-orderupload-wrapper').show();
			nbtou_load.block( $('form.variations_form') );
			$.ajax({
				url: nbt_solutions.ajax_url,
				data: {
					action:     'nbt_ou_variations_show',
					variation_id: md5(currentVariations)
				},
				type: 'POST',
				datatype: 'json',
				success: function( response ) {
					nbtou_load.unblock( $('form.variations_form') );
					$('.nbt-oupload-body').html('');
					if(response.complete != undefined && response.tpl != ''){
						$('.nbt-oupload-body').html(response.tpl);

						if( $('.nbt-oupload-body .nbt-file').length > 3) {
							$('.nbt-oupload-output .nbt-oupload-body').slimScroll({
								height: '184px'
							});
						}
					}else {
						$('.nbt-oupload-body').removeAttr('style');
						$(".nbt-oupload-output .nbt-oupload-body").slimScroll({destroy: true});
					}
				},
				error:function(){
					nbtou_load.unblock( $('form.variations_form') );
					//alert('There was an error when processing data, please try again !');
				}
			});
		},

		change_variations: function() {
			var count = $('.variations select').length;
			var selected = 0;
			$('.variations select').each(function( index ) {
				if( $(this).val() ) {
					selected += 1;
				}
			});

			if( count == selected ) {
				$('#nb-orderupload-wrapper').show();

				nbtou_load.block( $('form.variations_form') );
				var $form = $('form.variations_form'),
					variationData = $form.data( 'product_variations' ),
					attributeFields = $form.find( '.variations select' ),
					currentVariations   = '';

				/* Get current variations selected */
				attributeFields.each( function() {
					var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
					var value          = $( this ).val() || '';
					currentVariations += attribute_name + value;
				});

				$.ajax({
					url: nbt_solutions.ajax_url,
					data: {
						action:     'nbt_ou_variations_show',
						variation_id: md5(currentVariations)
					},
					type: 'POST',
					datatype: 'json',
					success: function( response ) {
						nbtou_load.unblock( $('form.variations_form') );
						$('.nbt-oupload-body').html('');
						if(response.complete != undefined && response.tpl != ''){
							$('.nbt-oupload-body').html(response.tpl);

							if( $('.nbt-oupload-body .nbt-file').length > 3) {
								$('.nbt-oupload-output .nbt-oupload-body').slimScroll({
									height: '184px'
								});
							}else {
								nbtou_js.destroy_upload();
							}
						}else {
							nbtou_js.destroy_upload();
						}
					},
					error:function(){
						nbtou_load.unblock( $('form.variations_form') );
						//alert('There was an error when processing data, please try again !');
					}
				});
			}else {
				if( nbtou.require_variation ) {
					$('#nb-orderupload-wrapper').hide();
				}
			}
		},

		destroy_upload: function() {
			$('.nbt-oupload-body').removeAttr('style');
			$(".nbt-oupload-output .nbt-oupload-body").slimScroll({destroy: true});
		},

			
		toggle_order_upload: function() {
			var $div = $(this).closest('.nbt-show-files');
			
			if( $(this).hasClass('active') ) {
				$div.find('ul').slideUp();
				$(this).removeClass('active');
			}else {
				$div.find('ul').slideDown();
				$(this).addClass('active');
			}
		},

		show_order_upload: function(e) {
			e.preventDefault();
			
			nbtou_load.block( $('.shop_table') );
			
			var product_fast = $(this).closest('.nbt-ou-fast');
			var product_id = product_fast.attr('id').replace("nbt-upload-cart-", "");
			
			$.ajax({
				url: nbt_solutions.ajax_url,
				data: {
					action:     'nbt_ou_show',
					product_id: product_id
				},
				type: 'POST',
				datatype: 'json',
				success: function( response ) {
					
					if(response.complete != undefined){
						$('#nbt-orderupload-popup .nbt-orderupload-popup-wrapper').html(response.tpl);
					}
				},
				complete: function() {
					nbtou_load.unblock( $('.shop_table') );
					
					$.magnificPopup.open({
						items: {
							src: '#nbt-orderupload-popup'
						},
						type: 'inline',
						midClick: true,
						mainClass: 'mfp-fade',
						closeOnBgClick: false,
						callbacks: {
							open: function(){
								var $current_window = $(window).width() - 50;
								var $width_table = $('.price-matrix-table').width() + 60;

								if($width_table > 500 && $current_window > $width_table){
									$('#price-matrix-popup').css({
										"maxWidth": $width_table
									});
								}
								
								
							}
						 }
					});
					
				},
				error:function(){
					alert('There was an error when processing data, please try again !');
				}
			});
						

		},
		
		require_upload: function() {
			if(nbtou.require_upload && $('body').hasClass('has-order-upload') ){
				var $file = $('.nbt-oupload-body .nbt-file').length;
				var $file_success = $('.nbt-oupload-body .nbt-file.success').length;

				if( $file == $file_success && $file != 0  ) {
					$('.single_add_to_cart_button').prop('disabled', false);
				}else {
					$('.single_add_to_cart_button').prop('disabled', true);
				}
			}
		},

		dragover_files: function(e){
		    e.preventDefault();
		    e.stopPropagation();
		    $(this).addClass('dragover');
		},

		dragleave_files: function(e){
		    e.preventDefault();
		    e.stopPropagation();
		    $(this).removeClass('dragover');
		},

		drop_files: function(e){
		    e.preventDefault();
		    e.stopPropagation();
		    $(this).removeClass('dragover');

		    nbtou_js.triggerCallback(e);
		},

		binarySearch: function(items, value){
			return items.indexOf(value);
		},
		triggerCallback: function(e){
			var show_error = false;
			var $product_id = $('[name="add-to-cart"]').val();
			var files;
			if(e.originalEvent.dataTransfer) {
				files = e.originalEvent.dataTransfer.files;
			} else if(e.target) {
				files = e.target.files;
			}
			
			
			var fail_filename = '';
			$.each(files, function(key, file){
				var extension = file.name.replace(/^.*\./, '').toLowerCase();

				if( nbtou_js.binarySearch( nbtou.file_extension, extension ) < 0 ) {
					fail_filename += file.name.toLowerCase() + ', ';
					files = jQuery.grep(files, function(k, value) {
						return value != key;
					});
				}
			});
			
			var number_files = files.length;
			if(number_files > nbtou.file_of_number) {
				var file_of_number = nbtou.label_numfiles.replace("%s", nbtou.file_of_number);
				alert( file_of_number );
				show_error = true;
			}
			
			if( fail_filename && ! show_error) {
				var label_restrict = nbtou.label_restrict.replace("%s", fail_filename.slice(', ', -2));
			}
			
			if(! show_error) {
				if(typeof nbt_solutions !== 'undefined' && nbt_solutions.customer_id != undefined){
					customer_id = nbt_solutions.customer_id;
				}else{
					customer_id = nbtou.customer_id;
				}

				var data = new FormData(),
					timestamp = new Date().getUTCMilliseconds(),
					currentVariations   = '',
					$html = '';

				for(var i=0; i<files.length; i++) {
					var attr_id = md5(timestamp + i);
					var fileext = nbtou_js.file_extension(files[i].name);

					data.append("nbt_id[]", attr_id);
					
					$html += '<div id="' + md5(customer_id + files[i].name) + '" attr-id="' + attr_id + '" class="nbt-file"><div class="nbt-file-left">';
					if( files[i].type.indexOf('image/') === 0 && fileext != 'svg' ) {
						$html += '<img width="50" src="' + URL.createObjectURL(files[i]) + '" />';
					}else {
						var img_src = nbtou.file_extension_src['default'];
						if( typeof nbtou.file_extension_src[fileext] != 'undefined' ) {
							img_src = nbtou.file_extension_src[fileext];
						}
						
						$html += '<img width="50" src="' + img_src + '" />';
					}

					
	
					$html += '</div><div class="nbt-file-right">';
					$html += '<div class="name">' + files[i].name + ' <i class="nbt-icon-cancel"></i></div><div class="size"> ' + nbtou_js.calcSize(files[i].size) + '</div><div class="nbt-ou-msg" style="display: none;"></div></div>';
					$html += '</div>';
				}
				$('.nbt-oupload-body').append($html);
				if( $('.nbt-oupload-output .nbt-oupload-body .nbt-file').length > 3 ) {
					$('.nbt-oupload-output .nbt-oupload-body').slimScroll({
						height: '184px'
					});
				}


				
				data.append("action", "nbt_order_upload");
				data.append("product_id", $product_id);





				$.each(files, function(key, value){
					data.append("nbt_files[]", value);
					
				});
				if(typeof nbt_solutions !== 'undefined' && nbt_solutions.ajax_url != undefined){
					ajax_url = nbt_solutions.ajax_url;
				}else{
					ajax_url = nbtou.ajax_url;
				}

				/* Get current variations selected */
				$( 'form.variations_form .variations select' ).each( function() {
					var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
					var value          = $( this ).val() || '';

					currentVariations += attribute_name + value;
				});

				if( $('.variation_id').length > 0 ) {
					data.append( "variation_id", md5(currentVariations) );
				}

				nbtou_load.block($('#nbt-order-upload'));
				$.ajax({
					url: ajax_url,
					type: 'POST',
					data: data,
					cache: false,
					dataType: 'json',
					processData: false, // Don't process the files
					contentType: false, // Set content type to false as jQuery will tell the server its a query string request
					xhr: function() {
						var xhr = new window.XMLHttpRequest();
						var started_at = new Date();

						xhr.upload.addEventListener("progress", function(evt){
						  if (evt.lengthComputable) {
							var loaded = evt.loaded;
							var total = evt.total;

						var seconds_elapsed =   ( new Date().getTime() - started_at.getTime() )/1000;
						var bytes_per_second =  seconds_elapsed ? loaded / seconds_elapsed : 0 ;
						var Kbytes_per_second = bytes_per_second / 1000 ;
						var remaining_bytes =   total - loaded;
						var seconds_remaining = seconds_elapsed ? remaining_bytes / bytes_per_second : 'calculating' ;
						jQuery( '.timeRemaining' ).html( '' );
						jQuery( '.timeRemaining' ).append( Math.round(seconds_remaining) );

						$('.progress').show();
							$('.progress').find('.progress-bar').css('width',Math.round((evt.loaded / evt.total) * 100) + "%");
						  }
						}, false);

						return xhr;
					},
					success: function(data, textStatus, jqXHR) {
						nbtou_load.unblock($('#nbt-order-upload'));

						if(data.msg != undefined) {
							alert(data.msg);
							return;
						}
						
						if( typeof data.response != 'undefined' ) {
							
							$.each(data.response, function( index, value ) {
								if( value.complete != undefined) {
									$('.progress').hide();
									$('[attr-id="' + index + '"]').addClass('success');
									$('[attr-id="' + index + '"]').attr('id', value.file_id);
								}else {
									if( $('#' + index ).length > 0 ) {
										$('#' + index + ' .nbt-ou-msg').show();
										$('#' + index).addClass('error');
										$('#' + index + ' .nbt-ou-msg').html(value);
									}else {
										$('.nbt-file[attr-id="' + index + '"] .nbt-ou-msg').show();
										$('.nbt-file[attr-id="' + index + '"]').addClass('error');
										$('.nbt-file[attr-id="' + index + '"] .nbt-ou-msg').html(value);
									}
								}

							});
						}



						nbtou_js.require_upload();
					}
				});
			}

		},

		click_files: function(){

			var isUpload = true;
			if( nbtou.file_of_number != undefined && $('.nbt-oupload-output .nbt-oupload-body .nbt-file').length >= nbtou.file_of_number) {
				var isUpload = false;
				var file_of_number = nbtou.label_numfiles.replace("%s", nbtou.file_of_number);
				alert( file_of_number );
			}

			if( nbtou.require_variation && $('.single_add_to_cart_button.disabled').length > 0 ) {
				var isUpload = false;
				alert(nbtou.label_variation);
			}

			if( isUpload ) {
				$('.nbt-upload-input').val('');
				$('.nbt-upload-input').trigger('click');
			}
		},

		change_files: function(e){
			nbtou_js.triggerCallback(e);
			nbtou_js.require_upload();
		},

		calcSize: function(nBytes) {
			if (nBytes == 0) {
				return {size: '', label: ''}
			}
			for (var aMultiples = ["Kb", "Mb", "Gb", "Tb", "Pb", "Eb", "Zb", "Yb"], nMultiple = 0, nApprox = nBytes / 1024; nApprox > 1000; nApprox /= 1000, nMultiple++) {
				//sOutput = nApprox.toFixed(3) + " " + aMultiples[nMultiple] + " (" + nBytes + " bytes)";
			}
			return nApprox.toFixed(2) + ' ' + aMultiples[nMultiple];
			
		},

		remove_files: function(){
			var $li = $(this).closest('.nbt-file'),
				$id = $li.attr('id'),
				currentVariations = '';
			nbtou_load.block($('#nbt-order-upload'));

			if(typeof nbt_solutions !== 'undefined' && nbt_solutions.ajax_url != undefined){
				ajax_url = nbt_solutions.ajax_url;
			}else{
				ajax_url = nbtou.ajax_url;
			}
			
			var isValid = true;
			if ( $li.hasClass("error") ) {
				var $id = $li.attr('attr-id');
				isValid = false;
			}

			/* Get current variations selected */
			$( 'form.variations_form .variations select' ).each( function() {
				var attribute_name = $( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );
				var value          = $( this ).val() || '';

				currentVariations += attribute_name + value;
			});

			

			$.ajax({
				url: ajax_url,
				data: {
					action:     'nbt_ou_remove',
					product_id: $('[name="add-to-cart"]').val(),
					variation_id: currentVariations != '' ? md5(currentVariations) : '',
					file : $id,
					valid: isValid
				},
				type: 'POST',
				datatype: 'json',
				success: function( rs ) {
					nbtou_load.unblock($('#nbt-order-upload'));
					if ( rs.complete != undefined ) {
						$('#' + rs.file_id).remove();
						nbtou_js.require_upload();
					}else {
						if( isValid ) {
							$('#' + rs.file_id).remove();
						}else {
							$('.nbt-file[attr-id="' + rs.file_id + '"]').remove();
						}
					}

					if( $('.nbt-oupload-body .nbt-file').length < 3) {
						$('.nbt-oupload-body').removeAttr('style');
						$(".nbt-oupload-output .nbt-oupload-body").slimScroll({destroy: true});
					}else {
						$('.nbt-oupload-output .nbt-oupload-body').slimScroll({
							height: '184px'
						});
					}
				},
				error:function(){
					alert('There was an error when processing data, please try again !');
					nbtou_load.unblock($('#nbt-order-upload'));
				}
			});

			return false;
		}
	}

	nbtou_js.init();

	var picker 			= 'file',
		oauthToken,
		driveclientId 	= nbtou.g_drive_clientid,
		driveapiKey 	= nbtou.g_drive_apikey,
		dropboxApi 		= nbtou.dropbox_apikey,
		boxApi 			= nbtou.box_apikey,
		service_order 	= ['drive','upload'],
		service_list 	= {'drive': 1 , 'upload': 1},		
		origin 			= window.location.protocol + '//' + window.location.host;

	var ol_services_js = {

		init: function() {
			this.support();
			this.bind_functions();
		},

		bind_functions: function() {
			$(document).on( 'click', '#add-g-drive', this.g_drive_handler );
			$(document).on('click', '#add-button-no-api', this.no_api_handle);
			$(document).on( 'click', '#add-dropbox', this.dropbox_handler );
			$(document).on( 'click', '#add-box', this.box_handler );
			$(document).on('click', '.tablinks', function(event) {
				event.preventDefault();

				$('.ou-tabcontent > div').css('display', 'none');
				$('.ou-tab > button').removeClass('activated');
				$(this).addClass('activated');

				var tab_rel = $(this).attr('tab-rel');
				$('.ou-tabcontent ' + '#' + tab_rel).fadeIn('slow');
			});
		},

		support: function() {
			if( driveapiKey && driveclientId){
                $("head").append("<script type='text/javascript' src='https://apis.google.com/js/api.js'></script>");    
            }
            if(dropboxApi){
                $("head").append("<script type='text/javascript' src='https://www.dropbox.com/static/api/2/dropins.js'></script>");       
            }
            if(boxApi){
                $("head").append("<script type='text/javascript' src='https://app.box.com/js/static/select.js'></script>");       
            }
		},

		g_drive_handler: function() {
			picker = 'file';
			ol_services_js.load_gapi();
		},

		load_gapi: function() {
			if( ! oauthToken ) {
				gapi.load( 'auth', {
					'callback': ol_services_js.onAuthApiLoad
				});
				gapi.load( 'picker', 1 );
			}
			else {
				ol_services_js.open_file_picker();
			}
		},

		onAuthApiLoad: function() {
			window.gapi.auth.authorize({
				'client_id': driveclientId,
			  	'scope': ['https://www.googleapis.com/auth/drive'],
			}, ol_services_js.callback_auth_result);
		},

		open_file_picker: function() {
			var picker = new google.picker.PickerBuilder()
				.setOrigin(origin)
				.setOAuthToken(oauthToken)
				// .setDeveloperKey(driveapiKey)
				.setCallback(ol_services_js.picker_callback);
			picker.addView(new google.picker.DocsView().setIncludeFolders(true));
			picker.addView(new google.picker.DocsUploadView().setIncludeFolders(true));
			picker.build().setVisible(true);
		},

		callback_auth_result: function(authResult) {
			if (authResult && !authResult.error) {
				oauthToken = authResult.access_token;
				ol_services_js.open_file_picker();
			}
		},

		picker_callback: function(data) {
			if(data[google.picker.Response.ACTION] == google.picker.Action.PICKED) {
				driverDocs = data[google.picker.Response.DOCUMENTS];

				var driver_url 	= driverDocs[0].url;
				var driver_name = driverDocs[0].name;
				var driver_id 	= driverDocs[0].id;

				ol_services_js.process_file(driver_url, driver_name, driver_id);
				
			}
		},

		process_file: function(os_url, os_name, os_id) {

			if(typeof nbt_solutions !== 'undefined' && nbt_solutions.ajax_url != undefined){
				ajax_url = nbt_solutions.ajax_url;
			}else{
				ajax_url = nbtou.ajax_url;
			}
			var product_id = $('[name="add-to-cart"]').val();
			var variation_id = 0;
			if( $('.variation_id').length > 0 ) {
				var variation_id = $('.variation_id').val();
			}

			//ajax insert attached file to post table
			$.ajax({
				url: ajax_url,
				type: 'POST',
				dataType: 'json',
				data: {
					action 		:'nbt_order_upload',
					os_url 		: os_url,
					os_name		: os_name,
					os_id 		: os_id,
					product_id: product_id,
					variation_id: variation_id,
					attached_from_outsoure: 1
				},
				beforeSend: function() {
					nbtou_load.block($('#from_online_services'));
				},
				success: function( response ) {
					nbtou_load.unblock($('#from_online_services'));
					var $html = '';
					
					$html += '<div id="' + os_id + '" class="nbt-file success">';
					$html += '<div class="nbt-file-left file-icon"><i class="fa fa-file-o fa-3x" aria-hidden="true"></i></div>';
					$html += '<div class="nbt-file-right">';
					$html += '<div class="name">' + os_name + ' <i class="nbt-icon-cancel"></i></div><div class="nbt-ou-msg" style="display: none;"></div></div>';
					$html += '</div>';

					$('.nbt-oupload-body').append($html);
					nbtou_js.require_upload();

				},
				error:function(){
					alert('There was an error when processing data, please try again !');
					nbtou_load.unblock($('#nbt-order-upload'));
				}
			})
		},

		no_api_handle: function() {
			tb_show("NO API", "#TB_inline?inlineId=g-drive-popup-wrap", null);
			ol_services_js.popup_position();
			return false;
		},

		dropbox_handler: function(event) {
			event.preventDefault();			
			Dropbox.init({
			    appKey: dropboxApi
			});
			Dropbox.choose({
			    linkType: "preview",
			    multiselect: false, // or true
			    success: function(files) {
			        var dbFile = files[0];
			        var dbURL = dbFile.link.replace("?dl=0", "");
			        var dbFileName = dbFile.name;
			        var dbFileID = dbFile.id;

			        ol_services_js.process_file(dbURL, dbFileName, dbFileID);
			    }
			});
		},

		box_handler: function() {
		    var boxoptions = {
		        clientId: boxApi,
		        linkType: 'shared',
		        multiselect: false
		    };
		    var boxSelect = new BoxSelect(boxoptions);
		    boxSelect.launchPopup();
		    boxSelect.success(function(response) {
		        
		        var boxFile = response[0];

		        var boxURL 		= boxFile.url;
		        var boxFileName = boxFile.name;
		        var boxFileID 	= boxFile.id;

		        ol_services_js.process_file(boxURL, boxFileName, boxFileID);
		        
		    });
		},

		popup_position: function() {
			var tbWindow = $('#TB_window');
			var width = $(window).width();
			var H = $(window).height();
			var W = (1080 < width) ? 1080 : width;
			if (tbWindow.size()) {
			    tbWindow.width(W - 50).height(H - 45);
			    $('#TB_ajaxContent').css({ 'width': '100%', 'height': '100%', 'padding': '0' });
			    tbWindow.css({ 'margin-left': '-' + parseInt(((W - 50) / 2), 10) + 'px' });
			    if (typeof document.body.style.maxWidth != 'undefined')
			        tbWindow.css({ 'top': '20px', 'margin-top': '0' });
			    $('#TB_title').css({ 'background-color': '#fff', 'color': '#cfcfcf' });
			};
		}
	}
	ol_services_js.init();
});