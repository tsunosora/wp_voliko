/* ===============================================
  OPEN CLOSE Menu
============================================= */

function printing_press_open_menu() {
  jQuery('button.menu-toggle').addClass('close-panal');
  setTimeout(function(){
    jQuery('nav#main-menu').show();
  }, 100);

  return false;
}

jQuery( "button.menu-toggle").on("click", printing_press_open_menu);

function printing_press_close_menu() {
  jQuery('button.close-menu').removeClass('close-panal');
  jQuery('nav#main-menu').hide();
}

jQuery( "button.close-menu").on("click", printing_press_close_menu);

/* ===============================================
  TRAP TAB FOCUS ON MODAL MENU
============================================= */

  jQuery('button.close-menu').on('keydown', function (e) {
    if (jQuery("this:focus") && (e.which === 9)) {
      e.preventDefault();
      jQuery(this).blur();
      jQuery('button.menu-toggle').focus();
    }
  });
  jQuery(document).ready(function() {
    window.addEventListener('load', (event) => {
        jQuery(".loader").delay(2000).fadeOut("slow");
      });
  })

    /* ===============================================
      Scroll Top //
    ============================================= */
    
    jQuery(window).scroll(function () {
      if (jQuery(this).scrollTop() > 100) {
          jQuery('.scroll-up').fadeIn();
      } else {
          jQuery('.scroll-up').fadeOut();
      }
    });
    
    jQuery('a[href="#tobottom"]').click(function () {
      jQuery('html, body').animate({scrollTop: 0}, 'slow');
      return false;
    });
    (function( $ ) {
      $(window).scroll(function(){
          var sticky = $('.sticky-header'),
          scroll = $(window).scrollTop();
    
          if (scroll >= 100) sticky.addClass('fixed-header');
          else sticky.removeClass('fixed-header');
        });
      })( jQuery );
    (function( $ ) {
    
    $(window).scroll(function(){
        var sticky = $('.sticky-header'),
        scroll = $(window).scrollTop();
    
        if (scroll >= 100) sticky.addClass('fixed-header');
        else sticky.removeClass('fixed-header');
      });
    })( jQuery );
    