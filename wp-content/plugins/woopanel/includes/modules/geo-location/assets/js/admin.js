(function($) {
'use strict';
	if( typeof H == 'undefined' ) {
		return;
	}

	var platform = new H.service.Platform({
		app_id: WooPanel.modules.geoApplicationID,
		app_code: WooPanel.modules.geoApplicationCode,
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
	
	var ajaxRequest = new XMLHttpRequest();
	var group = new H.map.Group();
	var bubble;

	var geoLocationAdmin = {
		
		mapContainer: document.getElementById('wplMapShow'),

		/**
		 * Initialize variations actions
		 */
		init: function() {
			if( $('#original_post_title').length > 0 ) {
				this.load();
			}
			$(document).on('click', '.m-tabs li .m-nav__link', this.load );
			$(document).on('keyup', '#user_geo_location', this.delay(this.userSetLocation, 800) );

			ajaxRequest.addEventListener("load", this.onAutoCompleteSuccess);
			ajaxRequest.addEventListener("error", this.onAutoCompleteFailed);
			ajaxRequest.responseType = "json";
		},
		
		load: function() {
			console.log('load');
			$('#wplMapShow').empty();

				var lat = $('#woopanel_map_lat').val(),
					lng = $('#woopanel_map_lng').val();
				if ( ! lat ) {
					lat = 21.0401;
					lng = 105.8504;
				}
	
				//Step 2: initialize a map - this map is centered over Europe
				var map = new H.Map($('#wplMapShow')[0],
				  defaultLayers.normal.map,{
				  center: { lat: lat, lng: lng },
				  zoom: 15,
				  pixelRatio: pixelRatio
				});
				
				var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
				
		
				// Create an icon, an object holding the latitude and longitude, and a marker:
				var icon = new H.map.Icon(svgMarkup),
				  coords = { lat: lat, lng: lng },
				  marker = new H.map.Marker(coords, {icon: icon});

				// Add the marker to the map and center the map at the location of the marker:
				map.addObject(marker);
				map.setCenter(coords);
				geoLocationAdmin.setUpClickListener(map);
		},
		
		userSetLocation: function(e) {
			e.preventDefault();
			
			var $this = this,
				AUTOCOMPLETION_URL = 'https://autocomplete.geocoder.api.here.com/6.2/suggest.json',
				query = '';
				
			if (query != $this.value){
				if ($this.value.length >= 1){

				  /**
				  * A full list of available request parameters can be found in the Geocoder Autocompletion
				  * API documentation.
				  *
				  */
				  var params = '?' +
					'query=' +  encodeURIComponent($this.value) +   // The search text which is the basis of the query
					'&beginHighlight=' + encodeURIComponent('<mark>') + //  Mark the beginning of the match in a token. 
					'&endHighlight=' + encodeURIComponent('</mark>') + //  Mark the end of the match in a token. 
					'&maxresults=1' +  // The upper limit the for number of suggestions to be included 
									  // in the response.  Default is set to 5.
					'&app_id=' + WooPanel.modules.geoApplicationID +
					'&app_code=' + WooPanel.modules.geoApplicationCode;
				  ajaxRequest.open('GET', AUTOCOMPLETION_URL + params );
				  ajaxRequest.send();
				}
			}
			
			query = $this.value;
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
		
		/**
		 * If the text in the text box  has changed, and is not empty,
		 * send a geocoding auto-completion request to the server.
		 *
		 * @param {Object} textBox the textBox DOM object linked to this event
		 * @param {Object} event the DOM event which fired this listener
		 */
		setUpClickListener: function(map) {
			map.addEventListener('tap', function (evt) {
				var coord = map.screenToGeo(evt.currentPointer.viewportX,
					evt.currentPointer.viewportY),
					lat = Math.abs(coord.lat.toFixed(4)),
					lng = Math.abs(coord.lng.toFixed(4));
	
				$('#woopanel_map_lat').val(lat);
				$('#woopanel_map_lng').val(lng);

			});
		},
		
		/*
		* The styling of the suggestions response on the map is entirely under the developer's control.
		* A representitive styling can be found the full JS + HTML code of this example
		* in the functions below:
		*/
		onAutoCompleteSuccess: function() {
			geoLocationAdmin.clearOldSuggestions();
			geoLocationAdmin.addSuggestionsToPanel(this.response);  // In this context, 'this' means the XMLHttpRequest itself.
			geoLocationAdmin.addSuggestionsToMap(this.response);
		},
		
		/**
		 * This function will be called if a communication error occurs during the XMLHttpRequest
		 */
		onAutoCompleteFailed: function() {
			alert('Ooops!');
		},
		

		
		/**
		* This function will be called once the Geocoder REST API provides a response
		* @param  {Object} result          A JSONP object representing the  location(s) found.
		*/	
		addSuggestionsToMap: function(response) {
			var onGeocodeSuccess = function (result) {
				var marker,
					locations = result.Response.View[0].Result,
					i;
	
				$('#wplMapShow').empty();
				
				var map = new H.Map($('#wplMapShow')[0],
				  defaultLayers.normal.map,{
				  center: {lat: locations[0].Location.DisplayPosition.Latitude, lng: locations[0].Location.DisplayPosition.Longitude },
				  zoom: 15,
				  pixelRatio: pixelRatio
				});
				
				var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
		
		
				// Create an icon, an object holding the latitude and longitude, and a marker:
				var icon = new H.map.Icon(svgMarkup),
				  coords = { lat: locations[0].Location.DisplayPosition.Latitude, lng: locations[0].Location.DisplayPosition.Longitude },
				  marker = new H.map.Marker(coords, {icon: icon});

				// Add the marker to the map and center the map at the location of the marker:
				map.addObject(marker);
				map.setCenter(coords);
				
				var mapObjectFinal = {};
				mapObjectFinal['lat'] = locations[0].Location.DisplayPosition.Latitude;
				mapObjectFinal['lng'] = locations[0].Location.DisplayPosition.Longitude;
				$('#woopanel_map_lat').val(mapObjectFinal['lat']);
				$('#woopanel_map_lng').val(mapObjectFinal['lng']);
				
				map.addEventListener('tap', function (evt) {
					var coord = map.screenToGeo(evt.currentPointer.viewportX,
						evt.currentPointer.viewportY),
						mapObject = {},
						lat = Math.abs(coord.lat.toFixed(4)),
						lng = Math.abs(coord.lng.toFixed(4));
						
					mapObject['lat'] = lat;
					mapObject['lng'] = lng;
					

					$('#user_geo_position').val(JSON.stringify(mapObject));

				});
			},
			
			/**
			 * This function will be called if a communication error occurs during the JSON-P request
			 * @param  {Object} error  The error message received.
			 */
			onGeocodeError = function (error) {
				alert('Ooops!');
			},
			
			 /**
			 * This function uses the geocoder service to calculate and display information
			 * about a location based on its unique `locationId`.
			 *
			 * A full list of available request parameters can be found in the Geocoder API documentation.
			 * see: http://developer.here.com/rest-apis/documentation/geocoder/topics/resource-search.html
			 *
			 * @param {string} locationId    The id assigned to a given location
			 */
			geocodeByLocationId = function (locationId) {
				var geocodingParameters = {
					locationId : locationId
				};

				geocoder.geocode (
					geocodingParameters,
					onGeocodeSuccess,
					onGeocodeError
				);
			}

		  /* 
		   * Loop through all the geocoding suggestions and make a request to the geocoder service
		   * to find out more information about them.
		   */
			response.suggestions.forEach(function (item, index, array) {
	
				geocodeByLocationId(item.locationId);
			});
		},
		
		clearOldSuggestions: function() {
			group.removeAll ();
			if(bubble){
				bubble.close();
			}
		},
		
		addSuggestionsToPanel: function(response) {
			$('#suggestions').html( JSON.stringify(response, null, ' ') );
		}
	}

	geoLocationAdmin.init();
})(jQuery);