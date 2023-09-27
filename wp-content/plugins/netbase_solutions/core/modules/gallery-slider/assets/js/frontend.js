jQuery( function( $ ) {
	var vertical = $('.twist-wrap').attr('data-gallery_layout');
	var nbtgs_js = {
		init: function(){
			if(jQuery().venobox) {
				this.call_venobox();
			}
			if(jQuery().slick) {
				this.call_slick();
			}
				
			if(jQuery().zoom) {
				$(window).load(function(){
					$('.nbt-gallery-horizontal .slider-nav, .woocommerce div.product div.images.twist-wrap .wc-product-gallery-image').show();
				});
				$('.woocommerce-product-gallery__image img').wrap('<span class="slider-inline-block" style="display:inline-block"></span>').parent().zoom({touch: false});
				$('.woocommerce-product-gallery__image img').load(function() {
					$('.nbt-gallery-horizontal .slider-nav, .woocommerce div.product div.images.twist-wrap .wc-product-gallery-image').show();
				    var imageObj = $('.woocommerce-product-gallery__image a');
				    if (!(imageObj.width() == 1 && imageObj.height() == 1)) {
				  
				    	$('.twist-pgs .woocommerce-product-gallery__image , #slide-nav-pgs .slick-slide .product-gallery__image_thumb').trigger('click');
				   		$('.woocommerce-product-gallery__image img').trigger('zoom.destroy');
				   		$('.woocommerce-product-gallery__image img').wrap('<span class="slider-inline-block" style="display:inline-block"></span>').parent().zoom({touch: false});

				   		/* Get first image */
				   		var src = $('.woocommerce-product-gallery__image.slick-active img').attr('src');
				   		$('.slider-nav .slick-current img').attr('src', src);
				   	
				   		
				    }
				});
			}
	 
		},
		call_venobox: function(){
			$('.venobox').venobox({
				framewidth: '800px',
				autoplay: true,
				titleattr: 'data-title',
				titleBackground: '#000000',
				titleBackground: '#000000',
				titleColor: '#fff',
				numerationColor: '#fff',
				arrowsColor: '5',
				titlePosition: 'bottom',
				numeratio: true,
				spinner : 'double-bounce',
				spinColor: '#fff',
				border: '5px',
				bgcolor: '#000000',
				infinigall: false,
				numerationPosition: 'bottom'
			});
		},
		call_slick: function(){
			$('.wc-product-image-gallery .twist-pgs').slick({
				accessibility: false,//prevent scroll to top
				lazyLoad: 'progressive',
				slidesToShow: 1,
				slidesToScroll: 1,
				arrows: true,
				fade: false,
				swipe :true,


				prevArrow: '<i class="btn-prev dashicons dashicons-arrow-left-alt2"></i>',
				nextArrow: '<i class="btn-next dashicons dashicons-arrow-right-alt2"></i>',
				rtl: false,
				infinite: false,
				autoplay: true,
				pauseOnDotsHover: true,
				autoplaySpeed: '5000',
				asNavFor: '#slide-nav-pgs',
				dots :false,
			 });

					  		

			$('.wc-product-image-gallery #slide-nav-pgs').slick({
				accessibility: false,//prevent scroll to top
				isSyn: false,//not scroll main image

				slidesToShow: 4,
				slidesToScroll: 4,
				infinite: false,
				asNavFor: '.twist-pgs',
				prevArrow: '<i class="btn-prev dashicons dashicons-arrow-left-alt2"></i>',
				nextArrow: '<i class="btn-next dashicons dashicons-arrow-right-alt2"></i>',
				dots: false,
				centerMode: false,
				rtl: false,
				vertical: JSON.parse(vertical),
				draggable: true,
				focusOnSelect: true,
				responsive: [
				{
				  breakpoint: 767,
				  settings: {
				    slidesToShow: 3,
				    slidesToScroll: 3,
				    vertical: false,
				    autoplay: false,//no autoplay in mobile
					isMobile: true,// let custom knows on mobile
					arrows: false //hide arrow on mobile
				  }
				},
				]
			});
		}

	}
	nbtgs_js.init();
});