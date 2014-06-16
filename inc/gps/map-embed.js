if ("function" === typeof jQuery) jQuery(document).ready( function($) {
	var setup_map = function(element, lat, lng) {
		// We'll use the venue coords to centre the map and for the marker
		var coords = new google.maps.LatLng(lat, lng);

		// Map setup
		var options = {
			zoom: parseInt(eventrocket_map.zoom),
			center: coords
		};

		// Marker setup
		var marker = new google.maps.Marker({
			position: coords
		});

		// Create the map and add the marker
		eventrocket_map.embedded_map = new google.maps.Map(element, options);
		marker.setMap(eventrocket_map.embedded_map);
	};

	// We need the map data array to exist
	if ("undefined" === typeof eventrocket_map) return;

	// Iterate through available map data and try to find each corresponding map placeholder
	$.each(eventrocket_map.markers, function(index, value) {
		var map_holder = document.getElementById("eventrocket_map_" + index);
		if (null !== map_holder) setup_map(map_holder, value[0], value[1]);
	});
});