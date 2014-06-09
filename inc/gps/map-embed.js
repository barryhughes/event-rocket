if ("function" === typeof jQuery) jQuery(document).ready( function($) {
	var setup_map = function(element, lat, lng) {
		// We'll use the venue coords to centre the map and for the marker
		var coords = new google.maps.LatLng(lat, lng);

		// Map setup
		var options = {
			zoom: 8,
			center: coords
		};

		// Marker setup
		var marker = new google.maps.Marker({
			position: coords
		});

		// Create the map and add the marker
		var map = new google.maps.Map(element, options);
		marker.setMap(map);
	};

	// We need the map data array to exist
	if ("undefined" === typeof eventrocket_map_data) return;

	// Iterate through available map data and try to find each corresponding map placeholder
	$.each(eventrocket_map_data, function(index, value) {
		var map_holder = document.getElementById("eventrocket_map_" + index);
		if (null !== map_holder) setup_map(map_holder, value[0], value[1]);
	});
});