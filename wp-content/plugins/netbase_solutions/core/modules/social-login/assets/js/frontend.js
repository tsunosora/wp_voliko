jQuery(document).ready( function($){
	$('.show-nbtsl-container').on('click', function(e){
        e.preventDefault();
        $('.nbtsl-container').slideToggle();
    });
});