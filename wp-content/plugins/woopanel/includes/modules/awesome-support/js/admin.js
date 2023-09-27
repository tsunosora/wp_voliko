(function($) {
'use strict';
  $(document).on('click', '.reply_submit', function(e) {
    e.preventDefault();



    var $this = $(this),
      wrapper = $this.closest('.reply-ticket-body');

    jQuery.ajax({
      url: WooPanel.ajaxurl,
      data: 'action=woopanel_awesome_reply_ticket&content=' + tinymce.get( 'content_reply' ).getContent( { format: 'text' } ) + '&id=' + $('[name="ticket_ID"]').val(),
      type: 'POST',
      datatype: 'json',
      success: function( response ) {
        $this.removeClass('m-loader');
        if( response.complete != undefined ) {
          location.reload();
        }else {
          alert(response.message);
        }
      },
      error:function( xhr, status, error ) {

        if( xhr.status == 403) {
          alert( WooPanel.label.i18n_deny);
        }else {
          alert('There was an error when processing data, please try again !');
        }
      }
    });

  });
})(jQuery);