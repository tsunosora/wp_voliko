(function($) {
'use strict';

    jQuery.nbMediaUploader({
        action : 'get_image',
        uploaderTitle : WooPanel.label.i18n_image_title,
        btnSelect : WooPanel.label.i18n_set_image,
        btnSetText : WooPanel.label.i18n_set_image,
        multiple : false,
    });

  // Asynchronous load
  var map,
    map_object = {
      is_loaded: true,
      marker: null,
      changed: false,
      store_location: null,
      map_marker: null,
      intialize: function(_callback) {

        var API_KEY = '';
        if (jsMap && jsMap.apiKey) {
          API_KEY = '&key=' + jsMap.apiKey;
        }

        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = '//maps.googleapis.com/maps/api/js?libraries=places,drawing&' +
          'callback=asl_map_intialized' + API_KEY;
        //+'callback=asl_map_intialized';
        document.body.appendChild(script);
        this.cb = _callback;
      },
      render_a_map: function(_lat, _lng) {



        var hdlr = this,
          map_div = document.getElementById('map_canvas'),
          _draggable = true;

        hdlr.store_location = (_lat && _lng) ? [parseFloat(_lat), parseFloat(_lng)] : [-37.815, 144.965];

        var latlng = new google.maps.LatLng(hdlr.store_location[0], hdlr.store_location[1]);


        if (!map_div) return false;

        var mapOptions = {
          zoom: 17,
          minZoom: 8,
          center: latlng,
          //maxZoom: 10,
          mapTypeId: google.maps.MapTypeId.ROADMAP,
          styles: [{ "stylers": [{ "saturation": -100 }, { "gamma": 1 }] }, { "elementType": "labels.text.stroke", "stylers": [{ "visibility": "off" }] }, { "featureType": "poi.business", "elementType": "labels.text", "stylers": [{ "visibility": "off" }] }, { "featureType": "poi.business", "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] }, { "featureType": "poi.place_of_worship", "elementType": "labels.text", "stylers": [{ "visibility": "off" }] }, { "featureType": "poi.place_of_worship", "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] }, { "featureType": "road", "elementType": "geometry", "stylers": [{ "visibility": "simplified" }] }, { "featureType": "water", "stylers": [{ "visibility": "on" }, { "saturation": 50 }, { "gamma": 0 }, { "hue": "#50a5d1" }] }, { "featureType": "administrative.neighborhood", "elementType": "labels.text.fill", "stylers": [{ "color": "#333333" }] }, { "featureType": "road.local", "elementType": "labels.text", "stylers": [{ "weight": 0.5 }, { "color": "#333333" }] }, { "featureType": "transit.station", "elementType": "labels.icon", "stylers": [{ "gamma": 1 }, { "saturation": 50 }] }]
        };

        hdlr.map_instance = map = new google.maps.Map(map_div, mapOptions);


        // && navigator.geolocation && _draggable
        if ( ! hdlr.store_location || typeof hdlr.store_location[0] == "undefined" ) {

          /*navigator.geolocation.getCurrentPosition(function(position){
          	
          	hdlr.changed = true;
          	hdlr.store_location = [position.coords.latitude,position.coords.longitude];
          	var loc = new google.maps.LatLng(position.coords.latitude,  position.coords.longitude);
          	hdlr.add_marker(loc);
          	map.panTo(loc);
          });*/

          hdlr.add_marker(latlng);
        } else if (hdlr.store_location) {
          if (isNaN(hdlr.store_location[0]) || isNaN(hdlr.store_location[1])) return;
          //var loc = new google.maps.LatLng(hdlr.store_location[0], hdlr.store_location[1]);
          hdlr.add_marker(latlng);
          map.panTo(latlng);
        }
      },
      add_marker: function(_loc) {
        var hdlr = this;

        hdlr.map_marker = new google.maps.Marker({
          draggable: true,
          position: _loc,
          map: map
        });

        var marker_icon = new google.maps.MarkerImage(jsMap.asset + 'assets/images/map-pin.png');
        marker_icon.size = new google.maps.Size(24, 39);
        marker_icon.anchor = new google.maps.Point(24, 39);
        hdlr.map_marker.setIcon(marker_icon);
        hdlr.map_instance.panTo(_loc);

        google.maps.event.addListener(
          hdlr.map_marker,
          'dragend',
          function() {

            hdlr.store_location = [hdlr.map_marker.position.lat(), hdlr.map_marker.position.lng()];
            hdlr.changed = true;
            var loc = new google.maps.LatLng(hdlr.map_marker.position.lat(), hdlr.map_marker.position.lng());
            //map.setPosition(loc);
            map.panTo(loc);

            store_changed(hdlr.store_location);
          });

      }
    };


    function store_changed(_position) {

      $('#lat').val(_position[0]);
      $('#lng').val(_position[1]);
    }


  function codeAddress(_address, _callback) {

    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({ 'address': _address }, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        _callback(results[0].geometry);
      } else {
        if( ! jsMap.apiKey ) {
          alert(jsMap.missing_apikey);
        }
      }
    });
  };

	window['asl_map_intialized'] = function() {
		var storeID = parseInt($('#storeID').val()),
			lat = parseFloat( $('#lat').val() ),
			lng = parseFloat( $('#lng').val() );

		if( storeID ) {
			map_object.render_a_map(lat, lng);
		}else {
      map_object.render_a_map(parseFloat(jsMap.default_lat), parseFloat(jsMap.default_lng));
    }
		
		
	};

      //init the maps
  jQuery(function() {
    $('#postal_code,#city,#state, #street').on('blur', function(e) {
      if ( $('#street').val() && $('#city').val() && $('#postal_code').val() ) {
         var address = [$('#street').val(), $('#city').val(), $('#postal_code').val(), $('#state').val()];

          var q_address = [];

          for (var i = 0; i < address.length; i++) {

            if (address[i])
              q_address.push(address[i]);
          }

          var _country = jQuery('#country option:selected').text();

          //Add country if available
          if ( _country ) {
            q_address.push(_country);
          }

          address = q_address.join(', ');

          codeAddress(address, function(_geometry) {
            var s_location = [_geometry.location.lat(), _geometry.location.lng()];
            var loc = new google.maps.LatLng(s_location[0], s_location[1]);
            map_object.map_marker.setPosition(_geometry.location);
            map.panTo(_geometry.location);
            store_changed(s_location);
          });
      }
    });

    if ( typeof google == 'undefined' ) {
      map_object.intialize();
    }
	});
})(jQuery);