( function ( $ ) {
    'use strict';
    $( document ).ready( function () {
        $(document).on('submit','form#search-store',function(event){
            var $this = $(this);
            event.preventDefault();
            $('.dokan-seller-wrap').html('<span class="loading-ajax"></span>');
            jQuery.ajax({
                url : dokan_ajax.url,
                type : 'post',
                dataType: 'json',
                data : {
                    action  : 'search_store',
                    input   : $this.serialize()
                },
                error : function(){
                    $('.dokan-seller-wrap').html('Opps!');
                },
                success : function( response ) {
                    if(response.complete != undefined ){
                        $('.dokan-seller-wrap').html(response.html);
                    }else{
                        $('.dokan-seller-wrap').html('<span class="empty-rs">' + response.html + '</span>');
                    }

                }
            });
        });




    })
} ( jQuery ) )