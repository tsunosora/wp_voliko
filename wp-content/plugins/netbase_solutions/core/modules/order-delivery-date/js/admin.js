jQuery( function( $ ) {	
	
	var nbtodd_admin = {
		/**
		 * Initialize variations actions
		 */
		init: function() {
			$(document).on('click', '.pm-icon.-minus', this.remove_row);
			$(document).on('click', '.pm-icon.-plus', this.add_row);
			
		},	
		
		add_row: function(){
			var $row = $('#table-mcs tbody > tr:first-child').html();
			

			var $tr = $( this ).closest('.pm-row');
			var $count = $('#table-mcs tbody > tr').length;
			var $option = $('#table-mcs tbody > tr:first-child').find('select.pm-attributes-field option').length - 1;
			
			//if($count < $option){
				$tr.after('<tr class="pm-row" id="pm-row-' +  $count + '" data-id="' + $count + '">' + $row + '</tr>');

				/*$('#pm-row-' +  $count+ ' .select2-container').remove();*/				

				$( "#table-mcs tbody > tr" ).each(function(index) {
					$(this).find('.order span').text(index + 1);

				});
			//}
			return false;
		},
		remove_row: function(){
			var $count = $('.nbtodd_pm_repeater tbody > tr').length;

			if($count > 1){
				$(this).closest('.pm-row').remove();
			}else{
				alert('Sorry, you can\'t remove this row!');
			}
			return false;
		},
		
		
		
	}

	
	nbtodd_admin.init();

});