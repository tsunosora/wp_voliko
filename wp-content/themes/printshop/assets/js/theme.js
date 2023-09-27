jQuery(document).ready(function() {
     "use strict";    
  jQuery('.gridlist-toggle').each(function(){
    jQuery(this).find('#grid').addClass('active');
    jQuery(this).find('#list').removeClass('active');
    jQuery('.site-main ul.products').addClass('grid');
    jQuery('.site-main ul.products').removeClass('list');
  });   
    
  jQuery('.wpnetbase_asl_container').each(function(){
  	jQuery('.header-search .fa-search').css('display','block');
  });
    jQuery('.wpnetbase-testimonials-carousel').each(function(){
		jQuery(".wpnetbase-testimonials-carousel").owlCarousel({
            autoplay: true,
            pagination: false,
			items:1
		});						
	});

    jQuery('#cat-drop-stack li.has-children a').click(function(e){
      e.preventDefault();
      if(jQuery(this).hasClass('active')){
        jQuery(this).removeClass('active');
        jQuery(this).closest('li').find('.sub-category').slideDown();
      }else{
        jQuery(this).addClass('active');
        jQuery(this).closest('li').find('.sub-category').slideUp();
      }
    });

  jQuery( ".header-search .fa-search" ).toggle(
    function() {
      jQuery(".header-search .wpnetbase_asl_container").addClass( "selected" );
    }, function() {
      jQuery(".header-search .wpnetbase_asl_container").removeClass( "selected" );
    }
  );  

  jQuery('.shortcodes-lst-products-cat.nbcarousel').each(function(){
    jQuery('.shortcodes-lst-products-cat.nbcarousel .products').addClass('owl-carousel');
    jQuery('.shortcodes-lst-products-cat.nbcarousel .products').owlCarousel({
      items:4,nav : true, margin: 20, dots: false,
      navText: ['<i class="fa fa-angle-left fa-2x"></i>', '<i class="fa fa-angle-right fa-2x"></i>'],
      responsive: {
        0: {
          items: 1,
        },
        480: {
          items: 2,
        },
        600: {
          items: 3,
        },
        992: {
          items: 4,

        },
      
      },
    });
  });

  jQuery('.shortcodes-lst-products-cat.catchild-carousel').each(function(){
    jQuery('.shortcodes-lst-products-cat.catchild-carousel .products').addClass('owl-carousel');
    jQuery('.shortcodes-lst-products-cat.catchild-carousel .products').owlCarousel({
      items:3,nav : true, margin: 10, dots: false,
      navText: ['<i class="fa fa-angle-left fa-2x"></i>', '<i class="fa fa-angle-right fa-2x"></i>'],
      responsive: {
        0: {
          items: 1,
        },
        480: {
          items: 2,
        },        
        992: {
          items: 3,

        },
      
      },
    });
  });
  	jQuery('.up-sells .products').addClass('owl-carousel');
  	jQuery('.up-sells .products').owlCarousel({
  		responsive: {
			0: {
				items: 1,
			},
			480: {
				items: 2,
			},
			600: {
				items: 3,
			},
			768: {
				items: 4,

			},
			
		},
  	});
	jQuery('.single-product .images .thumbnails').each(function(){
    	jQuery('.single-product .images .thumbnails').addClass('owl-carousel');
    	jQuery('.single-product .images .thumbnails').owlCarousel({
    		items:3,
    		responsive: {
							0: {
								items: 2,
							},
							480: {
								items: 2,
							},
							600: {
								items: 3,
							},
							768: {
								items: 3,

							},
							
						},
    	});

    });
    jQuery('#number-box .widget-title').each(function(){
            jQuery("#number-box .widget-title").lettering();
   });

    ( function() {
          var is_webkit = navigator.userAgent.toLowerCase().indexOf( 'webkit' ) > -1,
          is_opera  = navigator.userAgent.toLowerCase().indexOf( 'opera' )  > -1,
          is_ie     = navigator.userAgent.toLowerCase().indexOf( 'msie' )   > -1;
          if ( ( is_webkit || is_opera || is_ie ) && document.getElementById && window.addEventListener ) {
           window.addEventListener( 'hashchange', function() {
            var element = document.getElementById( location.hash.substring( 1 ) );

            if ( element ) {
             if ( ! /^(?:a|select|input|button|textarea)$/i.test( element.tagName ) )
              element.tabIndex = -1;

          element.focus();
      }
       }, false );
       }
   })();

    ( function() {
          jQuery('.site-content').fitVids();
      })();

    
     /**
      * Initialise transparent header
      */
      ( function() {

         var site_header_h = jQuery('.site-header').height();
         var logo_h = jQuery('.site-branding img').height();
         var nav_h = jQuery('.wpc-menu').height() - 30;
         var page_header = jQuery('.page-header-wrap');
         var page_header_pt = parseInt(page_header.css('padding-top'), 10);

         if ( jQuery('body').hasClass('header-transparent') && page_header.length ) {
             page_header.css('padding-top', page_header_pt + site_header_h + 'px');
         }

     })();

     /**
      * Parallax Section
      */
      
     
      ( function() {
         var isMobile = {
             Android: function() {
                 return navigator.userAgent.match(/Android/i);
             },
             BlackBerry: function() {
                 return navigator.userAgent.match(/BlackBerry/i);
             },
             iOS: function() {
                 return navigator.userAgent.match(/iPhone|iPad|iPod/i);
             },
             Opera: function() {
                 return navigator.userAgent.match(/Opera Mini/i);
             },
             Windows: function() {
                 return navigator.userAgent.match(/IEMobile/i);
             },
             any: function() {
                 return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
             }
         };

         var testMobile = isMobile.any();

         jQuery('.wpc_row_parallax').each(function() {
           var $this = jQuery(this);
           var bg    = $this.find('.wpc_parallax_bg');

           jQuery(bg).css('backgroundImage', 'url(' + $this.data('bg') + ')');

           if (testMobile == null) {
             jQuery(bg).addClass('not-mobile');
             jQuery(bg).removeClass('is-mobile');
             jQuery(bg).parallax('50%', 0.4);
         }
         else {
  
                 jQuery(bg).removeClass('not-mobile');
                 jQuery(bg).addClass('is-mobile');

             }

         });

     })();

     /**
      * Call magnificPopup when use
      */
      ( function() {

         jQuery('.gallery-lightbox').magnificPopup({
             delegate: '.gallery-item a',
             type:'image',
             gallery:{
                 enabled:true
             },
             zoom: {
                 enabled:true
             }
         });

         jQuery('.popup-video').magnificPopup({
             type: 'iframe',
             mainClass: 'mfp-fade',
             removalDelay: 160,
             preloader: false,
             fixedContentPos: false,
             zoom: {
                 enabled:true
             }
         });

     })();

     /**
      * Back To Top
      */
      ( function() {
         jQuery('#btt').fadeOut();
         jQuery(window).scroll(function() {
             if(jQuery(this).scrollTop() != 0) {
                 jQuery('#btt').fadeIn();    
             } else {
                 jQuery('#btt').fadeOut();
             }
         });

         jQuery('#btt').click(function() {
             jQuery('body,html').animate({scrollTop:0},800);
         });
     })();

     /**
      * Fixed Header + Navigation.
      */
      ( function() {

         if ( header_fixed_setting.fixed_header == '1' ) {
             var header_fixed = jQuery('.fixed-on');
             var p_to_top     = header_fixed.position().top;

             jQuery(window).scroll(function(){
                 if(jQuery(document).scrollTop() > p_to_top) {
                     header_fixed.addClass('header-fixed');
                     jQuery('.wpnetbase_asl_results').addClass('as-fixed');
                     header_fixed.stop().animate({},300);
                     if ( jQuery("body").hasClass('header-transparent') ) {
                         
                     } else {
                         //jQuery('.site-content').css('padding-top', header_fixed.height());
                     }
                    
                 } else {
                     header_fixed.removeClass('header-fixed');
                     jQuery('.wpnetbase_asl_results').removeClass('as-fixed');
                     header_fixed.stop().animate({},300);
                     if (jQuery("body").hasClass('header-transparent') ) {
                         
                     } else {
                         jQuery('.site-content').css('padding-top', '0');
                     }
                 }
             });
         }

     })();
     /**
      * Jquery Add to wishlist
      */
     jQuery( "a.add_to_wishlist" ).html("");
     jQuery( "a.yith-wcqv-button" ).html("");
     jQuery( "a.compare" ).html("");

     jQuery("ul.products a.add_to_cart_button.product_type_gift-card").html("");     

	   jQuery( ".yith-wcwl-wishlistexistsbrowse a" ).html("");
     jQuery( ".yith-wcwl-wishlistaddedbrowse a" ).html("");
	 
     jQuery( ".yith-wcwl-wishlistexistsbrowse .feedback" ).remove();
	   jQuery( ".yith-wcwl-wishlistaddedbrowse .feedback" ).remove();
	 
   jQuery("ul.products a.add_to_cart_button.product_type_gift-card").append("<span>Select amount</span>");
     jQuery( "a.add_to_wishlist" ).append( "<span>Wishlist</span>" );
     jQuery( "a.yith-wcqv-button" ).append( "<span>Quickview</span>" );
     jQuery( "a.compare" ).append( "<span>Compare</span>" );
  	 jQuery( ".yith-wcwl-wishlistexistsbrowse a" ).append( "<span>Browse Wishlist</span>" );
     jQuery( ".yith-wcwl-wishlistaddedbrowse a" ).append( "<span>Browse Wishlist</span>" );
     
     jQuery(".single-product #primary .summary .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a").attr("title", "Browse Wishlist");

    jQuery( ".product-content-info a:first-child" ).each(function( index ) {
      let translateLabel = nb_printshop.label;
      if (jQuery(this).text() == translateLabel['start_desgin']){
        jQuery(this).html("<span>" + translateLabel['start_desgin'] + "</span>" );
        jQuery( this ).addClass('nbt-desginer-btn');
      }
    });
});
/**
 *Jquery Menu
 */
jQuery(document).ready(function() {
    jQuery( "#netbase-responsive-toggle" ).on("click",function(e){
        e.preventDefault();
        jQuery(".header-right-wrap-top").animate({ width: 'toggle', height: '8335px'});
    });

    jQuery("#close-netbase-menu ").on("click",function(){
    jQuery(".header-right-wrap-top").animate({ width: 'toggle', height: '8335px'});
    });   
    if(jQuery('html').innerHeight() < jQuery(window).innerHeight()){
      var $window = jQuery(window).innerHeight();
      var $footer = jQuery('footer').innerHeight();
      var $header = jQuery('header').innerHeight();
      var $content = $window - ( $header + $footer );
      jQuery('#content').innerHeight($content);
    }
});
 
var $sync1 = jQuery(".featured-gallery"),
    $sync2 = jQuery(".thumb-gallery"),
    flag = false,
    duration = 300;

$sync1
    .owlCarousel({
        items: 1,
        margin: 10,
        animateOut: 'fadeOut',
        mouseDrag: false,
        dots: false,
        nav: true,
        navText: ["<i class=\"fa fa-angle-left\" aria-hidden=\"true\"></i>","<i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i>"]
    })
    .on('changed.owl.carousel', function (e) {
        if (!flag) {
            flag = true;
            $sync2.trigger('to.owl.carousel', [e.item.index, duration, true]);
            flag = false;
        }
    });

$sync2
    .owlCarousel({
        margin: 10,
        items: 4,
        center: false,
        dots: false,
        navRewind: false,
        nav: true,
        navText: ["<i class=\"fa fa-angle-left\" aria-hidden=\"true\"></i>","<i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i>"]
    })
    .on('click', '.owl-item', function () {
        $sync1.trigger('to.owl.carousel', [jQuery(this).index(), duration, true]);

    })
    .on('changed.owl.carousel', function (e) {
        if (!flag) {
            flag = true;
            $sync1.trigger('to.owl.carousel', [e.item.index, duration, true]);
            flag = false;
        }


    });

// jQuery('.product-images-carousel').owlCarousel({
//     loop:true,
//     margin:10,
//     nav:true,
//     responsive:{
//         0:{
//             items:1
//         },
//         600:{
//             items:3
//         },
//         1000:{
//             items:5
//         }
//     }
// });
//
// var swiperInit = function() {
//     'use strict'
//     if (jQuery('.featured-gallery').length && jQuery('.thumb-gallery').length){
//         var featuredObj = {};
//
//         featuredObj.nextButton = '.swiper-button-next';
//         featuredObj.prevButton = '.swiper-button-prev';
//
//         var galleryTop = new Swiper('.featured-gallery', featuredObj);
//
//         var thumbObj = {
//             spaceBetween: 10,
//             centeredSlides: true,
//             slidesPerView: 4,
//             touchRatio: 0.2,
//             slideToClickedSlide: true
//         };
//         var galleryThumbs = new Swiper('.thumb-gallery', thumbObj);
//
//         galleryTop.params.control = galleryThumbs;
//         galleryThumbs.params.control = galleryTop;
//     }
// };
// swiperInit();