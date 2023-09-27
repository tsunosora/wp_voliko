(function($) {
'use strict';

	if( typeof H == 'undefined') {
		return;
	}
	var platform = new H.service.Platform({
		app_id: wplModules.geoLocation.ApplicationID,
		app_code: wplModules.geoLocation.ApplicationCode,
		useCIT: false,
		useHTTPS: true
	});
	
	var geocoder = platform.getGeocodingService();
	var pixelRatio = window.devicePixelRatio || 1;
	var defaultLayers = platform.createDefaultLayers({
		tileSize: pixelRatio === 1 ? 256 : 512,
		ppi: pixelRatio === 1 ? undefined : 320
	});
	
	var svgMarkup = '<svg width="24" height="24" ' +
					'xmlns="http://www.w3.org/2000/svg">' +
					'<rect stroke="white" fill="#1b468d" x="1" y="1" width="22" ' +
					'height="22" /><text x="12" y="18" font-size="12pt" ' +
					'font-family="Arial" font-weight="bold" text-anchor="middle" ' +
					'fill="white">H</text></svg>';
	
	var geoLocationFrontend = {
		xhr_view: null,
		init: function() {
			
			// Single load map
			$('.woopanel-geolocation-single').each(function( index ) {
				geoLocationFrontend.loadMap($(this), false, false);
			});
			
			// Widget load map
			$('.woopanel-geolocation-advanced').each(function( index ) {
				geoLocationFrontend.loadMap($(this), false, false);
			});

			$(document).on('click', '.wc-tabs > *', this.loadMapTab );
			$(document).on('keyup', '.wpl-search-products, .wpl-search-location, .wpl-search-vendors', this.delay(this.searchProduct, 800) );
			$(document).on('change', '.wpl-product-cat', this.searchProduct );

			if( $('.woopanel-near-store').length > 0) {
				this.getNearStore();
			}
		},
		
		getNearStore: function() {
			if (navigator.geolocation) {
				navigator.geolocation.getCurrentPosition(geoLocationFrontend.loadNearStore);
			} else {
				console.log("Geolocation is not supported by this browser.");
			}
		},
	
		
		loadNearStore: function(position) {
			
			$('.woopanel-near-store').each(function() {
				var attr = {},
					$this = $(this);
					
				$.each(this.attributes, function() {
					if( this.name != 'class' ) {
						attr[this.name] = this.value;
					}
				});
				
				var data = {
					action: 'woopanel_geolocation_nearstore',
					lat: position.coords.latitude,
					lng: position.coords.longitude,
					attributes: attr
				};

				$this.html('<div class="loader"></div>');
				$.ajax({
					url: wplModules.ajax_url,
					data: data,
					type: 'POST',
					success: function( response ) {
						if( response.complete != undefined ) {
							$this.html(response.html);
						}
					}
				});
			});
			

			
			
		},
		
		loadMap: function($wrapper, lat, lng) {
			var latlng = $wrapper.attr('data-position'),
				$mapDiv = $wrapper.find('.woopanel-geolocation-map');


			if( typeof latlng != 'undefined' && latlng.length > 0) {
				$mapDiv.empty();
				
				var position = JSON.parse(latlng);

				if( ! lat ) {
					var lat = position.lat;
				}
				
				if( ! lng ) {
					var lng = position.lng;
				}
				
				var map = new H.Map($mapDiv[0],
					defaultLayers.normal.map,{
					center: { lat: lat, lng: lng },
					zoom: 15,
					pixelRatio: pixelRatio
				});
				
				var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
				
		
				// Create an icon, an object holding the latitude and longitude, and a marker:
				var icon = new H.map.Icon(svgMarkup),
				  coords = { lat: position.lat, lng: position.lng },
				  marker = new H.map.Marker(coords, {icon: icon});

				// Add the marker to the map and center the map at the location of the marker:
				map.addObject(marker);
				map.setCenter(coords);
			}
		},
		
		loadMapTab: function() {
			var $wrapper = $('.woopanel-geolocation-single');
			if( $wrapper.length > 0 && $(this).hasClass('location_tab_tab') ) {
				geoLocationFrontend.loadMap($wrapper, false, false);
			}
		},
		
		searchProduct: function() {
			var wrapper = $(this).closest('.woopanel-geolocation-wrapper'),
				type = wrapper.attr('data-type'),
				$mapDiv = wrapper.find('.woopanel-geolocation-map'),
				$location = wrapper.find('.wpl-search-location').val();
			
			if( $('body').hasClass('archive') ) {
				var data = {
					product: 	wrapper.find('.wpl-search-products').val(),
					cat:	wrapper.find('.wpl-product-cat').val(),
				}
			}else {
				var data = {
					vendor: 	wrapper.find('.wpl-search-vendors').val(),
				}
			}

			if( ! $location ) {
				alert('Please enter address!');
			}

			if( this.xhr_view && this.xhr_view.readyState != 4 ){ this.xhr_view.abort(); }
			$mapDiv.html('<div class="map-loader"></div>');
			this.xhr_view = $.ajax({
				url: wplModules.ajax_url,
				data: $.extend({},{
					action:     'woopanel_geolocation_search_products',
					location:	$location,
					type: type
				}, data),
				type: 'POST',
				success: function( response ) {

					if( response.complete != undefined ) {
						//$mapDiv.empty();
						var map = new H.Map($mapDiv[0],
							defaultLayers.normal.map,{
							center: { lat: response.lat, lng: response.lng },
							zoom: 15,
							pixelRatio: pixelRatio
						});
						
						var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
						
				
						$.each( response.items, function( key, value ) {
							console.log( key );
							console.log(value);

							var icon = new H.map.Icon(svgMarkup),
								coords = { lat: value.lat, lng: value.lng },
								marker = new H.map.Marker(coords, {icon: icon});

							map.addObject(marker);
							map.setCenter(coords);
						});
						
						
						
					}else {
						$('.woopanel-geolocation-map').empty();
						if( response.error != undefined ) {
							alert(response.error);
							geoLocationFrontend.loadMap(wrapper, response.lat, response.lng);
						}

						$('.woopanel-wrapper').addClass('empty-vendor');
					}

		
					$('.woopanel-wrapper').html(response.html);
	
					
				}
			});
		},
		
		delay: function(callback, ms) {
			var timer = 0;
			return function() {
				var context = this, args = arguments;
				clearTimeout(timer);
				timer = setTimeout(function () {
					callback.apply(context, args);
				}, ms || 0);
			}
		},
	}
	
	geoLocationFrontend.init();
})(jQuery);