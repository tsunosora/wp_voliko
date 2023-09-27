        var mapDIV = document.getElementById('map_canvas'),
        	mapLat = parseFloat(mapDIV.getAttribute('data-lat')),
        	mapLng = parseFloat(mapDIV.getAttribute('data-lng')),
        	mapIcon = mapDIV.getAttribute('data-icon');


        var map = new google.maps.Map(mapDIV, {
          zoom: 16,
          center: {lat: mapLat, lng: mapLng}
        });

        setMarkers(map);


         function setMarkers(map) {
        // Adds markers to the map.

        // Marker sizes are expressed as a Size of X,Y where the origin of the image
        // (0,0) is located in the top left of the image.

        // Origins, anchor positions and coordinates of the marker increase in the X
        // direction to the right and in the Y direction down.
        var image = {
          url: mapIcon,
          // This marker is 20 pixels wide by 32 pixels high.
          size: new google.maps.Size(24, 39),
          // The origin for this image is (0, 0).
          origin: new google.maps.Point(0, 0),
          // The anchor for this image is the base of the flagpole at (0, 32).
          anchor: new google.maps.Point(0, 32)
        };
        // Shapes define the clickable region of the icon. The type defines an HTML
        // <area> element 'poly' which traces out a polygon as a series of X,Y points.
        // The final coordinate closes the poly by connecting to the first coordinate.
        var shape = {
          coords: [1, 1, 1, 20, 18, 20, 18, 1],
          type: 'poly'
        };

          var marker = new google.maps.Marker({
            position: {lat: mapLat, lng: mapLng},
            map: map,
            icon: image,
            shape: shape,
            title: 'xxx',
            zIndex: 1
          });
   
      }