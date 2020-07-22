function map_plot(lat, lon){
	// Marker
	var marker = new google.maps.Marker( {
		map: map ,
		position: new google.maps.LatLng(lat, lon) ,
	} ) ;
}

function map_line(lon1, lat1, lon2, lat2){
	var path = [
		new google.maps.LatLng(lat1, lon1),
		new google.maps.LatLng(lat2, lon2)
	];
	var line = new google.maps.Polyline({
		path: path,
		strokeColor: "#FF0000",
		strokeOpacity: 1.0,
		strokeWeight: 5 
	});
	line.setMap(map);
}
