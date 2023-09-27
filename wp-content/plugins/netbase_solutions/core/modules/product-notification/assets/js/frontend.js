jQuery(document).ready(function($){
				$(document).on('click', '.pnotisubmit', function(){
					$('.nbt-alert-msg').hide();
					/*$('#nbt-alert').block({
						message: null,
						overlayCSS: {
							background: '#fff',
							opacity: 0.6
						}
					});*/
					$.ajax({
						url: nbt_solutions.ajax_url,
						data: {
							action:     'nbtpn_notification',
							product_id: $('#alert_id').val(),
							email: $('#alert_email').val(),
							
						},
						type: 'POST',
						datatype: 'json',
						success: function( response ) {
							var rs = JSON.parse(response);

							if(rs.complete != undefined){
								/*$('#nbt-alert').remove();*/
								$('.nbt-alert-msg').html(rs.msg).hide().slideToggle(500);
							}else{
								$('.nbt-alert-msg').html(rs.msg).hide().slideToggle(500);
							}
			
							/*$('#nbt-alert').unblock();*/

						},
						error:function(){
							alert('There was an error when processing data, please try again !');
							/*$('#nbt-alert').unblock();*/
						}
					});
					return false;
				});

				/*$(document).on('click', '.nbt-notifi-change', function(){
					$('#nbt_alerts_email').slideDown();

					return false;

				});*/

			});